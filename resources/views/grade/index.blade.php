<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} </h6>
            </div>
            <div class="col-lg-6">


            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">

        <section class="row">

            <div class="col-lg-8">
                <form action="{{ route('grade.non') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">

                        </div>
                        <div class="col-lg-8 mb-2">
                            <x-theme.button modal="Y" idModal="tambah" teks="Tambah Data" icon="fa-plus"
                                addClass="float-end" />
                            <a href="{{ route('grade.export_grade') }}" class="btn btn-primary  float-end me-2"><i
                                    class="fas fa-file-excel"></i>
                                export</a>
                            <x-theme.button modal="Y" idModal="import" icon="fas fa-upload" addClass="float-end"
                                teks="Import" />
                            <button class="btn btn-danger float-end me-2 nonaktif" type="submit"
                                style="display:none;">Non
                                Aktif</button>

                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <table class="table table-hover table-bordered" id="nanda">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Urutan</th>
                                    <th class="text-center">Aktif</th>
                                    <th class="text-center">Putih beras</th>
                                    <th class="text-center">Kuning</th>
                                    <th class="text-center">Aksi</th>
                                    {{-- <th class="text-center">
                                        nonaktifkan <br>
                                        <input type="checkbox" class="checkAll" name="" id="">
                                    </th> --}}

                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($grade as $g)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $g->nm_kategori }}
                                        </td>
                                        <td>{{ $g->nm_grade }}</td>
                                        <td class="text-center">{{ $g->urutan }}</td>
                                        <td class="text-center">
                                            @if ($g->aktif == 'Y')
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($g->putih == 'Y')
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($g->kuning == 'Y')
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#edit"
                                                data-id="{{ $g->id_grade_cong }}" class="btn btn-warning btn-sm edit"><i
                                                    class="fas fa-edit"></i></a>
                                        </td>
                                        {{-- <td>
                                            <input type="checkbox" class="checkbox-item" name="id[]"
                                                value="{{ $g->id_grade_cong }}">
                                        </td> --}}
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </form>
            </div>


        </section>

        <style>
            .modal-lg-mix {
                max-width: 1300px;
            }
        </style>

        <form action="{{ route('grade.save') }}" method="post">
            @csrf
            <x-theme.modal title="Tambah Data" idModal="tambah" size="modal-lg">
                <div class="row">
                    <div class="col-lg-4 col-12">
                        <label for="">Kategori</label>
                        <select name="kategori[]" class="form-control" id="">
                            <option value="">-Pilih Katgeori-</option>
                            @foreach ($kategori as $k)
                                <option value="{{ $k->id }}">{{ $k->nm_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-12">
                        <label for="">Grade</label>
                        <textarea name="nm_grade[]" id="" cols="8" rows="2" class="form-control">
                            </textarea>
                    </div>
                    <div class="col-lg-4 col-12">
                        <label for="">Urutan</label>
                        <input type="text" class="form-control" name="urutan[]">
                    </div>
                    <div class="col-lg-4 col-12 mt-2">
                        <label for="">Putih beras</label>
                        <select name="putih[]" class="form-control" id="">
                            <option value="Y">Aktif</option>
                            <option value="T">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-12 mt-2">
                        <label for="">Kuning</label>
                        <select name="kuning[]" class="form-control" id="">
                            <option value="Y">Aktif</option>
                            <option value="T">Tidak Aktif</option>
                        </select>
                    </div>
                    {{-- <div class="col-lg-1 col-1">
                        <label for="">Aksi</label>
                    </div> --}}
                    {{-- <div class="col-lg-4 col-12 mb-2">


                    </div>
                    <div class="col-lg-4 col-12 mb-2">


                    </div>
                    <div class="col-lg-3 col-12 mb-2">


                    </div> --}}


                </div>
                {{-- <div class="load_row">

                </div>

                <div class="row">
                    <div class="col-lg-12 mt-2">
                        <button type="button" class="btn btn-success float-end tambah_row">Tambah Baris</button>
                    </div>
                </div> --}}

            </x-theme.modal>
        </form>

        <form action="{{ route('grade.Edit') }}" method="post">
            @csrf
            <x-theme.modal title="Edit Data" idModal="edit" size="modal-lg">
                <div id="load_edit">

                </div>
            </x-theme.modal>
        </form>

        <form action="{{ route('grade.import_grade') }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-theme.modal title="Import Grade congan" idModal="import" btnSave="Y">
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">File</label>
                        <input type="file" class="form-control" name="file">
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
                var count = 3;
                $(document).on("click", ".tambah_row", function() {
                    count = count + 1;
                    $.ajax({
                        url: "/grade/load_row?count=" + count,
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

                function toggleButton() {
                    if ($("input:checkbox:checked").length > 0) {
                        $('.nonaktif').show();
                    } else {
                        $('.nonaktif').hide();
                    }
                }
                $(document).on("click", ".checkAll", function() {
                    $('input:checkbox').not(this).prop('checked', this.checked);
                    toggleButton();
                });

                $(document).on("change", "input:checkbox", function() {
                    toggleButton();
                });

                $(document).on("click", ".edit", function() {
                    var id = $(this).attr("data-id");

                    $.ajax({
                        type: "get",
                        url: "{{ route('grade.getEdit') }}",
                        data: {
                            id: id
                        },
                        success: function(response) {
                            $('#load_edit').html(response);
                        }
                    });
                });

            });
        </script>
    @endsection
</x-theme.app>
