<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Total Komponen</label>
            <input type="email" class="form-control" id="gt_total_komponen" name="gt_total_komponen" value="{{ $new_total_komponen }}" placeholder="name@example.com">
          </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Total Layanan</label>
            <input type="email" class="form-control" id="gt_total_layanan_racikan_v" name="gt_total_layanan_racikan_v" value="IDR {{ number_format($new_total_layanan,2) }}" placeholder="name@example.com">
            <input hidden type="email" class="form-control" id="gt_total_layanan_racikan" name="gt_total_layanan_racikan" value="{{ $new_total_layanan }}" placeholder="name@example.com">
          </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Jasa Resep</label>
            <input type="email" class="form-control" id="jasa_resep_racik" name="jasa_resep_racik" value="IDR {{ number_format($jasaresep,2) }}" placeholder="name@example.com">
          </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Jasa Embalase</label>
            <input type="email" class="form-control" id="jasa_embalase_racik" name="jasa_embalase_racik" value=" IDR {{ number_format($jasaembalase,2) }}" placeholder="name@example.com">
          </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Grand total</label>
            <input type="email" class="form-control" id="grand_total_layanan_racik" name="grand_total_layanan_racik" value="IDR {{ number_format($grandtotal,2) }}" placeholder="name@example.com">
            <input hidden type="email" class="form-control" id="grand_total_layanan_racik_b" name="grand_total_layanan_racik_b" value="{{ $grandtotal }}" placeholder="name@example.com">
          </div>
    </div>
</div>
