<ul class="nav nav-pills float-start" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $nm_gudang == 'bk' ? 'active' : '' }}"
            href="{{ route('gudangBk.index', ['nm_gudang' => 'bk']) }}" type="button" role="tab"
            aria-controls="pills-home" aria-selected="true">Gudang Bk</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $nm_gudang == 'produksi' ? 'active' : '' }}"
            href="{{ route('gudangBk.index', ['nm_gudang' => 'produksi']) }}" type="button" role="tab"
            aria-controls="pills-home" aria-selected="true">Gudang Produksi</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $nm_gudang == 'summary_produksi' ? 'active' : '' }}"
            href="{{ route('gudangBk.gudangProduksiGabung', ['nm_gudang' => 'summary_produksi']) }}" type="button"
            role="tab" aria-controls="pills-home" aria-selected="true">Gudang Gabung</a>
    </li>
    {{-- <li class="nav-item" role="presentation">
        <a class="nav-link {{ $nm_gudang == 'reject' ? 'active' : '' }}"
            href="{{ route('gudangBk.index', ['nm_gudang' => 'reject']) }}" type="button" role="tab"
            aria-controls="pills-home" aria-selected="true">Gudang Reject</a>
    </li> --}}
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $nm_gudang == 'wip' ? 'active' : '' }}"
            href="{{ route('halawal.index', ['nm_gudang' => 'wip']) }}" type="button" role="tab"
            aria-controls="pills-home" aria-selected="true">Gudang Wip</a>
    </li>
    @if (Auth::user()->posisi_id == '1')
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request()->routeIs('halawal.summary_wip') && request()->query('nm_gudang') == 'summary' ? 'active' : '' }}"
                href="{{ route('halawal.summary_wip', ['nm_gudang' => 'summary']) }}" type="button" role="tab"
                aria-controls="pills-home" aria-selected="true">Summary Wip</a>
        </li>
    @endif

    <li class="nav-item" role="presentation">
        <a class="nav-link {{ request()->routeIs('halawal.summary_wip') && request()->query('nm_gudang') == 'summarysinta' ? 'active' : '' }}"
            href="{{ route('halawal.summary_wip', ['nm_gudang' => 'summarysinta']) }}" type="button" role="tab"
            aria-controls="pills-home" aria-selected="true">Summary Wip Sinta</a>
    </li>


    <li class="nav-item" role="presentation">
        <a class="nav-link {{ request()->routeIs('halawal.susut') && request()->query('nm_gudang') == 'susut' ? 'active' : '' }}"
            href="{{ route('halawal.susut', ['nm_gudang' => 'susut']) }}" type="button" role="tab"
            aria-controls="pills-home" aria-selected="true">Susut</a>
    </li>

</ul>
