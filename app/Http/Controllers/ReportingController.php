<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportingController extends V2pelayananController
{
    public function index()
    {
        $menu = 'reporting';
        $now = $this->get_date();
        $mt_unit = DB::select('select * from mt_unit where kode_unit > 4000 AND kode_unit <= 4013');
        return view('reporting.index', compact([
            'menu', 'now', 'mt_unit'
        ]));
    }
    public function ambil_data_pemakaian(Request $request)
    {
        $tglawal = $request->awal;
        $tglakhir = $request->akhir;
        $unit = $request->unit;
        // $data_kunjungan = db::SELECT("select date(tgl_masuk) as tgl_masuk,kode_kunjungan,no_rm,fc_nama_px(no_rm) as nama_pasien,fc_nama_unit1(kode_unit) as nama_unit,no_sep,fc_nama_paramedis1(kode_paramedis) as nama_Dokter from ts_kunjungan where date(tgl_masuk) between '$tglawal' and '$tglakhir' and kode_unit < 2000 and kode_unit != 1002 and kode_unit != 1023 order by kode_kunjungan desc");
        $data = db::select("CALL sp_farmasi_laporan_order_layanan_resep('$tglawal','$tglakhir','','','')");
        return view('reporting.datapemakaian', compact([
            'tglawal',
            'tglakhir',
            'unit', 'data_kunjungan', 'data'
        ]));
    }
    public function Cetak_Data_pemakaian($tglawal, $tglakhir, $unit)
    {
        $data_kunjungan = db::SELECT("select date(tgl_masuk) as tgl_masuk,kode_kunjungan,no_rm,fc_nama_px(no_rm) as nama_pasien,fc_nama_unit1(kode_unit) as nama_unit,no_sep,fc_nama_paramedis1(kode_paramedis) as nama_Dokter from ts_kunjungan where date(tgl_masuk) between '$tglawal' and '$tglakhir' and kode_unit < 2000 and kode_unit != 1002 and kode_unit != 1023 order by kode_kunjungan desc");
        $data = db::select("CALL sp_farmasi_laporan_order_layanan_resep('$tglawal','$tglakhir','','','')");
        return view('reporting.cetakandatapemakaian', compact([
            'data_kunjungan'
        ]));
    }
}
