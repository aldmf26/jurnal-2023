<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} </h6>
            </div>
            <div class="col-lg-6">
                <x-theme.button modal="Y" idModal="tambah" teks="Tambah Data" icon="fa-plus" addClass="float-end" />
                <a href="{{ route('congan.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
                    class="btn  btn-success float-end me-2 icon icon-left"><i class="fas fa-file-excel"></i> Export</a>
                <x-theme.btn_filter title="Filter Pembelian Bk" />
            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        {{-- @include('pembelian_bk.nav') --}}

        @csrf
        {{-- @if (!empty($approve))
                <button class="float-end btn btn-primary btn-sm"><i class="fas fa-check"></i> Approve</button>
                <br>
                <br>
            @endif --}}
        <section class="row">
            <div class="col-lg-8"></div>
            <div class="col-lg-4 mb-2">
                <table class="float-end">
                    <td>Pencarian :</td>
                    <td><input type="text" id="pencarian" class="form-control float-end"></td>
                </table>

            </div>

            <table class="table table-hover table-bordered" id="tableSearch">
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-center">No Nota</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center">Harga Beli</th>
                        <th class="text-center">Harga (100%)</th>
                        <th class="text-center">Harga (%)</th>
                        <th class="text-center">GR</th>
                        @foreach ($grade as $g)
                            <th class="text-center">{{ $g->nm_grade }}(%)</th>
                        @endforeach
                        <th class="text-center">Air(%)</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($congan as $no => $c)
                        <tr style="text-align: center">
                            <td>{{ $no + 1 }}</td>
                            <td>
                                <a href="{{ route('congan.detail_nota', ['no_nota' => $c->no_nota]) }}">
                                    {{ $c->no_nota }}-{{ $c->ket }}
                                </a>
                            </td>
                            <td>{{ date('d M Y', strtotime($c->tgl)) }}</td>
                            <td>{{ $c->pemilik }}</td>
                            <td>{{ number_format($c->hrga_beli, 0) }}</td>
                            <td>{{ empty($c->gr) ? 0 : number_format(($c->ttl / $c->gr) * ((100 - $c->persen_air) / 100)) }}
                            </td>
                            <td>{{ empty($c->gr) ? 0 : number_format($c->ttl / $c->gr) }}</td>
                            <td>{{ number_format($c->gr, 0) }}</td>
                            @foreach ($grade as $g)
                                @php
                                    $persen = DB::selectOne(
                                        "SELECT a.gr  FROM tb_cong as a where a.no_nota = '$c->no_nota' and a.id_grade = '$g->id_grade_cong' and a.ket = '$c->ket'",
                                    );
                                @endphp
                                <td>{{ empty($persen->gr) ? '0' : number_format(($persen->gr / $c->gr) * 100, 2) }}
                                </td>
                            @endforeach
                            <td>{{ 100 - $c->persen_air }}</td>
                            <td style="white-space: nowrap">
                                @if (empty($c->no_invoice_bk))
                                    <a href="{{ route('congan.buat_nota', ['no_nota' => $c->no_nota]) }}"
                                        class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Nota</a>
                                @else
                                    <a href="{{ route('print_bk', ['no_nota' => $c->no_invoice_bk]) }}"
                                        class="btn btn-sm btn-success"><i class="fas fa-print"></i></a>
                                @endif
                                <a href="#" class="btn btn-sm btn-danger delete_nota"
                                    id_invoice_congan="{{ $c->id_invoice_congan }}" data-bs-toggle="modal"
                                    data-bs-target="#delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <style>
            .modal-lg-mix {
                max-width: 1300px;
            }
        </style>

        <form action="{{ route('congan.add_congan') }}" method="post">
            @csrf
            <x-theme.modal title="Tambah Data" idModal="tambah" size="modal-lg-mix">
                <div class="row">
                    <div class="col-lg-2 col-4">
                        <label for="">Tanggal</label>
                        <input type="date" class="form-control" name="tgl">
                    </div>
                    <div class="col-lg-2 col-4">
                        <label for="">Pemilik</label>
                        <input type="text" class="form-control" name="pemilik">
                    </div>
                    <div class="col-lg-2 col-4">
                        <label for="">Keterangan</label>
                        <input type="text" class="form-control" name="ket[]">
                    </div>
                    <div class="col-lg-2 col-4">
                        <label for="">Persen Air</label>
                        <input type="text" class="form-control" value="0" name="persen_air[]">
                    </div>
                </div>
                <div class="row">
                    <style>
                        @media only screen and (max-width: 767px) {

                            .label_hilang {
                                display: none
                            }
                        }
                    </style>
                    <div class="row  mt-4">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-6 col-4"><label for="">Grade</label></div>
                                <div class="col-lg-3 col-4"><label for="">Gr</label></div>
                                <div class="col-lg-3 col-4"><label for="">Harga</label></div>
                            </div>
                        </div>
                        <div class="col-lg-4 label_hilang">
                            <div class="row">
                                <div class="col-lg-6 col-4"><label for="">Grade</label></div>
                                <div class="col-lg-3 col-4"><label for="">Gr</label></div>
                                <div class="col-lg-3 col-4"><label for="">Harga</label></div>
                            </div>
                        </div>
                        <div class="col-lg-4 label_hilang">
                            <div class="row">
                                <div class="col-lg-6 col-4"><label for="">Grade</label></div>
                                <div class="col-lg-3 col-4"><label for="">Gr</label></div>
                                <div class="col-lg-3 col-4"><label for="">Harga</label></div>
                            </div>
                        </div>
                    </div>
                    @foreach ($grade as $g)
                        <div class="col-lg-4 mt-2" style="border-right: 1px solid black">
                            <div class="row">

                                <div class="col-lg-6 col-4">

                                    <input type="hidden" name="id_grade1[]" value="{{ $g->id_grade_cong }}">
                                    <input type="text" class="form-control" style="font-size: 12px"
                                        value="{{ $g->nm_grade }}" readonly>
                                </div>
                                <div class="col-lg-3 col-4">

                                    <input type="text" class="form-control gr gr1" count="1" value="0"
                                        name="gr1[]">
                                </div>
                                <div class="col-lg-3 col-4">

                                    <input type="text" class="form-control" value="0" name="harga1[]"
                                        readonly>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <br>
                <br>
                <div class="row">
                    <div class="col-lg-2">
                        <table style="padding: 10px">
                            <tr>
                                <td>
                                    <h6>Total Gram &nbsp;</h6>
                                </td>
                                <td><input type="text" class="form-control total_gram1" readonly value="0">
                                </td>

                            </tr>
                            <tr>
                                <td>
                                    <h6>Harga Beli &nbsp;</h6>
                                </td>
                                <td><input type="text" class="form-control hrga_beli" name="hrga_beli[]"
                                        value="0">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h6>Harga(100%) &nbsp;</h6>
                                </td>
                                <td><input type="text" class="form-control" readonly value="0"></td>
                            </tr>
                            <tr>
                                <td>
                                    <h6>Harga(%) &nbsp;</h6>
                                </td>
                                <td><input type="text" class="form-control" readonly></td>
                                <input type="hidden" name="count[]" value="1">
                            </tr>
                        </table>

                    </div>
                </div>
                <div class="load_row">

                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <button type="button" class="btn btn-success float-end tambah_row">Tambah Baris</button>
                    </div>
                </div>

            </x-theme.modal>
        </form>


        <form action="{{ route('congan.delete_nota') }}" method="get">
            <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <h5 class="text-danger ms-4 mt-4"><i class="fas fa-trash"></i> Hapus Data</h5>
                                <p class=" ms-4 mt-4">Apa anda yakin ingin menghapus ?</p>
                                <input type="hidden" class="id_invoice_congan" name="id_invoice_congan">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-danger"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>








    </x-slot>

    @section('scripts')
        <script>
            $(document).ready(function() {
                new DataTable('#tableSearch', {
                    "searching": false,
                    scrollY: '400px',
                    scrollX: '400px',
                    scrollCollapse: false,
                    "stateSave": true,
                    "autoWidth": true,
                    "paging": false,
                    "responsive": true
                });
                pencarian('pencarian', 'tableSearch')

                $(document).on('click', '.delete_nota', function() {
                    var id_invoice_congan = $(this).attr('id_invoice_congan');
                    $('.id_invoice_congan').val(id_invoice_congan);
                });

                $(document).on("keyup", ".gr", function() {
                    var count = $(this).attr('count');

                    var total = $('.gr' + count).toArray().reduce(function(acc, input) {
                        var value = parseFloat($(input).val()) || 0;
                        return acc + value;
                    }, 0);

                    $('.total_gram' + count).val(total);
                });


                var count = 3;
                $(document).on("click", ".tambah_row", function() {
                    count = count + 1;
                    $.ajax({
                        url: "/congan/load_row?count=" + count,
                        type: "Get",
                        success: function(data) {
                            $(".load_row").append(data);
                            $(".select").select2();
                        },
                    });
                });

                $(document).on("click", ".remove_baris", function() {
                    var delete_row = $(this).attr("count");
                    $(".baris" + delete_row).remove();
                });

            });
        </script>
    @endsection
</x-theme.app>
