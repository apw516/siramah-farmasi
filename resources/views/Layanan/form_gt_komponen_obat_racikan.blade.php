<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Total Layanan</label>
            <input type="email" class="form-control" id="gt_total_layanan_komponen_racikan_v" name="gt_total_layanan_komponen_racikan_v" value="IDR {{ number_format($new_gt,2) }}" placeholder="name@example.com">
            <input hidden type="email" class="form-control" id="gt_total_layanan_komponen_racikan" name="gt_total_layanan_komponen_racikan" value="{{ $new_gt }}" placeholder="name@example.com">
          </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Jasa Resep</label>
            <input type="email" class="form-control" id="jasa_resep_komponen_racik" name="jasa_resep_komponen_racik" value="IDR {{ number_format($jasa_resep,2) }}" placeholder="name@example.com">
          </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Jasa Racik</label>
            <input type="email" class="form-control" id="jasa_racik_komponen_racik" name="jasa_racik_komponen_racik" value="IDR {{ number_format($jasa_racik,2) }}" placeholder="name@example.com">
            <input hidden type="email" class="form-control" id="jasa_racik_komponen_racik_b" name="jasa_racik_komponen_racik_b" value="{{ $jasa_racik }}" placeholder="name@example.com">
          </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Grand total</label>
            <input type="email" class="form-control" id="gt_total_layanan_komponen_racik" name="gt_total_layanan_komponen_racik" value="IDR {{ number_format($gt_all,2) }}" placeholder="name@example.com">
            <input hidden type="email" class="form-control" id="gt_total_layanan_komponen_racik_b" name="gt_total_layanan_komponen_racik_b" value="{{ $gt_all }} " placeholder="name@example.com">
          </div>
    </div>
</div>
