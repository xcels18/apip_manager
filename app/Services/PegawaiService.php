<?php

namespace App\Services;

use App\Exceptions\PegawaiUnavailable;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class PegawaiService
{
    private ?Collection $employees = null;

    private bool $failed = false;

    public function get(array $query = []): Response
    {
        $url = config('services.pegawai.url');
        // A default pointing back at this application's proxy would recurse.
        if (! $url || rtrim($url, '/') === rtrim(config('app.url'), '/').'/api/pegawai') {
            return $this->unavailable();
        }

        try {
            $response = Http::withToken(config('services.pegawai.token'))
                ->acceptJson()->connectTimeout(3)->timeout(10)->get($url, $query);
        } catch (ConnectionException $e) {
            return $this->unavailable();
        }

        if (! $response->successful()) {
            return $this->unavailable();
        }
        $body = $response->json();
        if (! is_array($body)) {
            return $this->unavailable();
        }
        $items = $body['data'] ?? $body;
        if (! is_array($items) || ! array_is_list($items)) {
            return $this->unavailable();
        }
        foreach ($items as $item) {
            if (! is_array($item) || ! isset($item['id'], $item['nama'])
                || ! (is_int($item['id']) || (is_string($item['id']) && ctype_digit($item['id'])))
                || $item['id'] < 1 || ! is_string($item['nama'])) {
                return $this->unavailable();
            }
        }
        $meta = $body['meta'] ?? (isset($body['total']) ? $body : null);
        if ($meta !== null && (! isset($meta['total'], $meta['per_page'], $meta['current_page'])
            || ! is_numeric($meta['total']) || ! is_numeric($meta['per_page'])
            || ! is_numeric($meta['current_page']) || $meta['total'] < 0
            || $meta['per_page'] < 1 || $meta['current_page'] < 1)) {
            return $this->unavailable();
        }

        return new Response(new PsrResponse(200, ['Content-Type' => 'application/json'], json_encode([
            'data' => $items,
            ...($meta === null ? [] : ['meta' => array_intersect_key($meta, array_flip(['total', 'per_page', 'current_page', 'last_page']))]),
        ])));
    }

    public function all(): Collection
    {
        if ($this->failed) {
            throw new PegawaiUnavailable;
        }
        if ($this->employees !== null) {
            return $this->employees;
        }
        $items = collect();
        for ($page = 1; $page <= 1000; $page++) {
            $response = $this->get(['page' => $page, 'per_page' => 100]);
            if (! $response->successful()) {
                $this->failed = true;
                throw new PegawaiUnavailable;
            }
            $data = $response->json();
            $previousCount = $items->count();
            foreach ($data['data'] as $item) {
                $items->put($item['id'], (object) $item);
            }
            $meta = $data['meta'] ?? null;
            if (! $meta || $items->count() >= (int) $meta['total']) {
                return $this->employees = $items;
            }
            if ((int) $meta['current_page'] !== $page || $items->count() === $previousCount) {
                $this->failed = true;
                throw new PegawaiUnavailable;
            }
        }
        throw new PegawaiUnavailable;
    }

    public function allResponse(): Response
    {
        try {
            return new Response(new PsrResponse(200, ['Content-Type' => 'application/json'], json_encode(['data' => $this->all()->values()])));
        } catch (PegawaiUnavailable $e) {
            return $this->unavailable();
        }
    }

    private function unavailable(): Response
    {
        return new Response(new PsrResponse(503, ['Content-Type' => 'application/json'], json_encode([
            'data' => [], 'message' => 'Layanan pegawai sementara tidak tersedia. Silakan coba kembali.',
        ])));
    }
}
