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


    public function cetakPDF(Request $request)
    {




        $periode_start  = $request->get('periode_start');
        $periode_end    = $request->get('periode_end');
        $periodeSingle = $request->get('periode_pr');

         $query = VwExportreportabsen::query();

         if ($periode_start && $periode_end) {
            $query->whereBetween(DB::raw("DATE_FORMAT(tgl_absen, '%Y%m')"), [
                $periode_start, $periode_end
            ]);
          }

    $data_result = $query->get()->toArray();

        if (empty($data_result)) {
            return response()->json([
                'message' => 'Data tidak ditemukan pada periode tersebut',
                'periode_start' => $periode_start,
                'periode_end' => $periode_end
            ]);
        }


    	$this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->AddPage('L', 'A4');


        $this->fpdf->SetFont('Arial', 'B', 13);
		$this->fpdf->Cell(7, 0.7, '', 0, 0, 'C');
		$this->fpdf->Cell(14, 0.7, "PT.Mitra Bisnis Sopyan", 0, 0, 'C');
		$this->fpdf->Ln(0.8);

        $this->fpdf->SetFont('Arial', 'B', 13);
		$this->fpdf->Cell(7, 0.7, '', 0, 0, 'C');
		$this->fpdf->Cell(14, 0.7, "Laporan Absensi", 0, 0, 'C');
		$this->fpdf->Ln(0.5);


        Carbon::setLocale('id');
        $start = Carbon::createFromFormat('Ym', $periode_start)->startOfMonth();
        $end   = Carbon::createFromFormat('Ym', $periode_end)->endOfMonth();

        $periode_text = $start->translatedFormat('d/F/Y') . ' - ' . $end->translatedFormat('d/F/Y');

        $this->fpdf->SetFont('helvetica', '', 11);
        $this->fpdf->Cell(12, 0.5, "Periode", 0, 0, 'L');
        $this->fpdf->Ln(0.7);
        $this->fpdf->Cell(0, 0.5, $periode_text, 0, 0, 'L');

		$this->fpdf->SetFont('helvetica', 'I', 6);
		$this->fpdf->SetXY(15, 0.5);

		$this->fpdf->Ln(2);

        $this->fpdf->Cell(3.5, 0.7, '', 0, 0, 'L');
		$this->fpdf->Ln(1.5);

        $table = new easyTables($this->fpdf, "{2.5, 8, 10, 20, 8, 11, 11}", 'border:1;font-size:7.9;min-height:0.5;');

        $table->rowStyle('font-style:B;');
        $table->easyCell('NO', 'valign:M;align:C;rowspan:2;');
        $table->easyCell('Tanggal Absen', 'valign:M;align:C;rowspan:2;');
        $table->easyCell('No.HP', 'valign:M;align:C;rowspan:2;');
        $table->easyCell('Jadwal Absensi', 'valign:M;align:C;colspan:2;');
        $table->easyCell('Keterangan', 'valign:M;align:C;rowspan:2;');
        $table->easyCell('Status', 'valign:M;align:C;rowspan:2;');
        $table->printRow();

        $table->rowStyle('font-style:B;');
        $table->easyCell('Jadwal Masuk', 'valign:M;align:C;');
        $table->easyCell('Jadwal Keluar', 'valign:M;align:C;');
        // $table->easyCell('', 'valign:M;align:C;');
        // $table->easyCell('', 'valign:M;align:C;');
        // $table->easyCell('', 'valign:M;align:C;');
        // $table->easyCell('', 'valign:M;align:C;');
        // $table->easyCell('', 'valign:M;align:C;');
        // $table->easyCell('', 'valign:M;align:C;');
        // $table->easyCell('', 'valign:M;align:C;');
        $table->printRow();
        $this->fpdf->Ln(0);


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

        $no = 1;
        foreach ($groups as $rows) {

            $rowCount = count($rows);
            $nama = $rows[0]['name'] ?? '-';


            $table->rowStyle('font-style:B;bgcolor:#F8F9FA;');
            $table->easyCell('Nama: ' . $nama, 'colspan:7;align:L;');
            $table->printRow();

            foreach ($rows as $idx => $row) {

                $tgl_absen = $row['tgl_absen'] ?? ($row['tgl'] ?? '-');
                $no_hp = $row['no_hp'] ?? '-';
                $jam_masuk = $row['jam_masuk'] ?? '-';
                $jam_keluar = $row['jam_keluar'] ?? '-';
                $keterangan = $row['keterangan'] ?? '-';
                $status = $row['status'] ?? '-';

                $table->rowStyle('');

                $table->easyCell($no, 'valign:M;align:C;');
                $table->easyCell($tgl_absen, 'valign:M;align:C;');
                $table->easyCell($no_hp, 'valign:M;align:L;');
                $table->easyCell($jam_masuk, 'valign:M;align:C;');
                $table->easyCell($jam_keluar, 'valign:M;align:C;');
                $table->easyCell($keterangan, 'valign:M;align:L;');
                $table->easyCell($status, 'valign:M;align:C;');

                $table->printRow();

                $no++;
            }
        }

        $this->fpdf->Output();
        exit;

    }

}

