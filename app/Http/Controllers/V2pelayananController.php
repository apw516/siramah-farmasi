<?php

namespace App\Http\Controllers;

use App\Models\mt_racikan;
use App\Models\mt_racikan_detail_dummy;
use App\Models\order_racikan_detail;
use App\Models\order_racikan_header;
use App\Models\ti_kartu_stok;
use App\Models\ts_antrian_farmasi;
use App\Models\ts_layanan_detail_dummy;
use App\Models\ts_layanan_header_dummy;
use App\Models\ts_layanan_header_order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\PDF;
use simitsdk\phpjasperxml\PHPJasperXML;
use Codedge\Fpdf\Fpdf\Fpdf;

class V2pelayananController extends Controller
{
    public function IndexLayananResep()
    {
        $menu = 'Layananresep';
        $now = $this->get_date();
        return view('v2Layanan.indexlayanan', compact([
            'menu',
            'now'
        ]));
    }
    public function ambil_antrian_non_racikan(Request $request)
    {
        $tgl = $request->tanggal;
        $list = Db::connection('mysql2')->select('select *,status_antrian as status_order,simrs_waled.fc_nama_px(rm) as nama_pasien,fc_alamat(rm) as alamat_pasien,fc_nama_unit1(unit_pengirim) as nama_unit from ts_antrian_farmasi where date(tgl_antrian) = ? and kode_unit = ?', [$tgl, auth()->user()->unit]);
        return view('v2Layanan.tabel_antrian_non_racikan', compact('list'));
    }
    public function ambil_antrian_racikan(Request $request)
    {
        $tgl = $request->tanggal;
        $list = Db::connection('mysql2')->select('select *,status_antrian as status_order,simrs_waled.fc_nama_px(rm) as nama_pasien,fc_alamat(rm) as alamat_pasien,fc_nama_unit1(unit_pengirim) as nama_unit from ts_antrian_farmasi where date(tgl_antrian) = ? and kode_unit = ?', [$tgl, auth()->user()->unit]);
        return view('v2Layanan.tabel_antrian_racikan', compact('list'));
    }
    public function tampildatapasien(Request $request)
    {
        $awal = $request->awal;
        $akhir = $request->akhir;

        $list = Db::select('SELECT kode_kunjungan
        ,no_rm
        ,kode_unit
        ,date(tgl_masuk) as tgl_masuk
        ,fc_alamat(no_rm) as alamat
        ,fc_nama_px(no_rm) AS nama_pasien
        ,fc_nama_unit1(kode_unit) AS nama_unit FROM ts_kunjungan WHERE status_kunjungan != ? AND DATE(tgl_masuk) BETWEEN ? AND ? AND kode_unit < ?', [8,$awal,$akhir,'2000']);
        return view('v2Layanan.tabel_data_pasien', compact('list'));
    }
    public function ambil_detail_orderan(Request $request)
    {
        $kodekunjungan = $request->kodekunjungan;
        $idantrian = $request->idantrian;
        $ts_kunjungan = db::select('select no_rm,fc_nama_px(no_rm) as nama_pasien,fc_alamat(no_rm) alamat_pasien,fc_NAMA_PENJAMIN2(kode_penjamin) as nama_penjamin ,fc_nama_unit1(kode_unit) as nama_unit,fc_nama_paramedis1(kode_paramedis) as nama_dokter from ts_kunjungan where kode_kunjungan = ?', [$kodekunjungan]);
        $data_resep = DB::connection('mysql2')->select("SELECT
        a.id AS id_header
        ,b.id AS id_detail
        ,b.`kode_barang`
        ,fc_nama_barang(b.`kode_barang`) AS nama_barang
        ,b.`jumlah_layanan`
        ,b.`aturan_pakai`
        ,b.`tipe_anestesi`
        ,c.`sediaan`
        ,c.`dosis`
        ,b.aturan_pakai as aturan_pakai_def
        ,b.keterangan
        ,d.`nama_racikan` AS nama_racikan
        ,b.kategori_resep
        ,d.kemasan
        FROM ts_layanan_header_order a
        INNER JOIN ts_layanan_detail_order b ON a.id = b.`row_id_header`
        LEFT OUTER JOIN mt_barang c ON b.`kode_barang` = c.`kode_barang`
        LEFT OUTER JOIN ts_header_racikan_order d ON b.`kode_barang` = d.id
        WHERE a.`kode_kunjungan` = '$kodekunjungan' AND a.`status_layanan` = '1' AND b.`status_layanan_detail` = 'OPN' AND a.status_order = '1'");
        return view('v2Layanan.detailorderan', compact([
            'ts_kunjungan', 'kodekunjungan', 'data_resep', 'idantrian'
        ]));
    }
    public function simpan_pelayanan_resep_reguler(request $request)
    {
        $data = json_decode($_POST['data'], true);
        $kodekunjungan = $request->kodekunjungan;
        $idantrian = $request->idantrian;
        $arrayindex_reguler = [];
        $arrayindex_kronis = [];
        $arrayindex_kemo = [];
        $arrayindex_hibah = [];
        foreach ($data as $nama) {
            $index =  $nama['name'];
            $value =  $nama['value'];
            $dataSet_order_farmasi[$index] = $value;
            if ($index == 'keterangan') {
                if ($dataSet_order_farmasi['kronis'] == 80) {
                    $arrayindex_reguler[] = $dataSet_order_farmasi;
                } else if ($dataSet_order_farmasi['kronis'] == 81) {
                    $arrayindex_kronis[] = $dataSet_order_farmasi;
                } else if ($dataSet_order_farmasi['kronis'] == 82) {
                    $arrayindex_kemo[] = $dataSet_order_farmasi;
                } else if ($dataSet_order_farmasi['kronis'] == 83) {
                    $arrayindex_hibah[] = $dataSet_order_farmasi;
                }
                $arrayindex_far[] = $dataSet_order_farmasi;
            }
        }
        // dd($arrayindex_far);
        foreach ($arrayindex_far as $a) {
            if ($a['jenisresep'] == 'reguler') {
                //cek stok reguler
                $cek_stok = db::connection('mysql2')->select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? AND status_stok = ?)', ([$a['kodebarang'], auth()->user()->unit,'1']));
               if(count($cek_stok) > 0){
                   $stok_current = $cek_stok[0]->stok_current - $a['jumlah'];
                   if ($stok_current < 0) {
                       $data = [
                           'kode' => 500,
                           'message' => $a['nama_obat'] . ' ' . 'Stok Tidak Mencukupi !',
                       ];
                       echo json_encode($data);
                       die;
                   }
               }else{
                $data = [
                    'kode' => 500,
                    'message' => $a['nama_obat'] . ' ' . 'Stok Tidak Mencukupi !',
                ];
                echo json_encode($data);
                die;
               }
            } else {
                //cek stok komponen racikan
                $header_racikan = $a['kodebarang'];
                $detail_racikan = DB::connection('mysql2')->select('SELECT * FROM ts_header_racikan_order a
                INNER JOIN ts_detail_racikan_order b ON a.`id` = b.`id_header`
                WHERE a.id = ?', [$header_racikan]);
                foreach ($detail_racikan as $dr) {
                    $cek_stok = db::connection('mysql2')->select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? and status_stok = ? )', ([$dr->kode_barang, auth()->user()->unit,'1']));
                    $stok_current = $cek_stok[0]->stok_current - $dr->qty;
                    $mt_barang = db::select('select * from mt_barang where kode_barang = ?', [$dr->kode_barang]);
                    // dd($cek_stok);
                    if ($stok_current < 0) {
                        $data = [
                            'kode' => 500,
                            'message' => 'komponen racik ' . $mt_barang[0]->nama_barang . ' Stok Tidak Mencukupi !',
                        ];
                        echo json_encode($data);
                        die;
                    }
                }
            }
        }
        $cek_reg = count($arrayindex_reguler);
        $cek_kron = count($arrayindex_kronis);
        $cek_kemo = count($arrayindex_kemo);
        $cek_hib = count($arrayindex_hibah);
        $kodeunit = auth()->user()->unit;
        $data_kunjungan = DB::select('select *,fc_nama_px(no_rm) as nama_pasien,fc_alamat(no_rm) as alamat_pasien from ts_kunjungan where kode_kunjungan = ?', [$kodekunjungan]);
        $unit = DB::select('select * from mt_unit where kode_unit = ?', [$kodeunit]);
        //obat reguler
        if ($cek_reg > 0) {
            foreach ($arrayindex_far as $a) {
                if ($a['jenisresep'] == 'reguler') {
                    //cek stok reguler
                    $cek_stok = db::connection('mysql2')->select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? AND status_stok = ? )', ([$a['kodebarang'], auth()->user()->unit,'1']));
                    $stok_current = $cek_stok[0]->stok_current - $a['jumlah'];
                    if ($stok_current < 0) {
                        $data = [
                            'kode' => 500,
                            'message' => $a['nama_obat'] . ' ' . 'Stok Tidak Mencukupi !',
                        ];
                        echo json_encode($data);
                        die;
                    }
                } else {
                    //cek stok komponen racikan
                    $header_racikan = $a['kodebarang'];
                    $detail_racikan = DB::connection('mysql2')->select('SELECT * FROM ts_header_racikan_order a
                INNER JOIN ts_detail_racikan_order b ON a.`id` = b.`id_header`
                WHERE a.id = ?', [$header_racikan]);
                    foreach ($detail_racikan as $dr) {
                        $cek_stok = db::connection('mysql2')->select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? AND status_stok = ?)', ([$dr->kode_barang, auth()->user()->unit,'1']));
                        $stok_current = $cek_stok[0]->stok_current - $dr->qty;
                        $mt_barang = db::select('select * from mt_barang where kode_barang = ?', [$dr->kode_barang]);
                        // dd($cek_stok);
                        if ($stok_current < 0) {
                            $data = [
                                'kode' => 500,
                                'message' => 'komponen racik ' . $mt_barang[0]->nama_barang . ' Stok Tidak Mencukupi !',
                            ];
                            echo json_encode($data);
                            die;
                        }
                    }
                }
            }

            $unitlog = auth()->user()->unit;
            $r = DB::connection('mysql2')->select("CALL GET_NOMOR_LAYANAN_HEADER($unitlog)");
            $kode_layanan_header = $r[0]->no_trx_layanan;
            if (strlen($kode_layanan_header) < 5) {
                $year = date('y');
                $kode_layanan_header = $unit[0]->prefix_unit . $year . date('m') . date('d') . '000001';
                DB::connection('mysql2')->select('insert into mt_nomor_trx (tgl,no_trx_layanan,unit) values (?,?,?)', [date('Y-m-d h:i:s'), $kode_layanan_header, $kodeunit]);
            }
            try {
                $ts_layanan_header = [
                    'kode_layanan_header' => $kode_layanan_header,
                    'tgl_entry' => $this->get_now(),
                    'kode_kunjungan' => $kodekunjungan,
                    'kode_unit' => $kodeunit,
                    'kode_tipe_transaksi' => '2',
                    'pic' => auth()->user()->id_simrs,
                    'status_layanan' => '8',
                    'keterangan' => 'FARMASI BARU',
                    'status_retur' => 'OPN',
                    'tagihan_pribadi' => '0',
                    'tagihan_penjamin' => '0',
                    'status_pembayaran' => 'OPN',
                    'dok_kirim' => $data_kunjungan[0]->kode_paramedis,
                    'unit_pengirim' => $data_kunjungan[0]->kode_unit
                ];
                $header = ts_layanan_header_dummy::create($ts_layanan_header);
            } catch (\Exception $e) {
                $data = [
                    'kode' => 500,
                    'message' => $e->getMessage(),
                ];
                echo json_encode($data);
            }
            $now = $this->get_now();
            $totalheader = 0;
            foreach ($arrayindex_reguler as $a) {
                if ($a['jenisresep'] == 'reguler') {
                    $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$a['kodebarang']]);
                    $total = $mt_barang[0]->harga_jual * $a['jumlah'];
                    if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                        $tagihan_pribadi = 0;
                        $tagihan_penjamin = $total;
                        $kategori_resep = 'Resep Kredit';
                        $kode_tipe_transaki = 2;
                        $status_layanan = 2;
                    } else {
                        $tagihan_pribadi = $total;
                        $tagihan_penjamin = 0;
                        $kategori_resep = 'Resep Tunai';
                        $kode_tipe_transaki = 1;
                        $status_layanan = 1;
                    }
                    $kode_detail_obat = $this->createLayanandetail();
                    try {
                        $grandtotal_detail = $total + 1700;
                        $ts_layanan_detail = [
                            'id_layanan_detail' => $kode_detail_obat,
                            'kode_layanan_header' => $kode_layanan_header,
                            'kode_tarif_detail' => '',
                            'total_tarif' => $mt_barang[0]->harga_jual,
                            'jumlah_layanan' => $a['jumlah'],
                            'total_layanan' => $total,
                            'grantotal_layanan' => $grandtotal_detail,
                            'diskon_layanan' => '1700',
                            'status_layanan_detail' => 'OPN',
                            'tgl_layanan_detail' => $now,
                            'kode_barang' => $a['kodebarang'],
                            'aturan_pakai' => $a['aturanpakai'],
                            'kategori_resep' => $kategori_resep,
                            'satuan_barang' => $mt_barang[0]->satuan,
                            'tipe_anestesi' => $a['kronis'],
                            'tagihan_pribadi' => $tagihan_pribadi,
                            'tagihan_penjamin' => $tagihan_penjamin,
                            'tgl_layanan_detail_2' => $now,
                            'row_id_header' => $header->id,
                            'kode_dokter1' => $data_kunjungan[0]->kode_paramedis
                        ];
                        $detail = ts_layanan_detail_dummy::create($ts_layanan_detail);
                        ts_layanan_header_order::whereRaw('id = ?', array($a['idheader']))->update(['status_order' => '2', 'status_layanan' => '2']);
                    } catch (\Exception $e) {
                        $data = [
                            'kode' => 500,
                            'message' => $e->getMessage(),
                        ];
                        echo json_encode($data);
                    }
                    $totalheader = $totalheader + $grandtotal_detail;
                } else {
                    //ambildetailracikan
                    $kode_racikan_header = $a['kodebarang'];
                    $detail_racikan = DB::connection('mysql2')->select('SELECT * FROM ts_header_racikan_order a
                    INNER JOIN ts_detail_racikan_order b ON a.`id` = b.`id_header`
                    WHERE a.id = ?', [$kode_racikan_header]);
                    if ($detail_racikan[0]->tipe_racikan == 1) {
                        $tipe_racikan = 'NS';
                    } else {
                        $tipe_racikan = 'S';
                    }
                    if ($detail_racikan[0]->kemasan == 1) {
                        $kemasan = 'KAPSUL';
                    } else if ($detail_racikan[0]->kemasan == 2) {
                        $kemasan = 'KERTAS PERKAMEN';
                    } else if ($detail_racikan[0]->kemasan == 3) {
                        $kemasan = 'POT SALEP';
                    }
                    $jumlah_racikan = $detail_racikan[0]->jumlah_racikan;
                    $namaracikan = $detail_racikan[0]->nama_racikan;
                    $kode_racik = $this->createKodeRacikan();
                    $mt_racik_header = [
                        'kode_racik' => $kode_racik,
                        'tgl_racik' => $this->get_now(),
                        'nama_racik' => $namaracikan,
                        'tipe_racik' => $tipe_racikan,
                        'qty_racik' => $jumlah_racikan,
                        'kemasan' => $kemasan,
                    ];
                    $header_r = mt_racikan::create($mt_racik_header);
                    $totalracik = 0;
                    foreach ($detail_racikan as $dr) {
                        $mt_barang = db::select('select * from mt_barang where kode_barang = ?', [$dr->kode_barang]);
                        $jasa_embalase = 1700;
                        $gt_barang = $mt_barang[0]->harga_jual * $dr->qty;
                        $gt_barang_total = $gt_barang + $jasa_embalase;
                        $mt_racik_detail = [
                            'kode_racik' => $kode_racik,
                            'kode_barang' => $dr->kode_barang,
                            'qty_barang' => $dr->qty,
                            'satuan_barang' => $mt_barang[0]->satuan,
                            'harga_satuan_barang' => $mt_barang[0]->harga_jual,
                            'grantotal_barang' => $gt_barang_total,
                            'harga_brg_embalase' => $jasa_embalase,
                        ];
                        $totalracik = $totalracik + $gt_barang_total;
                        $savedetail_racikan = mt_racikan_detail_dummy::create($mt_racik_detail);
                    }
                    mt_racikan::whereRaw('id = ?', array($header_r->id))->update(['total_racik' => $totalracik]);
                    if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                        $tagihan_pribadi = 0;
                        $tagihan_penjamin = $totalracik;
                        $kategori_resep = 'Resep Kredit';
                        $kode_tipe_transaki = 2;
                        $status_layanan = 2;
                    } else {
                        $tagihan_pribadi = $totalracik;
                        $tagihan_penjamin = 0;
                        $kategori_resep = 'Resep Tunai';
                        $kode_tipe_transaki = 1;
                        $status_layanan = 1;
                    }
                    $kode_detail_obat = $this->createLayanandetail();
                    try {
                        $ts_layanan_detail = [
                            'id_layanan_detail' => $kode_detail_obat,
                            'kode_layanan_header' => $kode_layanan_header,
                            'kode_tarif_detail' => '',
                            'total_tarif' => $totalracik,
                            'jumlah_layanan' => $jumlah_racikan,
                            'total_layanan' => $totalracik,
                            'grantotal_layanan' => $totalracik,
                            'status_layanan_detail' => 'OPN',
                            'tgl_layanan_detail' => $now,
                            'kode_barang' => $kode_racik,
                            'aturan_pakai' => $detail_racikan[0]->aturan_pakai,
                            'kategori_resep' => $kategori_resep,
                            'satuan_barang' => '',
                            'tipe_anestesi' => $a['kronis'],
                            'tagihan_pribadi' => $tagihan_pribadi,
                            'tagihan_penjamin' => $tagihan_penjamin,
                            'tgl_layanan_detail_2' => $now,
                            'row_id_header' => $header->id,
                            'kode_dokter1' => $data_kunjungan[0]->kode_paramedis,
                            'keterangan' => $a['keterangan']
                        ];
                        $detail = ts_layanan_detail_dummy::create($ts_layanan_detail);
                        ts_layanan_header_order::whereRaw('id = ?', array($a['idheader']))->update(['status_order' => '2', 'status_layanan' => '2']);
                    } catch (\Exception $e) {
                        $data = [
                            'kode' => 500,
                            'message' => $e->getMessage(),
                        ];
                        echo json_encode($data);
                    }
                    $totalheader = $totalheader + $totalracik;
                }
            }

            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagian_penjamin_head = 1000;
                $tagian_pribadi_head = 0;
            } else {
                $tagian_penjamin_head = 0;
                $tagian_pribadi_head = 1000;
            }
            $JASA_BACA = 1000;
            $ts_layanan_detail3 = [
                'id_layanan_detail' => $this->createLayanandetail(),
                'kode_layanan_header' => $kode_layanan_header,
                'kode_tarif_detail' => 'TX23523',
                'total_tarif' => $JASA_BACA,
                'jumlah_layanan' => 1,
                'total_layanan' => $JASA_BACA,
                'grantotal_layanan' => $JASA_BACA,
                'status_layanan_detail' => 'OPN',
                'tgl_layanan_detail' => $now,
                'kode_dokter1' => $data_kunjungan[0]->kode_paramedis,
                'tagihan_pribadi' => $tagian_pribadi_head,
                'tagihan_penjamin' => $tagian_penjamin_head,
                'tgl_layanan_detail_2' => $now,
                'row_id_header' => $header->id,
            ];
            $detail3 = ts_layanan_detail_dummy::create($ts_layanan_detail3);


            //membedakan racikan dan non racikan;
            $get_detail_obat = DB::connection('mysql2')->select('select * from ts_layanan_detail where row_id_header = ? and kode_tarif_detail = ?', [$header->id, '']);
            foreach ($get_detail_obat as $do) {
                $kode_barang = $do->kode_barang;
                $awal = substr($kode_barang, 0, 1);
                if ($awal == 'R') {
                    $detail_racikan = db::connection('mysql2')->select('select * from mt_racikan_detail where kode_racik = ?',[$kode_barang]);
                    foreach($detail_racikan as $dr){
                        $cek_stok = db::connection('mysql2')->select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? AND status_stok = ? )', ([$dr->kode_barang, auth()->user()->unit,'1']));
                        $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$dr->kode_barang]);
                        $stok_current = $cek_stok[0]->stok_current - $dr->qty_barang;
                        if ($stok_current < 0) {
                            $data = [
                                'kode' => 500,
                                'message' => $mt_barang[0]->nama_barang . ' ' . 'Stok Tidak Mencukupi !',
                            ];
                            echo json_encode($data);
                            die;
                        }
                        $data_ti_kartu_stok = [
                            'no_dokumen' => $do->kode_layanan_header,
                            'no_dokumen_detail' => $do->id_layanan_detail,
                            'tgl_stok' => $this->get_now(),
                            'kode_unit' => auth()->user()->unit,
                            'kode_barang' => $dr->kode_barang,
                            'stok_last' => $cek_stok[0]->stok_current,
                            'stok_out' => $dr->qty_barang,
                            'stok_current' => $stok_current,
                            'stok_global' => '0',
                            'harga_beli' => $mt_barang[0]->hna,
                            'act' => '1',
                            'act_ed' => '1',
                            'inputby' => auth()->user()->id,
                            'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien,
                            'status_stok' => '2'
                        ];
                        $insert_ti_kartu_stok = ti_kartu_stok::create($data_ti_kartu_stok);
                    }
                } else {
                    //cek stok obat reguler
                    $cek_stok = db::connection('mysql2')->select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? AND status_stok = ? )', ([$do->kode_barang, auth()->user()->unit,'1']));

                    $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$do->kode_barang]);
                    $stok_current = $cek_stok[0]->stok_current - $do->jumlah_layanan;
                    if ($stok_current < 0) {
                        $data = [
                            'kode' => 500,
                            'message' => $mt_barang[0]->nama_barang . ' ' . 'Stok Tidak Mencukupi !',
                        ];
                        echo json_encode($data);
                        die;
                    }
                    //end of cek stok reguler
                    //insert kartu stok
                    $data_ti_kartu_stok = [
                        'no_dokumen' => $do->kode_layanan_header,
                        'no_dokumen_detail' => $do->id_layanan_detail,
                        'tgl_stok' => $this->get_now(),
                        'kode_unit' => auth()->user()->unit,
                        'kode_barang' => $do->kode_barang,
                        'stok_last' => $cek_stok[0]->stok_current,
                        'stok_out' => $do->jumlah_layanan,
                        'stok_current' => $stok_current,
                        'stok_global' => '0',
                        'harga_beli' => $mt_barang[0]->hna,
                        'act' => '1',
                        'act_ed' => '1',
                        'inputby' => auth()->user()->id,
                        'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien,
                        'status_stok' => '2'
                    ];
                    $insert_ti_kartu_stok = ti_kartu_stok::create($data_ti_kartu_stok);
                    //end of kartu stok
                }
            }

            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagihan_penjamin_header = $totalheader+$JASA_BACA;
                $tagihan_pribadi_header = '0';
            } else {
                $tagihan_penjamin_header = '0';
                $tagihan_pribadi_header = $totalheader+$JASA_BACA;
            }
            foreach ($arrayindex_reguler as $ar) {
                $idheader = $ar['idheader'];
            }
            ts_layanan_header_dummy::where('id', $header->id)
                ->update(['status_layanan' => $status_layanan, 'kode_tipe_transaksi' => $kode_tipe_transaki, 'total_layanan' => $totalheader, 'tagihan_penjamin' => $tagihan_penjamin_header, 'tagihan_pribadi' => $tagihan_pribadi_header]);
                ti_kartu_stok::where('no_dokumen',$kode_layanan_header)->update(['status_stok' => 1]);
        }

         //obat kronis
         if ($cek_kron > 0) {
            foreach ($arrayindex_far as $a) {
                if ($a['jenisresep'] == 'reguler') {
                    //cek stok reguler
                    $cek_stok = db::connection('mysql2')->select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? AND status_stok = ? )', ([$a['kodebarang'], auth()->user()->unit,'1']));
                    $stok_current = $cek_stok[0]->stok_current - $a['jumlah'];
                    if ($stok_current < 0) {
                        $data = [
                            'kode' => 500,
                            'message' => $a['nama_obat'] . ' ' . 'Stok Tidak Mencukupi !',
                        ];
                        echo json_encode($data);
                        die;
                    }
                } else {
                    //cek stok komponen racikan
                    $header_racikan = $a['kodebarang'];
                    $detail_racikan = DB::connection('mysql2')->select('SELECT * FROM ts_header_racikan_order a
                INNER JOIN ts_detail_racikan_order b ON a.`id` = b.`id_header`
                WHERE a.id = ?', [$header_racikan]);
                    foreach ($detail_racikan as $dr) {
                        $cek_stok = db::connection('mysql2')->select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? AND status_stok = ?)', ([$dr->kode_barang, auth()->user()->unit,'1']));
                        $stok_current = $cek_stok[0]->stok_current - $dr->qty;
                        $mt_barang = db::select('select * from mt_barang where kode_barang = ?', [$dr->kode_barang]);
                        // dd($cek_stok);
                        if ($stok_current < 0) {
                            $data = [
                                'kode' => 500,
                                'message' => 'komponen racik ' . $mt_barang[0]->nama_barang . ' Stok Tidak Mencukupi !',
                            ];
                            echo json_encode($data);
                            die;
                        }
                    }
                }
            }

            $unitlog = auth()->user()->unit;
            $r = DB::connection('mysql2')->select("CALL GET_NOMOR_LAYANAN_HEADER($unitlog)");
            $kode_layanan_header = $r[0]->no_trx_layanan;
            if (strlen($kode_layanan_header) < 5) {
                $year = date('y');
                $kode_layanan_header = $unit[0]->prefix_unit . $year . date('m') . date('d') . '000001';
                DB::connection('mysql2')->select('insert into mt_nomor_trx (tgl,no_trx_layanan,unit) values (?,?,?)', [date('Y-m-d h:i:s'), $kode_layanan_header, $kodeunit]);
            }
            try {
                $ts_layanan_header = [
                    'kode_layanan_header' => $kode_layanan_header,
                    'tgl_entry' => $this->get_now(),
                    'kode_kunjungan' => $kodekunjungan,
                    'kode_unit' => $kodeunit,
                    'kode_tipe_transaksi' => '2',
                    'pic' => auth()->user()->id_simrs,
                    'status_layanan' => '8',
                    'keterangan' => 'FARMASI BARU',
                    'status_retur' => 'OPN',
                    'tagihan_pribadi' => '0',
                    'tagihan_penjamin' => '0',
                    'status_pembayaran' => 'OPN',
                    'dok_kirim' => $data_kunjungan[0]->kode_paramedis,
                    'unit_pengirim' => $data_kunjungan[0]->kode_unit
                ];
                $header = ts_layanan_header_dummy::create($ts_layanan_header);
            } catch (\Exception $e) {
                $data = [
                    'kode' => 500,
                    'message' => $e->getMessage(),
                ];
                echo json_encode($data);
            }
            $now = $this->get_now();
            $totalheader = 0;
            foreach ($arrayindex_kronis as $a) {
                if ($a['jenisresep'] == 'reguler') {
                    $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$a['kodebarang']]);
                    $total = $mt_barang[0]->harga_jual * $a['jumlah'];
                    if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                        $tagihan_pribadi = 0;
                        $tagihan_penjamin = $total;
                        $kategori_resep = 'Resep Kredit';
                        $kode_tipe_transaki = 2;
                        $status_layanan = 2;
                    } else {
                        $tagihan_pribadi = $total;
                        $tagihan_penjamin = 0;
                        $kategori_resep = 'Resep Tunai';
                        $kode_tipe_transaki = 1;
                        $status_layanan = 1;
                    }
                    $kode_detail_obat = $this->createLayanandetail();
                    try {
                        $ts_layanan_detail = [
                            'id_layanan_detail' => $kode_detail_obat,
                            'kode_layanan_header' => $kode_layanan_header,
                            'kode_tarif_detail' => '',
                            'total_tarif' => $mt_barang[0]->harga_jual,
                            'jumlah_layanan' => $a['jumlah'],
                            'total_layanan' => $total,
                            'grantotal_layanan' => $total,
                            'status_layanan_detail' => 'OPN',
                            'tgl_layanan_detail' => $now,
                            'kode_barang' => $a['kodebarang'],
                            'aturan_pakai' => $a['aturanpakai'],
                            'kategori_resep' => $kategori_resep,
                            'satuan_barang' => $mt_barang[0]->satuan,
                            'tipe_anestesi' => $a['kronis'],
                            'tagihan_pribadi' => $tagihan_pribadi,
                            'tagihan_penjamin' => $tagihan_penjamin,
                            'tgl_layanan_detail_2' => $now,
                            'row_id_header' => $header->id,
                            'kode_dokter1' => $data_kunjungan[0]->kode_paramedis
                        ];
                        $detail = ts_layanan_detail_dummy::create($ts_layanan_detail);
                        ts_layanan_header_order::whereRaw('id = ?', array($a['idheader']))->update(['status_order' => '2', 'status_layanan' => '2']);
                    } catch (\Exception $e) {
                        $data = [
                            'kode' => 500,
                            'message' => $e->getMessage(),
                        ];
                        echo json_encode($data);
                    }
                    $totalheader = $totalheader + $total;
                } else {
                    //ambildetailracikan
                    $kode_racikan_header = $a['kodebarang'];
                    $detail_racikan = DB::connection('mysql2')->select('SELECT * FROM ts_header_racikan_order a
                    INNER JOIN ts_detail_racikan_order b ON a.`id` = b.`id_header`
                    WHERE a.id = ?', [$kode_racikan_header]);
                    if ($detail_racikan[0]->tipe_racikan == 1) {
                        $tipe_racikan = 'NS';
                    } else {
                        $tipe_racikan = 'S';
                    }
                    if ($detail_racikan[0]->kemasan == 1) {
                        $kemasan = 'KAPSUL';
                    } else if ($detail_racikan[0]->kemasan == 2) {
                        $kemasan = 'KERTAS PERKAMEN';
                    } else if ($detail_racikan[0]->kemasan == 3) {
                        $kemasan = 'POT SALEP';
                    }
                    $jumlah_racikan = $detail_racikan[0]->jumlah_racikan;
                    $namaracikan = $detail_racikan[0]->nama_racikan;
                    $kode_racik = $this->createKodeRacikan();
                    $mt_racik_header = [
                        'kode_racik' => $kode_racik,
                        'tgl_racik' => $this->get_now(),
                        'nama_racik' => $namaracikan,
                        'tipe_racik' => $tipe_racikan,
                        'qty_racik' => $jumlah_racikan,
                        'kemasan' => $kemasan,
                    ];
                    $header_r = mt_racikan::create($mt_racik_header);
                    $total = 0;
                    foreach ($detail_racikan as $dr) {
                        $mt_barang = db::select('select * from mt_barang where kode_barang = ?', [$dr->kode_barang]);
                        $mt_racik_detail = [
                            'kode_racik' => $kode_racik,
                            'kode_barang' => $dr->kode_barang,
                            'qty_barang' => $dr->qty,
                            'satuan_barang' => $mt_barang[0]->satuan,
                            'harga_satuan_barang' => $mt_barang[0]->harga_jual,
                            'grantotal_barang' => $mt_barang[0]->harga_jual * $dr->qty,
                        ];
                        $total = $total + $mt_barang[0]->harga_jual * $dr->qty;
                        $savedetail_racikan = mt_racikan_detail_dummy::create($mt_racik_detail);
                    }
                    if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                        $tagihan_pribadi = 0;
                        $tagihan_penjamin = $total;
                        $kategori_resep = 'Resep Kredit';
                        $kode_tipe_transaki = 2;
                        $status_layanan = 2;
                    } else {
                        $tagihan_pribadi = $total;
                        $tagihan_penjamin = 0;
                        $kategori_resep = 'Resep Tunai';
                        $kode_tipe_transaki = 1;
                        $status_layanan = 1;
                    }
                    $kode_detail_obat = $this->createLayanandetail();
                    try {
                        $ts_layanan_detail = [
                            'id_layanan_detail' => $kode_detail_obat,
                            'kode_layanan_header' => $kode_layanan_header,
                            'kode_tarif_detail' => '',
                            'total_tarif' => $total,
                            'jumlah_layanan' => $jumlah_racikan,
                            'total_layanan' => $total,
                            'grantotal_layanan' => $total,
                            'status_layanan_detail' => 'OPN',
                            'tgl_layanan_detail' => $now,
                            'kode_barang' => $kode_racik,
                            'aturan_pakai' => $detail_racikan[0]->aturan_pakai,
                            'kategori_resep' => $kategori_resep,
                            'satuan_barang' => '',
                            'tipe_anestesi' => $a['kronis'],
                            'tagihan_pribadi' => $tagihan_pribadi,
                            'tagihan_penjamin' => $tagihan_penjamin,
                            'tgl_layanan_detail_2' => $now,
                            'row_id_header' => $header->id,
                            'kode_dokter1' => $data_kunjungan[0]->kode_paramedis,
                            'keterangan' => $a['keterangan']
                        ];
                        $detail = ts_layanan_detail_dummy::create($ts_layanan_detail);
                        ts_layanan_header_order::whereRaw('id = ?', array($a['idheader']))->update(['status_order' => '2', 'status_layanan' => '2']);
                    } catch (\Exception $e) {
                        $data = [
                            'kode' => 500,
                            'message' => $e->getMessage(),
                        ];
                        echo json_encode($data);
                    }
                    $totalheader = $totalheader + $total;
                }
            }

            //membedakan racikan dan non racikan;
            $get_detail_obat = DB::connection('mysql2')->select('select * from ts_layanan_detail where row_id_header = ? and kode_tarif_detail = ?', [$header->id, '']);
            foreach ($get_detail_obat as $do) {
                $kode_barang = $do->kode_barang;
                $awal = substr($kode_barang, 0, 1);
                if ($awal == 'R') {
                    $detail_racikan = db::connection('mysql2')->select('select * from mt_racikan_detail where kode_racik = ?',[$kode_barang]);
                    foreach($detail_racikan as $dr){
                        $cek_stok = db::connection('mysql2')->select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? AND status_stok = ? )', ([$dr->kode_barang, auth()->user()->unit,'1']));
                        $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$dr->kode_barang]);
                        $stok_current = $cek_stok[0]->stok_current - $dr->qty_barang;
                        if ($stok_current < 0) {
                            $data = [
                                'kode' => 500,
                                'message' => $mt_barang[0]->nama_barang . ' ' . 'Stok Tidak Mencukupi !',
                            ];
                            echo json_encode($data);
                            die;
                        }
                        $data_ti_kartu_stok = [
                            'no_dokumen' => $do->kode_layanan_header,
                            'no_dokumen_detail' => $do->id_layanan_detail,
                            'tgl_stok' => $this->get_now(),
                            'kode_unit' => auth()->user()->unit,
                            'kode_barang' => $dr->kode_barang,
                            'stok_last' => $cek_stok[0]->stok_current,
                            'stok_out' => $dr->qty_barang,
                            'stok_current' => $stok_current,
                            'stok_global' => '0',
                            'harga_beli' => $mt_barang[0]->hna,
                            'act' => '1',
                            'act_ed' => '1',
                            'inputby' => auth()->user()->id,
                            'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien,
                            'status_stok' => '2'
                        ];
                        $insert_ti_kartu_stok = ti_kartu_stok::create($data_ti_kartu_stok);
                    }
                } else {
                    //cek stok obat reguler
                    $cek_stok = db::connection('mysql2')->select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? AND status_stok = ? )', ([$do->kode_barang, auth()->user()->unit,'1']));

                    $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$do->kode_barang]);
                    $stok_current = $cek_stok[0]->stok_current - $do->jumlah_layanan;
                    if ($stok_current < 0) {
                        $data = [
                            'kode' => 500,
                            'message' => $mt_barang[0]->nama_barang . ' ' . 'Stok Tidak Mencukupi !',
                        ];
                        echo json_encode($data);
                        die;
                    }
                    //end of cek stok reguler
                    //insert kartu stok
                    $data_ti_kartu_stok = [
                        'no_dokumen' => $do->kode_layanan_header,
                        'no_dokumen_detail' => $do->id_layanan_detail,
                        'tgl_stok' => $this->get_now(),
                        'kode_unit' => auth()->user()->unit,
                        'kode_barang' => $do->kode_barang,
                        'stok_last' => $cek_stok[0]->stok_current,
                        'stok_out' => $do->jumlah_layanan,
                        'stok_current' => $stok_current,
                        'stok_global' => '0',
                        'harga_beli' => $mt_barang[0]->hna,
                        'act' => '1',
                        'act_ed' => '1',
                        'inputby' => auth()->user()->id,
                        'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien,
                        'status_stok' => '2'
                    ];
                    $insert_ti_kartu_stok = ti_kartu_stok::create($data_ti_kartu_stok);
                    //end of kartu stok
                }
            }

            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagihan_penjamin_header = $totalheader;
                $tagihan_pribadi_header = '0';
            } else {
                $tagihan_penjamin_header = '0';
                $tagihan_pribadi_header = $totalheader;
            }
            foreach ($arrayindex_reguler as $ar) {
                $idheader = $ar['idheader'];
            }
            ts_layanan_header_dummy::where('id', $header->id)
                ->update(['status_layanan' => $status_layanan, 'kode_tipe_transaksi' => $kode_tipe_transaki, 'total_layanan' => $totalheader, 'tagihan_penjamin' => $tagihan_penjamin_header, 'tagihan_pribadi' => $tagihan_pribadi_header]);
                ti_kartu_stok::where('no_dokumen',$kode_layanan_header)->update(['status_stok' => 1]);
        }

        ts_antrian_farmasi::whereRaw('id = ?', array($idantrian))->update(['status_antrian' => '1']);
        $data = [
            'kode' => 200,
            'message' => 'sukses',
            'idheader' => $header->id
        ];
        echo json_encode($data);
        die;
    }
    public function createLayanandetail()
    {
        $q = DB::connection('mysql2')->select('SELECT id,id_layanan_detail,RIGHT(id_layanan_detail,6) AS kd_max  FROM ts_layanan_detail
        WHERE DATE(tgl_layanan_detail) = CURDATE()
        ORDER BY id DESC
        LIMIT 1');
        $kd = "";
        if (count($q) > 0) {
            foreach ($q as $k) {
                $tmp = ((int) $k->kd_max) + 1;
                $kd = sprintf("%06s", $tmp);
            }
        } else {
            $kd = "000001";
        }
        date_default_timezone_set('Asia/Jakarta');
        return 'DET' . date('ymd') . $kd;
    }
    public function get_now()
    {
        $dt = Carbon::now()->timezone('Asia/Jakarta');
        $date = $dt->toDateString();
        $time = $dt->toTimeString();
        $now = $date . ' ' . $time;
        return $now;
    }
    public function get_date()
    {
        $dt = Carbon::now()->timezone('Asia/Jakarta');
        $date = $dt->toDateString();
        $now = $date;
        return $now;
    }
    public function riwayat_obat_hari_ini(Request $request)
    {
        $unit = auth()->user()->unit;
        $get_header = DB::connection('mysql2')->select("select * from ts_layanan_header where kode_kunjungan = ? and kode_unit = ?",[$request->kodekunjungan,$unit]);
        $list = DB::connection('mysql2')->select("SELECT a.kode_layanan_header,a.`kode_kunjungan`,a.id AS id_header
        ,b.id AS id_detail
        ,b.`kode_barang`
        ,fc_nama_barang(b.`kode_barang`) AS namma_barang
        ,b.`jumlah_layanan`
        ,b.`aturan_pakai`
        ,b.`tipe_anestesi`
        ,b.`satuan_barang`
        ,c.nama_racik
        ,c.kemasan
        ,b.keterangan01
         FROM ts_layanan_header a
        INNER JOIN ts_layanan_detail b ON a.id = b.`row_id_header`
        LEFT OUTER JOIN mt_racikan c on b.kode_barang = c.kode_racik
        WHERE A.`status_layanan` = '2' AND a.kode_kunjungan = '$request->kodekunjungan' AND b.`status_layanan_detail` = 'OPN' AND kode_unit = '$unit'");
        $kodekunjungan = $request->kodekunjungan;
        // dd($get_header);
        return view('v2Layanan.tabel_riwayat_obat_hari_ini', compact([
            'list','kodekunjungan','get_header'
        ]));
    }
    public function createKodeRacikan()
    {
        $q = DB::connection('mysql2')->select('SELECT id,kode_racik,RIGHT(kode_racik,3) AS kd_max  FROM mt_racikan
        WHERE DATE(tgl_racik) = CURDATE()
        ORDER BY id DESC
        LIMIT 1');
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
        return 'R' . date('ymd') . $kd;
    }
    public function cari_obat_reguler(Request $request)
    {
        $nama = $request->nama;
        // $pencarian_obat = DB::select('CALL sp_cari_obat_semua(?,?)', [$nama,auth()->user()->unit]);
        $pencarian_obat = DB::select('CALL sp_cari_obat_stok_all_erm(?,?)',[$nama,auth()->user()->unit]);
        return view('v2Layanan.tabel_obat_reguler', compact([
            'pencarian_obat'
        ]));
    }
    public function cari_obat_komponen_racik(Request $request)
    {
        $nama = $request->nama;
        // $pencarian_obat = DB::select('CALL sp_cari_obat_semua(?,?)', [$nama,auth()->user()->unit]);
        $obat = DB::select('CALL sp_cari_obat_stok_all_erm(?,?)',[$nama,auth()->user()->unit]);
        return view('v2Layanan.tabel_obat_komponen_racik', compact([
            'obat'
        ]));
    }
    public function add_draft_komponen(Request $request)
    {
            $dataheader = json_decode($_POST['dataheader'], true);
            $datalist = json_decode($_POST['datalist'], true);
            $jumlahracikan = $request->jumlahracikan;
            foreach ($dataheader as $nama) {
                $index =  $nama['name'];
                $value =  $nama['value'];
                $dataSet[$index] = $value;
            }
            $ts_kunjungan = DB::select('select * from ts_kunjungan where kode_kunjungan = ?', [$request->kodekunjungan]);
            if ($ts_kunjungan[0]->kode_penjamin == 'PO1') {
                $unit_tujuan = '4002';
            } else {
                $unit_tujuan = '4008';
            }
            $jumlah_racikan = $dataSet['jumlahracikan'];
            $data_header = [
                'nama_racikan' => $dataSet['namaracikan'],
                'tipe_racikan' => $dataSet['tiperacikan'],
                'jumlah_racikan' => $dataSet['jumlahracikan'],
                'kemasan' => $dataSet['kemasanracikan'],
                'aturan_pakai' => $dataSet['aturanpakairacikan'],
                'pic' => auth()->user()->id,
                'tgl_entry' => $this->get_now(),
                'kode_unit' => auth()->user()->unit,
                'kode_kunjungan' => $request->kodekunjungan,
                'unit_tujuan' => $unit_tujuan,
            ];
            $header = order_racikan_header::create($data_header);
            foreach ($datalist as $nama) {
                $index =  $nama['name'];
                $value =  $nama['value'];
                $dataList[$index] = $value;
                if ($index == 'dosisracik') {
                    $array_list[] = $dataList;
                }
            }
            // dd($array_list);
            $list_ket = [];
            foreach ($array_list as $arr) {
                $qty = $arr['dosisracik'] * $dataSet['jumlahracikan'] / $arr['dosis'];
                $list_ket[] = $arr['namaobat'] . ' Dosis Awal : ' . $arr['dosis'] . ' Dosis Racik : ' . $arr['dosisracik'].' Kebutuhan obat :'.$qty;
                $data_detail = [
                    'id_header' => $header->id,
                    'kode_barang' => $arr['kodebarang'],
                    'qty' => $qty,
                    'dosis_awal' => $arr['dosis'],
                    'dosis_racik' =>  $arr['dosisracik'],
                    'tgl_entry' => $this->get_now(),
                ];
                order_racikan_detail::create($data_detail);
            }
            $ket = implode(' ', $list_ket);
            // dd($list_ket);
            if ($dataSet['kemasanracikan'] == 1) {
                $sediaan = 'KAPSUL';
            } elseif ($dataSet['kemasanracikan'] == 2) {
                $sediaan = 'KERTAS PERKAMEN';
            } elseif ($dataSet['kemasanracikan'] == 3) {
                $sediaan = 'POT SALEP';
            }
            // dd($dataSet);
            return "<div class='row mt-2 text-xs'>
            <div class='col-md-2'>
                <div class='form-group'>
                    <label for='exampleFormControlInput1'>Nama Obat</label>
                    <input readonly type='text' class='form-control form-control-sm' id='nama_obat' name='nama_obat' value='$dataSet[namaracikan]'placeholder='name@example.com'>
                    <input hidden readonly type='text' class='form-control form-control-sm' id='kodebarang' name='kodebarang' value='$header->id'placeholder='name@example.com'>
                    <input hidden readonly type='text' class='form-control form-control-sm' id='idheader' name='idheader' value='0'placeholder='name@example.com'>
                    <input hidden readonly type='text' class='form-control form-control-sm' id='iddetail' name='iddetail' value='0'placeholder='name@example.com'>
                </div>
            </div>
            <div class='col-md-1'>
                <div class='form-group'>
                    <label for='exampleFormControlInput1'>Jenis Resep</label>
                    <input readonly type='text' class='form-control form-control-sm' id='jenisresep' name='jenisresep' value='racikan' placeholder='name@example.com'>
                </div>
            </div>
            <div class='col-md-1'>
                <div class='form-group'>
                    <label for='exampleFormControlInput1'>Dosis</label>
                    <input readonly type='text' class='form-control form-control-sm' id='dosis' name='dosis' value='-' placeholder='name@example.com'>
                </div>
            </div>
            <div class='col-md-1'>
                <div class='form-group'>
                    <label for='exampleFormControlInput1'>Sediaan</label>
                    <input readonly type='text' class='form-control form-control-sm' id='sediaan' name='sediaan' value='$sediaan' placeholder='name@example.com'>
                </div>
            </div>
            <div class='col-md-1'>
                <div class='form-group'>
                    <label for='exampleFormControlInput1'>Tipe anestesi</label>
                    <select class='form-control form-control-sm' id='kronis' name='kronis'>
                    <option value='80'>Reguler</option>
                    <option value='81'>Kronis</option>
                    <option value='82'>Kemoterapi</option>
                    <option value='83'>Hibah</option></select>
                </div>
            </div>
            <div class='col-md-1'>
                <div class='form-group'>
                    <label for='exampleFormControlInput1'>Jumlah</label>
                    <input readonly type='text' class='form-control form-control-sm' id='jumlah' name='jumlah' value='$dataSet[jumlahracikan]' placeholder='name@example.com'>
                </div>
            </div>
            <div class='col-md-2'>
                <div class='form-group'>
                    <label for='exampleFormControlInput1'>Aturan Pakai</label>
                    <textarea readonly type='text' class='form-control form-control-sm' id='aturanpakai' name='aturanpakai' value='' placeholder='name@example.com'>$dataSet[aturanpakairacikan]</textarea>
                </div>
            </div>
            <div class='col-md-2'>
                <div class='form-group'>
                    <label for='exampleFormControlInput1'>Keterangan</label>
                    <textarea readonly type='text' class='form-control form-control-sm' id='keterangan' name='keterangan' value='' placeholder='name@example.com'>$ket</textarea>
                </div>
            </div>
            <i class='bi bi-x-square remove_field form-group col-md-1 text-danger' kode2='' subtot='' jenis='' nama_barang='' kode_barang='' id_stok='' harga2='' satuan='' stok='' qty='' harga='' disc='' dosis='' sub='' sub2='' status='80' jenisracik='racikan'></i>
        </div>";
    }
    public function cetaketiket_2_all($id)
    {
        $get_header = DB::connection('mysql2')->select('select * from ts_layanan_header where kode_kunjungan = ?', [$id]);
        $dtpx = DB::select('SELECT no_rm,fc_nama_px(no_rm) AS nama, fc_umur(no_rm) AS umur,DATE(fc_tgl_lahir(no_rm)) AS tgl_lahir,fc_alamat(no_rm) AS alamat FROM ts_kunjungan WHERE kode_kunjungan = ?', [$get_header[0]->kode_kunjungan]);
        $get_detail = DB::connection('mysql2')->select('SELECT a.kode_barang,b.`nama_barang`,a.aturan_pakai,a.`ed_obat`,a.jumlah_layanan,a.jumlah_retur,d.nama_racik FROM ts_layanan_detail a
        LEFT OUTER JOIN mt_barang b ON a.`kode_barang` = b.`kode_barang`
        INNER JOIN ts_layanan_header c on a.row_id_header = c.id
        LEFT OUTER JOIN mt_racikan d on a.kode_barang = d.kode_racik
        WHERE c.kode_kunjungan = ?', [$id]);
        // dd($get_detail);
        // $pdf = new PDF('P', 'in', array('1.97', '2.36'));
        $pdf = new PDF('P', 'in', array('1.97', '2.36'));
        $i = $pdf->GetY();
        // $pdf->AliasNbPages();
        // $pdf->AddPage();
        $pdf->SetTitle('Cetak Etiket');
        $pdf->SetFont('Arial', 'B', 8);
        foreach ($get_detail as $d) {
            $total_barang = $d->jumlah_layanan - $d->jumlah_retur;
            if ($d->kode_barang != '') {
                $pdf->SetXY(0, $i);
                $pdf->Cell(0.1, 10, '' . $i, 0, 1);
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetXY(0, 0.4);
                $pdf->Cell(0.3, 0.10, $dtpx[0]->no_rm, 0, 0);
                $pdf->SetXY(0.8, 0.4);
                $pdf->Cell(0.3, 0.10, $dtpx[0]->tgl_lahir . '/ usia ' . $dtpx[0]->umur, 0, 0);
                $pdf->SetXY(0, 0.6);
                $pdf->Cell(0.3, 0.10, $dtpx[0]->nama, 0, 0);
                $pdf->SetFont('Arial', '', 5);
                $pdf->SetXY(0, 0.8);
                $pdf->MultiCell(1.9, 0.1, $dtpx[0]->alamat);
                $y = $pdf->GetY();
                // $pdf->Cell(0.3, 0.10, $dtpx[0]->alamat, 0, 0);
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->SetXY(0, $y + 0.1);
                $pdf->MultiCell(1.8, 0.10, $d->nama_barang . $d->nama_racik);
                $y = $pdf->GetY() + 0.007;
                $pdf->SetXY(0, $y);
                $pdf->MultiCell(1.9, 0.10, $d->aturan_pakai);
                // $pdf->Cell(0.3, 0.10, $d->nama_barang, 0, 0);
                // //A set
                $code = 'CODE 128';
                $pdf->Code128(0.1, 1.6, $code, 1.8, 0.4);
                // // $pdf->Cell(0.3, 0.10, $barcode, 0, 0);
                $y = $pdf->GetY();
                $pdf->SetFont('Arial', 'b', 5);
                $pdf->SetXY(0, $y);
                $pdf->Cell(0.3, 0.10, $get_header[0]->tgl_entry, 0, 0);
                $pdf->SetXY(1.2, $y);
                $pdf->Cell(0.3, 0.10, 'EXP' . $d->ed_obat, 0, 0);
                $i = 10;
            }
        }
        $pdf->Output();
        exit;
        // return;
    }
    public function CetakEtiket($id)
    {
        $get_header = DB::connection('mysql2')->select('select * from ts_layanan_header where id = ?', [$id]);
        $dtpx = DB::select('SELECT no_rm,fc_nama_px(no_rm) AS nama, fc_umur(no_rm) AS umur,DATE(fc_tgl_lahir(no_rm)) AS tgl_lahir,fc_alamat(no_rm) AS alamat FROM ts_kunjungan WHERE kode_kunjungan = ?', [$get_header[0]->kode_kunjungan]);
        $get_detail = DB::connection('mysql2')->select('SELECT a.kode_barang,b.`nama_barang`,a.aturan_pakai,a.`ed_obat`,a.jumlah_layanan,a.jumlah_retur,d.nama_racik FROM ts_layanan_detail a
        LEFT OUTER JOIN mt_barang b ON a.`kode_barang` = b.`kode_barang`
        INNER JOIN ts_layanan_header c on a.row_id_header = c.id
        LEFT OUTER JOIN mt_racikan d on a.kode_barang = d.kode_racik
        WHERE c.id = ?', [$id]);
        // dd($get_detail);
        // $pdf = new PDF('P', 'in', array('1.97', '2.36'));
        $pdf = new PDF('P', 'in', array('1.97', '2.36'));
        $i = $pdf->GetY();
        // $pdf->AliasNbPages();
        // $pdf->AddPage();
        $pdf->SetTitle('Cetak Etiket');
        $pdf->SetFont('Arial', 'B', 8);
        foreach ($get_detail as $d) {
            $total_barang = $d->jumlah_layanan - $d->jumlah_retur;
            if ($d->kode_barang != '') {
                $pdf->SetXY(0, $i);
                $pdf->Cell(0.1, 10, '' . $i, 0, 1);
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetXY(0, 0.4);
                $pdf->Cell(0.3, 0.10, $dtpx[0]->no_rm, 0, 0);
                $pdf->SetXY(0.8, 0.4);
                $pdf->Cell(0.3, 0.10, $dtpx[0]->tgl_lahir . '/ usia ' . $dtpx[0]->umur, 0, 0);
                $pdf->SetXY(0, 0.6);
                $pdf->Cell(0.3, 0.10, $dtpx[0]->nama, 0, 0);
                $pdf->SetFont('Arial', '', 5);
                $pdf->SetXY(0, 0.8);
                $pdf->MultiCell(1.9, 0.1, $dtpx[0]->alamat);
                $y = $pdf->GetY();
                // $pdf->Cell(0.3, 0.10, $dtpx[0]->alamat, 0, 0);
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->SetXY(0, $y + 0.1);
                $pdf->MultiCell(1.8, 0.10, $d->nama_barang . $d->nama_racik);
                $y = $pdf->GetY() + 0.007;
                $pdf->SetXY(0, $y);
                $pdf->MultiCell(1.9, 0.10, $d->aturan_pakai);
                // $pdf->Cell(0.3, 0.10, $d->nama_barang, 0, 0);
                // //A set
                $code = 'CODE 128';
                $pdf->Code128(0.1, 1.6, $code, 1.8, 0.4);
                // // $pdf->Cell(0.3, 0.10, $barcode, 0, 0);
                $y = $pdf->GetY();
                $pdf->SetFont('Arial', 'b', 5);
                $pdf->SetXY(0, $y);
                $pdf->Cell(0.3, 0.10, $get_header[0]->tgl_entry, 0, 0);
                $pdf->SetXY(1.2, $y);
                $pdf->Cell(0.3, 0.10, 'EXP' . $d->ed_obat, 0, 0);
                $i = 10;
            }
        }
        $pdf->Output();
        exit;
        // return;
    }
    // public function cetaknotafarmasi_2($id)
    // {
    //     $DH = DB::select('select * from ts_layanan_header where kode_kunjungan = ?', [$id]);
    //     $DK = DB::select('select * from ts_kunjungan where kode_kunjungan = ?', [$id]);
    //     $KODE_HEADER = $DH[0]->kode_layanan_header;
    //     $ID_HEADER = $DK[0]->counter;
    //     $PDO = DB::connection()->getPdo();
    //     $QUERY = $PDO->prepare("CALL SP_CETAK_ETIKET_FARMASI('$id')");
    //     $QUERY->execute();
    //     $data = $QUERY->fetchAll();
    //     $filename = 'C:\cetakanerm\etiket.jrxml';
    //     $config = ['driver' => 'array', 'data' => $data];
    //     $report = new PHPJasperXML();
    //     $report->load_xml_file($filename)
    //         ->setDataSource($config)
    //         ->export('Pdf');
    // }
    public function cetaknotafarmasi_2($id)
    {
        $get_header = DB::connection('mysql2')->select('select *,fc_NAMA_USER(pic) as nama_user from ts_layanan_header where kode_kunjungan = ?', [$id]);

        $dtpx = DB::select('SELECT counter,no_rm,fc_nama_px(no_rm) AS nama, fc_umur(no_rm) AS umur,DATE(fc_tgl_lahir(no_rm)) AS tgl_lahir,fc_alamat(no_rm) AS alamat,fc_NAMA_PENJAMIN2(kode_penjamin) as nama_penjamin,fc_nama_unit1(kode_unit) as unit,fc_nama_paramedis(kode_paramedis) as dokter,kode_penjamin FROM ts_kunjungan WHERE kode_kunjungan = ?', [$get_header[0]->kode_kunjungan]);

        $get_detail = DB::connection('mysql2')->select('SELECT a.kode_tarif_detail,a.kode_barang,b.`nama_barang`,a.jumlah_layanan,a.jumlah_retur,a.tagihan_pribadi,a.tagihan_penjamin,d.nama_racik,a.keterangan FROM ts_layanan_detail a
        LEFT OUTER JOIN mt_barang b ON a.`kode_barang` = b.`kode_barang`
        LEFT OUTER JOIN ts_layanan_header c on a.row_id_header = c.id
        LEFT OUTER JOIN mt_racikan d on a.kode_barang = d.kode_racik
        WHERE c.kode_kunjungan = ?', [$id]);
        // dd($get_detail);
        if ($dtpx[0]->kode_penjamin == 'P01') {
            $jenis_Resep = 'Resep Tunai';
        } else {
            $jenis_Resep = 'Resep Kredit';
        }
        $pdf = new Fpdf('P', 'cm', array('11', '14'));
        $pdf->AddPage();
        $pdf->SetTitle('Cetak nota farmasi');
        $pdf->SetMargins('15', '20', '14');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Image('public/img/logo_rs.png', 0.5, 0.2, 1.5, 1.1);
        $pdf->SetXY(2, 0.5);
        $pdf->Cell(10, 0.5, 'RINCIAN BIAYA FARMASI', 0, 1);
        $pdf->SetXY(2, 0.8);
        $pdf->Cell(10, 0.5, 'RSUD WALED KAB.CIREBON', 0, 1);
        $pdf->SetXY(8, 1);
        $pdf->Cell(10, 0.5, $jenis_Resep, 0, 1);
        $pdf->SetLineWidth(0.05);
        $pdf->Line(0, 1.6, 78, 1.6);
        $pdf->SetXY(0.5, 1.8);
        $pdf->Cell(10, 0.5, 'Kode Layanan', 0, 1);
        $pdf->SetXY(3, 1.8);
        $pdf->Cell(10, 0.5, ': ' . $get_header[0]->kode_layanan_header, 0, 1);
        $pdf->SetXY(6.5, 1.8);
        $pdf->Cell(10, 0.5, 'RM / Counter', 0, 1);
        $pdf->SetXY(8.3, 1.8);
        $pdf->Cell(10, 0.5, ': ' . $dtpx[0]->no_rm . ' / ' . $dtpx[0]->counter, 0, 1);
        $pdf->SetXY(0.5, 2.2);
        $pdf->Cell(10, 0.5, 'Nama Pasien', 0, 1);
        $pdf->SetXY(3, 2.2);
        $pdf->Cell(10, 0.5, ': ' . $dtpx[0]->nama, 0, 1);
        $pdf->SetXY(0.5, 2.6);
        $pdf->Cell(10, 0.5, 'Tanggal Lahir', 0, 1);
        $pdf->SetXY(3, 2.6);
        $pdf->Cell(10, 0.5, ': ' . $dtpx[0]->tgl_lahir, 0, 1);
        $pdf->SetXY(0.5, 3);
        $pdf->Cell(10, 0.5, 'Alamat', 0, 1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(3, 3);
        $pdf->MultiCell(6, 0.4, ': ' . $dtpx[0]->alamat);
        $y = $pdf->GetY() + 0.2;
        $x = $pdf->GetX();
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(0.5, $y);
        $pdf->Cell(10, 0.5, 'Penjamin', 0, 1);
        $pdf->SetXY(3, $y);
        $pdf->Cell(10, 0.5, ': ' . $dtpx[0]->nama_penjamin, 0, 1);
        $y2 = $y + 0.5;
        $pdf->SetXY(0.5, $y2);
        $pdf->Cell(10, 0.5, 'Unit Asal', 0, 1);
        $pdf->SetXY(3, $y2);
        $pdf->Cell(10, 0.5, ': ' . $dtpx[0]->unit, 0, 1);
        $y3 = $y2 + 0.5;
        $pdf->SetXY(0.5, $y3);
        $pdf->Cell(10, 0.5, 'Dokter', 0, 1);
        $pdf->SetXY(3, $y3);
        $pdf->Cell(10, 0.5, ': ' . $dtpx[0]->dokter, 0, 1);
        $y4 = $y3 + 0.5;
        $pdf->Line(0, $y4, 78, $y4);
        $y5 = $y4 + 0.01;
        $pdf->SetXY(0.5, $y5);
        $pdf->Cell(10, 0.5, 'Nama Obat', 0, 1);
        $pdf->SetXY(7, $y5);
        $pdf->Cell(10, 0.5, 'QTY', 0, 1);
        $pdf->SetXY(9, $y5);
        $pdf->Cell(10, 0.5, 'Jumlah', 0, 1);
        $y6 = $y5 + 0.4;
        $pdf->Line(0, $y6, 78, $y6);
        $y7 = $y6 + 0.2;
        $total_item = 0;
        $jasa_resep = 0;
        $jasa_resep_total = 0;
        $subtotal = 0;
        foreach ($get_detail as $d) {
            $pdf->SetFont('Arial', 'B', 7);
            if ($d->kode_barang != '') {
                if ($dtpx[0]->kode_penjamin == 'P01') {
                    $jumlah = $d->tagihan_pribadi;
                } else {
                    $jumlah = $d->tagihan_penjamin;
                }
                $qty = $d->jumlah_layanan - $d->jumlah_retur;
                // if ($qty > 0) {
                    $pdf->SetXY(0.5, $y7);
                    $pdf->MultiCell(5, 0.4, $d->nama_barang.$d->nama_racik.'('. $d->keterangan .')');
                    $pdf->SetXY(7.3, $y7);
                    $pdf->Cell(10, 0.5, $qty, 0, 1);
                    $pdf->SetXY(8.5, $y7);
                    $pdf->Cell(10, 0.5, number_format($jumlah, 2), 0, 1);
                    $y7 = $y7 + 0.8;
                    $total_item = $total_item + 1;
                    $subtotal = $subtotal + $jumlah;
                // }
            }
            if ($d->kode_tarif_detail == 'TX23523') {
                if ($dtpx[0]->kode_penjamin == 'P01') {
                    $jumlah_resep = $d->tagihan_pribadi;
                } else {
                    $jumlah_resep = $d->tagihan_penjamin;
                }
                $jasa_resep = $jasa_resep + 1;
                $jasa_resep_total = $jasa_resep_total + $jumlah_resep;
            }
        }
        $y8 = $pdf->GetY() + 0.5;
        $pdf->Line(0, $y8, 78, $y8);
        $y9 = $y8 + 0.3;
        $pdf->SetXY(0.5, $y9);
        $pdf->Cell(10, 0.5, 'Total item : ' . $total_item, 0, 1);
        $pdf->SetXY(6, $y9);
        $pdf->Cell(10, 0.5, 'Subtotal', 0, 1);
        $pdf->SetXY(8.5, $y9);
        $pdf->Cell(10, 0.5, ': ' . number_format($subtotal, 2), 0, 1);
        $pdf->SetXY(6, $y9 + 0.4);
        $pdf->Cell(10, 0.5, 'Jasa Resep' . ' ( ' . $jasa_resep . ' )', 0, 1);
        $pdf->SetXY(8.5, $y9 + 0.4);
        $pdf->Cell(10, 0.5, ': ' . number_format($jasa_resep_total, 2), 0, 1);
        $y10 = $pdf->GetY() + 0.1;
        $pdf->Line(6, $y10, 78, $y10);
        $y11 = $pdf->GetY() + 0.1;
        $pdf->SetXY(6, $y11);
        $pdf->Cell(10, 0.5, 'Total Bayar', 0, 1);
        $pdf->SetXY(8.5, $y11);
        $total = $subtotal + $jasa_resep_total;
        $pdf->Cell(10, 0.5, ': ' . number_format($total, 2), 0, 1);
        $y12 = $pdf->GetY() + 0.1;
        $pdf->Line(6, $y12, 78, $y12);
        $y13 = $pdf->GetY() + 0.2;
        $pdf->Line(6, $y13, 78, $y13);
        $y14 = $pdf->GetY() + 0.2;
        $pdf->SetXY(0.5, $y14);
        $pdf->Cell(10, 0.5, 'Tgl Input : ' . $get_header[0]->tgl_entry, 0, 1);
        $pdf->SetXY(0.5, $y14 + 0.5);
        $pdf->Cell(10, 0.5, 'Input by : ' . $get_header[0]->nama_user, 0, 1);
        $pdf->SetXY(0.5, $y14 + 1);
        $pdf->Cell(10, 0.5, $this->get_now(), 0, 1);
        $pdf->Output();
        exit;
        // return;
    }
}
