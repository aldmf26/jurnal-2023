<x-theme.app title="{{ $title }}" nav="Y" rot1="barang_dagangan.index" rot2="barang_dagangan.stok_masuk"
    rot3="barang_dagangan.opname" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <hr class="mt-3">

            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}
                </h6>

            </div>
            <div class="col-lg-4">
                <select name="example" class="form-control float-end select-gudang" id="select2">
                    <option value="" selected>All Warehouse </option>
                    @foreach ($gudang as $g)
                        <option {{ Request::segment(2) == $g->id_gudang ? 'selected' : '' }} value="{{ $g->id_gudang }}">
                            {{ ucwords($g->nm_gudang) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2">
                @if (!empty($create))
                    <div class="btn-group dropstart float-end mb-1">
                        <button type="button" class="btn btn-primary dropdown-toggle show" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="true">
                            Tambah
                        </button>
                        <div class="dropdown-menu"
                            style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate3d(-104px, 0px, 0px);"
                            data-popper-placement="left-start">
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#tambah">Produk
                                Baru</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#tambah2">Gudang</a>
                        </div>
                    </div>
                @endif
                <x-theme.akses :halaman="$halaman" route="barang_dagangan.index" />
            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">

        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Satuan</th>
                        <th>Qty</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produk as $no => $d)
                        @php
                            $debit = $d->debit ?? 0;
                            $kredit = $d->kredit ?? 0;
                            $stk = $debit - $kredit;
                        @endphp
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>P-{{ kode($d->kd_produk) }}</td>
                            <td>{{ ucwords($d->nm_produk) }}</td>
                            <td>
                                {{ ucwords($d->nm_satuan) }}
                            </td>
                            <td>{{ $stk }}</td>
                            <td align="center">
                                <div class="btn-group dropstart mb-1">
                                    <span class="btn btn-lg" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v text-primary"></i>
                                    </span>
                                    <div class="dropdown-menu">
                                        @php
                                            $emptyKondisi = [$edit, $delete, $detail];
                                        @endphp
                                        <x-theme.dropdown_kosong :emptyKondisi="$emptyKondisi" />

                                        @if (!empty($edit))
                                            <a id_produk="{{ $d->id_produk }}" data-bs-toggle="modal"
                                                data-bs-target="#edit" class="dropdown-item text-primary edit"
                                                href="#"><i class="me-2 fas fa-pen"></i>
                                                Edit</a>
                                        @endif

                                        @if (!empty($delete))
                                            <a class="dropdown-item text-danger delete_nota"
                                                no_nota="{{ $d->id_produk }}" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete"><i class="me-2 fas fa-trash"></i>Delete
                                            </a>
                                        @endif

                                        @if (!empty($detail))
                                            <a class="dropdown-item text-info" href="#"><i
                                                    class="me-2 fas fa-search"></i>
                                                Detail</a>
                                        @endif
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </section>



        {{-- tambah produk --}}
        @include('persediaan_barang.barang_dagangan.tambah_produk')
        {{-- ------ --}}
        {{-- gudang create --}}
        <form action="{{ route('gudang.create') }}" method="post">
            @csrf
            <x-theme.modal size="modal-lg" title="Tambah Baru" idModal="tambah2">
                <div class="row">
                    <input type="hidden" name="url" value="{{ request()->route()->getName() }}">
                    <input type="hidden" name="segment" value="{{ request()->segment(2) }}">
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Kode Gudang</label>
                            <input required type="text" name="kd_gudang" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">Kategori Persediaan</label>
                            <select required name="kategori_id" class="form-control select2-tambah2" id="">
                                <option value="">- Pilih Kategori -</option>
                                <option value="1">Atk & Peralatan</option>
                                <option value="2">Bahan Baku</option>
                                <option value="3">Barang Dagangan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Nama Gudang</label>
                            <input type="text" name="nm_gudang" class="form-control">
                        </div>
                    </div>
                </div>
            </x-theme.modal>
        </form>
        {{-- ------ --}}
        {{-- edit produk --}}
        <form action="{{ route('barang_dagangan.edit') }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-theme.modal size="modal-lg" title="Edit Produk" idModal="edit">
                <div id="load-edit"></div>
            </x-theme.modal>
        </form>

        <x-theme.btn_alert_delete route="barang_dagangan.delete" name="id_produk" :tgl1="$tgl1" :tgl2="$tgl2"
            :id_proyek="$id_proyek" />

    </x-slot>

    @section('js')
        <script>
            $(document).ready(function() {
                $(".select-gudang").change(function(e) {
                    e.preventDefault();
                    var gudang_id = $(this).val()
                    document.location.href = `/barang_dagangan/${gudang_id}`
                });

                $(document).on('change', '.tambah-gudang-select', function() {
                    if ($(this).val() == 'tambah') {
                        $("#tambah2").modal('show')
                    }
                })

                // edit
                edit('edit', 'id_produk', 'produk/edit', 'load-edit')
            });
        </script>
    @endsection
</x-theme.app>
