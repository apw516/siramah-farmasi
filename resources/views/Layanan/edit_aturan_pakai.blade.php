<form class="formedit_aturanpakai">
    <div class="form-group">
        <label for="exampleInputEmail1">Nama Barang</label>
        <input readonly type="text" class="form-control" id="namabarang" name="namabarang" aria-describedby="emailHelp"
            value="{{ $namabarang }}">
        <input hidden type="text" class="form-control" id="iddetail" name="iddetail" aria-describedby="emailHelp"
            value="{{ $id }}">
    </div>
    <div class="form-group">
        <label for="exampleInputPassword1">Aturan Pakai</label>
        <textarea type="text" class="form-control" id="aturanpakai" name="aturanpakai">{{ $aturanpakai }}</textarea>
    </div>
</form>
