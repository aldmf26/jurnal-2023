<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="row">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} : {{ tanggal($tgl1) }}~{{ tanggal($tgl2) }}</h6>
            </div>
            <div class="col-lg-6">
                <x-theme.button modal="T" href="{{ route('dashboard_kandang.add_penjualan_telur') }}" icon="fa-plus"
                    addClass="float-end" teks="Buat Nota" />
                <x-theme.btn_dashboard route="dashboard_kandang.index" />

                <x-theme.btn_filter />
            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <table class="table table-hover" id="table">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Tanggal</th>
                        <th>Nota</th>
                        <th>Customer</th>
                        <th>Total Rp</th>
                        <th>Diterima</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penjualan as $no => $s)
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>{{ tanggal($s->tgl) }}</td>
                        <td>{{ $s->no_nota }}</td>
                        <td>{{ $s->customer }}</td>
                        <td>{{ number_format($s->ttl_rp, 0) }} </td>
                        <td><span class="btn btn-sm btn-success">{{ ucwords($s->admin_cek) ?? '' }}</span></td>
                        <td>
                            <div class="btn-group" role="group">
                                <span class="btn btn-sm" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v text-primary"></i>
                                </span>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <li><a class="dropdown-item text-primary "
                                            href="{{ route('dashboard_kandang.edit_telur', ['no_nota ' => $s->no_nota ]) }}"><i
                                                class="me-2 fas fa-pen"></i>Edit</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger delete_nota" no_nota="{{ $s->no_nota  }}"
                                            href="#" data-bs-toggle="modal" data-bs-target="#delete"><i
                                                class="me-2 fas fa-trash"></i>Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </section>
    </x-slot>
    @section('js')
    <script>
        $(document).ready(function() {
                $(document).on('click', '.delete_nota', function() {
                    var no_nota = $(this).attr('no_nota');
                    $('.no_nota').val(no_nota);
                })
            });
    </script>
    @endsection
</x-theme.app>