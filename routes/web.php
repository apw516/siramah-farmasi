<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelayananController;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('dashboard');
// });

Route::get('/', [DashboardController::class, 'Index'])->name('dashboard');
Route::get('layananresep', [PelayananController::class, 'IndexLayananResep'])->name('layananresep');
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
Route::get('cetaketiket/{id}', [PelayananController::class, 'cetaknotafarmasi_2']); //formpasien_bpjs