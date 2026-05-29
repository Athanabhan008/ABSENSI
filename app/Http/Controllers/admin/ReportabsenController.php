<?php

namespace App\Http\Controllers\admin;

use App\Models\Izinsakit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\Vwreportabsen;
use App\Models\Cuti;
use App\Models\Vwcuti;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use App\Libraries\easyTables;
use App\Libraries\exFPDF;
use App\Models\VwExportreportabsen;

class ReportabsenController extends Controller
{
    public function __construct()
    {
        $this->fpdf = new exFPDF('P', 'cm', 'A4');
    }
    public function index()
    {
       return view('report.report_absen', [
        'active' => 'report_absen'
       ]);
    }

    public function datatable()
    {
        $draw = (int) request()->get('draw');
        $start = request()->get('start', 0);
        $length = request()->get('length');
        $id_user = request()->get('id');

        // Dukungan filter: range bulan (prioritas) atau 1 bulan (kompatibilitas lama)
        $periode_start = request()->get('periode_start');
        $periode_end = request()->get('periode_end');
        $periodeSingle = request()->get('periode_pr');

        $user = auth()->user();
        $query = Vwreportabsen::query();

        if ($periode_start && $periode_end) {
            // Frontend mengirim format YYYYMM (contoh: 202603).
            // Convert ke range tanggal agar filter cocok dengan tipe kolom `tgl_absen` (DATE/DATETIME).
            $periodeRangeStart = null;
            $periodeRangeEnd = null;

            if (preg_match('/^\d{6}$/', (string) $periode_start)) {
                $periodeRangeStart = Carbon::createFromFormat('Ym', $periode_start)->startOfMonth();
            } elseif (preg_match('/^\d{4}-\d{2}$/', (string) $periode_start)) {
                $periodeRangeStart = Carbon::createFromFormat('Y-m', $periode_start)->startOfMonth();
            } else {
                $periodeRangeStart = Carbon::parse($periode_start)->startOfMonth();
            }

            if (preg_match('/^\d{6}$/', (string) $periode_end)) {
                $periodeRangeEnd = Carbon::createFromFormat('Ym', $periode_end)->endOfMonth()->endOfDay();
            } elseif (preg_match('/^\d{4}-\d{2}$/', (string) $periode_end)) {
                $periodeRangeEnd = Carbon::createFromFormat('Y-m', $periode_end)->endOfMonth()->endOfDay();
            } else {
                $periodeRangeEnd = Carbon::parse($periode_end)->endOfMonth()->endOfDay();
            }

            if ($periodeRangeStart && $periodeRangeEnd) {
                // Pastikan urutan benar
                if ($periodeRangeStart->gt($periodeRangeEnd)) {
                    [$periodeRangeStart, $periodeRangeEnd] = [$periodeRangeEnd, $periodeRangeStart];
                }

                $query->whereBetween(
                    'tgl_absen',
                    [$periodeRangeStart->toDateTimeString(), $periodeRangeEnd->toDateTimeString()]
                );
            }
        } elseif ($periodeSingle) {
            // Kompatibilitas: 1 bulan (misal YYYYMM atau YYYY-MM)
            if (preg_match('/^\d{6}$/', (string) $periodeSingle)) {
                $m = Carbon::createFromFormat('Ym', $periodeSingle);
            } elseif (preg_match('/^\d{4}-\d{2}$/', (string) $periodeSingle)) {
                $m = Carbon::createFromFormat('Y-m', $periodeSingle);
            } else {
                $m = Carbon::parse($periodeSingle);
            }

            $query->whereBetween(
                'tgl_absen',
                [$m->startOfMonth()->toDateTimeString(), $m->endOfMonth()->endOfDay()->toDateTimeString()]
            );
        }

        if ($id_user) {
            $query->where('nip_user', $id_user);
        }

        // Filter berdasarkan user yang login
        // Konsisten dengan nilai role yang dipakai di middleware (mis. 'superadmin')
        if (!in_array($user->role, ['superadmin', 'admin', 'manager'])) {
            $query->where('id', $user->id);
        }

        $total = $query->count();

        // Apply pagination - jika length adalah -1, null, atau sangat besar (> 10000), ambil semua data tanpa pagination
        // Juga handle jika length adalah 0 atau tidak valid
        if ($length == -1 || $length === null || $length === '' || (is_numeric($length) && (int)$length > 10000) || (is_numeric($length) && (int)$length <= 0)) {
            // Ambil semua data tanpa pagination
            $results = $query->get();
        } else {
            // Apply pagination normal dengan validasi
            $start = is_numeric($start) ? (int)$start : 0;
            $results = $query->offset($start)
                            ->limit(is_numeric($length) ? (int)$length : 10)
                            ->get();
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $results
        ]);
    }

/**
 * cetakPDF - Modified Version
 * Perubahan:
 *  - 1 user = 1 halaman baru (AddPage per grup)
 *  - Tampilan lebih menarik: header berwarna, garis pemisah, baris belang-seling
 *  - Fungsi-fungsi lain TIDAK diubah
 */

 public function cetakPDF(Request $request)
 {
     // ─── Helper: hitung selisih jam (TIDAK DIUBAH) ────────────────────────────
     function selisih($jam_masuk, $jam_keluar)
     {
         list($h, $m, $s) = explode(":", $jam_masuk);
         $dtAwal  = mktime($h, $m, $s, "1", "1", "1");
         list($h, $m, $s) = explode(":", $jam_keluar);
         $dtAkhir = mktime($h, $m, $s, "1", "1", "1");
         $dtSelisih  = $dtAkhir - $dtAwal;
         $totalmenit = $dtSelisih / 60;
         $jam        = explode(".", $totalmenit / 60);
         $sisamenit  = ($totalmenit / 60) - $jam[0];
         $sisamenit2 = $sisamenit * 60;
         $jml_jam    = $jam[0];
         return $jml_jam . ":" . round($sisamenit2);
     }

     // ─── Ambil parameter request (TIDAK DIUBAH) ───────────────────────────────
     $periode_start  = $request->get('periode_start');
     $periode_end    = $request->get('periode_end');
     $periodeSingle  = $request->get('periode_pr');

     // ─── Query data (TIDAK DIUBAH) ────────────────────────────────────────────
     $query = VwExportreportabsen::query();

     if ($periode_start && $periode_end) {
         $query->whereBetween(DB::raw("DATE_FORMAT(tgl_absen, '%Y%m')"), [
             $periode_start, $periode_end,
         ]);
     }

     $data_result = $query->get()->toArray();

     if (empty($data_result)) {
         return response()->json([
             'message'       => 'Data tidak ditemukan pada periode tersebut',
             'periode_start' => $periode_start,
             'periode_end'   => $periode_end,
         ]);
     }

     // ─── Format teks periode ──────────────────────────────────────────────────
     Carbon::setLocale('id');
     $start        = Carbon::createFromFormat('Ym', $periode_start)->startOfMonth();
     $end          = Carbon::createFromFormat('Ym', $periode_end)->endOfMonth();
     $periode_text = $start->translatedFormat('d F Y') . ' s/d ' . $end->translatedFormat('d F Y');

     // ─── Kelompokkan data per user, urutkan per tanggal (TIDAK DIUBAH) ────────
     $groups = [];
     foreach ($data_result as $row) {
         $groupKey = $row['nip_user'] ?? ($row['id_user'] ?? ($row['name'] ?? ''));
         if ($groupKey === '' || $groupKey === null) {
             $groupKey = $row['id'] ?? ($row['tgl_absen'] ?? uniqid('user_'));
         }
         $groups[$groupKey][] = $row;
     }
     foreach ($groups as &$rows) {
         usort($rows, function ($a, $b) {
             $ta = strtotime($a['tgl_absen'] ?? ($a['tgl'] ?? '')) ?: 0;
             $tb = strtotime($b['tgl_absen'] ?? ($b['tgl'] ?? '')) ?: 0;
             return $ta <=> $tb;
         });
     }
     unset($rows);

     // ─── Setup FPDF (cm) ──────────────────────────────────────────────────────
     // Landscape A4 = 29.7 cm x 21 cm
     // Margin kiri default FPDF = 1 cm
     // Lebar kerja = 29.7 - 2*1 = 27.7 cm → dibulatkan 27 cm
     $this->fpdf->SetFont('Arial', '', 12);

     // ─── Lebar kolom (total = 27 cm) ─────────────────────────────────────────
     // NO | Tanggal | Masuk | Keluar | Terlambat | Ket.Masuk | Ket.Pulang
     $colW = [1.2, 5.0, 3.0, 3.0, 3.8, 5.5, 5.5];
     // Total = 1.2+5.0+3.0+3.0+3.8+5.5+5.5 = 27.0 cm ✓

     $no = 1;

     foreach ($groups as $rows) {

         // ── 1 user = 1 halaman baru ───────────────────────────────────────────
         $this->fpdf->AddPage('L', 'A4');

         $nama = $rows[0]['name']     ?? '-';
         $nip  = $rows[0]['nip_user'] ?? ($rows[0]['id_user'] ?? '-');

         // ════════════════════════════════════════════════════════════════════
         // HEADER BANNER – latar biru tua, mulai X=1, Y=0.8, lebar 27, tinggi 1.8 cm
         // ════════════════════════════════════════════════════════════════════
         $this->fpdf->SetFillColor(26, 62, 107);   // #1A3E6B biru tua
         $this->fpdf->SetTextColor(255, 255, 255);
         $this->fpdf->Rect(1, 0.8, 27, 1.8, 'F');

         // Nama perusahaan
         $this->fpdf->SetFont('Arial', 'B', 13);
         $this->fpdf->SetXY(1.3, 0.9);
         $this->fpdf->Cell(13, 0.8, 'PT. Mitra Bisnis Sopyan', 0, 0, 'L');

         // Judul laporan
         $this->fpdf->SetFont('Arial', 'B', 10);
         $this->fpdf->SetXY(1.3, 1.75);
         $this->fpdf->Cell(13, 0.6, 'LAPORAN ABSENSI KARYAWAN', 0, 0, 'L');

         // Periode – kanan atas
         $this->fpdf->SetFont('Arial', '', 9);
         $this->fpdf->SetXY(1, 1.1);
         $this->fpdf->Cell(27, 0.6, 'Periode : ' . $periode_text, 0, 0, 'R');

         $this->fpdf->SetTextColor(0, 0, 0);

         // ════════════════════════════════════════════════════════════════════
         // BAR INFO KARYAWAN – latar biru muda, Y=2.8, tinggi 0.9 cm
         // ════════════════════════════════════════════════════════════════════
         $totalHadir = count(array_filter($rows, fn($r) => ($r['jam_masuk'] ?? '-') !== '-'));

         $this->fpdf->SetFillColor(234, 241, 251);  // #EAF1FB biru sangat muda
         $this->fpdf->SetDrawColor(26, 62, 107);
         $this->fpdf->SetLineWidth(0.03);
         $this->fpdf->Rect(1, 2.75, 27, 0.9, 'FD');

         $this->fpdf->SetFont('Arial', 'B', 9);
         $this->fpdf->SetXY(1.3, 2.85);
         $this->fpdf->Cell(3.5, 0.6, 'Nama Karyawan :', 0, 0, 'L');
         $this->fpdf->SetFont('Arial', '', 9);
         $this->fpdf->Cell(7, 0.6, $nama, 0, 0, 'L');

         $this->fpdf->SetFont('Arial', 'B', 9);
         $this->fpdf->Cell(1.5, 0.6, 'NIP :', 0, 0, 'L');
         $this->fpdf->SetFont('Arial', '', 9);
         $this->fpdf->Cell(6, 0.6, $nip, 0, 0, 'L');

         $this->fpdf->SetFont('Arial', 'B', 9);
         $this->fpdf->Cell(2.5, 0.6, 'Total Hadir :', 0, 0, 'L');
         $this->fpdf->SetFont('Arial', '', 9);
         $this->fpdf->Cell(3, 0.6, $totalHadir . ' hari', 0, 0, 'L');

         // ════════════════════════════════════════════════════════════════════
         // HEADER TABEL – mulai Y=3.85
         // ════════════════════════════════════════════════════════════════════
         $this->fpdf->SetY(3.85);
         $this->fpdf->SetX(1);
         $this->fpdf->SetLineWidth(0.02);
         $this->fpdf->SetDrawColor(26, 62, 107);
         $rowH = 0.55;

         // --- Baris header 1 ---
         $this->fpdf->SetFillColor(46, 109, 164);   // #2E6DA4 biru medium
         $this->fpdf->SetTextColor(255, 255, 255);
         $this->fpdf->SetFont('Arial', 'B', 8);

         $yRow1 = $this->fpdf->GetY();
         $this->fpdf->SetX(1);

         // NO (rowspan 2 → tinggi 2*rowH manual)
         $this->fpdf->Cell($colW[0], $rowH * 2, 'NO',              1, 0, 'C', true);
         // Tanggal Absen (rowspan 2)
         $this->fpdf->Cell($colW[1], $rowH * 2, 'Tanggal Absen',   1, 0, 'C', true);
         // Jadwal Absensi (colspan 2 → satu baris)
         $this->fpdf->Cell($colW[2] + $colW[3], $rowH, 'Jadwal Absensi', 1, 0, 'C', true);
         // Terlambat (rowspan 2)
         $this->fpdf->Cell($colW[4], $rowH * 2, 'Terlambat',       1, 0, 'C', true);
         // Ket Masuk (rowspan 2)
         $this->fpdf->Cell($colW[5], $rowH * 2, 'Keterangan Masuk', 1, 0, 'C', true);
         // Ket Pulang (rowspan 2)
         $this->fpdf->Cell($colW[6], $rowH * 2, 'Keterangan Pulang', 1, 0, 'C', true);
         $this->fpdf->Ln($rowH);

         // --- Baris header 2 (hanya kolom sub-jadwal) ---
         // Pindah X ke posisi setelah NO + Tanggal
         $xSub = 1 + $colW[0] + $colW[1];
         $this->fpdf->SetXY($xSub, $yRow1 + $rowH);
         $this->fpdf->Cell($colW[2], $rowH, 'Jadwal Masuk',  1, 0, 'C', true);
         $this->fpdf->Cell($colW[3], $rowH, 'Jadwal Keluar', 1, 0, 'C', true);
         $this->fpdf->Ln($rowH);

         // ════════════════════════════════════════════════════════════════════
         // BARIS DATA
         // ════════════════════════════════════════════════════════════════════
         $this->fpdf->SetDrawColor(180, 200, 220);
         $rowH2    = 0.6;
         $rowIndex = 0;

         foreach ($rows as $row) {

             $tgl_absen         = $row['tgl_absen']         ?? ($row['tgl'] ?? '-');
             $jam_masuk         = $row['jam_masuk']         ?? '-';
             $jam_keluar        = $row['jam_keluar']        ?? '-';
             $keterangan_masuk  = $row['keterangan_masuk']  ?? '-';
             $keterangan_pulang = $row['keterangan_pulang'] ?? '-';

             // Kalkulasi status (TIDAK DIUBAH)
             $status      = '-';
             $isTerlambat = false;
             if ($jam_masuk !== '-' && $jam_masuk > '08:05:00') {
                 $jamterlambat = selisih('08:05:00', $jam_masuk);
                 $status       = 'Terlambat ' . $jamterlambat;
                 $isTerlambat  = true;
             } elseif ($jam_masuk !== '-') {
                 $status = 'Tepat Waktu';
             }

             // Baris belang-seling
             if ($rowIndex % 2 === 0) {
                 $this->fpdf->SetFillColor(255, 255, 255);  // putih
             } else {
                 $this->fpdf->SetFillColor(234, 241, 251);  // biru sangat muda
             }

             $this->fpdf->SetTextColor(0, 0, 0);
             $this->fpdf->SetFont('Arial', '', 8);
             $this->fpdf->SetX(1);

             $this->fpdf->Cell($colW[0], $rowH2, $no,          1, 0, 'C', true);
             $this->fpdf->Cell($colW[1], $rowH2, $tgl_absen,   1, 0, 'C', true);
             $this->fpdf->Cell($colW[2], $rowH2, $jam_masuk,   1, 0, 'C', true);
             $this->fpdf->Cell($colW[3], $rowH2, $jam_keluar,  1, 0, 'C', true);

             // Status – warna teks
             if ($isTerlambat) {
                 $this->fpdf->SetTextColor(192, 57, 43);    // merah
             } elseif ($status === 'Tepat Waktu') {
                 $this->fpdf->SetTextColor(30, 132, 73);    // hijau
             } else {
                 $this->fpdf->SetTextColor(120, 120, 120);  // abu
             }
             $this->fpdf->SetFont('Arial', 'B', 8);
             $this->fpdf->Cell($colW[4], $rowH2, $status, 1, 0, 'C', true);

             $this->fpdf->SetTextColor(0, 0, 0);
             $this->fpdf->SetFont('Arial', '', 8);
             $this->fpdf->Cell($colW[5], $rowH2, $keterangan_masuk,  1, 0, 'C', true);
             $this->fpdf->Cell($colW[6], $rowH2, $keterangan_pulang, 1, 0, 'L', true);
             $this->fpdf->Ln();

             $no++;
             $rowIndex++;
         }

         // ════════════════════════════════════════════════════════════════════
         // FOOTER RINGKASAN
         // ════════════════════════════════════════════════════════════════════
         $totalTerlambat = count(array_filter($rows, function ($r) {
             $jm = $r['jam_masuk'] ?? '-';
             return $jm !== '-' && $jm > '08:05:00';
         }));
         $totalTepat = count(array_filter($rows, function ($r) {
             $jm = $r['jam_masuk'] ?? '-';
             return $jm !== '-' && $jm <= '08:05:00';
         }));

         $this->fpdf->Ln(0.2);

         // Garis biru tipis
         $this->fpdf->SetFillColor(26, 62, 107);
         $yFooter = $this->fpdf->GetY();
         $this->fpdf->Rect(1, $yFooter, 27, 0.04, 'F');
         $this->fpdf->Ln(0.15);

         $this->fpdf->SetFont('Arial', '', 8);
         $this->fpdf->SetTextColor(80, 80, 80);
         $this->fpdf->SetX(1);
         $this->fpdf->Cell(6,  0.5, 'Tepat Waktu : ' . $totalTepat    . ' hari', 0, 0, 'L');
         $this->fpdf->Cell(7,  0.5, 'Terlambat   : ' . $totalTerlambat . ' hari', 0, 0, 'L');
         $this->fpdf->Cell(14, 0.5, 'Dicetak : ' . now()->format('d/m/Y H:i'),   0, 0, 'R');

         $this->fpdf->SetTextColor(0, 0, 0);
     }

     // ─── Output PDF ───────────────────────────────────────────────────────────
     $this->fpdf->Output();
     exit;
 }

}

