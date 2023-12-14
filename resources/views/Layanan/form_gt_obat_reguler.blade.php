<div class="row">
    <div class="col-md-1">
        <div class="form-group">
            <label for="exampleFormControlInput1">Total Item</label>
            <input type="email" class="form-control" id="gt_total_item" name="gt_total_item" value="{{ $total_item }}" placeholder="name@example.com">
          </div>
    </div>
    <div class="col-md-1">
        <div class="form-group">
            <label for="exampleFormControlInput1">Total Resep</label>
            <input type="email" class="form-control" id="gt_total_resep" name="gt_total_resep" value="{{ $total_resep }}" placeholder="name@example.com">
            <input type="email"  hidden class="form-control" id="resep_kronis" name="resep_kronis" value="{{ $jlh_kronis }}" placeholder="name@example.com">
            <input type="email" hidden  class="form-control" id="resep_hibah" name="resep_hibah" value="{{ $jlh_hibah }}" placeholder="name@example.com">
            <input type="email"  hidden class="form-control" id="resep_reguler" name="resep_reguler" value="{{ $jlh_reguler }}" placeholder="name@example.com">
            <input type="email"  hidden class="form-control" id="resep_kemo" name="resep_kemo" value="{{ $jlh_kemo }}" placeholder="name@example.com">
          </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Total Layanan</label>
            <input type="email" class="form-control" id="gt_total_layanan_reguler_v" name="gt_total_layanan_reguler_v" value="IDR {{ number_format($total_layanan,2) }}" placeholder="name@example.com">
            <input hidden type="email" class="form-control" id="gt_total_layanan_reguler" name="gt_total_layanan_reguler" value="{{ $total_layanan }}" placeholder="name@example.com">
          </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Jasa Baca</label>
            <input type="email" class="form-control" id="jasa_baca_reguler" name="jasa_baca_reguler" value="IDR {{ number_format($jasa_baca,2)}}" placeholder="name@example.com">
          </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Embalase</label>
            <input type="email" class="form-control" id="gt_total_layanan_reguler" name="gt_total_layanan_reguler" value="IDR {{ number_format($jasa_embalase,2)}} " placeholder="name@example.com">
          </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Jasa Resep</label>
            <input type="email" class="form-control" id="gt_total_layanan_reguler" name="gt_total_layanan_reguler" value="IDR {{ number_format($jasa_resep,2)}} " placeholder="name@example.com">
          </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="exampleFormControlInput1">Grand total</label>
            {{-- <input type="email" class="form-control" id="gt_total_layanan_reguler" name="gt_total_layanan_reguler" value="IDR {{ number_format($grandtotal,2)}} " placeholder="name@example.com"> --}}
          </div>
    </div>
</div>
