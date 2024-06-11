<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\PDF;
use Codedge\Fpdf\Fpdf\Fpdf;

class ReportingController extends V2pelayananController
{
    public function index()
    {
        $menu = 'reporting';
        $now = $this->get_date();
        $mt_unit = DB::select('select * from mt_unit');
        return view('reporting.index', compact([
            'menu', 'now', 'mt_unit'
        ]));
    }
    public function ambil_data_pemakaian()
    {

        $kunjungan = db::select("SELECT kode_kunjungan,no_rm,fc_nama_px(no_rm) as nama_pasien,kode_unit FROM ts_kunjungan
        WHERE DATE(tgl_masuk) BETWEEN '2024-06-10' AND '2024-06-10' AND kode_unit = '1014' AND status_kunjungan != 8");

        $order_header = db::select("SELECT b.kode_kunjungan,b.dok_kirim,b.diagnosa,c.kode_barang,c.aturan_pakai,c.jumlah_layanan FROM ts_kunjungan a
        INNER JOIN ts_layanan_header_order b ON a.kode_kunjungan = b.kode_kunjungan
        INNER JOIN ts_layanan_detail_order c on b.id = c.row_id_header
        WHERE DATE(a.tgl_masuk) BETWEEN '2024-06-10' AND '2024-06-10' AND a.kode_unit = '1014' AND a.status_kunjungan != 8");

        // dd($kunjungan);
        // $pdf = new PDF('P', 'in', array('1.97', '2.36'));
        $pdf = new Fpdf('P', 'mm', 'A4');
        $i = $pdf->GetY();
        // $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetTitle('Cetak Etiket');
        $pdf->SetFont('Arial', 'B', 8);
        $y = 10;
        foreach ($kunjungan as $d) {
            foreach ($order_header as $oh) {
                if ($oh->kode_kunjungan == $d->kode_kunjungan) {
                    $pdf->Image('public/img/logo_rs.png', 5, $y, 15, 10);
                    $pdf->SetXY(2, $y);
                    $pdf->Cell(10, 0.5, 'RINCIAN BIAYA FARMASI', 0, 1);
                    $pdf->SetXY(2, $y);
                    $pdf->Cell(10, 0.5, 'RSUD WALED KAB.CIREBON', 0, 1);
                    // $pdf->SetXY(8, 1);
                    // $pdf->SetXY(4, $y);
                    // $pdf->Cell(100, 10, $d->no_rm . ' | ' . $d->nama_pasien . ' | Diagnosa :' . $oh->diagnosa, 10, 1);
                    $y = $y + 10;
                }
            }
        }
        $pdf->Output();
        exit;
    }
}
