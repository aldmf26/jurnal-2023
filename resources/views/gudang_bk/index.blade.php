<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">

    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} </h6>
            </div>
            <div class="col-lg-6">
                @if (auth()->user()->posisi_id == '1')
                    <x-theme.button modal="Y" idModal="import" icon="fas fa-upload" addClass="float-end"
                        teks="Import" />
                    <x-theme.button modal="Y" idModal="import2" icon="fas fa-upload" addClass="float-end"
                        teks="Import G Gabung" />
                    <form action="{{ route('gudangBk.export_buku_campur_bk') }}" method="post">
                        @csrf
                        <button class="btn btn-success float-end me-2"><i class="fas fa-file-excel"></i> Export</button>
                    </form>
                    <form action="{{ route('gudangBk.export_gudang_produksi') }}" method="post">
                        @csrf
                        <button class="btn btn-success float-end me-2"><i class="fas fa-file-excel"></i> Export
                            G Gabung</button>
                    </form>
                @else
                    <x-theme.button modal="Y" idModal="import2" icon="fas fa-upload" addClass="float-end"
                        teks="Import G Gabung" />
                    <form action="{{ route('gudangBk.export_gudang_produksi') }}" method="post">
                        @csrf
                        <button class="btn btn-success float-end me-2"><i class="fas fa-file-excel"></i> Export
                        </button>
                    </form>
                @endif


            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        {{-- <form action="{{ route('gudangBk.export_buku_campur_bk') }}" method="post"> --}}
        @csrf
        <section class="row">

            <div class="col-lg-9">
                @include('gudang_bk.nav')
            </div>
            {{-- <div class="col-lg-3">
                    <table class="float-end">
                        <td>Search :</td>
                        <td><input type="text" id="pencarian" class="form-control float-end"></td>
                    </table>
                </div> --}}
            <div class="col-lg-12 mt-2">

                <table class="table table-hover table-bordered" id="table" width="100%">
                    <thead>
                        @php
                            $ttlRp = 0;
                            $pcs = 0;
                            $gr = 0;
                            foreach ($gudang as $g) {
                                $pcs += $g->pcs;
                                $gr += $g->gr;
                                $ttlRp += $g->rupiah * $g->gr;
                            }
                        @endphp
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">ID</th>
                            <th class="dhead">Buku</th>

                            <th class="dhead">Date</th>
                            <th class="dhead">Grade</th>
                            <th class="dhead text-end">Pcs <br> {{ number_format($pcs, 0) }}</th>
                            <th class="dhead text-end">Gram <br> {{ number_format($gr, 0) }}</th>
                            @if ($presiden)
                                <th class="dhead">Rp/Gr</th>
                            @endif
                            <th class="dhead">Lot</th>
                            <th class="dhead">Keterangan / Nama Partai Herry</th>
                            <th class="dhead">Keterangan / Nama Partai Sinta</th>
                            @if ($presiden)
                                <th class="dhead text-end">Ttl Rp <br> {{ number_format($ttlRp, 0) }}</th>
                            @endif
                            <th class="dhead">Lok</th>
                            {{-- <th class="dhead text-center">
                                @if ($nm_gudang == 'produksi' || $nm_gudang == 'wip')
                                    <button type="submit" name="submit" value="export_produksi"
                                        class="badge bg-success"><i class="fas fa-file-excel"></i></button>
                                @else
                                    <button type="submit" name="submit" value="export" class="badge bg-success"><i
                                            class="fas fa-file-excel"></i></button>
                                @endif
                                <br>

                                <input type="hidden" name="gudang" value="{{ $nm_gudang }}" id="">
                            </th> --}}


                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gudang as $no => $g)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $g->id_buku_campur }}</td>
                                <td>{{ $g->buku }}</td>
                                {{-- <td>{{ $g->suplier_awal }} </td> --}}
                                <td>{{ $g->tgl }}</td>
                                <td>{{ $g->nm_grade }}</td>
                                <td class="text-end">{{ number_format($g->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($g->gr, 0) }}</td>
                                @if ($presiden)
                                    <td class="text-end">{{ number_format($g->rupiah, 0) }}</td>
                                @endif
                                <td>{{ $g->no_lot }}</td>
                                <td>{{ $g->ket }}</td>
                                <td>{{ $g->ket2 }}</td>
                                @if ($presiden)
                                    <td class="text-end">{{ number_format($g->rupiah * $g->gr, 0) }}</td>
                                @endif
                                <td>{{ $g->lok_tgl }}</td>


                                {{-- <td class="text-center">
                                    <input type="checkbox" class="checkbox-item" name="id_buku_campur[]"
                                        value="{{ $g->id_buku_campur }}" id="">
                                </td> --}}


                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </section>
        {{-- </form> --}}
        <form action="{{ route('gudangBk.import_buku_campur_bk') }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-theme.modal title="Gudang Bk" idModal="import" btnSave="Y">
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">File</label>
                        <input type="file" class="form-control" name="file">
                        <input type="hidden" name="gudang" value="{{ $nm_gudang }}" id="">
                    </div>
                </div>

            </x-theme.modal>
        </form>
        <form action="{{ route('gudangBk.import_gudang_produksi_new') }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-theme.modal title="Gudang Gabung" idModal="import2" btnSave="Y">
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">File</label>
                        <input type="file" class="form-control" name="file">
                        <input type="hidden" name="gudang" value="{{ $nm_gudang }}" id="">
                    </div>
                </div>

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
            });
        </script>
    @endsection
</x-theme.app>
