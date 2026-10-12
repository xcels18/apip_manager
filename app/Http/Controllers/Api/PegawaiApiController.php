<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PegawaiUnavailable;
use App\Http\Controllers\Controller;
use App\Services\PegawaiService;
use Illuminate\Http\Request;

class PegawaiApiController extends Controller
{
    public function index(Request $request, PegawaiService $pegawai)
    {
        $query = $request->validate(['search' => 'nullable|string|max:200', 'page' => 'sometimes|integer|min:1', 'per_page' => 'sometimes|integer|min:1|max:100']);
        $response = $pegawai->get($query + ['page' => 1, 'per_page' => 15]);
        if (! $response->successful()) {
            throw new PegawaiUnavailable;
        }
        $body = $response->json();
        if (! isset($body['meta'])) {
            $page = (int) ($query['page'] ?? 1);
            $perPage = (int) ($query['per_page'] ?? 15);
            $items = collect($body['data']);

            return response()->json([
                'data' => $items->slice(($page - 1) * $perPage, $perPage)->values(),
                'meta' => ['total' => $items->count(), 'per_page' => $perPage, 'current_page' => $page],
            ]);
        }

        return response()->json($body);
    }

    public function show(string $id, PegawaiService $pegawai)
    {
        abort_unless(ctype_digit($id), 404);
        $employee = $pegawai->all()->get($id);
        abort_unless($employee, 404);

        return response()->json(['success' => true, 'data' => $employee]);
    }
}
