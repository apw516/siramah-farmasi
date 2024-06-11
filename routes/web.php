<?php

use App\Http\Controllers\antrianController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelayananController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\V2pelayananController;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('dashboard');
// });

Route::get('/', [AuthController::class, 'Index'])->name('index');
Route::get('/login', [AuthController::class, 'index'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard', [DashboardController::class, 'Index'])->name('dashboard');
Route::get('layananresep', [PelayananController::class, 'IndexLayananResep'])->name('layananresep');
Route::get('kartustok', [PelayananController::class, 'Indexkartustok'])->name('kartustok');
Route::post('ambil_orderan_poli', [PelayananController::class, 'AmbilOrderanPoli'])->name('ambil_orderan_poli');
Route::post('ambil_form_pencarian_pasien', [PelayananController::class, 'FormPencarianPasien'])->name('ambil_form_pencarian_pasien');
Route::post('ambil_data_pencarian_pasien', [PelayananController::class, 'AmbilDataPencarianPasien'])->name('ambil_data_pencarian_pasien');
Route::post('ambil_form_resep', [PelayananController::class, 'AmbilFormResep'])->name('ambil_form_resep');
Route::post('ambil_data_order_poli', [PelayananController::class, 'AmbilDataOrderPoli'])->name('ambil_data_order_poli');
Route::post('cari_obat_reguler', [PelayananController::class, 'cari_obat_reguler'])->name('cari_obat_reguler');
Route::post('cari_obat_racik', [PelayananController::class, 'cari_obat_racik'])->name('cari_obat_racik');
Route::post('jumlah_grand_total_obat_reguler', [PelayananController::class, 'jumlah_grand_total_obat_reguler'])->name('jumlah_grand_total_obat_reguler');
Route::post('jumlah_grand_total_racikan', [PelayananController::class, 'jumlah_grand_total_racikan'])->name('jumlah_grand_total_racikan');
Route::post('minus_grand_total', [PelayananController::class, 'minus_grand_total_reguler'])->name('minus_grand_total');
Route::post('minus_grand_total_racikan', [PelayananController::class, 'minus_grand_total_racikan'])->name('minus_grand_total_racikan');
Route::post('simpanorderan_far', [PelayananController::class, 'simpanorderan_far_reguler'])->name('simpanorderan_far');
Route::post('simpanorderan_far_racik', [PelayananController::class, 'simpanorderan_far_racik'])->name('simpanorderan_far_racik');
Route::post('riwayat_obat_hari_ini', [PelayananController::class, 'riwayat_obat_hari_ini'])->name('riwayat_obat_hari_ini');
Route::post('hitunganracikan', [PelayananController::class, 'hitunganracikan'])->name('hitunganracikan');
Route::post('post_komponen_racik', [PelayananController::class, 'post_komponen_racik'])->name('post_komponen_racik');
Route::post('jumlah_grand_total_komponen_racikan', [PelayananController::class, 'jumlah_grand_total_komponen_racikan'])->name('jumlah_grand_total_komponen_racikan');
Route::post('detail_obat_racik', [PelayananController::class, 'detail_obat_racik'])->name('detail_obat_racik');
Route::post('edit_aturan_pakai', [PelayananController::class, 'edit_aturan_pakai'])->name('edit_aturan_pakai');
Route::post('simpanedit_aturanpakai', [PelayananController::class, 'simpanedit_aturanpakai'])->name('simpanedit_aturanpakai');
Route::post('retur_obat', [PelayananController::class, 'retur_obat'])->name('retur_obat');
Route::post('cek_obat_hibah', [PelayananController::class, 'cek_obat_hibah'])->name('cek_obat_hibah');
Route::post('ambil_riwayat_resep', [PelayananController::class, 'ambil_riwayat_resep'])->name('ambil_riwayat_resep');
Route::post('detail_resep_obat', [PelayananController::class, 'detail_resep_obat'])->name('detail_resep_obat');
Route::get('cetaknota/{id}', [PelayananController::class, 'cetaknotafarmasi']); //formpasien_bpjs
Route::get('cetaketiket/{id}', [PelayananController::class, 'CetakEtiket']); //formpasien_bpjs

Route::post('ambil_master_barang', [PelayananController::class, 'ambil_master_barang'])->name('ambil_master_barang');
Route::post('ambil_detail_stok', [PelayananController::class, 'ambil_detail_stok'])->name('ambil_detail_stok');


// v2
Route::get('layananresep', [V2pelayananController::class, 'IndexLayananResep'])->name('layananresep2');
Route::post('ambil_antrian_non_racikan', [V2pelayananController::class, 'ambil_antrian_non_racikan'])->name('ambil_antrian_non_racikan');
Route::post('ambil_antrian_racikan', [V2pelayananController::class, 'ambil_antrian_racikan'])->name('ambil_antrian_racikan');
Route::post('tampildatapasien', [V2pelayananController::class, 'tampildatapasien'])->name('tampildatapasien');
Route::post('ambil_detail_orderan', [V2pelayananController::class, 'ambil_detail_orderan'])->name('ambil_detail_orderan');
Route::post('simpan_pelayanan_resep_reguler', [V2pelayananController::class, 'simpan_pelayanan_resep_reguler'])->name('simpan_pelayanan_resep_reguler');
Route::post('riwayat_obat_hari_ini', [V2pelayananController::class, 'riwayat_obat_hari_ini'])->name('riwayat_obat_hari_ini');
Route::post('cari_obat_reguler2', [V2pelayananController::class, 'cari_obat_reguler'])->name('cari_obat_reguler2');
Route::post('cari_obat_komponen_racik', [V2pelayananController::class, 'cari_obat_komponen_racik'])->name('cari_obat_komponen_racik');
Route::post('v2_add_draft_komponen', [V2pelayananController::class, 'add_draft_komponen'])->name('v2_add_draft_komponen');

Route::get('cetaketiket_2_all/{id}', [V2pelayananController::class, 'cetaketiket_2_all']); //formpasien_bpjs
Route::get('cetaketiket_2/{id}', [V2pelayananController::class, 'CetakEtiket']); //formpasien_bpjs
Route::get('cetaknotafarmasi_2/{id}/{kodeheader}', [V2pelayananController::class, 'cetaknota_new']); //formpasien_bpjs

// cari_obat_reguler2

//antrian
Route::get('/antrian', [antrianController::class, 'Index'])->name('index');
Route::post('/ambilantrian', [antrianController::class, 'ambilantrian'])->name('ambilantrian');

Route::get('datapemakaianobat', [ReportingController::class, 'index'])->name('datapemakaianobat');
Route::get('ambil_data_pemakaian', [ReportingController::class, 'ambil_data_pemakaian'])->name('ambil_data_pemakaian');
