<?php

namespace App\Http\Controllers;

use App\Models\ts_antrian_farmasi;
use App\Models\ts_layanan_header_order;
use App\Models\ts_retur_header_dummy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class antrianController extends Controller
{
    public function index()
    {
        return view('template_login/antrian');
    }
    public function ambilantrian(request $request)
    {
        $rm = $request->rm;
        $date = $this->get_date();
        //ambil ts_kunjungan
        $ts_kunjungan = DB::connection('mysql')->select('select * from ts_kunjungan where no_rm = ? and status_kunjungan = ? and date(tgl_masuk) = ?', [$rm, 1, $date]);
        if (count($ts_kunjungan) == 0) {
            $data = [
                'kode' => 500,
                'message' => 'Tidak ada kunjungan hari ini !',
            ];
            echo json_encode($data);
            die;
        } else {
            $kodekunjungan = $ts_kunjungan[0]->kode_kunjungan;
            $cek_antrian = db::connection('mysql2')->select('select * from ts_antrian_farmasi where kode_kunjungan = ?', [$kodekunjungan]);
            if (count($cek_antrian) > 0) {
                $data = [
                    'kode' => 500,
                    'message' => 'Nomor antrian sudah diambil !',
                ];
                echo json_encode($data);
                die;
            } else {
                $orderan = db::connection('mysql2')->select("select * from ts_layanan_header_order where kode_kunjungan = '$kodekunjungan' and kode_unit in ('4002','4008') and status_order = '0'");
                if (count($orderan) > 0) {
                $cek_racikan = DB::connection('mysql2')->select("SELECT DISTINCT b.kode_kunjungan
                ,b.`no_rm`
                ,simrs_waled.fc_nama_px(b.no_rm) AS  nama_pasien
                ,fc_nama_unit1(unit_pengirim) AS nama_unit
                ,fc_nama_paramedis1(dok_kirim) AS nama_dokter
                ,fc_hitung_racikan_(b.kode_kunjungan) AS jumlah_racikan
                ,a.status_order
                FROM ts_layanan_header_order a
                INNER JOIN simrs_waled.ts_kunjungan b ON a.kode_kunjungan = b.`kode_kunjungan`
                WHERE a.kode_kunjungan = '$kodekunjungan' and a.status_order = '0'");
                    $mt_unit = db::select('select * from mt_unit where kode_unit = ?', [$orderan[0]->kode_unit]);
                    $pref = $mt_unit[0]->prefix_unit;
                    $kodeunit = $mt_unit[0]->kode_unit;
                    if ($cek_racikan[0]->jumlah_racikan == 0) {
                        $jenis_antrian = 'REGULER';
                        $nomor_antrian = $this->get_nomor_antrian_reguler($pref, $kodeunit);
                    } else {
                        $jenis_antrian = 'RACIKAN';
                        $nomor_antrian = $this->get_nomor_antrian_racikan($pref, $kodeunit);
                    }
                    $data_antrian = [
                        'rm' => $ts_kunjungan[0]->no_rm,
                        'nomor_antrian' => $nomor_antrian,
                        'jenis_antrian' => $jenis_antrian,
                        'kode_unit' => $orderan[0]->kode_unit,
                        'unit_pengirim' => $ts_kunjungan[0]->kode_unit,
                        'kode_kunjungan' => $kodekunjungan,
                        'tgl_antrian' => $this->get_now(),
                    ];
                    ts_antrian_farmasi::create($data_antrian);
                    foreach ($orderan as $od) {
                        $update = ts_layanan_header_order::whereRaw('kode_kunjungan = ? and kode_unit = ? and status_layanan = ?', array($kodekunjungan, $kodeunit, 1))->update(['status_order' => '1']);
                    }
                    $data = [
                        'kode' => 200,
                        'message' => 'Nomor antrian berhasil diambil !',
                    ];
                    echo json_encode($data);
                    die;
                }else{
                    $data = [
                        'kode' => 500,
                        'message' => 'Order belum dikirim poli !',
                    ];
                    echo json_encode($data);
                    die;
                }
            }
        }
    }
    public function get_date()
    {
        $dt = Carbon::now()->timezone('Asia/Jakarta');
        $date = $dt->toDateString();
        $now = $date;
        return $now;
    }
    public function get_now()
    {
        $dt = Carbon::now()->timezone('Asia/Jakarta');
        $date = $dt->toDateString();
        $time = $dt->toTimeString();
        $now = $date . ' ' . $time;
        return $now;
    }
    public function get_nomor_antrian_reguler($pref, $kodeunit)
    {
        $q = DB::connection('mysql2')->select("SELECT id,nomor_antrian,RIGHT(nomor_antrian,3) AS kd_max  FROM ts_antrian_farmasi
        WHERE DATE(tgl_antrian) = CURDATE() AND jenis_antrian =  'REGULER' AND kode_unit = '$kodeunit'
        ORDER BY id DESC
        LIMIT 1");
        $kd = "";
        if (count($q) > 0) {
            foreach ($q as $k) {
                $tmp = ((int) $k->kd_max) + 1;
                $kd = sprintf("%03s", $tmp);
            }
        } else {
            $kd = "001";
        }
        date_default_timezone_set('Asia/Jakarta');
        return 'A - '.$pref . $kd;
    }
    public function get_nomor_antrian_racikan($pref, $kodeunit)
    {
        $q = DB::connection('mysql2')->select("SELECT id,nomor_antrian,RIGHT(nomor_antrian,3) AS kd_max  FROM ts_antrian_farmasi
        WHERE DATE(tgl_antrian) = CURDATE() AND jenis_antrian = 'RACIKAN' and kode_unit = '$kodeunit'
        ORDER BY id DESC
        LIMIT 1");
        $kd = "";
        if (count($q) > 0) {
            foreach ($q as $k) {
                $tmp = ((int) $k->kd_max) + 1;
                $kd = sprintf("%03s", $tmp);
            }
        } else {
            $kd = "001";
        }
        date_default_timezone_set('Asia/Jakarta');
        return 'B - '.$pref . $kd;
    }
}
