<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">

    <x-slot name="cardHeader">
        <div class="row justify-content-end">

            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6>
            </div>
            <div class="col-lg-6">

            </div>

        </div>

    </x-slot>
    <style>
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #000000;
            line-height: 36px;
            /* font-size: 12px; */
            width: 150px;

        }
    </style>

    <x-slot name="cardBody">
        <form action="{{ route('dashboard_kandang.save_edit_telur') }}" method="post" class="save_jurnal">
            @csrf
            <section class="row">

                <div class="col-lg-8">
                    <table class="table">
                        <tr>
                            <th class="dhead">Tanggal</th>
                            <th class="dhead">Nota</th>
                        </tr>
                        <tr>
                            <td>
                                <input type="date" class="form-control" name="tgl" value="{{ $telur->tgl }}">
                            </td>
                            <td>
                                <input type="text" readonly class="form-control" name="nota" value="{{ $telur->nota_transfer }}">
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-12">
                    <hr style="border: 1px solid black">
                </div>
                <div class="col-lg-12">
                    <div class="row">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead" width="20%">Produk</th>
                                    <th class="dhead" style="text-align: right" width="15%">Pcs</th>
                                    <th class="dhead" style="text-align: right" width="15%">Kg</th>
                                    <th class="dhead" style="text-align: right" width="15%">Ikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>

                                    <td>
                                        <select name="id_produk_telur" id="" class="select2_add">
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produk as $p)
                                                <option value="{{ $p->id_produk_telur }}"
                                                    {{ $telur->id_telur == $p->id_produk_telur ? 'selected' : '' }}>
                                                    {{ $p->nm_telur }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="pcs" class="form-control pcs"
                                            style="text-align: right" value="{{ $telur->pcs_kredit }}">
                                    </td>
                                    <td><input type="text" style="text-align: right" name="kg"
                                            class="form-control" value="{{ $telur->kg_kredit }}">
                                        <input type="hidden" name="id_stok_telur" value="{{ $telur->id_stok_telur }}">
                                    </td>
                                    <td align="right" class="ikat">{{ number_format($telur->pcs_kredit / 180, 2) }}</td>

                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

            </section>

    </x-slot>
    <x-slot name="cardFooter">
        <button type="submit" class="float-end btn btn-primary button-save">Simpan</button>
        <button class="float-end btn btn-primary btn_save_loading" type="button" disabled hidden>
            <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
            Loading...
        </button>
        <a href="{{ route('stok_telur') }}" class="float-end btn btn-outline-primary me-2">Batal</a>
        </form>
    </x-slot>



    @section('scripts')
        <script>
            $(document).on("keyup", ".pcs", function() {
                var pcs = $('.pcs').val()
                var ikat = parseFloat(pcs) / 180;
                $('.ikat').text(ikat.toFixed(1));
            });
            aksiBtn("form");
        </script>
    @endsection
</x-theme.app>
