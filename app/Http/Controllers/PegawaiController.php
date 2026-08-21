<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1);
        $perPage = 10;

        $response = \Illuminate\Support\Facades\Http::withToken(config('services.pegawai.token'))
            ->get(config('services.pegawai.url'), [
                'search' => $search,
                'page' => $page,
                'per_page' => $perPage,
            ]);

        if ($response->successful()) {
            $apiData = $response->json();
            
            if (isset($apiData['meta'])) {
                $items = collect($apiData['data'])->map(function($item) {
                    return (object) $item;
                });
                
                $pegawai = new \Illuminate\Pagination\LengthAwarePaginator(
                    $items,
                    $apiData['meta']['total'],
                    $apiData['meta']['per_page'],
                    $apiData['meta']['current_page'],
                    ['path' => route('pegawai.index'), 'query' => request()->query()]
                );
            } else {
                $items = collect($apiData['data'] ?? $apiData)->map(function($item) {
                    return (object) $item;
                });
                
                $perPage = 10;
                $currentPage = (int) $page;
                $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

                $pegawai = new \Illuminate\Pagination\LengthAwarePaginator(
                    $currentItems, 
                    $items->count(), 
                    $perPage, 
                    $currentPage, 
                    ['path' => route('pegawai.index'), 'query' => request()->query()]
                );
            }
        } else {
            $pegawai = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, ['path' => route('pegawai.index')]);
            session()->flash('error', 'Gagal mengambil data dari API Eksternal (Status: ' . $response->status() . ')');
        }

        return view('pegawai.index', compact('pegawai', 'search'));
    }

    /**
     * Search pegawai data via API for AJAX requests.
     */
    public function searchApi(Request $request)
    {
        $search = $request->input('search');
        
        $response = \Illuminate\Support\Facades\Http::withToken(config('services.pegawai.token'))
            ->get(config('services.pegawai.url'), [
                'search' => $search,
                'per_page' => 15,
            ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['data' => []], 200);
    }
}
