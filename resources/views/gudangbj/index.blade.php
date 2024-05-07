<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">

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
                            <th>Grade</th>
                            <th class=" text-end">Pcs</th>
                            <th class=" text-end">Gr</th>
                            <th class=" text-end">Pcs Ambil</th>
                            <th class=" text-end">Gr Ambil</th>
                            <th class="bg-danger text-end text-white">Pcs Sisa</th>
                            <th class="bg-danger text-end text-white">Gr Sisa</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bj as $no => $g)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $g->grade }}</td>
                                <td class="text-end">{{ $g->pcs }}</td>
                                <td class="text-end">{{ $g->gr }}</td>
                                <td class="text-end">{{ $g->pcs_kredit }}</td>
                                <td class="text-end">{{ $g->gr_kredit }}</td>
                                <td class="text-end">{{ number_format($g->pcs - $g->pcs_kredit, 0) }}</td>
                                <td class="text-end">{{ number_format($g->gr - $g->gr_kredit, 0) }}</td>
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
