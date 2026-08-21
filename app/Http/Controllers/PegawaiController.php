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

        $response = \Illuminate\Support\Facades\Http::withToken('pm8AvVAkArBgqck6lP0b2yfBahfuPzsEY9XLNAuG4ed6a0dc')
            ->get('http://localhost:8000/api/pegawai', [
                'search' => $search,
                'page' => $page
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
}
