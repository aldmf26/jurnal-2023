<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">

    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} </h6>
            </div>
            <div class="col-lg-6">

                <x-theme.button modal="Y" idModal="import2" icon="fas fa-upload" addClass="float-end"
                    teks="Import" />
                <form action="{{ route('gudangBk.export_gudang_produksi') }}" method="post">
                    @csrf
                    <button class="btn btn-success float-end me-2"><i class="fas fa-file-excel"></i> Export</button>
                </form>
                <x-theme.button modal="Y" idModal="tambah" icon="fas fa-plus" addClass="float-end"
                    teks="Tambah BK" />
            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        {{-- <form action="{{ route('gudangBk.export_buku_campur_bk') }}" method="post"> --}}
        @csrf
        <section class="row">

            {{-- <div class="col-lg-9">
                @include('gudangnew.nav')
            </div> --}}
            {{-- <div class="col-lg-3">
                    <table class="float-end">
                        <td>Search :</td>
                        <td><input type="text" id="pencarian" class="form-control float-end"></td>
                    </table>
                </div> --}}
            <div class="col-lg-12 mt-2">

                <table class="table table-hover table-bordered" id="table" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Ket/Nama Partai</th>
                            <th>Grade</th>
                            <th class="text-end">Pcs bk</th>
                            <th class="text-end">Gr bk</th>
                            <th class="text-end">Cost bk</th>
                            <th class="text-end">Pcs timbang ulang</th>
                            <th class="text-end">Gr timbang ulang</th>
                            <th class="text-end">Pcs Susut</th>
                            <th class="text-end">Gr Susut</th>
                            <th class="text-end bg-danger">Pcs Sisa</th>
                            <th class="text-end bg-danger">Gr Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gudang as $g)
                            @php
                                $ket = $g->ket2;
                                $resSum = Cache::remember(
                                    'datacabutsum5_' . $ket,
                                    now()->addMinutes(5),
                                    function () use ($ket, $linkApi) {
                                        return Http::get("$linkApi/datacabutsum2", ['nm_partai' => $ket])->object();
                                    },
                                );
                                $c = $resSum;
                                $g->relatedModel = $c;

                                $wipPcs = $g->pcs ?? 0;
                                $wipGr = $g->gr ?? 0;

                                $bkPcs = $c->pcs_bk ?? 0;
                                $bkGr = $c->gr_awal_bk ?? 0;
                                $gr_susut = $g->gr_susut ?? 0;
                                $pcs_susut = $g->pcs_susut ?? 0;
                                $WipSisaPcs = $wipPcs - $bkPcs - $pcs_susut;
                                $WipSisaGr = $wipGr - $bkGr - $gr_susut;

                                if ($WipSisaPcs + $WipSisaGr == '0') {
                                    continue;
                                }

                                $no = 0;
                            @endphp
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $g->ket2 }}</td>
                                <td>{{ $g->nm_grade }}</td>
                                <td class="text-end">{{ number_format($g->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($g->gr, 0) }}</td>
                                <td class="text-end">{{ number_format($g->total_rp, 0) }}</td>
                                <td class="text-end">{{ number_format($bkPcs, 0) }}</td>
                                <td class="text-end">{{ number_format($bkGr, 0) }}</td>
                                <td class="text-end">
                                    <a href="#" class="partai fw-bold" partai="{{ $g->ket2 }}"
                                        data-bs-toggle="modal" data-bs-target="#susut">
                                        <u>{{ number_format($pcs_susut, 0) }}</u>
                                    </a>
                                </td>
                                <td class="text-end"><a href="#" class="partai fw-bold"
                                        partai="{{ $g->ket2 }}" data-bs-toggle="modal" data-bs-target="#susut">
                                        <u>{{ number_format($gr_susut, 0) }}</u>
                                    </a>
                                </td>
                                <td class="text-end">{{ number_format($WipSisaPcs, 0) }}</td>
                                <td class="text-end">{{ number_format($WipSisaGr, 0) }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </section>
        {{-- </form> --}}

        <form action="{{ route('gudangnew.import_buku_campur_produksi') }}" method="post"
            enctype="multipart/form-data">
            @csrf
            <x-theme.modal title="Gudang Gabung" idModal="import2" btnSave="Y">
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">File</label>
                        <input type="file" class="form-control" name="file">
                    </div>
                </div>
            </x-theme.modal>
        </form>

        <form action="{{ route('gudangnew.save_gudang_bk') }}" method="post">
            @csrf
            <x-theme.modal title="Tambah Bk Awal" size="modal-lg-max" idModal="tambah" btnSave="Y">
                <div class="row">
                    <input type="hidden" value="wip" name="gudang">
                    <input type="hidden" value="sinta" name="lokasi">
                    {{-- <div class="col-lg-2">
                        <label for="">Suplier Awal</label>
                        <input type="text" class="form-control" name="suplier_awal[]">
                    </div> --}}
                    <div class="col-lg-2">
                        <label for="">Date</label>
                        <input type="date" class="form-control" name="tgl[]">
                    </div>
                    <div class="col-lg-1">
                        <label for="">Grade</label>
                        <input type="text" class="form-control" name="grade[]">
                    </div>
                    <div class="col-lg-2">
                        <label for="">Pcs</label>
                        <input type="text" class="form-control" name="pcs[]">
                    </div>
                    <div class="col-lg-2">
                        <label for="">Gr</label>
                        <input type="text" class="form-control" name="gr[]">
                    </div>
                    <div class="col-lg-2">
                        <label for="">Rp/Gr</label>
                        <input type="text" class="form-control" name="rp_gram[]">
                    </div>
                    <div class="col-lg-1">
                        <label for="">Lot</label>
                        <input type="text" class="form-control" name="lot[]">
                    </div>
                    <div class="col-lg-2 mt-2">
                        <label for="">Nama Partai Herry
                        </label>
                        <input type="text" class="form-control" name="ket1[]">
                    </div>
                    <div class="col-lg-2 mt-2">
                        <label for="">Nama Partai Sinta</label>
                        <input type="text" class="form-control" name="ket2[]">
                    </div>

                </div>
                <div class="tambah-data"></div>
                <div class="col-lg-12 mt-2">
                    <button type="button" class="btn btn-block btn-lg tbh_baris"
                        style="background-color: #F4F7F9; color: #8FA8BD; font-size: 14px; padding: 13px;">
                        <i class="fas fa-plus"></i> Tambah Baris Baru
                    </button>
                </div>

            </x-theme.modal>
        </form>

        <form action="{{ route('gudangnew.save_susut') }}" method="post">
            @csrf
            <x-theme.modal title="Susut" idModal="susut" btnSave="Y" size="modal-lg">
                <div id="load_susut"></div>
            </x-theme.modal>
        </form>

    </x-slot>

    @section('scripts')
        <script>
            $(document).ready(function() {
                pencarian('pencarian', 'tableSearch')
                $(document).on('click', '#checkAll', function() {
                    // Setel properti checked dari kotak centang individu sesuai dengan status "cek semua"
                    $('.checkbox-item').prop('checked', $(this).prop('checked'));
                });

                var count = 3;
                $(document).on("click", ".tbh_baris", function() {
                    count = count + 1;
                    $.ajax({
                        url: "/gudangnew/tbh_baris?count=" + count,
                        type: "Get",
                        success: function(data) {
                            $(".tambah-data").append(data);
                        },
                    });
                });

                $(document).on("click", ".remove_baris", function() {
                    var delete_row = $(this).attr("count");
                    $(".baris" + delete_row).remove();
                });
                $(document).on("click", ".partai", function() {
                    var partai = $(this).attr("partai");
                    $.ajax({
                        type: "get",
                        url: "{{ route('gudangnew.get_susut') }}",
                        data: {
                            partai: partai,
                        },
                        success: function(response) {
                            $('#load_susut').html(response);
                        }
                    });
                });
            });
        </script>
    @endsection
</x-theme.app>
