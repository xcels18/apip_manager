<?php

namespace Tests\Feature;

use App\Models\Pengawasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApplicationAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        config(['services.pegawai.url' => 'https://pegawai.test/api/pegawai', 'services.pegawai.token' => 'audit-token']);
    }

    private function payload(): array
    {
        return ['nomor_st' => 'AUDIT-001', 'tanggal_st' => '2026-09-07', 'lama_penugasan' => 2,
            'jenis_penugasan' => 'Audit', 'uraian_penugasan' => 'Audit test', 'lokasi_penugasan' => 'Test',
            'alat_angkut' => 'darat', 'status' => 'belum_selesai', 'dasar_hukum' => ['Dasar test'],
            'penanggung_jawab_id' => 101, 'ketua_tim_id' => 102];
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_pegawai_api_is_available(): void
    {
        $this->fakePegawai();
        Sanctum::actingAs(User::factory()->create());
        $this->getJson('/api/pegawai')->assertOk();
    }

    public function test_pengawasan_api_lists_existing_records(): void
    {
        $this->fakePegawai();
        Sanctum::actingAs(User::factory()->create());
        Pengawasan::create(collect($this->payload())->except('dasar_hukum')->all());
        $this->getJson('/api/pengawasan')->assertOk();
    }

    public function test_pengawasan_api_creates_valid_record(): void
    {
        $this->fakePegawai();
        Sanctum::actingAs(User::factory()->create());
        $this->postJson('/api/pengawasan', $this->payload())->assertCreated();
    }

    public function test_failed_api_creation_does_not_leave_partial_data(): void
    {
        $this->fakePegawai();
        Sanctum::actingAs(User::factory()->create());
        \Illuminate\Support\Facades\Event::listen('eloquent.creating: App\\Models\\PengawasanDasar', function () {
            throw new \RuntimeException('Simulated child write failure');
        });
        $this->postJson('/api/pengawasan', $this->payload())->assertStatus(500)
            ->assertJsonMissing(['error' => 'Simulated child write failure']);
        $this->assertDatabaseCount('pengawasan', 0);
        $this->assertDatabaseCount('pengawasan_dasar', 0);
    }

    public function test_dashboard_handles_pegawai_connection_failure(): void
    {
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Simulated outage'));
        $this->actingAs(User::factory()->create())->get('/dashboard')->assertOk();
    }

    public function test_main_pages_render_with_available_pegawai_service(): void
    {
        Http::fake(['*' => Http::response(['data' => [], 'meta' => ['total' => 0, 'per_page' => 10, 'current_page' => 1]])]);
        $this->actingAs(User::factory()->create());
        foreach (['/dashboard', '/pegawai', '/pengawasan', '/pengawasan/create', '/laporan', '/rekap', '/setting'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_api_crud_preserves_snapshot_and_ordered_dasar_hukum(): void
    {
        $this->fakePegawai();
        Sanctum::actingAs(User::factory()->create());
        $payload = $this->payload() + ['anggota' => [103]];
        $payload['dasar_hukum'] = ['Pertama', 'Kedua'];
        $created = $this->postJson('/api/pengawasan', $payload)->assertCreated()
            ->assertJsonPath('data.ketua_tim.id', 102)->assertJsonPath('data.anggota.0.id', 103)
            ->assertJsonPath('data.dasar_hukum.1.urutan', 2);
        $id = $created->json('data.id');
        $this->resetPegawaiHttp();
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Offline'));
        $this->getJson("/api/pengawasan/$id")->assertOk()->assertJsonPath('data.ketua_tim.nama', 'Ketua Tim');
        $this->patchJson("/api/pengawasan/$id", ['uraian_penugasan' => 'Diubah', 'nomor_st' => 'UPDATED-001'])->assertOk()
            ->assertJsonPath('data.nomor_st', 'UPDATED-001')
            ->assertJsonPath('data.ketua_tim.nama', 'Ketua Tim');
        Http::assertNothingSent();
        $this->deleteJson("/api/pengawasan/$id")->assertOk();
        $this->assertDatabaseCount('pengawasan', 0);
        $this->assertDatabaseCount('pengawasan_anggota', 0);
        $this->assertDatabaseCount('pengawasan_dasar', 0);
    }

    public function test_duplicate_number_and_completed_without_report_are_validation_errors(): void
    {
        $this->fakePegawai();
        Sanctum::actingAs(User::factory()->create());
        $this->postJson('/api/pengawasan', $this->payload())->assertCreated();
        $this->postJson('/api/pengawasan', $this->payload())->assertUnprocessable()->assertJsonValidationErrors('nomor_st');
        $this->postJson('/api/pengawasan', array_replace($this->payload(), ['nomor_st' => 'OTHER', 'status' => 'selesai']))
            ->assertUnprocessable()->assertJsonValidationErrors('status');
        $this->assertDatabaseCount('pengawasan', 1);
    }

    public function test_external_employee_absent_from_local_table_can_be_assigned(): void
    {
        $this->fakePegawai();
        Sanctum::actingAs(User::factory()->create());
        $this->assertDatabaseCount('pegawai', 0);
        $this->assertSame(1, (int) \Illuminate\Support\Facades\DB::selectOne('PRAGMA foreign_keys')->foreign_keys);
        $this->postJson('/api/pengawasan', $this->payload() + ['anggota' => [103]])->assertCreated();
        $this->assertDatabaseHas('pengawasan_anggota', ['pegawai_id' => 103]);
        $this->assertDatabaseCount('pegawai', 0);
    }

    public function test_new_invalid_employee_rolls_back_the_whole_update(): void
    {
        $this->fakePegawai();
        Sanctum::actingAs(User::factory()->create());
        $id = $this->postJson('/api/pengawasan', $this->payload() + ['anggota' => [103]])->assertCreated()->json('data.id');
        $this->patchJson("/api/pengawasan/$id", ['anggota' => [999], 'dasar_hukum' => ['Changed'], 'uraian_penugasan' => 'Changed'])
            ->assertUnprocessable()->assertJsonValidationErrors('personil');
        $this->assertDatabaseHas('pengawasan', ['id' => $id, 'uraian_penugasan' => 'Audit test']);
        $this->assertDatabaseHas('pengawasan_dasar', ['pengawasan_id' => $id, 'isi_dasar' => 'Dasar test']);
        $this->assertDatabaseHas('pengawasan_anggota', ['pengawasan_id' => $id, 'pegawai_id' => 103]);
        $this->assertDatabaseMissing('pengawasan_anggota', ['pegawai_id' => 999]);
    }

    public function test_service_outage_rolls_back_creation_and_search_returns_503(): void
    {
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Offline'));
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $this->postJson('/api/pengawasan', $this->payload())->assertStatus(503);
        $this->assertDatabaseCount('pengawasan', 0);
        $this->assertDatabaseCount('pengawasan_dasar', 0);
        $this->actingAs($user)->getJson('/ajax/pegawai/search?search=A')->assertStatus(503);
    }

    public function test_pagination_fetches_employees_after_first_page_and_rejects_stuck_upstream(): void
    {
        Http::fake(function ($request) {
            $page = (int) $request['page'];

            return Http::response(['data' => [['id' => $page, 'nama' => "Pegawai $page"]],
                'meta' => ['current_page' => $page, 'per_page' => 1, 'total' => 3]]);
        });
        $employees = app(\App\Services\PegawaiService::class)->all();
        $this->assertCount(3, $employees);
        $this->assertSame('Pegawai 3', $employees->get(3)->nama);
        Http::assertSentCount(3);
        $this->resetPegawaiHttp();
        Http::fake(['*' => Http::response(['data' => [['id' => 1, 'nama' => 'One']], 'meta' => ['current_page' => 1, 'per_page' => 1, 'total' => 3]])]);
        $this->expectException(\App\Exceptions\PegawaiUnavailable::class);
        app(\App\Services\PegawaiService::class)->all();
    }

    public function test_conflict_checks_cross_roles_and_inclusive_end_dates(): void
    {
        $this->fakePegawai();
        Sanctum::actingAs(User::factory()->create());
        $id = $this->postJson('/api/pengawasan', $this->payload() + ['anggota' => [103]])->assertCreated()->json('data.id');
        $second = array_replace($this->payload(), ['nomor_st' => 'SECOND', 'tanggal_st' => '2026-09-08', 'ketua_tim_id' => 103]);
        $this->postJson('/api/pengawasan', $second)->assertUnprocessable()->assertJsonValidationErrors('personil');
        $second['tanggal_st'] = '2026-09-09';
        $this->postJson('/api/pengawasan', $second)->assertCreated();
        $this->patchJson("/api/pengawasan/$id", ['uraian_penugasan' => 'Same dates'])->assertOk();
    }

    public function test_web_create_allows_draft_number_and_snapshots_inside_transaction(): void
    {
        $this->fakePegawai();
        $this->actingAs(User::factory()->create());
        $payload = $this->payload() + ['penandatangan_type' => 'definitif', 'anggota' => [103]];
        unset($payload['nomor_st']);
        $this->post('/pengawasan', $payload)->assertRedirect('/pengawasan')->assertSessionHasNoErrors();
        $record = Pengawasan::firstOrFail();
        $this->assertNull($record->nomor_st);
        $this->assertNotNull($record->personil_snapshot);
        $this->assertSame('BOTTEN TANDIPADA', $record->penandatangan_definitif_nama);
        $this->resetPegawaiHttp();
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Offline'));
        $payload['tanggal_st'] = '2026-10-01';
        $this->postJson('/pengawasan', $payload)->assertStatus(503);
        $this->assertDatabaseCount('pengawasan', 1);
    }

    public function test_login_is_throttled_and_logout_requires_post(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', ['email' => 'audit@example.test', 'password' => 'wrong'])->assertUnprocessable();
        }
        $this->postJson('/api/login', ['email' => 'audit@example.test', 'password' => 'wrong'])->assertStatus(429);
        $this->get('/logout')->assertStatus(405);
    }

    public function test_api_pagination_is_bounded_and_employee_mutations_are_not_proxied(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->getJson('/api/pengawasan?per_page=0')->assertUnprocessable();
        $this->getJson('/api/pengawasan?per_page=100000')->assertUnprocessable();
        $this->getJson('/api/pegawai?per_page=100000')->assertUnprocessable();
        $this->postJson('/api/pegawai', ['nama' => 'Test'])->assertStatus(405);
        Http::assertNothingSent();
    }

    public function test_export_is_a_download_and_preserves_text_and_nip(): void
    {
        $this->fakePegawai();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $this->postJson('/api/pengawasan', array_replace($this->payload(), ['uraian_penugasan' => '=1+1']))->assertCreated();
        $response = $this->actingAs($user)->get('/rekap/export?tahun=2026')->assertOk()->assertDownload();
        $file = tempnam(sys_get_temp_dir(), 'apip-export-');
        try {
            file_put_contents($file, $response->streamedContent());
            $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $book->getActiveSheet();
            $this->assertSame('=1+1', $sheet->getCell('F2')->getValue());
            $this->assertSame('s', $sheet->getCell('F2')->getDataType());
            $this->assertSame('1001', $sheet->getCell('J2')->getValue());
            $this->assertSame('s', $sheet->getCell('J2')->getDataType());
            $book->disconnectWorksheets();
        } finally {
            unlink($file);
        }
        config(['pengawasan.export_max_assignments' => 0]);
        $this->getJson('/rekap/export')->assertUnprocessable()->assertJsonValidationErrors('export');
    }

    public function test_failed_report_update_keeps_old_file_and_cleans_new_file(): void
    {
        $this->fakePegawai();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $id = $this->postJson('/api/pengawasan', $this->payload())->assertCreated()->json('data.id');
        \Illuminate\Support\Facades\Storage::fake('public');
        \Illuminate\Support\Facades\Storage::disk('public')->put('laporan_pengawasan/old.pdf', 'old report');
        \Illuminate\Support\Facades\DB::table('pengawasan')->where('id', $id)->update(['file_laporan' => 'laporan_pengawasan/old.pdf']);
        \Illuminate\Support\Facades\Event::listen('eloquent.updating: App\\Models\\Pengawasan', function () {
            throw new \RuntimeException('Simulated report update failure');
        });
        $this->actingAs($user)->post("/pengawasan/$id/status", [
            'status' => 'selesai', 'file_laporan' => \Illuminate\Http\UploadedFile::fake()->create('new.pdf', 10, 'application/pdf'),
        ])->assertRedirect()->assertSessionHas('error');
        $this->assertSame(['laporan_pengawasan/old.pdf'], \Illuminate\Support\Facades\Storage::disk('public')->allFiles());
        $this->assertDatabaseHas('pengawasan', ['id' => $id, 'status' => 'belum_selesai', 'file_laporan' => 'laporan_pengawasan/old.pdf']);
    }

    public function test_surat_tugas_pdf_renders_from_saved_snapshot(): void
    {
        $this->fakePegawai();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $id = $this->postJson('/api/pengawasan', $this->payload())->assertCreated()->json('data.id');
        $this->resetPegawaiHttp();
        $response = $this->actingAs($user)->get("/pengawasan/$id/cetak-surat-tugas")->assertOk();
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        Http::assertNothingSent();
    }

    public function test_settings_reads_are_batched_and_invalidated_after_update(): void
    {
        \App\Models\SystemSetting::create(['key' => 'definitif_nama', 'value' => 'Before']);
        $this->assertSame('Before', \App\Models\SystemSetting::valueFor('definitif_nama'));
        \App\Models\SystemSetting::where('key', 'definitif_nama')->firstOrFail()->update(['value' => 'After']);
        $this->assertSame('After', \App\Models\SystemSetting::valueFor('definitif_nama'));
        \Illuminate\Support\Facades\DB::enableQueryLog();
        \Illuminate\Support\Facades\DB::flushQueryLog();
        \App\Models\SystemSetting::valueFor('definitif_nama');
        \App\Models\SystemSetting::valueFor('kop_instansi');
        $this->assertCount(0, \Illuminate\Support\Facades\DB::getQueryLog());
        \Illuminate\Support\Facades\DB::disableQueryLog();
    }

    public function test_personil_mapping_batches_pivot_queries_for_many_assignments(): void
    {
        $this->fakePegawai();
        $records = collect();
        for ($i = 0; $i < 30; $i++) {
            $p = Pengawasan::create(array_replace($this->payload(), ['nomor_st' => "BATCH-$i"]));
            \Illuminate\Support\Facades\DB::table('pengawasan_anggota')->insert(['pengawasan_id' => $p->id, 'pegawai_id' => 103]);
            $records->push($p);
        }
        \Illuminate\Support\Facades\DB::enableQueryLog();
        \Illuminate\Support\Facades\DB::flushQueryLog();
        app(\App\Services\PersonilService::class)->inject($records, true);
        $this->assertLessThanOrEqual(2, count(\Illuminate\Support\Facades\DB::getQueryLog()));
        \Illuminate\Support\Facades\DB::disableQueryLog();
        $this->assertSame('Anggota', $records->last()->anggota->first()->nama);
        Http::assertSentCount(1);
    }

    private function resetPegawaiHttp(): void
    {
        app()->forgetInstance(\App\Services\PegawaiService::class);
        Http::swap(new \Illuminate\Http\Client\Factory);
        Http::preventStrayRequests();
    }

    private function fakePegawai(): void
    {
        Http::fake(['*' => Http::response(['data' => [
            ['id' => 101, 'nama' => 'Penanggung Jawab', 'nip' => '1001', 'jabatan' => 'Auditor', 'golongan' => 'IV/a'],
            ['id' => 102, 'nama' => 'Ketua Tim', 'nip' => '1002', 'jabatan' => 'Auditor', 'golongan' => 'III/d'],
            ['id' => 103, 'nama' => 'Anggota', 'nip' => '1003', 'jabatan' => 'Auditor', 'golongan' => 'III/c'],
        ]])]);
    }
}
