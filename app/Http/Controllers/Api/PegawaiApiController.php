<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Http\Resources\PegawaiResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PegawaiApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pegawai::query();

        // Search by name or NIP
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Filter by jabatan
        if ($request->has('jabatan')) {
            $query->where('jabatan', 'like', "%{$request->jabatan}%");
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $pegawai = $query->orderBy('nama')->paginate($perPage);

        return PegawaiResource::collection($pegawai);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'required|string|max:18|unique:pegawai,nip',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'golongan' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $pegawai = Pegawai::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pegawai created successfully',
                'data' => new PegawaiResource($pegawai),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create pegawai',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pegawai = Pegawai::with(['pendidikanFormal', 'diklat', 'dokumen'])->find($id);

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PegawaiResource($pegawai),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pegawai = Pegawai::find($id);

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nip' => 'sometimes|required|string|max:18|unique:pegawai,nip,' . $id,
            'nama' => 'sometimes|required|string|max:255',
            'jabatan' => 'sometimes|required|string|max:255',
            'golongan' => 'sometimes|required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $pegawai->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pegawai updated successfully',
                'data' => new PegawaiResource($pegawai),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update pegawai',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pegawai = Pegawai::find($id);

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai not found',
            ], 404);
        }

        try {
            $pegawai->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pegawai deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete pegawai',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
