<x-theme.app title="{{ $title }}" table="Y" cont="container-fluid" sizeCard="12">

    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} </h6>
            </div>
            <div class="col-lg-6">
                <a href="{{ route('gudangnew.export_g_c_pgws') }}" class="btn btn-success float-end me-2"><i
                        class="fas fa-file-excel"></i> Export</a>
            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12 mt-2">
                <table class="table table-hover table-bordered" id="table" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Partai</th>
                            <th>No Box</th>
                            <th>Tipe</th>
                            <th>Ket</th>
                            <th>Warna</th>
                            <th>Tgl Terima</th>
                            <th>Pengawas</th>
                            <th>Nama Anak</th>
                            <th>Kelas</th>
                            <th class="text-end">Pcs</th>
                            <th class="text-end">Gr</th>
                            <th class=" text-end">Pcs Tidak Cetak</th>
                            <th class=" text-end">Gr Tidak Cetak</th>
                            <th class=" text-end">Pcs Awal Cetak</th>
                            <th class=" text-end">Gr Awal Cetak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cetak as $no => $g)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $g->nm_partai }}</td>
                                <td>{{ $g->no_box }}</td>
                                <td>{{ $g->tipe }}</td>
                                <td>{{ $g->ket }}</td>
                                <td>{{ $g->warna }}</td>
                                <td>{{ tanggal($g->tgl) }}</td>
                                <td>{{ $g->name }}</td>
                                <td>{{ $g->nama }}</td>
                                <td>{{ $g->id_kelas }}</td>
                                <td class="text-end">{{ number_format($g->pcs_ambil, 0) }}</td>
                                <td class="text-end">{{ number_format($g->gr_ambil, 0) }}</td>
                                <td class="text-end">{{ number_format($g->pcs_tdk_ctk, 0) }}</td>
                                <td class="text-end">{{ number_format($g->gr_tdk_ctk, 0) }}</td>
                                <td class="text-end">{{ number_format($g->pcs_awal_ctk, 0) }}</td>
                                <td class="text-end">{{ number_format($g->gr_awal_ctk, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
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
