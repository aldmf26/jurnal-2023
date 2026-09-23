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
        @csrf
        <section class="row">

            <div class="col-lg-9">
                @include('gudang_bk.nav')
            </div>
            <div class="col-lg-12 mt-2">

                <table class="table table-hover table-bordered" id="tableGudangBk" width="100%">
                    <thead>
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">ID</th>
                            <th class="dhead">Buku</th>
                            <th class="dhead">Date</th>
                            <th class="dhead">Grade</th>
                            <th class="dhead text-end">Pcs <br> {{ number_format($totalPcs, 0) }}</th>
                            <th class="dhead text-end">Gram <br> {{ number_format($totalGr, 0) }}</th>
                            @if ($presiden)
                                <th class="dhead">Rp/Gr</th>
                            @endif
                            <th class="dhead">Lot</th>
                            <th class="dhead">Keterangan / Nama Partai Herry</th>
                            <th class="dhead">Keterangan / Nama Partai Sinta</th>
                            @if ($presiden)
                                <th class="dhead text-end">Ttl Rp <br> {{ number_format($totalRp, 0) }}</th>
                            @endif
                            <th class="dhead">Lok</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div>

        </section>
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
                const presiden = {{ $presiden ? 'true' : 'false' }};
                const fmt = (v) => {
                    if (v === null || v === undefined || v === '') return '';
                    return Number(v).toLocaleString('id-ID', {
                        maximumFractionDigits: 0
                    });
                };
                const columns = [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: (d, t, r, meta) => meta.row + meta.settings._iDisplayStart + 1
                    },
                    {
                        data: 'id_buku_campur'
                    },
                    {
                        data: 'buku'
                    },
                    {
                        data: 'tgl'
                    },
                    {
                        data: 'nm_grade'
                    },
                    {
                        data: 'pcs',
                        className: 'text-end',
                        render: (d) => fmt(d)
                    },
                    {
                        data: 'gr',
                        className: 'text-end',
                        render: (d) => fmt(d)
                    },
                ];
                if (presiden) {
                    columns.push({
                        data: 'rupiah',
                        className: 'text-end',
                        render: (d) => fmt(d)
                    });
                }
                columns.push({
                    data: 'no_lot'
                }, {
                    data: 'ket'
                }, {
                    data: 'ket2'
                });
                if (presiden) {
                    columns.push({
                        data: null,
                        className: 'text-end',
                        orderable: false,
                        searchable: false,
                        render: (d, t, r) => fmt((Number(r.rupiah) || 0) * (Number(r.gr) || 0))
                    });
                }
                columns.push({
                    data: 'lok_tgl'
                });

                $('#tableGudangBk').DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: true,
                    pageLength: 25,
                    ajax: {
                        url: "{{ route('gudangBk.datatable') }}",
                        data: (d) => {
                            d.nm_gudang = "{{ $nm_gudang }}";
                        }
                    },
                    columns: columns,
                    order: [
                        [1, 'asc']
                    ],
                });
            });
        </script>
    @endsection
</x-theme.app>
