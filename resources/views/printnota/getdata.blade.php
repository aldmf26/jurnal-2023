<x-theme.app title="Data" table="Y" sizeCard="12">

    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">Data </h6>
            </div>
            <div class="col-lg-6">

            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <table class="table table-hover table-bordered" id="table" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Nota</th>
                    <th>Tanggal</th>
                    <th>Suplier</th>
                    <th>Gr</th>
                    <th>Rp Gr</th>
                    <th>Ttl Rp</th>
                    <th>Print</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bk as $no => $b)
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>{{ $b->nota_bk }}</td>
                        <td>{{ tanggal($b->tanggal) }}</td>
                        <td>{{ $b->suplier_akhir }}</td>
                        <td>{{ round($b->gr_beli, 0) }}</td>
                        <td>{{ number_format($b->harga, 0) }}</td>
                        <td>{{ number_format($b->harga * $b->gr_beli, 0) }}</td>
                        <td>
                            <a target="_blank"
                                href="{{ route('printnota.index', ['id_bkin' => $b->id_bkin]) }}">print</a>


                            <a target="_blank"
                                href="{{ route('printnota.index', ['id_bkin' => $b->id_bkin]) }}">print2</a>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-slot>


</x-theme.app>
