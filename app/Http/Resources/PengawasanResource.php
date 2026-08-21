<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengawasanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nomor_st' => $this->nomor_st,
            'tanggal_st' => $this->tanggal_st?->format('Y-m-d'),
            'lama_penugasan' => $this->lama_penugasan,
            'jenis_penugasan' => $this->jenis_penugasan,
            'uraian_penugasan' => $this->uraian_penugasan,
            'lokasi_penugasan' => $this->lokasi_penugasan,
            'alat_angkut' => $this->alat_angkut,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'file_laporan' => $this->file_laporan ? asset('storage/' . $this->file_laporan) : null,

            // Personil
            'penanggung_jawab' => $this->whenLoaded('penanggungJawab', function() {
                return $this->penanggungJawab ? new PegawaiResource($this->penanggungJawab) : null;
            }),
            'pengendali_teknis' => $this->whenLoaded('pengendaliTeknis', function() {
                return $this->pengendaliTeknis ? new PegawaiResource($this->pengendaliTeknis) : null;
            }),
            'ketua_tim' => $this->whenLoaded('ketuaTim', function() {
                return $this->ketuaTim ? new PegawaiResource($this->ketuaTim) : null;
            }),
            'anggota' => $this->whenLoaded('anggota', function() {
                return PegawaiResource::collection($this->anggota);
            }),

            // Dasar Hukum
            'dasar_hukum' => $this->whenLoaded('dasarHukum', function() {
                return DasarHukumResource::collection($this->dasarHukum);
            }),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
