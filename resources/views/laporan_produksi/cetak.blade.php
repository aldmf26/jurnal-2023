<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">

    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} </h6>
            </div>
            <div class="col-lg-6">
                <a href="{{ route('gudangcetak.export_laporan_boxproduksicetak') }}"
                    class="btn btn-success float-end me-2"><i class="fas fa-file-excel"></i> Export</a>
            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        {{-- <form action="{{ route('gudangBk.export_buku_campur_bk') }}" method="post"> --}}
        @csrf
        <section class="row">

            {{-- <div class="col-lg-9">
                @include('laporan_produksi.navlaporan')
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
                            <th class="dhead" rowspan="2">#</th>
                            <th class="dhead" rowspan="2">ket / nama partai</th>
                            <th class="dhead" rowspan="2">No Box</th>
                            <th class="dhead" rowspan="2">Tipe</th>
                            <th class="dhead" rowspan="2">Pengawas</th>
                            <th class="dhead text-center" colspan="2">BK</th>
                            <th class="dhead text-center" colspan="12">Cetak</th>

                            <th class=" text-center text-white  bg-danger" colspan="2">Sisa</th>
                        </tr>
                        <tr>
                            <th class="dhead">Pcs</th>
                            <th class="dhead">Gr</th>

                            <th class="dhead text-end">Pcs Awal</th>
                            <th class="dhead text-end">Gr Awal</th>
                            <th class="dhead text-end">Pcs Tidak Cetak</th>
                            <th class="dhead text-end">Gr Tidak Cetak</th>
                            <th class="dhead text-end">Pcs Awal Cetak</th>
                            <th class="dhead text-end">Gr Awal Cetak</th>
                            <th class="dhead text-end">Pcs Cu</th>
                            <th class="dhead text-end">Gr Cu</th>
                            <th class="dhead text-end">Pcs Akhir</th>
                            <th class="dhead text-end">Gr Akhir</th>
                            <th class="dhead text-end">Sst%</th>
                            <th class="dhead text-end">Cost Cetak</th>




                            <th class="text-white text-end bg-danger">Pcs Sisa</th>
                            <th class="text-white text-end bg-danger">Gr Sisa</th>


                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cabut as $no => $c)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $c->nm_partai }}</td>
                                <td>{{ $c->no_box }}</td>
                                <td>{{ $c->tipe }}</td>
                                <td>{{ $c->name }}</td>
                                <td class="text-end">{{ $c->pcs_awal }}</td>
                                <td class="text-end">{{ $c->gr_awal }}</td>

                                <td class="text-end">{{ $c->pcs_awal_ambil }}</td>
                                <td class="text-end">{{ $c->gr_awal_ambil }}</td>
                                <td class="text-end">{{ $c->pcs_tdk_ctk }}</td>
                                <td class="text-end">{{ $c->gr_tdk_ctk }}</td>
                                <td class="text-end">{{ $c->pcs_awal_ctk }}</td>
                                <td class="text-end">{{ $c->gr_awal_ctk }}</td>
                                <td class="text-end">{{ $c->pcs_cu }}</td>
                                <td class="text-end">{{ $c->gr_cu }}</td>
                                <td class="text-end">{{ $c->pcs_akhir }}</td>
                                <td class="text-end">{{ $c->gr_akhir }}</td>

                                <td class="text-end">
                                    {{ $c->gr_akhir == 0 ? 0 : number_format((1 - ($c->gr_akhir + $c->gr_cu) / $c->gr_awal_ctk) * 100, 1) }}
                                    %
                                </td>

                                <td class="text-end">{{ number_format($c->ttl_rp ?? 0, 0) }}</td>
                                <td class="text-end text-danger">{{ $c->pcs_awal - $c->pcs_awal_ambil }}</td>
                                <td class="text-end text-danger">{{ $c->gr_awal - $c->gr_awal_ambil }}
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

        </section>
        {{-- </form> --}}
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
