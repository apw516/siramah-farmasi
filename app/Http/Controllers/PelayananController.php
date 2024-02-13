<?php

namespace App\Http\Controllers;

use App\Models\mt_racikan_detail_dummy;
use App\Models\mt_racikan_header_dummy;
use App\Models\ti_kartu_stok;
use App\Models\ts_layanan_detail_dummy;
use App\Models\ts_layanan_header_dummy;
use App\Models\xxxmt_racikan_header;
use App\Models\ts_retur_header_dummy;
use App\Models\ts_retur_detail_dummy;
use App\Models\xxxmt_racikan_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use Codedge\Fpdf\Fpdf\PDF;
use simitsdk\phpjasperxml\PHPJasperXML;

class PelayananController extends Controller
{
    public function IndexLayananResep()
    {
        $menu = 'Layananresep';
        $now = $this->get_date();
        return view('Layanan.layananresep', compact([
            'menu',
            'now'
        ]));
    }
    public function Indexkartustok()
    {
        $menu = 'kartustok';
        $now = $this->get_date();
        return view('gudang.index_kartu_stok', compact([
            'menu',
            'now'
        ]));
    }
    public function FormPencarianPasien()
    {
        $now = $this->get_date();
        $unit = DB::select('select * from mt_unit where group_unit = ? OR group_unit = ?', ['J', 'I']);
        return view('Layanan.form_pencarian_pasien', compact([
            'unit',
            'now'
        ]));
    }
    public function AmbilOrderanPoli()
    {
        $cari_order = DB::select('SELECT date(tgl_entry) as tgl_entry
        ,b.no_rm
        ,fc_nama_px(b.no_rm) AS nama_pasien
        ,fc_alamat(b.no_rm) AS alamat
        ,a.id
        ,a.kode_layanan_header
        ,a.status_layanan
        ,a.kode_kunjungan
        ,fc_NAMA_PARAMEDIS1(a.dok_kirim) AS nama_dokter
        ,fc_nama_unit1(a.kode_unit) AS nama_unit
        ,a.unit_pengirim as kode_unit_pengirim
        ,fc_nama_unit1(a.unit_pengirim) as nama_unit_pengirim
        ,status_order
        FROM ts_layanan_header_order a
        LEFT OUTER JOIN ts_kunjungan b ON a.`kode_kunjungan` = b.`kode_kunjungan`
        WHERE a.kode_unit = ? and date(tgl_entry) = curdate()', ([$this->get_unit_login()]));
        return view('Layanan.tabel_order_poliklinik', compact([
            'cari_order'
        ]));
    }
    public function AmbilDataPencarianPasien(Request $request)
    {
        $tgl = $request->tgl;
        $unit = $request->unit;
        $now = $this->get_date();
        if ($tgl == $now) {
            $kunjungan = DB::select('SELECT date(tgl_masuk) as tgl_masuk,no_rm,kode_kunjungan,fc_nama_px(no_rm) as nama_pasien,fc_alamat(no_rm) as alamat,fc_nama_unit1(kode_unit) AS nama_unit,kode_unit, fc_NAMA_PENJAMIN2(kode_penjamin) AS nama_penjamin FROM ts_kunjungan WHERE kode_unit = ? AND status_kunjungan = ? AND date(tgl_masuk) = ?', [$unit, '1', $tgl]);
        } else {
            $kunjungan = DB::select('SELECT date(tgl_masuk) as tgl_masuk,no_rm,kode_kunjungan,fc_nama_px(no_rm) as nama_pasien,fc_alamat(no_rm) as alamat,fc_nama_unit1(kode_unit) AS nama_unit,kode_unit, fc_NAMA_PENJAMIN2(kode_penjamin) AS nama_penjamin FROM ts_kunjungan WHERE kode_unit = ? AND status_kunjungan <> ? AND date(tgl_masuk) = ?', [$unit, '8', $tgl]);
        }
        return view('Layanan.tabel_pencarian_pasien', compact([
            'kunjungan'
        ]));
    }
    public function AmbilFormResep(Request $request)
    {
        $rm = $request->rm;
        $kodekunjungan = $request->kodekunjungan;
        $datapasien = DB::select('select no_rm,fc_umur(no_rm) as umur,nama_px,fc_alamat(no_rm) as alamatnya,date(tgl_lahir) as tgl_lahir from mt_pasien where no_rm = ?', [$rm]);
        $datakunjungan = DB::select('select *,fc_NAMA_PENJAMIN2(kode_penjamin) as nama_penjamin from ts_kunjungan where kode_kunjungan = ?', [$kodekunjungan]);
        $assdok = DB::select('select *  from assesmen_dokters where id_kunjungan = ?', [$kodekunjungan]);
        if (count($assdok) > 0) {
            $diagnosa = $assdok[0]->diagnosakerja;
        } else {
            $diagnosa = '-';
        }
        return view('Layanan.form_input_resep', compact([
            'datapasien',
            'datakunjungan',
            'diagnosa'
        ]));
    }
    public function ambil_riwayat_resep(Request $request)
    {
        $tgl_awal = $request->tglawal;
        $tgl_akhir = $request->tglakhir;
        $riwayat = DB::connection('mysql2')->select('SELECT tgl_entry,b.no_rm,fc_nama_px(b.no_rm) AS nama_pasien,fc_alamat(no_rm) AS alamat,a.id AS id_header ,kode_layanan_header,b.kode_kunjungan,unit_pengirim,fc_NAMA_PARAMEDIS1(dok_kirim) AS dokter_pengirim
        FROM ts_layanan_header a
        LEFT OUTER JOIN ts_kunjungan b ON a.`kode_kunjungan` = b.`kode_kunjungan`
        WHERE DATE(tgl_entry) BETWEEN ? AND ? AND a.kode_unit = ?', [$tgl_awal, $tgl_akhir, auth()->user()->unit]);
        return view('Layanan.riwayat_resep_farmasi', compact([
            'riwayat'
        ]));
    }
    public function detail_resep_obat(Request $request)
    {
        $id = $request->id;
        $rm = $request->rm;
        $nama = $request->nama;
        $alamat = $request->alamat;
        $dokter = $request->dokter;
        $unit = $request->unit;
        $detailresep = DB::connection('mysql2')->SELECT('SELECT fc_nama_barang(a.`kode_barang`) AS nama_barang,c.`kode_racik`,c.`nama_racik`,b.`nama_anestesi`,jumlah_layanan,a.aturan_pakai,kategori_resep,satuan_barang
        FROM ts_layanan_detail a
        LEFT OUTER JOIN mt_anestesi_tipe b ON a.`tipe_anestesi` = b.`id`
        LEFT OUTER JOIN mt_racikan c ON a.`kode_barang` = c.`kode_racik`
        WHERE row_id_header = ? AND a.`kode_barang` != ?', [$id, '']);
        return view('Layanan.detail_resep_v', compact([
            'id',
            'rm',
            'nama',
            'alamat',
            'dokter',
            'unit',
            'detailresep'
        ]));
    }
    public function AmbilDataOrderPoli(Request $request)
    {
        $orderan = db::select('SELECT * ,fc_nama_unit1(unit_pengirim) AS nama_unit,fc_nama_paramedis1(dok_kirim) AS nama_dokter FROM ts_layanan_detail_order a
        LEFT OUTER JOIN ts_layanan_header_order b ON a.`row_id_header` = b.`id`
        WHERE b.`kode_kunjungan` = ?', ([$request->kode]));
        return view('Layanan.tabel_orderan_poli', compact([
            'orderan'
        ]));
    }
    public function cari_obat_reguler(Request $request)
    {
        $nama = $request->namaobat;
        $pencarian_obat = DB::select('CALL sp_cari_obat_semua(?,?)', [$nama, $this->get_unit_login()]);
        return view('Layanan.tabel_obat_reguler', compact([
            'pencarian_obat'
        ]));
    }
    public function cari_obat_racik(Request $request)
    {
        $nama = $request->namaobat;
        $pencarian_obat = DB::select('CALL sp_cari_obat_semua(?,?)', [$nama, $this->get_unit_login()]);
        // dd($pencarian_obat);
        return view('Layanan.tabel_obat_racik', compact([
            'pencarian_obat'
        ]));
    }
    public function jumlah_grand_total_obat_reguler(Request $request)
    {
        $data_obat = json_decode($_POST['data1'], true);
        $arrayindex_reguler = [];
        $arrayindex_kronis = [];
        $arrayindex_kemo = [];
        $arrayindex_hibah = [];
        //normalisasi array dan pemisahan jenis jenis resep
        $total_layanan = 0;
        foreach ($data_obat as $nama) {
            $index = $nama['name'];
            $value = $nama['value'];
            $dataSet[$index] = $value;
            if ($index == 'sub_total_order') {
                $arrayindex_far[] = $dataSet;
            }
        }
        $total_layanan = 0;
        $total_item = 0;
        $total_item_2 = 0;
        $resep_reguler = 0;
        $resep_kronis = 0;
        $resep_kemo = 0;
        $resep_hibah = 0;
        foreach ($arrayindex_far as  $ab) {
            $total_layanan = $total_layanan + $ab['sub_total_order_2'];
            if ($ab['status_order_3'] == 'RACIKAN') {
                $total_item++;
                $total_item_2;
            } else {
                $total_item++;
                $total_item_2++;
            }
            $jasa_1200 = 1200;
            $jasa_500 = 500;
            $cek_barang = DB::select('SELECT * from mt_barang where kode_barang = ?', [$ab['kode_barang_order']]);
            if (count($cek_barang) > 0) {
                if ($cek_barang[0]->kode_tipe == 3) {
                    $resep_hibah = $resep_hibah + 1;
                } else {
                    if ($ab['status_order_2'] == '80') {
                        $resep_reguler = $resep_reguler + 1;
                    } else if ($ab['status_order_2'] == '81') {
                        $resep_kronis = $resep_kronis + 1;
                    } else if ($ab['status_order_2'] == '82') {
                        $resep_kemo = $resep_kemo + 1;
                    } else if ($ab['status_order_2'] == '83') {
                        $resep_reguler = $resep_reguler + 1;
                    }
                }
            } else {
                $resep_reguler = $resep_reguler + 1;
            }
            // if ($ab['status_order_2'] == '80') {
            //     $resep_reguler = $resep_reguler + 1;
            // } else if ($ab['status_order_2'] == '81') {
            //     $resep_kronis = $resep_kronis + 1;
            // } else if ($ab['status_order_2'] == '82') {
            //     $resep_kemo = $resep_kemo + 1;
            // } else if ($ab['status_order_2'] == '83') {
            //     $resep_hibah = $resep_hibah + 1;
            // }
        }
        $total_item_3 = $total_item_2 - $resep_hibah;
        $jasa_baca = $total_item_3 * $jasa_1200;
        $jasa_embalase = $total_item_3 * $jasa_500;
        if ($resep_reguler > 0) {
            $jlh_reguler = $resep_reguler;
            $reguler = 1;
        } else {
            $jlh_reguler = 0;
            $reguler = 0;
        }
        if ($resep_kronis > 0) {
            $jlh_kronis = $resep_kronis;
            $kronis = 1;
        } else {
            $kronis = 0;
            $jlh_kronis = 0;
        }
        if ($resep_kemo > 0) {
            $kemo = 1;
            $jlh_kemo = $resep_kemo;
        } else {
            $kemo = 0;
            $jlh_kemo = 0;
        }
        if ($resep_hibah > 0) {
            $hibah = 1;
            $jlh_hibah = $resep_hibah;
        } else {
            $hibah = 0;
            $jlh_hibah = 0;
        }
        $total_resep = $reguler + $kronis + $kemo + $hibah;
        $jasa_resep = 1000 * $total_resep;
        $grandtotal = $total_layanan + $jasa_resep + $jasa_baca + $jasa_embalase;
        return view('Layanan.form_gt_obat_reguler', compact([
            'total_layanan',
            'total_item',
            'jasa_baca',
            'jasa_embalase',
            'total_resep',
            'jasa_resep',
            'reguler',
            'kronis',
            'kemo',
            'hibah',
            'jlh_reguler',
            'jlh_kronis',
            'jlh_kemo',
            'jlh_hibah',
            'grandtotal'
        ]));
    }
    public function cek_obat_hibah(Request $request)
    {
        $kodebarang = $request->kode;
        $status = $request->status;
        $cek_barang = DB::select('select * from mt_barang where kode_barang = ?', [$kodebarang]);
        if ($cek_barang[0]->kode_tipe == 3) {
            $status = '83';
            $status_name = 'HIBAH';
        } else {
            $status = $status;
            if ($status == '80') {
                $status_name = 'REGULER';
            } else if ($status == '81') {
                $status_name = 'KRONIS';
            } else if ($status == '82') {
                $status_name = 'KEMOTHERAPI';
            } else if ($status == '83') {
                $status_name = 'REGULER';
            }
        }
        $data = [
            'kode' => 200,
            'status' => $status,
            'nama' => $status_name,
        ];
        echo json_encode($data);
        die;
    }
    public function jumlah_grand_total_racikan(Request $request)
    {
        $komponenracik = json_decode($_POST['komponenracik'], true);
        $new_total_layanan = 0;
        foreach ($komponenracik as $nama) {
            $index = $nama['name'];
            $value = $nama['value'];
            $dataSet[$index] = $value;
            if ($index == 'sub_total_order') {
                $arrayindex_far[] = $dataSet;
            }
        }
        foreach ($arrayindex_far as $ada) {
            $new_total_layanan =  $ada['sub_total_order'] + $new_total_layanan;
        }
        $f = $request->kemasan;
        $qtytotal_racik = $request->qtytotal_racik;
        $total_komponen = $request->gt_total_komponen;
        $new_total_komponen = $total_komponen + 1;
        $dosis_racik = $request->dosis_racik;
        $gt_total_layanan_racikan = $request->gt_total_layanan_racikan;
        $harga_obat = $request->harga_obat * $qtytotal_racik;
        // if ($f == 3) {
        //     $new_total_layanan = $harga_obat + $gt_total_layanan_racikan;
        // } else {
        //     $harga_satuan_kecil = $harga_obat / $request->dosis_obat;
        //     $new_total_layanan = $dosis_racik * $harga_satuan_kecil + $gt_total_layanan_racikan;
        // }
        $jasaresep = 1200 * $new_total_komponen;
        $jasaembalase = 500 * $new_total_komponen;
        $grandtotal = $new_total_layanan + $jasaresep + $jasaembalase;
        return view('Layanan.form_gt_obat_racikan', compact([
            'new_total_komponen',
            'new_total_layanan',
            'jasaresep',
            'jasaembalase',
            'grandtotal',
            'dosis_racik'
        ]));
    }
    public function minus_grand_total_racikan(Request $request)
    {
        $new_total_komponen = $request->totalkomponen - 1;
        $jasaresep = 1200 * $new_total_komponen;
        $jasaembalase = 500 * $new_total_komponen;
        $new_total_layanan = $request->gt_total_layanan_racikan - $request->harga;
        $grandtotal = 0;
        return view('Layanan.form_gt_obat_racikan', compact([
            'new_total_komponen',
            'jasaresep',
            'jasaembalase',
            'jasaresep',
            'new_total_layanan',
            'grandtotal'
        ]));
    }
    public function minus_grand_total_reguler(Request $request)
    {
        $data_obat = json_decode($_POST['data1'], true);
        $arrayindex_reguler = [];
        $arrayindex_kronis = [];
        $arrayindex_kemo = [];
        $arrayindex_hibah = [];
        //normalisasi array dan pemisahan jenis jenis resep
        $total_layanan = 0;
        if (count($data_obat)  > 0) {
            foreach ($data_obat as $nama) {
                $index = $nama['name'];
                $value = $nama['value'];
                $dataSet[$index] = $value;
                if ($index == 'sub_total_order') {
                    $arrayindex_far[] = $dataSet;
                }
            }
            $total_layanan = 0;
            $total_item = 0;
            $total_item_2 = 0;
            $resep_reguler = 0;
            $resep_kronis = 0;
            $resep_kemo = 0;
            $resep_hibah = 0;

            foreach ($arrayindex_far as  $ab) {
                $total_layanan = $total_layanan + $ab['sub_total_order_2'];
                if ($ab['status_order_3'] == 'RACIKAN') {
                    $total_item++;
                    $total_item_2;
                } else {
                    $total_item++;
                    $total_item_2++;
                }
                $jasa_1200 = 1200;
                $jasa_500 = 500;
                $cek_barang = DB::select('SELECT * from mt_barang where kode_barang = ?', [$ab['kode_barang_order']]);
                if ($cek_barang[0]->kode_tipe == 3) {
                    $resep_hibah = $resep_hibah + 1;
                } else {
                    if ($ab['status_order_2'] == '80') {
                        $resep_reguler = $resep_reguler + 1;
                    } else if ($ab['status_order_2'] == '81') {
                        $resep_kronis = $resep_kronis + 1;
                    } else if ($ab['status_order_2'] == '82') {
                        $resep_kemo = $resep_kemo + 1;
                    } else if ($ab['status_order_2'] == '83') {
                        $resep_reguler = $resep_reguler + 1;
                    }
                }


                // if ($ab['status_order_2'] == '80') {
                //     $resep_reguler = $resep_reguler + 1;
                // } else if ($ab['status_order_2'] == '81') {
                //     $resep_kronis = $resep_kronis + 1;
                // } else if ($ab['status_order_2'] == '82') {
                //     $resep_kemo = $resep_kemo + 1;
                // } else if ($ab['status_order_2'] == '83') {
                //     $resep_hibah = $resep_hibah + 1;
                // }
            }
            $total_item_3 = $total_item_2 - $resep_hibah;
            $jasa_baca = $total_item_3 * $jasa_1200;
            $jasa_embalase = $total_item_3 * $jasa_500;
            if ($resep_reguler > 0) {
                $jlh_reguler = $resep_reguler;
                $reguler = 1;
            } else {
                $jlh_reguler = 0;
                $reguler = 0;
            }
            if ($resep_kronis > 0) {
                $jlh_kronis = $resep_kronis;
                $kronis = 1;
            } else {
                $kronis = 0;
                $jlh_kronis = 0;
            }
            if ($resep_kemo > 0) {
                $kemo = 1;
                $jlh_kemo = $resep_kemo;
            } else {
                $kemo = 0;
                $jlh_kemo = 0;
            }
            if ($resep_hibah > 0) {
                $hibah = 1;
                $jlh_hibah = $resep_hibah;
            } else {
                $hibah = 0;
                $jlh_hibah = 0;
            }
        } else {
            $total_layanan = 0;
            $total_item = 0;
            $jasa_baca = 0;
            $jasa_embalase = 0;
            $total_resep = 0;
            $jasa_resep = 0;
            $reguler = 0;
            $kronis = 0;
            $kemo = 0;
            $hibah = 0;
            $jlh_reguler = 0;
            $jlh_kronis = 0;
            $jlh_kemo = 0;
            $jlh_hibah = 0;
            $grandtotal = 0;
        }
        $total_resep = $reguler + $kronis + $kemo + $hibah;
        $jasa_resep = 1000 * $total_resep;
        $grandtotal = $total_layanan + $jasa_resep + $jasa_baca + $jasa_embalase;
        return view('Layanan.form_gt_obat_reguler', compact([
            'total_layanan',
            'total_item',
            'jasa_baca',
            'jasa_embalase',
            'total_resep',
            'jasa_resep',
            'reguler',
            'kronis',
            'kemo',
            'hibah',
            'jlh_reguler',
            'jlh_kronis',
            'jlh_kemo',
            'jlh_hibah',
            'grandtotal'
        ]));
        // $arrayindex_reguler = [];
        // $arrayindex_kronis = [];
        // $arrayindex_kemo = [];
        // $arrayindex_hibah = [];
        // //normalisasi array dan pemisahan jenis jenis resep
        // $total_layanan = 0;
        // foreach ($data_obat as $nama) {
        //     $index = $nama['name'];
        //     $value = $nama['value'];
        //     $dataSet[$index] = $value;
        //     if ($index == 'sub_total_order') {
        //         $arrayindex_far[] = $dataSet;
        //     }
        // }
        // dd($arrayindex_far);
        // $jenisracik = $request->jenisracik;
        // $jenis_resep = $request->status;
        // $total_layanan = $request->gt_lama_2 - $request->total_layanan_1;
        // $total_item = $request->gt_total_item_2 - 1;
        // $resep_kronis = $request->resep_kronis_2;
        // $resep_hibah = $request->resep_hibah_2;
        // $resep_reguler = $request->resep_reguler_2;
        // $resep_kemo = $request->resep_kemo_2;
        // $jlh_reguler = $resep_reguler;
        // $jlh_kronis = $resep_kronis;
        // $jlh_hibah = $resep_hibah;
        // $jlh_kemo = $resep_kemo;
        // if ($jenis_resep == 80) {
        //     $resep_reguler = $resep_reguler - 1;
        //     $jlh_reguler = $resep_reguler;
        // }
        // if ($jenis_resep == 81) {
        //     $resep_kronis = $resep_kronis - 1;
        //     $jlh_kronis = $resep_kronis;
        // }
        // if ($jenis_resep == 82) {
        //     $resep_kemo = $resep_kemo - 1;
        //     $jlh_kemo = $resep_kemo;
        // }
        // if ($jenis_resep == 83) {
        //     $resep_hibah = $resep_hibah - 1;
        //     $jlh_hibah = $resep_hibah;
        // }

        // if ($resep_reguler > 0) {
        //     $reguler = 1;
        // } else {
        //     $reguler = 0;
        // }
        // if ($resep_kronis > 0) {
        //     $kronis = 1;
        // } else {
        //     $kronis = 0;
        // }
        // if ($resep_kemo > 0) {
        //     $kemo = 1;
        // } else {
        //     $kemo = 0;
        // }
        // if ($resep_hibah > 0) {
        //     $hibah = 1;
        // } else {
        //     $hibah = 0;
        // }
        // $total_resep = $reguler + $kronis + $kemo + $hibah;
        // $jasa_baca = $total_item * 1200;
        // $jasa_embalase = $total_item * 500;
        // $jasa_resep = 1000 * $total_resep;
        // return view('Layanan.form_gt_obat_reguler', compact([
        //     'total_layanan',
        //     'total_item',
        //     'jasa_baca',
        //     'jasa_embalase',
        //     'total_resep',
        //     'jasa_resep',
        //     'reguler',
        //     'kronis',
        //     'kemo',
        //     'hibah',
        //     'jlh_reguler',
        //     'jlh_kronis',
        //     'jlh_kemo',
        //     'jlh_hibah'
        // ]));
    }
    public function simpanorderan_far_reguler(Request $request)
    {
        $jsf = DB::select('select * from mt_jasa_farmasi');
        $data_obat = json_decode($_POST['data1'], true);
        $arrayindex_reguler = [];
        $arrayindex_kronis = [];
        $arrayindex_kemo = [];
        $arrayindex_hibah = [];
        //normalisasi array dan pemisahan jenis jenis resep
        foreach ($data_obat as $nama) {
            $index = $nama['name'];
            $value = $nama['value'];
            $dataSet[$index] = $value;
            if ($index == 'sub_total_order') {
                if ($dataSet['status_order_2'] == 80) {
                    $arrayindex_reguler[] = $dataSet;
                } else if ($dataSet['status_order_2'] == 81) {
                    $arrayindex_kronis[] = $dataSet;
                } else if ($dataSet['status_order_2'] == 82) {
                    $arrayindex_kemo[] = $dataSet;
                } else if ($dataSet['status_order_2'] == 83) {
                    $arrayindex_hibah[] = $dataSet;
                }
                $arrayindex_far[] = $dataSet;
            }
        }
        //end normalisasi
        //cek stok obat 1
        foreach ($arrayindex_far as $a) {
            if ($a['status_order_3'] == 'NON-RACIKAN') {
                $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$a['kode_barang_order'], $this->get_unit_login()]));
                $stok_current = $cek_stok[0]->stok_current - $a['qty_order'];
                if ($stok_current < 0) {
                    $data = [
                        'kode' => 500,
                        'message' => $a['nama_barang_order'] . ' ' . 'Stok Tidak Mencukupi !',
                    ];
                    echo json_encode($data);
                    die;
                }
            } else {
                $cek_order = db::connection('mysql2')->select('SELECT a.id,b.`kode_barang`,b.`qty_barang`,a.tipe_racik FROM xxxmt_racikan a
                LEFT OUTER JOIN xxxmt_racikan_detail b ON a.`kode_racik` = b.`kode_racik` WHERE a.id = ? AND b.`satuan_barang` <> ?', [$a['id_racik'], '-']);
                foreach ($cek_order as $co) {
                    $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$co->kode_barang, $this->get_unit_login()]));
                    if ($co->tipe_racik == 'NS') {
                        $stok_current = $cek_stok[0]->stok_current - $co->qty_barang;
                    } else {
                        $stok_current = $cek_stok[0]->stok_current - 1;
                    }
                    if ($stok_current < 0) {
                        $data = [
                            'kode' => 500,
                            'message' => $a['nama_barang_order'] . ' ' . 'Stok Tidak Mencukupi !',
                        ];
                        echo json_encode($data);
                        die;
                    }
                }
            }
        }
        //end of cek obat
        $cek_reg = count($arrayindex_reguler);
        $cek_kron = count($arrayindex_kronis);
        $cek_kemo = count($arrayindex_kemo);
        $cek_hib = count($arrayindex_hibah);
        $data_kunjungan = DB::select('select *,fc_nama_px(no_rm) AS nama_pasien,fc_alamat(no_rm) AS alamat_pasien from ts_kunjungan where kode_kunjungan = ?', [$request->kodekunjungan]);
        $kodeunit = $this->get_unit_login();
        $unit = DB::select('select * from mt_unit where kode_unit = ?', [$kodeunit]);
        if ($data_kunjungan[0]->kode_penjamin != 'P01') {
            $kategori_resep = 'Resep Kredit';
            $kode_tipe_transaki = 2;
            $status_layanan = 2;
        } else {
            $kategori_resep = 'Resep Tunai';
            $kode_tipe_transaki = 1;
            $status_layanan = 1;
        }

        //insert resep reguler
        if ($cek_reg > 0) {
            //cek stok obat 2
            foreach ($arrayindex_reguler as $a) {
                if ($a['status_order_3'] == 'NON-RACIKAN') {
                    $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$a['kode_barang_order'], $this->get_unit_login()]));
                    $stok_current = $cek_stok[0]->stok_current - $a['qty_order'];
                    if ($stok_current < 0) {
                        $data = [
                            'kode' => 500,
                            'message' => $a['nama_barang_order'] . ' ' . 'Stok Tidak Mencukupi !',
                        ];
                        echo json_encode($data);
                        die;
                    }
                } else {
                    $cek_order = db::connection('mysql2')->select('SELECT a.id,b.`kode_barang`,b.`qty_barang`,a.tipe_racik FROM xxxmt_racikan a
                    LEFT OUTER JOIN xxxmt_racikan_detail b ON a.`kode_racik` = b.`kode_racik` WHERE a.id = ? AND b.`satuan_barang` <> ?', [$a['id_racik'], '-']);
                    foreach ($cek_order as $co) {
                        $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$co->kode_barang, $this->get_unit_login()]));
                        if ($co->tipe_racik == 'NS') {
                            $stok_current = $cek_stok[0]->stok_current - $co->qty_barang;
                        } else {
                            $stok_current = $cek_stok[0]->stok_current - 1;
                        }
                        if ($stok_current < 0) {
                            $data = [
                                'kode' => 500,
                                'message' => $a['nama_barang_order'] . ' ' . 'Stok Tidak Mencukupi !',
                            ];
                            echo json_encode($data);
                            die;
                        }
                    }
                }
            }
            //end of cek stok obat
            $unitlog = $this->get_unit_login();
            $r = DB::connection('mysql2')->select("CALL GET_NOMOR_LAYANAN_HEADER($unitlog)");
            $kode_layanan_header = $r[0]->no_trx_layanan;
            if (strlen($kode_layanan_header) < 5) {
                $year = date('y');
                $kode_layanan_header = $unit[0]['prefix_unit'] . $year . date('m') . date('d') . '000001';
                DB::connection('mysql2')->select('insert into mt_nomor_trx (tgl,no_trx_layanan,unit) values (?,?,?)', [date('Y-m-d h:i:s'), $kode_layanan_header, $kodeunit]);
            }
            //insert layanan header resep reguler
            try {
                $ts_layanan_header = [
                    'kode_layanan_header' => $kode_layanan_header,
                    'tgl_entry' => $this->get_now(),
                    'kode_kunjungan' => $request->kodekunjungan,
                    'kode_unit' => $this->get_unit_login(),
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
            //end of insert layanan header resep reguler

            $now = $this->get_now();
            $totalheader = 0;
            //insert layanan detail obat reguler
            // dd($arrayindex_reguler);
            foreach ($arrayindex_reguler as $a) {
                if ($a['status_order_3'] == 'NON-RACIKAN') {
                    $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$a['kode_barang_order']]);
                    $total = $a['harga2_order'] * $a['qty_order'];
                    $diskon = $a['disc_order'];
                    $hitung = $diskon / 100 * $total;
                    $grandtotal = $total - $hitung + $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                } else {
                    $cek_order = db::connection('mysql2')->select('SELECT a.id,b.`kode_barang`,b.`qty_barang` FROM xxxmt_racikan a
                    LEFT OUTER JOIN xxxmt_racikan_detail b ON a.`kode_racik` = b.`kode_racik` WHERE a.id = ? AND b.`satuan_barang` <> ?', [$a['id_racik'], '-']);
                    $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$cek_order[0]->kode_barang, $this->get_unit_login()]));
                    $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$cek_order[0]->kode_barang]);
                    $total = $a['sub_total_order_2'];
                    $diskon = $a['disc_order'];
                    $hitung = $diskon / 100 * $total;
                    $grandtotal = $total - $hitung;
                }
                if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                    $tagihan_pribadi = 0;
                    $tagihan_penjamin = $grandtotal;
                } else {
                    $tagihan_pribadi = $grandtotal;
                    $tagihan_penjamin = 0;
                }
                $kode_detail_obat = $this->createLayanandetail();
                // dd($grandtotal);
                try {
                    if ($a['status_order_3'] == 'NON-RACIKAN') {
                        $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$a['kode_barang_order'], $this->get_unit_login()]));
                        $stok_current = $cek_stok[0]->stok_current - $a['qty_order'];
                        $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$a['kode_barang_order']]);
                        $ts_layanan_detail = [
                            'id_layanan_detail' => $kode_detail_obat,
                            'kode_layanan_header' => $kode_layanan_header,
                            'kode_tarif_detail' => '',
                            'total_tarif' => $a['harga2_order'],
                            'jumlah_layanan' => $a['qty_order'],
                            'total_layanan' => $total,
                            'diskon_layanan' => $a['disc_order'],
                            'grantotal_layanan' => $grandtotal,
                            'status_layanan_detail' => 'OPN',
                            'tgl_layanan_detail' => $now,
                            'kode_barang' => $a['kode_barang_order'],
                            'aturan_pakai' => $a['dosis_order'],
                            'kategori_resep' => $kategori_resep,
                            'satuan_barang' => $mt_barang[0]->satuan,
                            'tipe_anestesi' => $a['status_order_2'],
                            'tagihan_pribadi' => $tagihan_pribadi,
                            'tagihan_penjamin' => $tagihan_penjamin,
                            'tgl_layanan_detail_2' => $now,
                            'row_id_header' => $header->id,
                        ];
                        $data_ti_kartu_stok = [
                            'no_dokumen' => $kode_layanan_header,
                            'no_dokumen_detail' => $kode_detail_obat,
                            'tgl_stok' => $this->get_now(),
                            'kode_unit' => $this->get_unit_login(),
                            'kode_barang' => $a['kode_barang_order'],
                            'stok_last' => $cek_stok[0]->stok_current,
                            'stok_out' => $a['qty_order'],
                            'stok_current' => $stok_current,
                            'stok_global' => '0',
                            'harga_beli' => $mt_barang[0]->hna,
                            'act' => '1',
                            'act_ed' => '1',
                            'inputby' => auth()->user()->id,
                            'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien,
                        ];
                        $insert_ti_kartu_stok = ti_kartu_stok::create($data_ti_kartu_stok);
                        $ts_layanan_detail2 = [
                            'id_layanan_detail' => $this->createLayanandetail(),
                            'kode_layanan_header' => $kode_layanan_header,
                            'kode_tarif_detail' => 'TX23513',
                            'total_tarif' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'jumlah_layanan' => 1,
                            'total_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'grantotal_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'status_layanan_detail' => 'OPN',
                            'tgl_layanan_detail' => $now,
                            'tagihan_pribadi' => 0,
                            'tagihan_penjamin' => 0,
                            'tgl_layanan_detail_2' => $now,
                            'row_id_header' => $header->id,
                        ];
                    } else if ($a['status_order_3'] == 'RACIKAN') {
                        $kode_racik = $this->get_kode_racik_real();
                        $ts_layanan_detail = [
                            'id_layanan_detail' => $kode_detail_obat,
                            'kode_layanan_header' => $kode_layanan_header,
                            'kode_tarif_detail' => '',
                            'total_tarif' => $a['harga2_order'],
                            'jumlah_layanan' => $a['qty_order'],
                            'total_layanan' => $total,
                            'diskon_layanan' => $a['disc_order'],
                            'grantotal_layanan' => $grandtotal,
                            'status_layanan_detail' => 'OPN',
                            'tgl_layanan_detail' => $now,
                            'kode_barang' => $kode_racik,
                            'aturan_pakai' => $a['dosis_order'],
                            'kategori_resep' => $kategori_resep,
                            'satuan_barang' => $mt_barang[0]->satuan,
                            'tipe_anestesi' => $a['status_order_2'],
                            'tagihan_pribadi' => $tagihan_pribadi,
                            'tagihan_penjamin' => $tagihan_penjamin,
                            'tgl_layanan_detail_2' => $now,
                            'row_id_header' => $header->id,
                        ];
                        $detail_racikan = DB::connection('mysql2')->select('SELECT * FROM xxxmt_racikan a
                        LEFT OUTER JOIN xxxmt_racikan_detail b ON a.`kode_racik` = b.`kode_racik`
                         WHERE a.id = ? AND b.`satuan_barang` != ?', [$a['id_racik'], '-']);
                        if ($detail_racikan[0]->tipe_racik == 'NS') {
                            $total_tarif = 700;
                            $total_layanan = 700 * $a['qty_order'];
                        } else {
                            $total_tarif = 7000;
                            $total_layanan = 7000;
                        }
                        $ts_layanan_detail2 = [
                            'id_layanan_detail' => $this->createLayanandetail(),
                            'kode_layanan_header' => $kode_layanan_header,
                            'kode_tarif_detail' => 'TX23513',
                            'total_tarif' => $total_tarif,
                            'jumlah_layanan' => $a['qty_order'],
                            'total_layanan' => $total_layanan,
                            'grantotal_layanan' => $total_layanan,
                            'status_layanan_detail' => 'OPN',
                            'tgl_layanan_detail' => $now,
                            'tagihan_pribadi' => 0,
                            'tagihan_penjamin' => 0,
                            'tgl_layanan_detail_2' => $now,
                            'row_id_header' => $header->id,
                        ];
                        $detail_racikan = DB::connection('mysql2')->select('SELECT * FROM xxxmt_racikan a
                        LEFT OUTER JOIN xxxmt_racikan_detail b ON a.`kode_racik` = b.`kode_racik`
                         WHERE a.id = ? AND b.`satuan_barang` != ?', [$a['id_racik'], '-']);
                        $header_racikan = [
                            'kode_racik' => $kode_racik,
                            'tgl_racik' => $now,
                            'nama_racik' => $detail_racikan[0]->nama_racik,
                            'total_racik' => $detail_racikan[0]->total_racik,
                            'tipe_racik' => $detail_racikan[0]->tipe_racik,
                            'qty_racik' => $detail_racikan[0]->qty_racik,
                            'kemasan' => $detail_racikan[0]->kemasan,
                            'hrg_kemasan' => $detail_racikan[0]->hrg_kemasan
                        ];
                        $mt_racikan_header = mt_racikan_header_dummy::create($header_racikan);
                        foreach ($detail_racikan as $dr) {
                            $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$dr->kode_barang, $this->get_unit_login()]));
                            if ($detail_racikan[0]->tipe_racik == 'NS') {
                                $stok_current = $cek_stok[0]->stok_current - $dr->qty_barang;
                                $stok_out =  $dr->qty_barang;
                            } else {
                                $stok_current = $cek_stok[0]->stok_current - 1;
                                $stok_out =  1;
                            }
                            $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$dr->kode_barang]);
                            $data_obat_racik = [
                                'kode_racik' => $kode_racik,
                                'kode_barang' => $dr->kode_barang,
                                'qty_barang' => $dr->qty_barang,
                                'satuan_barang' => $dr->satuan_barang,
                                'harga_satuan_barang' => $dr->harga_satuan_barang,
                                'subtotal_barang' => $dr->subtotal_barang,
                                'grantotal_barang' => $dr->grantotal_barang,
                                'harga_brg_embalase' => $dr->harga_brg_embalase,
                            ];
                            $mt_racikan_detail_1 = mt_racikan_detail_dummy::create($data_obat_racik);
                            $data_ti_kartu_stok = [
                                'no_dokumen' => $kode_layanan_header,
                                'no_dokumen_detail' => $kode_detail_obat,
                                'tgl_stok' => $this->get_now(),
                                'kode_unit' => $this->get_unit_login(),
                                'kode_barang' => $dr->kode_barang,
                                'stok_last' => $cek_stok[0]->stok_current,
                                'stok_out' => $stok_out,
                                'stok_global' => '0',
                                'stok_current' => $stok_current,
                                'harga_beli' => $mt_barang[0]->hna,
                                'act' => '1',
                                'act_ed' => '1',
                                'inputby' => auth()->user()->id,
                                'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien,
                            ];
                            $insert_ti_kartu_stok = ti_kartu_stok::create($data_ti_kartu_stok);
                            $data_embalase = [
                                'kode_racik' => $kode_racik,
                                'kode_barang' => 'TX23513',
                                'qty_barang' => '1',
                                'satuan_barang' => '-',
                                'harga_satuan_barang' => '1700',
                                'subtotal_barang' => '1700',
                                'grantotal_barang' => '1700',
                                'harga_brg_embalase' => '1700',
                            ];
                            $mt_racikan_detail_2 = mt_racikan_detail_dummy::create($data_embalase);
                        }
                    }
                    $detail = ts_layanan_detail_dummy::create($ts_layanan_detail);
                    $detail2 = ts_layanan_detail_dummy::create($ts_layanan_detail2);
                } catch (\Exception $e) {
                    $data = [
                        'kode' => 500,
                        'message' => $e->getMessage(),
                    ];
                    echo json_encode($data);
                    die;
                }
                $totalheader = $totalheader + $grandtotal;
            }
            // dd('ok');
            //end of insert layanan detail obat reguler
            $get_detail_obat = DB::connection('mysql2')->select('select * from ts_layanan_detail where row_id_header = ? and kode_tarif_detail = ?', [$header->id, '']);
            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagian_penjamin_head = $jsf[0]->jasa_baca;
                $tagian_pribadi_head = 0;
            } else {
                $tagian_penjamin_head = 0;
                $tagian_pribadi_head = $jsf[0]->jasa_baca;
            }

            $ts_layanan_detail3 = [
                'id_layanan_detail' => $this->createLayanandetail(),
                'kode_layanan_header' => $kode_layanan_header,
                'kode_tarif_detail' => 'TX23523',
                'total_tarif' => $jsf[0]->jasa_baca,
                'jumlah_layanan' => 1,
                'total_layanan' => $jsf[0]->jasa_baca,
                'grantotal_layanan' => $jsf[0]->jasa_baca,
                'status_layanan_detail' => 'OPN',
                'tgl_layanan_detail' => $now,
                'tagihan_pribadi' => $tagian_pribadi_head,
                'tagihan_penjamin' => $tagian_penjamin_head,
                'tgl_layanan_detail_2' => $now,
                'row_id_header' => $header->id,
            ];
            $detail3 = ts_layanan_detail_dummy::create($ts_layanan_detail3);

            //update layanan header
            $totalheader = $totalheader + $jsf[0]->jasa_baca;
            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagihan_penjamin_header = $totalheader;
                $tagihan_pribadi_header = '0';
            } else {
                $tagihan_penjamin_header = '0';
                $tagihan_pribadi_header = $totalheader;
            }
            ts_layanan_header_dummy::where('id', $header->id)
                ->update(['status_layanan' => $status_layanan, 'kode_tipe_transaksi' => $kode_tipe_transaki, 'total_layanan' => $totalheader, 'tagihan_penjamin' => $tagihan_penjamin_header, 'tagihan_pribadi' => $tagihan_pribadi_header]);
            //end update layanan header
        }
        //end resep reguler
        //insert resep kronis
        if ($cek_kron > 0) {
            //cek stok obat 2
            foreach ($arrayindex_kronis as $a) {
                $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$a['kode_barang_order'], $this->get_unit_login()]));
                $stok_current = $cek_stok[0]->stok_current - $a['qty_order'];
                if ($stok_current < 0) {
                    $data = [
                        'kode' => 500,
                        'message' => $a['nama_barang_order'] . ' ' . 'Stok Tidak Mencukupi !',
                    ];
                    echo json_encode($data);
                }
            }
            //end of cek stok obat
            $unitlog = $this->get_unit_login();
            $r = DB::connection('mysql2')->select("CALL GET_NOMOR_LAYANAN_HEADER($unitlog)");
            $kode_layanan_header = $r[0]->no_trx_layanan;
            if (strlen($kode_layanan_header) < 5) {
                $year = date('y');
                $kode_layanan_header = $unit[0]['prefix_unit'] . $year . date('m') . date('d') . '000001';
                DB::connection('mysql2')->select('insert into mt_nomor_trx (tgl,no_trx_layanan,unit) values (?,?,?)', [date('Y-m-d h:i:s'), $kode_layanan_header, $kodeunit]);
            }
            //insert layanan header resep reguler
            try {
                $ts_layanan_header = [
                    'kode_layanan_header' => $kode_layanan_header,
                    'tgl_entry' => $this->get_now(),
                    'kode_kunjungan' => $request->kodekunjungan,
                    'kode_unit' => $this->get_unit_login(),
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
            //end of insert layanan header resep reguler
            $now = $this->get_now();
            $totalheader = 0;
            //insert layanan detail obat reguler
            foreach ($arrayindex_kronis as $a) {
                $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$a['kode_barang_order']]);
                $total = $a['harga2_order'] * $a['qty_order'];
                $diskon = $a['disc_order'];
                $hitung = $diskon / 100 * $total;
                $grandtotal = $total - $hitung + $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                    $tagihan_pribadi = 0;
                    $tagihan_penjamin = $grandtotal;
                } else {
                    $tagihan_pribadi = $grandtotal;
                    $tagihan_penjamin = 0;
                }
                $kode_detail_obat = $this->createLayanandetail();
                try {
                    $ts_layanan_detail = [
                        'id_layanan_detail' => $kode_detail_obat,
                        'kode_layanan_header' => $kode_layanan_header,
                        'kode_tarif_detail' => '',
                        'total_tarif' => $a['harga2_order'],
                        'jumlah_layanan' => $a['qty_order'],
                        'total_layanan' => $total,
                        'diskon_layanan' => $a['disc_order'],
                        'grantotal_layanan' => $grandtotal,
                        'status_layanan_detail' => 'OPN',
                        'tgl_layanan_detail' => $now,
                        'kode_barang' => $a['kode_barang_order'],
                        'aturan_pakai' => $a['dosis_order'],
                        'kategori_resep' => $kategori_resep,
                        'satuan_barang' => $mt_barang[0]->satuan,
                        'tipe_anestesi' => $a['status_order_2'],
                        'tagihan_pribadi' => $tagihan_pribadi,
                        'tagihan_penjamin' => $tagihan_penjamin,
                        'tgl_layanan_detail_2' => $now,
                        'row_id_header' => $header->id,
                    ];
                    $detail = ts_layanan_detail_dummy::create($ts_layanan_detail);
                } catch (\Exception $e) {
                    $data = [
                        'kode' => 500,
                        'message' => $e->getMessage(),
                    ];
                    echo json_encode($data);
                }
                $totalheader = $totalheader + $grandtotal;
            }
            //end of insert layanan detail obat reguler
            $get_detail_obat = DB::connection('mysql2')->select('select * from ts_layanan_detail where row_id_header = ? and kode_tarif_detail = ?', [$header->id, '']);
            foreach ($get_detail_obat as $do) {
                //cek stok obat reguler
                $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$do->kode_barang, $this->get_unit_login()]));
                $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$do->kode_barang]);
                $stok_current = $cek_stok[0]->stok_current - $do->jumlah_layanan;
                if ($stok_current < 0) {
                    $data = [
                        'kode' => 500,
                        'message' => $a['nama_barang_order'] . ' ' . 'Stok Tidak Mencukupi !',
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
                    'kode_unit' => $this->get_unit_login(),
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
                ];
                $insert_ti_kartu_stok = ti_kartu_stok::create($data_ti_kartu_stok);
                //end of kartu stok
                if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                    $tagihan_pribadi_js = 0;
                    $tagihan_penjamin_js = $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                } else {
                    $tagihan_pribadi_js = $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                    $tagihan_penjamin_js = 0;
                }
                $ts_layanan_detail2 = [
                    'id_layanan_detail' => $this->createLayanandetail(),
                    'kode_layanan_header' => $kode_layanan_header,
                    'kode_tarif_detail' => 'TX23513',
                    'total_tarif' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                    'jumlah_layanan' => 1,
                    'total_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                    'grantotal_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                    'status_layanan_detail' => 'OPN',
                    'tgl_layanan_detail' => $now,
                    'tagihan_pribadi' => 0,
                    'tagihan_penjamin' => 0,
                    'tgl_layanan_detail_2' => $now,
                    'row_id_header' => $header->id,
                ];
                $detail2 = ts_layanan_detail_dummy::create($ts_layanan_detail2);
            }

            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagian_penjamin_head = $jsf[0]->jasa_baca;
                $tagian_pribadi_head = 0;
            } else {
                $tagian_penjamin_head = 0;
                $tagian_pribadi_head = $jsf[0]->jasa_baca;
            }

            $ts_layanan_detail3 = [
                'id_layanan_detail' => $this->createLayanandetail(),
                'kode_layanan_header' => $kode_layanan_header,
                'kode_tarif_detail' => 'TX23523',
                'total_tarif' => $jsf[0]->jasa_baca,
                'jumlah_layanan' => 1,
                'total_layanan' => $jsf[0]->jasa_baca,
                'grantotal_layanan' => $jsf[0]->jasa_baca,
                'status_layanan_detail' => 'OPN',
                'tgl_layanan_detail' => $now,
                'tagihan_pribadi' => $tagian_pribadi_head,
                'tagihan_penjamin' => $tagian_penjamin_head,
                'tgl_layanan_detail_2' => $now,
                'row_id_header' => $header->id,
            ];
            $detail3 = ts_layanan_detail_dummy::create($ts_layanan_detail3);

            //update layanan header
            $totalheader = $totalheader + $jsf[0]->jasa_baca;
            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagihan_penjamin_header = $totalheader;
                $tagihan_pribadi_header = '0';
            } else {
                $tagihan_penjamin_header = '0';
                $tagihan_pribadi_header = $totalheader;
            }
            ts_layanan_header_dummy::where('id', $header->id)
                ->update(['status_layanan' => $status_layanan, 'kode_tipe_transaksi' => $kode_tipe_transaki, 'total_layanan' => $totalheader, 'tagihan_penjamin' => $tagihan_penjamin_header, 'tagihan_pribadi' => $tagihan_pribadi_header]);
            //end update layanan header
        }
        //end resep kronis

        //insert resep kemoterapi
        if ($cek_kemo > 0) {
            //cek stok obat 2
            foreach ($arrayindex_kemo as $a) {
                $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$a['kode_barang_order'], $this->get_unit_login()]));
                $stok_current = $cek_stok[0]->stok_current - $a['qty_order'];
                if ($stok_current < 0) {
                    $data = [
                        'kode' => 500,
                        'message' => $a['nama_barang_order'] . ' ' . 'Stok Tidak Mencukupi !',
                    ];
                    echo json_encode($data);
                }
            }
            //end of cek stok obat
            $unitlog = $this->get_unit_login();
            $r = DB::connection('mysql2')->select("CALL GET_NOMOR_LAYANAN_HEADER($unitlog)");
            $kode_layanan_header = $r[0]->no_trx_layanan;
            if (strlen($kode_layanan_header) < 5) {
                $year = date('y');
                $kode_layanan_header = $unit[0]['prefix_unit'] . $year . date('m') . date('d') . '000001';
                DB::connection('mysql2')->select('insert into mt_nomor_trx (tgl,no_trx_layanan,unit) values (?,?,?)', [date('Y-m-d h:i:s'), $kode_layanan_header, $kodeunit]);
            }
            //insert layanan header resep reguler
            try {
                $ts_layanan_header = [
                    'kode_layanan_header' => $kode_layanan_header,
                    'tgl_entry' => $this->get_now(),
                    'kode_kunjungan' => $request->kodekunjungan,
                    'kode_unit' => $this->get_unit_login(),
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
            //end of insert layanan header resep reguler
            $now = $this->get_now();
            $totalheader = 0;
            //insert layanan detail obat reguler
            foreach ($arrayindex_kemo as $a) {
                $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$a['kode_barang_order']]);
                $total = $a['harga2_order'] * $a['qty_order'];
                $diskon = $a['disc_order'];
                $hitung = $diskon / 100 * $total;
                $grandtotal = $total - $hitung + $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                    $tagihan_pribadi = 0;
                    $tagihan_penjamin = $grandtotal;
                } else {
                    $tagihan_pribadi = $grandtotal;
                    $tagihan_penjamin = 0;
                }
                $kode_detail_obat = $this->createLayanandetail();
                try {
                    $ts_layanan_detail = [
                        'id_layanan_detail' => $kode_detail_obat,
                        'kode_layanan_header' => $kode_layanan_header,
                        'kode_tarif_detail' => '',
                        'total_tarif' => $a['harga2_order'],
                        'jumlah_layanan' => $a['qty_order'],
                        'total_layanan' => $total,
                        'diskon_layanan' => $a['disc_order'],
                        'grantotal_layanan' => $grandtotal,
                        'status_layanan_detail' => 'OPN',
                        'tgl_layanan_detail' => $now,
                        'kode_barang' => $a['kode_barang_order'],
                        'aturan_pakai' => $a['dosis_order'],
                        'kategori_resep' => $kategori_resep,
                        'satuan_barang' => $mt_barang[0]->satuan,
                        'tipe_anestesi' => $a['status_order_2'],
                        'tagihan_pribadi' => $tagihan_pribadi,
                        'tagihan_penjamin' => $tagihan_penjamin,
                        'tgl_layanan_detail_2' => $now,
                        'row_id_header' => $header->id,
                    ];
                    $detail = ts_layanan_detail_dummy::create($ts_layanan_detail);
                } catch (\Exception $e) {
                    $data = [
                        'kode' => 500,
                        'message' => $e->getMessage(),
                    ];
                    echo json_encode($data);
                }
                $totalheader = $totalheader + $grandtotal;
            }
            //end of insert layanan detail obat reguler
            $get_detail_obat = DB::connection('mysql2')->select('select * from ts_layanan_detail where row_id_header = ? and kode_tarif_detail = ?', [$header->id, '']);
            foreach ($get_detail_obat as $do) {
                //cek stok obat reguler
                $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$do->kode_barang, $this->get_unit_login()]));
                $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$do->kode_barang]);
                $stok_current = $cek_stok[0]->stok_current - $do->jumlah_layanan;
                if ($stok_current < 0) {
                    $data = [
                        'kode' => 500,
                        'message' => $a['nama_barang_order'] . ' ' . 'Stok Tidak Mencukupi !',
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
                    'kode_unit' => $this->get_unit_login(),
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
                ];
                $insert_ti_kartu_stok = ti_kartu_stok::create($data_ti_kartu_stok);
                //end of kartu stok
                if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                    $tagihan_pribadi_js = 0;
                    $tagihan_penjamin_js = $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                } else {
                    $tagihan_pribadi_js = $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                    $tagihan_penjamin_js = 0;
                }
                $ts_layanan_detail2 = [
                    'id_layanan_detail' => $this->createLayanandetail(),
                    'kode_layanan_header' => $kode_layanan_header,
                    'kode_tarif_detail' => 'TX23513',
                    'total_tarif' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                    'jumlah_layanan' => 1,
                    'total_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                    'grantotal_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                    'status_layanan_detail' => 'OPN',
                    'tgl_layanan_detail' => $now,
                    'tagihan_pribadi' => 0,
                    'tagihan_penjamin' => 0,
                    'tgl_layanan_detail_2' => $now,
                    'row_id_header' => $header->id,
                ];
                $detail2 = ts_layanan_detail_dummy::create($ts_layanan_detail2);
            }

            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagian_penjamin_head = $jsf[0]->jasa_baca;
                $tagian_pribadi_head = 0;
            } else {
                $tagian_penjamin_head = 0;
                $tagian_pribadi_head = $jsf[0]->jasa_baca;
            }

            $ts_layanan_detail3 = [
                'id_layanan_detail' => $this->createLayanandetail(),
                'kode_layanan_header' => $kode_layanan_header,
                'kode_tarif_detail' => 'TX23523',
                'total_tarif' => $jsf[0]->jasa_baca,
                'jumlah_layanan' => 1,
                'total_layanan' => $jsf[0]->jasa_baca,
                'grantotal_layanan' => $jsf[0]->jasa_baca,
                'status_layanan_detail' => 'OPN',
                'tgl_layanan_detail' => $now,
                'tagihan_pribadi' => $tagian_pribadi_head,
                'tagihan_penjamin' => $tagian_penjamin_head,
                'tgl_layanan_detail_2' => $now,
                'row_id_header' => $header->id,
            ];
            $detail3 = ts_layanan_detail_dummy::create($ts_layanan_detail3);

            //update layanan header
            $totalheader = $totalheader + $jsf[0]->jasa_baca;
            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagihan_penjamin_header = $totalheader;
                $tagihan_pribadi_header = '0';
            } else {
                $tagihan_penjamin_header = '0';
                $tagihan_pribadi_header = $totalheader;
            }
            ts_layanan_header_dummy::where('id', $header->id)
                ->update(['status_layanan' => $status_layanan, 'kode_tipe_transaksi' => $kode_tipe_transaki, 'total_layanan' => $totalheader, 'tagihan_penjamin' => $tagihan_penjamin_header, 'tagihan_pribadi' => $tagihan_pribadi_header]);
            //end update layanan header
        }
        //end resep kemoterapi

        //insert resep hibah
        if ($cek_hib > 0) {
            //cek stok obat 2
            foreach ($arrayindex_hibah as $a) {
                $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$a['kode_barang_order'], $this->get_unit_login()]));
                $stok_current = $cek_stok[0]->stok_current - $a['qty_order'];
                if ($stok_current < 0) {
                    $data = [
                        'kode' => 500,
                        'message' => $a['nama_barang_order'] . ' ' . 'Stok Tidak Mencukupi !',
                    ];
                    echo json_encode($data);
                }
            }
            //end of cek stok obat
            $unitlog = $this->get_unit_login();
            $r = DB::connection('mysql2')->select("CALL GET_NOMOR_LAYANAN_HEADER($unitlog)");
            $kode_layanan_header = $r[0]->no_trx_layanan;
            if (strlen($kode_layanan_header) < 5) {
                $year = date('y');
                $kode_layanan_header = $unit[0]['prefix_unit'] . $year . date('m') . date('d') . '000001';
                DB::connection('mysql2')->select('insert into mt_nomor_trx (tgl,no_trx_layanan,unit) values (?,?,?)', [date('Y-m-d h:i:s'), $kode_layanan_header, $kodeunit]);
            }
            //insert layanan header resep reguler
            try {
                $ts_layanan_header = [
                    'kode_layanan_header' => $kode_layanan_header,
                    'tgl_entry' => $this->get_now(),
                    'kode_kunjungan' => $request->kodekunjungan,
                    'kode_unit' => $this->get_unit_login(),
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
            //end of insert layanan header resep reguler
            $now = $this->get_now();
            $totalheader = 0;
            //insert layanan detail obat reguler
            foreach ($arrayindex_hibah as $a) {
                $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$a['kode_barang_order']]);
                $total = $a['harga2_order'] * $a['qty_order'];
                $diskon = $a['disc_order'];
                $hitung = $diskon / 100 * $total;
                $grandtotal = $total - $hitung + 0 + 0;
                if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                    $tagihan_pribadi = 0;
                    $tagihan_penjamin = 0;
                } else {
                    $tagihan_pribadi = 0;
                    $tagihan_penjamin = 0;
                }
                $kode_detail_obat = $this->createLayanandetail();
                try {
                    $ts_layanan_detail = [
                        'id_layanan_detail' => $kode_detail_obat,
                        'kode_layanan_header' => $kode_layanan_header,
                        'kode_tarif_detail' => '',
                        'total_tarif' => 0,
                        'jumlah_layanan' => $a['qty_order'],
                        'total_layanan' => $total,
                        'diskon_layanan' => $a['disc_order'],
                        'grantotal_layanan' => 0,
                        'status_layanan_detail' => 'OPN',
                        'tgl_layanan_detail' => $now,
                        'kode_barang' => $a['kode_barang_order'],
                        'aturan_pakai' => $a['dosis_order'],
                        'kategori_resep' => $kategori_resep,
                        'satuan_barang' => $mt_barang[0]->satuan,
                        'tipe_anestesi' => $a['status_order_2'],
                        'tagihan_pribadi' => 0,
                        'tagihan_penjamin' => 0,
                        'tgl_layanan_detail_2' => $now,
                        'row_id_header' => $header->id,
                    ];
                    $detail = ts_layanan_detail_dummy::create($ts_layanan_detail);
                } catch (\Exception $e) {
                    $data = [
                        'kode' => 500,
                        'message' => $e->getMessage(),
                    ];
                    echo json_encode($data);
                }
                $totalheader = $totalheader + $grandtotal;
            }
            //end of insert layanan detail obat reguler
            $get_detail_obat = DB::connection('mysql2')->select('select * from ts_layanan_detail where row_id_header = ? and kode_tarif_detail = ?', [$header->id, '']);
            foreach ($get_detail_obat as $do) {
                //cek stok obat reguler
                $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$do->kode_barang, $this->get_unit_login()]));
                $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$do->kode_barang]);
                $stok_current = $cek_stok[0]->stok_current - $do->jumlah_layanan;
                if ($stok_current < 0) {
                    $data = [
                        'kode' => 500,
                        'message' => $a['nama_barang_order'] . ' ' . 'Stok Tidak Mencukupi !',
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
                    'kode_unit' => $this->get_unit_login(),
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
                ];
                $insert_ti_kartu_stok = ti_kartu_stok::create($data_ti_kartu_stok);
                //end of kartu stok
                if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                    $tagihan_pribadi_js = 0;
                    $tagihan_penjamin_js = 0;
                } else {
                    $tagihan_pribadi_js = 0;
                    $tagihan_penjamin_js = 0;
                }
                $ts_layanan_detail2 = [
                    'id_layanan_detail' => $this->createLayanandetail(),
                    'kode_layanan_header' => $kode_layanan_header,
                    'kode_tarif_detail' => 'TX23513',
                    'total_tarif' => 0,
                    'jumlah_layanan' => 1,
                    'total_layanan' => 0,
                    'grantotal_layanan' => 0,
                    'status_layanan_detail' => 'OPN',
                    'tgl_layanan_detail' => $now,
                    'tagihan_pribadi' => 0,
                    'tagihan_penjamin' => 0,
                    'tgl_layanan_detail_2' => $now,
                    'row_id_header' => $header->id,
                ];
                $detail2 = ts_layanan_detail_dummy::create($ts_layanan_detail2);
            }
            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagian_penjamin_head = 0;
                $tagian_pribadi_head = 0;
            } else {
                $tagian_penjamin_head = 0;
                $tagian_pribadi_head = 0;
            }
            $ts_layanan_detail3 = [
                'id_layanan_detail' => $this->createLayanandetail(),
                'kode_layanan_header' => $kode_layanan_header,
                'kode_tarif_detail' => 'TX23523',
                'total_tarif' => 0,
                'jumlah_layanan' => 1,
                'total_layanan' => 0,
                'grantotal_layanan' => 0,
                'status_layanan_detail' => 'OPN',
                'tgl_layanan_detail' => $now,
                'tagihan_pribadi' => $tagian_pribadi_head,
                'tagihan_penjamin' => $tagian_penjamin_head,
                'tgl_layanan_detail_2' => $now,
                'row_id_header' => $header->id,
            ];
            $detail3 = ts_layanan_detail_dummy::create($ts_layanan_detail3);
            //update layanan header
            $totalheader =  0;
            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagihan_penjamin_header = 0;
                $tagihan_pribadi_header = '0';
            } else {
                $tagihan_penjamin_header = '0';
                $tagihan_pribadi_header = 0;
            }
            ts_layanan_header_dummy::where('id', $header->id)
                ->update(['status_layanan' => $status_layanan, 'kode_tipe_transaksi' => $kode_tipe_transaki, 'total_layanan' => $totalheader, 'tagihan_penjamin' => $tagihan_penjamin_header, 'tagihan_pribadi' => $tagihan_pribadi_header]);
            //end update layanan header
        }
        //end resep hibah
        $data = [
            'kode' => 200,
            'message' => 'sukses',
            'idheader' => $header->id
        ];
        echo json_encode($data);
        die;
    }
    public function simpanorderan_far_racik(Request $request)
    {
        $jsf = DB::select('select * from mt_jasa_farmasi');
        $data_obat = json_decode($_POST['data1'], true);
        $data_kunjungan = DB::select('select *,fc_nama_px(no_rm) AS nama_pasien,fc_alamat(no_rm) AS alamat_pasien from ts_kunjungan where kode_kunjungan = ?', [$request->kodekunjungan]);
        $kodeunit = $this->get_unit_login();
        $data_header = [
            'kode_layanan_header' => '',
            'tgl_entry' => $this->get_now(),
            'kode_kunjungan' => $request->kodekunjungan,
            'kode_unit' => $this->get_unit_login(),
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
        foreach ($data_obat as $nama) {
            $index = $nama['name'];
            $value = $nama['value'];
            $dataSet[$index] = $value;
            if ($index == 'dk_subtot_racikan') {
                $array_obat[] = $dataSet;
            }
        }

        foreach ($array_obat as $ob) {
            $data_detail = [
                'id_layanan_detail' => '',
                'kode_layanan_header' => '',
                'total_tarif' => '',
                'jumlah_layanan' => '',
                'total_layanan' => '',
                'grantotal_layanan' => '',
                'kode_dokter1' => '',
                'status_layanan_detail' => '',
                'tgl_layanan_detail' => '',
                'kode_barang' => '',
                'aturan_pakai' => '',
                'kategori_resep' => '',
                'tagihan_pribadi' => '',
                'tagihan_penjamin' => '',
                'tgl_layanan_detail_2' => '',
                'tipe_anestesi' => '',
                'row_id_header' => ''
            ];
        }
    }
    public function riwayat_obat_hari_ini(Request $request)
    {
        $kodekunjungan = $request->kode;
        $header = DB::connection('mysql2')->select('select * from ts_layanan_header where kode_kunjungan = ? AND status_layanan < ?', [$kodekunjungan, 3]);
        $detail = DB::connection('mysql2')->select('SELECT a.id as id_header,a.kode_layanan_header,d.nama_racik,b.id,row_id_header,kode_barang,fc_nama_barang(kode_barang) AS nama_barang,aturan_pakai,jumlah_layanan,jumlah_retur,grantotal_layanan,a.`total_layanan`,c.nama_anestesi,d.tipe_racik,b.status_layanan_detail FROM ts_layanan_header a
        LEFT OUTER JOIN ts_layanan_detail b ON a.`id` = b.`row_id_header`
        LEFT OUTER JOIN mt_anestesi_tipe c on b.tipe_anestesi = c.id
        LEFT OUTER JOIN mt_racikan d on b.kode_barang = d.kode_racik
        WHERE a.`kode_kunjungan` = ? AND b.`kode_barang` != ? AND a.status_layanan < ?', [$kodekunjungan, '', 3]);
        return view('Layanan.tabel_riwayat_obat_hari_ini', compact([
            'header',
            'detail'
        ]));
    }
    public function hitunganracikan(Request $request)
    {
        $a = $request->dosis_racik;
        $b = $request->qtyracikan;
        $c = $request->dosis_obat;
        $d = $request->harga_obat;
        $e = $request->tiperacikan;
        $f = $request->kemasan;
        if ($c == 0) {
            $data = [
                'kode' => 500,
                'message' => 'Dosis Awal Tidak Boleh Kosong !'
            ];
            echo json_encode($data);
            die;
        }
        $kode_barang = $request->kode_barang;
        $get_master_barang = DB::select('SELECT * from mt_barang where kode_barang = ?', [$kode_barang]);
        //         v_subtot
        // subtotalracik_2
        if ($f == 3) {
            $kat11 = $get_master_barang[0]->kat11;
            if ($kat11 == 1) {
                // $harga_satuan_kecil = $d / $c;
                $qtytotal = $a * $b / $c;
                $subtot = $d * $qtytotal;
                $v_subtot = 'IDR ' . number_format($subtot, 2);
            } else {
                $subtot = $d;
                $v_subtot = 'IDR ' . number_format($d, 2);
                $qtytotal = 1;
            }
        } else {
            $harga_satuan_kecil = $d / $c;
            $qtytotal = $a * $b / $c;
            $subtot = $d * $qtytotal;
            $v_subtot = 'IDR ' . number_format($subtot, 2);
        }
        $data = [
            'kode' => 200,
            'subtotal' => $subtot,
            'qtytotal' => $qtytotal,
            'v_subtot' => $v_subtot
        ];
        echo json_encode($data);
        die;
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
    public function get_kode_retur_header()
    {
        $q = DB::connection('mysql2')->select('SELECT id,kode_retur_header,RIGHT(kode_retur_header,6) AS kd_max  FROM ts_retur_header
        WHERE DATE(tgl_retur) = CURDATE()
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
        return 'RET' . 'DP2' . date('ymd') . $kd;
    }
    public function get_kode_retur_detail()
    {
        $q = DB::connection('mysql2')->select('SELECT id,kode_retur_detail,RIGHT(kode_retur_detail,6) AS kd_max  FROM ts_retur_detail
        WHERE DATE(tgl_retur_detail) = CURDATE()
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
        return 'RETDET' . date('ymd') . $kd;
    }
    public function get_kode_racik()
    {
        $q = DB::connection('mysql2')->select('SELECT id,kode_racik,RIGHT(kode_racik,3) AS kd_max  FROM xxxmt_racikan
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
    public function get_kode_racik_real()
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
    public function post_komponen_racik(Request $request)
    {
        // $header = $request->header;
        $header = json_decode($_POST['header'], true);
        $detail_racik = json_decode($_POST['detail_racik'], true);
        foreach ($detail_racik as $nama) {
            $index = $nama['name'];
            $value = $nama['value'];
            $dataSet[$index] = $value;
            if ($index == 'sub_total_order') {
                $array_obat[] = $dataSet;
            }
        }
        // dd($detail_racik);
        foreach ($header as $nama) {
            $index =  $nama['name'];
            $value =  $nama['value'];
            $dataheader[$index] = $value;
        }
        $aturan_pakai = $dataheader['aturanpakairacik'];
        // dd($dataheader);
        if ($dataheader['kemasan'] == 3) {
            $jasa_racik = 7000;
        } else {
            $jasa_racik = 700 * $dataheader['qtyracikan'];
        }
        $gt_1 = $request->gt;
        $gt_2 = $request->gt + $jasa_racik;
        if ($dataheader['tiperacikan'] == 1) {
            $tipe_racik_kode = 'NS';
        } else {
            $tipe_racik_kode = 'S';
        }
        if ($dataheader['kemasan'] == 1) {
            $KEMASAN = 'KAPSUL';
            $harga_kemasan = 700;
        } else if ($dataheader['kemasan'] == 2) {
            $KEMASAN = 'KERTAS PERKAMEN';
            $harga_kemasan = 700;
        } else {
            $KEMASAN = 'POT SALEP';
            $harga_kemasan = 7000;
        }


        $now = $this->get_now();
        $kode_racik = $this->get_kode_racik();
        $data_xxx_racikan_header = [
            'kode_racik' => $kode_racik,
            'tgl_racik' => $now,
            'nama_racik' => $dataheader['komponen_nama_racikan'],
            'total_racik' => $gt_1,
            'tipe_racik' => $tipe_racik_kode,
            'qty_racik' => $dataheader['qtyracikan'],
            'kemasan' => $KEMASAN,
            'hrg_kemasan' => $harga_kemasan
        ];
        $mt_racikan_header = xxxmt_racikan_header::create($data_xxx_racikan_header);
        foreach ($array_obat as $o)
        // dd($o['harga_order']);
        {
            if ($dataheader['kemasan'] == 3) {
                $QTY_ORDER = $o['dosis_order'];
                $JASA = 1700;
            } else {
                $QTY_ORDER = $o['qty_order'];
                $JASA = 1700;
            }
            $sub_total_barang = $o['harga_order'] * $o['qty_order'];
            $grand_total_barang = $sub_total_barang + $JASA;
            $harga_brg_embalase = $grand_total_barang / $o['qty_order'];
            $data_obat_racik = [
                'kode_racik' => $kode_racik,
                'kode_barang' => $o['kode_barang_order'],
                'qty_barang' => $QTY_ORDER,
                'satuan_barang' => $o['satuan_order'],
                'harga_satuan_barang' => $o['harga_order'],
                'subtotal_barang' => $sub_total_barang,
                'grantotal_barang' => $grand_total_barang,
                'harga_brg_embalase' => $harga_brg_embalase,
            ];
            $mt_racikan_detail_1 = xxxmt_racikan_detail::create($data_obat_racik);
            $data_embalase = [
                'kode_racik' => $kode_racik,
                'kode_barang' => 'TX23513',
                'qty_barang' => '1',
                'satuan_barang' => '-',
                'harga_satuan_barang' => $JASA,
                'subtotal_barang' => $JASA,
                'grantotal_barang' => $JASA,
                'harga_brg_embalase' => $JASA,
            ];
            $mt_racikan_detail_2 = xxxmt_racikan_detail::create($data_embalase);
        }
        $V_harga_racikan = 'IDR ' . number_format($gt_1, 2);
        if ($dataheader['kemasan'] == 3) {
            $sub_total = $gt_1 + 7000;
            $v_sub_total = 'IDR ' . number_format($sub_total, 2);
        } else {
            $sub_total = 700 * $dataheader['qtyracikan'] + $gt_1;
            $v_sub_total = 'IDR ' . number_format(700 * $dataheader['qtyracikan'] + $gt_1, 2);
        }
        return "<div class='row mt-2 text-xs'>
        <div class='col-md-2'>
            <div class='form-group'>
                <label for='exampleFormControlInput1'>Nama Barang / Tindakan </label>
                <input readonly type='text' class='form-control form-control-sm' id='nama_barang_order' name='nama_barang_order' value='$dataheader[komponen_nama_racikan]' placeholder='name@example.com'>
                <input hidden readonly type='text' class='form-control form-control-sm' id='status_order_2' name='status_order_2' value='80' placeholder='name@example.com'>
                <input hidden readonly class='form-control form-control-sm' id='' name='kode_barang_order' value=''>
                <input hidden readonly class='form-control form-control-sm' id='' name='id_stok_order' value=''>
                <input hidden readonly class='form-control form-control-sm' id='' name='harga2_order' value='$gt_1'>
                <input hidden readonly class='' id='' name='sub_total_order_2' value='$sub_total'>
                <input hidden readonly type='' class='form-control form-control-sm' id='' name='id_racik' value='$mt_racikan_header->id'>
            </div>
        </div>
        <div class='col-md-1'>
            <div class='form-group'>
                <label for='exampleFormControlInput1'>Stok</label>
                <input readonly type='text' class='form-control form-control-sm' id='stok_curr_order' name='stok_curr_order' value='-' placeholder='name@example.com'>
            </div>
        </div>
        <div class='col-md-1'>
            <div class='form-group'>
                <label for='exampleFormControlInput1'>Qty</label>
                <input readonly type='text' class='form-control form-control-sm' id='qty_order' name='qty_order' value='$dataheader[qtyracikan]' placeholder='name@example.com'>
            </div>
        </div>
        <div class='col-md-1'>
            <div class='form-group'>
                <label for='exampleFormControlInput1'>Satuan</label>
                <input readonly type='text' class='form-control form-control-sm' id='satuan_order' name='satuan_order' value='$KEMASAN' placeholder='name@example.com'>
            </div>
        </div>
        <div class='col-md-1'>
            <div class='form-group'>
                <label for='exampleFormControlInput1'>Harga</label>
                <input readonly type='text' class='form-control form-control-sm' id='harga_order' name='harga_order' value='$V_harga_racikan' placeholder='name@example.com'>
            </div>
        </div>
        <div class='col-md-1'>
            <div class='form-group'>
                <label for='exampleFormControlInput1'>Diskon</label>
                <input readonly type='text' class='form-control form-control-sm' id='disc_order' name='disc_order' value='0' placeholder='name@example.com'>
            </div>
        </div>
        <div class='col-md-2'>
            <div class='form-group'>
                <label for='exampleFormControlInput1'>Aturan Pakai</label>
                <input readonly type='text' class='form-control form-control-sm' id='dosis_order' name='dosis_order' value='$aturan_pakai' placeholder='name@example.com'>
            </div>
        </div>
        <div class='col-md-1'>
            <div class='form-group'>
                <label for='exampleFormControlInput1'>Status</label>
                <input readonly type='text' class='form-control form-control-sm' id='status_order_1' name='status_order_1' value='REGULER'>
            </div>
        </div>
        <div hidden class='col-md-1'>
            <div class='form-group'>
                <label for='exampleFormControlInput1'>Tipe</label>
                <input readonly type='text' class='form-control form-control-sm' id='status_order_3' name='status_order_3' value='RACIKAN'>
            </div>
        </div>
        <div class='col-md-1'>
            <div class='form-group'>
                <label for='exampleFormControlInput1'>Sub Total</label>
                <input readonly type='text' class='form-control form-control-sm' id='sub_total_order' name='sub_total_order' value='$v_sub_total' placeholder='name@example.com'>
            </div>
        </div><i class='bi bi-x-square remove_field form-group col-md-1 text-danger' kode2='$kode_racik' subtot='$sub_total' jenis='' nama_barang='' kode_barang='' id_stok='' harga2='' satuan='' stok='' qty='' harga='' disc='' dosis='' sub='' sub2='' status='80' jenisracik='racikan'></i>
    </div>";
    }
    public function jumlah_grand_total_komponen_racikan(Request $request)
    {
        $qtyracikan = $request->qtyracikan;
        $jasa_racik_old = $request->jasa_racik_old;
        $gt_komponen_racikan = $request->gt_komponen_racikan;
        $new_gt = $gt_komponen_racikan + $request->gt;
        $jasa_resep = 1000;
        $jasa_racik = 700 * $qtyracikan + $jasa_racik_old;
        $gt_all = $new_gt + 1000 + $jasa_racik;
        return view('Layanan.form_gt_komponen_obat_racikan', compact([
            'new_gt',
            'jasa_resep',
            'gt_all',
            'jasa_racik'
        ]));
    }
    public function cetaknotafarmasi_2($id)
    {
        $DH = DB::select('select * from ts_layanan_header where id = ?', [$id]);
        $DK = DB::select('select * from ts_kunjungan where kode_kunjungan = ?', [$DH[0]->kode_kunjungan]);
        $KODE_HEADER = $DH[0]->kode_layanan_header;
        $ID_HEADER = $DK[0]->counter;
        $PDO = DB::connection()->getPdo();
        $QUERY = $PDO->prepare("CALL SP_CETAK_ETIKET_FARMASI_WD('$KODE_HEADER','$id')");
        $QUERY->execute();
        $data = $QUERY->fetchAll();
        $filename = 'C:\cetakanerm\etiket.jrxml';
        $config = ['driver' => 'array', 'data' => $data];
        $report = new PHPJasperXML();
        $report->load_xml_file($filename)
            ->setDataSource($config)
            ->export('Pdf');
    }
    public function detail_obat_racik(Request $request)
    {
        $detail = DB::connection('mysql2')->select('SELECT c.`kode_barang`,d.`nama_barang`,qty_racik AS qty,kemasan FROM ts_layanan_detail a
        LEFT OUTER JOIN mt_racikan b ON a.`kode_barang` = b.`kode_racik`
        LEFT OUTER JOIN mt_racikan_detail c ON b.`kode_racik` = c.`kode_racik`
        LEFT OUTER JOIN mt_barang d ON c.`kode_barang` = d.`kode_barang`
        WHERE a.id = ? AND c.`satuan_barang` <> ?', [$request->id, '-']);
        return view('Layanan.detail_racikan', compact([
            'detail'
        ]));
    }
    public function edit_aturan_pakai(Request $request)
    {
        $id = $request->id;
        $aturanpakai = $request->aturanpakai;
        $namabarang = $request->namabarang;
        return view('Layanan.edit_aturan_pakai', compact([
            'id',
            'aturanpakai',
            'namabarang'
        ]));
    }
    public function simpanedit_aturanpakai(Request $request)
    {
        $id = $request->iddetail;
        $aturan = $request->aturanpakai;
        ts_layanan_detail_dummy::where('id', $id)
            ->update(['aturan_pakai' => $aturan]);
        $data = [
            'kode' => 200,
            'message' => 'Sukses',
        ];
        echo json_encode($data);
    }
    public function retur_obat(Request $request)
    {
        $id_detail = $request->id;
        $detail = DB::connection('mysql2')->select('select * from ts_layanan_detail where id = ?', [$id_detail]);
        if ($detail[0]->status_layanan_detail == 'CLS') {
            $data = [
                'kode' => 500,
                'message' => 'Layanan / Obat sudah diretur !',
            ];
            echo json_encode($data);
            die;
        }
        $cek_resep = DB::connection('mysql2')->select('SELECT b.`kode_racik` FROM ts_layanan_detail a
        LEFT OUTER JOIN mt_racikan b ON a.kode_barang = b.kode_racik where a.id = ?', [$id_detail]);
        if ($cek_resep[0]->kode_racik != '') {
            $data = [
                'kode' => 500,
                'message' => 'Racikan tidak bisa diretur !',
            ];
            echo json_encode($data);
            die;
        }
        $id_header = $detail[0]->row_id_header;
        $header = DB::connection('mysql2')->select('select * from ts_layanan_header where id = ?', [$id_header]);
        $kode_kunjungan = $header[0]->kode_kunjungan;
        $update_layanan_detail_1 = [
            'status_layanan_detail' => 'CLS',
            'jumlah_retur' => $detail[0]->jumlah_layanan,
            'tagihan_penjamin' => 0,
            'tagihan_pribadi' => 0,
        ];
        ts_layanan_detail_dummy::where('id', $id_detail)
            ->update($update_layanan_detail_1);
        $update_layanan_detail_2 = [
            'status_layanan_detail' => 'CLS',
            'jumlah_retur' => 1,
            'tagihan_penjamin' => 0,
            'tagihan_pribadi' => 0,
        ];
        ts_layanan_detail_dummy::where('row_id_header', $id_header)->where('kode_tarif_detail', 'TX23513')
            ->update($update_layanan_detail_2);

        if ($header[0]->kode_tipe_transaksi == 1) {
            $total_header = $header[0]->tagihan_pribadi - $detail[0]->grantotal_layanan;
        } else {
            $total_header = $header[0]->tagihan_penjamin - $detail[0]->grantotal_layanan;
        }

        $cek_detail = DB::connection('mysql2')->select('SELECT * from ts_layanan_detail where row_id_header = ? AND status_layanan_detail = ?', [$id_header, 'OPN']);
        $status_layanan_header = 2;
        $status_retur = 'OPN';
        if ($header[0]->kode_tipe_transaksi == 1) {
            $tagihan_penjamin = 0;
            $tagihan_pribadi = $total_header;
        } else {
            $tagihan_pribadi = 0;
            $tagihan_penjamin = $total_header;
        }

        $total_retur = $detail[0]->grantotal_layanan;
        if (count($cek_detail) <= 1) {
            $total_retur = $detail[0]->grantotal_layanan + 1000;
            $update_layanan_detail_3 = [
                'status_layanan_detail' => 'CLS',
                'jumlah_retur' => 1,
                'tagihan_penjamin' => 0,
                'tagihan_pribadi' => 0,
            ];
            ts_layanan_detail_dummy::where('row_id_header', $id_header)->where('kode_tarif_detail', 'TX23523')
                ->update($update_layanan_detail_3);
            $total_header =  $total_header - 1000;
            $status_layanan_header = 3;
            $status_retur = 'CCL';
            if ($header[0]->kode_tipe_transaksi == 1) {
                $tagihan_penjamin = 0;
                $tagihan_pribadi = $total_header;
            } else {
                $tagihan_pribadi = 0;
                $tagihan_penjamin = $total_header;
            }
        }
        $kode_retur_header = $this->get_kode_retur_header();
        $ts_retur_header = [
            'kode_kunjungan' => $kode_kunjungan,
            'kode_retur_header' => $kode_retur_header,
            'kode_layanan_header' => $header[0]->kode_layanan_header,
            'tgl_retur' => $this->get_now(),
            'total_retur' => $total_retur,
            'alasan_retur' => 'RETUR',
            'status_retur' => 'CLS',
            'pic' => auth()->user()->id_simrs,
            'status_pembayaran' => 'CLS',
        ];
        $rh = ts_retur_header_dummy::create($ts_retur_header);
        $row_id_h_ret = $rh->id;
        $tgl_Ret = $this->get_now();
        $KODE_DET_RET = $this->get_kode_retur_detail();
        $ts_retur_detail_1 = [
            'kode_retur_detail' => $KODE_DET_RET,
            'tgl_retur_detail' => $tgl_Ret,
            'kode_retur_header' => $kode_retur_header,
            'id_layanan_detail' => '',
            'qty_awal' =>  $detail[0]->jumlah_layanan,
            'qty_retur' =>  $detail[0]->jumlah_layanan,
            'qty_sisa' => 0,
            'tarif_layanan' =>  $detail[0]->total_layanan,
            'total_retur_detail' => $detail[0]->grantotal_layanan,
            'status_retur_detail' => 'CLS',
            'row_id_header' => $row_id_h_ret,
        ];
        ts_retur_detail_dummy::create($ts_retur_detail_1);
        $ts_retur_detail_2 = [
            'kode_retur_detail' => $this->get_kode_retur_detail(),
            'tgl_retur_detail' => $tgl_Ret,
            'kode_retur_header' => $kode_retur_header,
            'id_layanan_detail' => '',
            'qty_awal' => 1,
            'qty_retur' => '1',
            'qty_sisa' => '0',
            'tarif_layanan' => '1700',
            'total_retur_detail' => '1700',
            'status_retur_detail' => 'CLS',
            'row_id_header' => $row_id_h_ret,
        ];
        ts_retur_detail_dummy::create($ts_retur_detail_2);
        if (count($cek_detail) <= 1) {
            $ts_retur_detail_3 = [
                'kode_retur_detail' =>  $this->get_kode_retur_detail(),
                'tgl_retur_detail' => $tgl_Ret,
                'kode_retur_header' => $kode_retur_header,
                'id_layanan_detail' => '',
                'qty_awal' => 1,
                'qty_retur' => '1',
                'qty_sisa' => '0',
                'tarif_layanan' => '1000',
                'total_retur_detail' => '1000',
                'status_retur_detail' => 'CLS',
                'row_id_header' => $row_id_h_ret,
            ];
            ts_retur_detail_dummy::create($ts_retur_detail_3);
        }
        $update_header = [
            'status_layanan' => $status_layanan_header,
            'status_retur' => $status_retur,
            'tagihan_penjamin' => $tagihan_penjamin,
            'tagihan_pribadi' => $tagihan_pribadi,
        ];
        ts_layanan_header_dummy::where('id', $id_header)->update($update_header);
        $UNIT =  $header[0]->kode_unit;
        $KODEBARANG =  $detail[0]->kode_barang;
        $stok_in =  $detail[0]->jumlah_layanan;
        $cek_stok = db::select('SELECT * FROM ti_kartu_stok WHERE NO = ( SELECT MAX(a.no ) AS nomor FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? )', ([$KODEBARANG, $UNIT]));
        $stok_current = $cek_stok[0]->stok_current + $stok_in;
        $stok_out = 0;
        $stok_last = $cek_stok[0]->stok_current;
        $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$KODEBARANG]);
        $data_kunjungan = DB::select('select *,fc_nama_px(no_rm) AS nama_pasien,fc_alamat(no_rm) AS alamat_pasien from ts_kunjungan where kode_kunjungan = ?', [$header[0]->kode_kunjungan]);
        $ti_kartu_stok = [
            'no_dokumen' => $kode_retur_header,
            'no_dokumen_detail' => $KODE_DET_RET,
            'tgl_stok' => $this->get_now(),
            'kode_unit' => $this->get_unit_login(),
            'kode_barang' => $KODEBARANG,
            'stok_last' => $stok_last,
            'stok_in' => $stok_in,
            'stok_current' => $stok_current,
            'harga_beli' => $mt_barang[0]->hna,
            'inputby' => '1',
            'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien
        ];
        $insert_ti_kartu_stok = ti_kartu_stok::create($ti_kartu_stok);
        $data = [
            'kode' => 200,
            'message' => 'Sukses',
        ];
        echo json_encode($data);
    }
    public function cetaknotafarmasi($id)
    {
        $get_header = DB::connection('mysql2')->select('select *,fc_NAMA_USER(pic) as nama_user from ts_layanan_header where id = ?', [$id]);
        $dtpx = DB::select('SELECT counter,no_rm,fc_nama_px(no_rm) AS nama, fc_umur(no_rm) AS umur,DATE(fc_tgl_lahir(no_rm)) AS tgl_lahir,fc_alamat(no_rm) AS alamat,fc_NAMA_PENJAMIN2(kode_penjamin) as nama_penjamin,fc_nama_unit1(kode_unit) as unit,fc_nama_paramedis(kode_paramedis) as dokter,kode_penjamin FROM ts_kunjungan WHERE kode_kunjungan = ?', [$get_header[0]->kode_kunjungan]);
        $get_detail = DB::connection('mysql2')->select('SELECT a.kode_tarif_detail,a.kode_barang,b.`nama_barang`,a.jumlah_layanan,a.jumlah_retur,a.tagihan_pribadi,a.tagihan_penjamin FROM ts_layanan_detail a
        LEFT OUTER JOIN mt_barang b ON a.`kode_barang` = b.`kode_barang`
        WHERE a.row_id_header = ?', [$id]);
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
            if ($d->nama_barang != '') {
                if ($dtpx[0]->kode_penjamin == 'P01') {
                    $jumlah = $d->tagihan_pribadi;
                } else {
                    $jumlah = $d->tagihan_penjamin;
                }
                $qty = $d->jumlah_layanan - $d->jumlah_retur;
                if ($qty > 0) {
                    $pdf->SetXY(0.5, $y7);
                    $pdf->MultiCell(7, 0.4, $d->nama_barang);
                    $pdf->SetXY(7.3, $y7);
                    $pdf->Cell(10, 0.5, $qty, 0, 1);
                    $pdf->SetXY(8.5, $y7);
                    $pdf->Cell(10, 0.5, number_format($jumlah, 2), 0, 1);
                    $y7 = $y7 + 0.4;
                    $total_item = $total_item + 1;
                    $subtotal = $subtotal + $jumlah;
                }
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
        $y8 = $pdf->GetY() + 0.3;
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
    public function CetakEtiket($id)
    {
        $get_header = DB::connection('mysql2')->select('select * from ts_layanan_header where id = ?', [$id]);
        $dtpx = DB::select('SELECT no_rm,fc_nama_px(no_rm) AS nama, fc_umur(no_rm) AS umur,DATE(fc_tgl_lahir(no_rm)) AS tgl_lahir,fc_alamat(no_rm) AS alamat FROM ts_kunjungan WHERE kode_kunjungan = ?', [$get_header[0]->kode_kunjungan]);
        $get_detail = DB::connection('mysql2')->select('SELECT a.kode_barang,b.`nama_barang`,a.aturan_pakai,a.`ed_obat`,a.jumlah_layanan,a.jumlah_retur FROM ts_layanan_detail a
        LEFT OUTER JOIN mt_barang b ON a.`kode_barang` = b.`kode_barang`
        WHERE a.row_id_header = ?', [$id]);
        // $pdf = new PDF('P', 'in', array('1.97', '2.36'));
        $pdf = new PDF('P', 'in', array('1.97', '2.36'));
        $i = $pdf->GetY();
        // $pdf->AliasNbPages();
        // $pdf->AddPage();
        $pdf->SetTitle('Cetak Etiket');
        $pdf->SetFont('Arial', 'B', 8);
        foreach ($get_detail as $d) {
            $total_barang = $d->jumlah_layanan - $d->jumlah_retur;
            if ($d->kode_barang != '' && $total_barang > 0) {
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
                $pdf->MultiCell(1.8, 0.10, $d->nama_barang);
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
    public function get_unit_login()
    {
        $kodeunit = auth()->user()->unit;
        return $kodeunit;
    }
    public function ambil_master_barang()
    {
        $mt_barang = DB::select('select * from mt_barang');
        return view('gudang.tabel_barang', compact([
            'mt_barang'
        ]));
    }
    public function ambil_detail_stok(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $detail = DB::select('SELECT a.no,tgl_stok,fc_nama_barang(a.`kode_barang`) AS nama_barang,fc_nama_unit1(a.kode_unit) AS nama_unit,keterangan,stok_last,stok_in,stok_out,stok_current
        FROM ti_kartu_stok a WHERE kode_barang = ? AND kode_unit = ? ORDER BY a.no DESC LIMIT 10', [$kode_barang, auth()->user()->unit]);
        return view('gudang.tabel_stok', compact([
            'detail'
        ]));
    }
}
