<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengawasan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapController extends Controller
{
    public function index()
    {
        return view('rekap.index');
    }

    public function export(Request $request)
    {
        // Get filter parameters
        $status = $request->input('status');
        $jenis = $request->input('jenis');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        // Build query
        $query = Pengawasan::with([
            'penanggungJawab',
            'pengendaliTeknis',
            'ketuaTim',
            'anggota',
            'dasarHukum'
        ]);

        // Apply filters
        if ($status) {
            $query->where('status', $status);
        }

        if ($jenis) {
            $query->where('jenis_penugasan', $jenis);
        }

        if ($bulan && $tahun) {
            $query->whereMonth('tanggal_st', $bulan)
                  ->whereYear('tanggal_st', $tahun);
        } elseif ($tahun) {
            $query->whereYear('tanggal_st', $tahun);
        }

        $pengawasanList = $query->orderBy('tanggal_st', 'desc')->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Inspektorat Kabupaten Puncak Jaya')
            ->setTitle('Rekap Data Pengawasan')
            ->setSubject('Data Pengawasan')
            ->setDescription('Rekap data pengawasan lengkap');

        // Set header style
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '7c3aed']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];

        // Set headers
        $headers = [
            'No',
            'Nomor ST',
            'Tanggal ST',
            'Lama Penugasan',
            'Jenis Penugasan',
            'Uraian Penugasan',
            'Lokasi Penugasan',
            'Status',
            'Nama Pegawai',
            'NIP',
            'Jabatan',
            'Peran',
            'Dasar Hukum'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(22);
        $sheet->getColumnDimension('K')->setWidth(30);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(50);

        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true
            ]
        ];

        // Fill data - 1 orang per baris
        $row = 2;
        $no = 1;
        foreach ($pengawasanList as $pengawasan) {
            // Kumpulkan semua personil dengan perannya
            $personilList = [];

            if ($pengawasan->penanggungJawab) {
                $personilList[] = [
                    'pegawai' => $pengawasan->penanggungJawab,
                    'peran'   => 'Penanggung Jawab',
                ];
            }
            if ($pengawasan->pengendaliTeknis) {
                $personilList[] = [
                    'pegawai' => $pengawasan->pengendaliTeknis,
                    'peran'   => 'Pengendali Teknis',
                ];
            }
            if ($pengawasan->ketuaTim) {
                $personilList[] = [
                    'pegawai' => $pengawasan->ketuaTim,
                    'peran'   => 'Ketua Tim',
                ];
            }
            foreach ($pengawasan->anggota as $anggota) {
                $personilList[] = [
                    'pegawai' => $anggota,
                    'peran'   => 'Anggota Tim',
                ];
            }

            if (empty($personilList)) {
                $personilList[] = ['pegawai' => null, 'peran' => '-'];
            }

            $jumlahPersonil = count($personilList);
            $startRow = $row;

            // Dasar hukum (sama untuk semua baris dalam 1 ST)
            $dasarHukumList = $pengawasan->dasarHukum->map(function($dasar, $index) {
                return ($index + 1) . '. ' . $dasar->isi_dasar;
            })->implode("\n");

            foreach ($personilList as $item) {
                $pegawai = $item['pegawai'];

                $sheet->setCellValue('I' . $row, $pegawai ? $pegawai->nama : '-');
                $sheet->setCellValue('J' . $row, $pegawai ? $pegawai->nip : '-');
                $sheet->setCellValue('K' . $row, $pegawai ? $pegawai->jabatan : '-');
                $sheet->setCellValue('L' . $row, $item['peran']);

                $sheet->getStyle('I' . $row . ':L' . $row)->applyFromArray($dataStyle);
                $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $row++;
            }

            $endRow = $row - 1;

            // Isi kolom info ST (A-H) dan merge jika lebih dari 1 personil
            $sheet->setCellValue('A' . $startRow, $no);
            $sheet->setCellValue('B' . $startRow, $pengawasan->nomor_st);
            $sheet->setCellValue('C' . $startRow, $pengawasan->tanggal_st->format('d/m/Y'));
            $sheet->setCellValue('D' . $startRow, $pengawasan->lama_penugasan . ' Hari');
            $sheet->setCellValue('E' . $startRow, $pengawasan->jenis_penugasan);
            $sheet->setCellValue('F' . $startRow, $pengawasan->uraian_penugasan);
            $sheet->setCellValue('G' . $startRow, $pengawasan->lokasi_penugasan);
            $sheet->setCellValue('H' . $startRow, $pengawasan->status_label);
            $sheet->setCellValue('M' . $startRow, $dasarHukumList);

            if ($jumlahPersonil > 1) {
                foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'M'] as $c) {
                    $sheet->mergeCells($c . $startRow . ':' . $c . $endRow);
                }
            }

            $sheet->getStyle('A' . $startRow . ':H' . $endRow)->applyFromArray($dataStyle);
            $sheet->getStyle('M' . $startRow . ':M' . $endRow)->applyFromArray($dataStyle);

            $sheet->getStyle('A' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $no++;
        }

        // Set row height to auto
        for ($i = 2; $i < $row; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1);
        }

        // Generate filename
        $filename = 'Rekap_Pengawasan_' . date('Y-m-d_His') . '.xlsx';

        // Create writer and download
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
