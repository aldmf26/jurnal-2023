<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} : {{ tanggal($tgl1) }} ~ {{ tanggal($tgl2) }}</h6>
            </div>
            <div class="col-lg-6">

                @if (!empty($create))
                    <x-theme.button modal="T" href="#" icon="fa-plus" addClass="float-end buat_baru"
                        teks="Buat Baru" />
                @endif
                <x-theme.button modal="Y" idModal="import" icon="fas fa-upload" addClass="float-end"
                    teks="Import" />

                @if (!empty($export))
                    <x-theme.button modal="T" href="/export_bk?tgl1={{ $tgl1 }}&tgl2={{ $tgl2 }}"
                        icon="fa-file-excel" addClass="float-end float-end btn btn-success me-2" teks="Export" />
                @endif
                {{-- <x-theme.button modal="T" href="/export_bk_m?tgl1={{ $tgl1 }}&tgl2={{ $tgl2 }}"
                    icon="fa-file-excel" addClass="float-end float-end btn btn-success me-2" teks="Export M" /> --}}

                <x-theme.btn_filter title="Filter Pembelian Bk" />

                <x-theme.akses :halaman="$halaman" route="pembelian_bk" />
            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        {{-- @include('pembelian_bk.nav') --}}
        <form action="{{ route('approve_invoice_bk') }}" method="post">
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
                <table class="table table-hover " id="tableSearch" width="100%">
                    <thead>
                        <tr>
                            <th class="dhead text-center" rowspan="2" width="5">#</th>
                            <th class="dhead text-center" rowspan="2">Tanggal</th>
                            <th class="dhead text-center" rowspan="2">No Nota</th>
                            <th class="dhead text-center" rowspan="2">No Lot</th>
                            <th class="dhead text-center" rowspan="2">Suplier Awal</th>
                            <th class="dhead text-center" rowspan="2">Suplier Akhir</th>
                            <th class="dhead" rowspan="2" style="text-align: right">Total Harga</th>
                            <th class="dhead" colspan="5" style="text-align: center">Status</th>

                            {{-- @if (!empty($approve))
                                <th rowspan="2" class="dhead" style="text-align: center">Approve <br> <input
                                        type="checkbox" name="" id="checkAll" id="">
                                </th>
                            @endif --}}
                            <th rowspan="2" class="dhead">Aksi</th>
                        </tr>
                        <tr>
                            <th class="dhead" style="text-align: center">Pembayaran</th>
                            <th class="dhead" style="text-align: center">Grading</th>
                            <th class="dhead" style="text-align: center">Export <br> <button type="submit"
                                    name="submit" value="export" class="badge bg-success"><i
                                        class="fas fa-file-excel"></i></button>
                                <br><input type="checkbox" name="" id="checkAll" id="">
                            </th>
                            <th class="dhead" style="text-align: center">Harga</th>
                            <th class="dhead" style="text-align: center">Approve <br> <button type="submit"
                                    name="submit" value="approve" class="badge bg-primary"><i
                                        class="fas fa-check"></i></button> <br>
                                <input type="checkbox" name="" id="checkAll2" id="">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembelian as $no => $p)
                            <tr>
                                <td class="text-center">{{ $no + 1 }}</td>
                                <td class="text-center">{{ tanggal($p->tgl) }}</td>
                                <td class="text-center">{{ $p->no_nota }}</td>
                                <td class="text-center">{{ $p->no_lot }}</td>
                                <td class="text-center">{{ ucwords(strtolower($p->nm_suplier)) }}</td>
                                <td class="text-center">{{ ucwords(strtolower($p->suplier_akhir)) }}</td>
                                <td align="right">
                                    {{-- <a href="javascript:void(0);" class="get_print" no_nota="{{ $p->no_nota }}">

                                    </a> --}}
                                    <a href="#" class="get_detail" no_nota="{{ $p->no_nota }}"
                                        data-bs-toggle="modal" data-bs-target="#viewdetail">Rp.
                                        {{ number_format($p->total_harga, 0) }}</a>

                                </td>

                                <td align="center">
                                    <span
                                        class="badge {{ $p->lunas == 'D' ? 'bg-warning' : ($p->total_harga + $p->debit - $p->kredit == 0 ? 'bg-success' : 'bg-danger') }}">
                                        {{ $p->lunas == 'D' ? 'Draft' : ($p->total_harga + $p->debit - $p->kredit == 0 ? 'Paid' : 'Unpaid') }}
                                    </span>
                                </td>

                                <td align="center">
                                    @if (empty($p->nota_grading))
                                        <i class="fas fa-times text-danger"></i>
                                    @else
                                        <a href="#" class="btn btn-sm btn-success grading_nota"
                                            no_nota="{{ $p->no_nota }}" data-bs-toggle="modal"
                                            data-bs-target="#viewgrading"><i class="fas fa-eye"></i></a>
                                    @endif

                                </td>

                                <td class="text-center">
                                    @if (empty($p->nota_grading) || empty($p->nota_bk_campur))
                                        <i class="fas fa-times text-danger"></i>
                                        <input type="checkbox" name="ceknota_excel[]" id=""
                                            value="{{ $p->no_nota }}" hidden>
                                    @else
                                        <input type="checkbox" name="ceknota_excel[]" class="checkbox-item-excel"
                                            id="" value="{{ $p->no_nota }}">
                                    @endif
                                </td>
                                <td align="center">
                                    @if (empty($p->nota_grading) || empty($p->nota_bk_campur))
                                        <i class="fas fa-times text-danger"></i>
                                    @else
                                        <span class="badge {{ empty($p->rupiah) ? 'bg-danger' : 'bg-success' }}">
                                            {!! empty($p->rupiah) ? 'Harga ?' : 'Harga <i class="fas fa-check"></i>' !!}
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    @if (!empty($approve))
                                        @if ($p->approve == 'Y')
                                            <i class="fas fa-check text-primary"></i>
                                            <input type="hidden" name="ceknota[]" id="" value="Y">
                                        @else
                                            <input type="checkbox" name="ceknota[]" class="checkbox-item"
                                                id="" value="{{ $p->no_nota }}">
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <span class="btn btn-sm" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v text-primary"></i>
                                        </span>
                                        <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                            @php
                                                $emptyKondisi = [$edit, $delete, $print, $grading];
                                            @endphp
                                            <x-theme.dropdown_kosong :emptyKondisi="$emptyKondisi" />
                                            @if (!empty($edit))
                                                <li>
                                                    <a class="dropdown-item text-primary edit_akun "
                                                        href="{{ route('edit_pembelian_bk', ['nota' => $p->no_nota]) }}">
                                                        <i class="me-2 fas fa-pen"></i> Edit
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($p->approve == 'Y')
                                            @else
                                                @if (!empty($delete))
                                                    <li>
                                                        <a class="dropdown-item  text-danger delete_nota {{ !empty($p->rupiah) ? 'd-none' : '' }}"
                                                            no_nota="{{ $p->no_nota }}" href="#"
                                                            data-bs-toggle="modal" data-bs-target="#delete"><i
                                                                class="me-2 fas fa-trash"></i>Delete
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif
                                            @if (!empty($print))
                                                <li>
                                                    <a class="dropdown-item  text-info detail_nota" target="_blank"
                                                        href="{{ route('print_bk', ['no_nota' => $p->no_nota]) }}"><i
                                                            class="me-2 fas fa-print"></i>Print
                                                    </a>
                                                </li>
                                            @endif

                                            @if (!empty($grading))
                                                <li>
                                                    <a href="#"
                                                        class="dropdown-item  text-info grading_notatambah {{ !empty($p->rupiah) ? 'd-none' : '' }}"
                                                        no_nota="{{ $p->no_nota }}" data-bs-toggle="modal"
                                                        data-bs-target="#grading"><i
                                                            class="me-2 fas fa-balance-scale-right"></i>Grading
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>


            </section>
        </form>

        <form action="{{ route('grading') }}" method="post">
            @csrf
            <x-theme.modal title="Campur BKIN" size="modal-lg" idModal="grading">
                <div id="grading_nota2"></div>
                <input type="hidden" name="tgl_nota" value="{{ $tgl1 }}">
            </x-theme.modal>
        </form>


        <x-theme.modal title="Campur BKIN" size="modal-lg" idModal="viewgrading" btnSave="T">
            <div id="grading_nota"></div>

        </x-theme.modal>
        <x-theme.modal title="Detail Nota" size="modal-lg" idModal="viewdetail" btnSave="T">
            <div id="load_detail"></div>

        </x-theme.modal>

        <form action="{{ route('import_buku_campur') }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-theme.modal title="Import Buku Campur" idModal="import" btnSave="Y">
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">File</label>
                        <input type="file" class="form-control" name="file">
                    </div>
                </div>

            </x-theme.modal>
        </form>

        <form action="{{ route('delete_bk') }}" method="get">
            <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <h5 class="text-danger ms-4 mt-4"><i class="fas fa-trash"></i> Hapus Data
                                </h5>
                                <p class=" ms-4 mt-4">Apa anda yakin ingin menghapus ?</p>
                                <input type="hidden" class="no_nota" name="no_nota">
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
                    scrollX: false,
                    scrollCollapse: false,
                    "stateSave": false,
                    "autoWidth": false,
                    "paging": false,
                    "info": false
                });
                pencarian('pencarian', 'tableSearch')

                $(document).on('click', '.delete_nota', function() {
                    var no_nota = $(this).attr('no_nota');
                    $('.no_nota').val(no_nota);
                })
                $(document).on('click', '.grading_nota', function() {
                    var no_nota = $(this).attr('no_nota');
                    $.ajax({
                        type: "get",
                        url: "/get_grading?no_nota=" + no_nota,
                        success: function(data) {
                            $('#grading_nota').html(data);
                            $('.nota_grading').val(no_nota);
                            $('.nota_grading_text').text(no_nota);
                        }
                    });

                });

                function load_grade(no_nota) {
                    $.ajax({
                        type: "get",
                        url: "{{ route('load_grade') }}",
                        data: {
                            no_nota: no_nota
                        },
                        success: function(r) {
                            $("#load_grade").html(r);
                        }
                    });
                }
                $(document).on('click', '.grading_notatambah', function() {
                    var no_nota = $(this).attr('no_nota');
                    $.ajax({
                        type: "get",
                        url: "/get_grading2?no_nota=" + no_nota,
                        success: function(data) {
                            $('#grading_nota2').html(data);
                            $('.nota_grading').val(no_nota);
                            $('.nota_grading_text').text(no_nota);
                            load_grade(no_nota)
                        }
                    });


                });


                $(document).on('click', '#checkAll', function() {
                    // Setel properti checked dari kotak centang individu sesuai dengan status "cek semua"
                    $('.checkbox-item-excel').prop('checked', $(this).prop('checked'));
                });
                $(document).on('click', '#checkAll2', function() {
                    // Setel properti checked dari kotak centang individu sesuai dengan status "cek semua"
                    $('.checkbox-item').prop('checked', $(this).prop('checked'));
                });
                $(document).on('click', '.hapus_grade', function() {
                    var id_grade = $(this).attr('id_grade');
                    var no_nota = $(this).attr('no_nota');
                    Swal.fire({
                        title: 'Apakah yakin ingin menghapus?',
                        text: "Data yang dihapus tidak bisa dikembalikan lagi!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batalkan'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: "Get",
                                url: "{{ route('delete_tipe_grade') }}",
                                data: {
                                    id_grade: id_grade
                                },
                                success: function(response) {
                                    load_grade(no_nota);
                                    Toastify({
                                        text: "Data berhasil dihapus",
                                        duration: 3000,
                                        style: {
                                            background: "#EAF7EE",
                                            color: "#7F8B8B"
                                        },
                                        close: true,
                                        avatar: "https://cdn-icons-png.flaticon.com/512/190/190411.png"
                                    }).showToast();
                                }
                            });

                        }
                    })

                });


                $(document).on('click', '.btn-save', function() {
                    var nm_grade = $("input[name='nm_grade']").val();
                    var no_nota = $("input[name='no_nota']").val();
                    if (nm_grade !== "") {
                        $.ajax({
                            url: "{{ route('save_grade') }}",
                            method: "Get",
                            data: {
                                nm_grade: nm_grade
                            },
                            success: function(response) {
                                load_grade(no_nota);
                                Toastify({
                                    text: "Data berhasil disimpan",
                                    duration: 3000,
                                    style: {
                                        background: "#EAF7EE",
                                        color: "#7F8B8B"
                                    },
                                    close: true,
                                    avatar: "https://cdn-icons-png.flaticon.com/512/190/190411.png"
                                }).showToast();
                            },
                            error: function() {
                                Toastify({
                                    text: "Terjadi kesalahan dalam menyimpan data",
                                    duration: 3000,
                                    style: {
                                        background: "#FCEDE9",
                                        color: "#7F8B8B"
                                    },
                                    close: true,
                                    avatar: "https://cdn-icons-png.flaticon.com/512/564/564619.png"
                                }).showToast();
                            }
                        });
                    } else {
                        Toastify({
                            text: "Nama Grade tidak boleh kosong",
                            duration: 3000,
                            position: 'center',
                            style: {
                                background: "#FCEDE9",
                                color: "#7F8B8B"
                            },
                            close: true,
                            avatar: "https://cdn-icons-png.flaticon.com/512/564/564619.png"
                        }).showToast();
                    }
                });

                $('.buat_baru').on('click', function(event) {
                    event.preventDefault(); // Mencegah perilaku default (misalnya, submit form)
                    $.ajax({
                        url: "{{ route('add_new_bk') }}",
                        type: 'GET',
                        success: function(response) {
                            // Pindah ke URL baru tanpa refresh
                            window.location.href = response.url;
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                });
                $('.get_detail').on('click', function(event) {
                    event.preventDefault(); // Mencegah perilaku default (misalnya, submit form)
                    var no_nota = $(this).attr('no_nota');

                    $.ajax({
                        url: "{{ route('get_print') }}",
                        data: {
                            no_nota: no_nota
                        },
                        type: 'GET',
                        success: function(response) {
                            $('#load_detail').html(response);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                });

                function navigateInputs(className, e) {
                    if (e.keyCode === 40 || e.keyCode === 38) { // 40 untuk down, 38 untuk up
                        e.preventDefault();

                        var $input = $(e.target);
                        var currentCount = parseInt($input.attr('count')); // Ambil nilai count dari input saat ini

                        // Tentukan arah navigasi (1 untuk down, -1 untuk up)
                        var direction = (e.keyCode === 40) ? 1 : (e.keyCode === 38) ? -1 : 0;

                        // Cari baris berikutnya (atau sebelumnya) dalam tabel
                        var $row = $input.closest('tr');
                        var $nextRow = direction === 1 ? $row.next('tr') : $row.prev(
                            'tr'); // Baris berikut atau sebelumnya

                        // Cari input di dalam baris tersebut dengan class yang sesuai dan count yang diinginkan
                        var $nextInput = $nextRow.find('.' + className + '[count="' + (currentCount + direction) +
                            '"]');

                        // Jika input berikutnya ditemukan, fokuskan input tersebut
                        if ($nextInput.length) {
                            $nextInput.focus();

                            // Jika input tersebut memiliki kelas pcsAwal atau grAwal, pilih seluruh teks dalam input
                            if ($nextInput.hasClass('pcsAwal') || $nextInput.hasClass('grAwal')) {
                                $nextInput.select(); // Memilih seluruh teks
                            }
                        }
                    }
                }

                // Menggunakan event delegation untuk menangani semua input dalam tabel dengan kelas .table2
                $(document).on('keydown', '.table2 input', function(e) {
                    var className = $(this).attr('class').split(' ')[
                        1]; // Mendapatkan kelas kedua seperti 'pcsAwal' atau 'grAwal'
                    if (e.keyCode === 40 || e.keyCode === 38) { // 40 untuk down, 38 untuk up
                        e.preventDefault();

                        var $input = $(e.target);
                        var currentCount = parseInt($input.attr(
                            'count')); // Ambil nilai count dari input saat ini



                        // Tentukan arah navigasi (1 untuk down, -1 untuk up)
                        var direction = (e.keyCode === 40) ? 1 : (e.keyCode === 38) ? -1 : 0;

                        // Cari baris berikutnya (atau sebelumnya) dalam tabel
                        var $row = $input.closest('tr');
                        var $nextRow = direction === 1 ? $row.next('tr') : $row.prev(
                            'tr'); // Baris berikut atau sebelumnya

                        // Debugging - apakah baris berikutnya ditemukan?
                        console.log('Navigating to next row:', $nextRow);

                        // Cari input di dalam baris tersebut dengan class yang sesuai dan count yang diinginkan
                        var $nextInput = $nextRow.find('.' + className + '[count="' + (currentCount +
                            direction) + '"]');

                        // Debugging - apakah input berikutnya ditemukan?
                        console.log('Next input found:', $nextInput);

                        // Jika input berikutnya ditemukan, fokuskan input tersebut
                        if ($nextInput.length) {
                            $nextInput.focus();

                            // Jika input tersebut memiliki kelas pcsAwal atau grAwal, pilih seluruh teks dalam input
                            if ($nextInput.hasClass('pcsAwal') || $nextInput.hasClass('grAwal')) {
                                $nextInput.select(); // Memilih seluruh teks
                            }
                        } else {
                            // Debugging - jika input tidak ditemukan, beri pesan
                            console.log('Next input not found for direction', direction);
                        }
                    }
                });




            });
        </script>
    @endsection
</x-theme.app>
