<div class="header-top">
    <div class="container">
        <style>
            .layout-horizontal .header-top {
                background-color: #8c8989;
                padding: 0rem;
            }

            .layout-horizontal .main-navbar {
                background-color: #F7914D;
                padding: 4px;
            }



            .layout-horizontal .header-top .logo img {
                height: 30px;
            }

            .avatar.avatar-md2 .avatar-content,
            .avatar.avatar-md2 img {
                font-size: .8rem;
                height: 25px;
                width: 25px;
            }

            .font-testing {
                font-weight: normal;
                font-size: 16px;
            }
        </style>
        <div class="logo">
            <a href="dashboard"><img src="/assets/login/img/empat.svg" alt="Logo" width="90px"></a>
            <p class="fw-bold text-center font-testing text-white">KAS AGA</p>
        </div>
        <div class="header-top-right">

            <div class="dropdown">
                <a href="#" id="topbarUserDropdown"
                    class="user-dropdown d-flex align-items-center dropend dropdown-toggle " data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <div class="avatar avatar-md2">
                        {{-- @php
                            $idPosisi = auth()->user()->posisi->id_posisi;
                            $gambar = $idPosisi == 1 ? 'kitchen' : 'server';
                        @endphp --}}
                        <img src='{{ asset('img/kitchen.png') }}' alt="Avatar">
                    </div>
                    <div class="text">
                        <p class="user-dropdown-name">{{ ucwords(auth()->user()->name) }}</p>
                        {{-- <p class="user-dropdown-status text-sm text-muted">
                            {{ ucwords(auth()->user()->posisi->nm_posisi) }}
                        </p> --}}
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="topbarUserDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a>
                    </li>
                    <li>
                        <form id="myForm" method="post" action="{{ route('logout') }}">
                            @csrf
                        </form>
                        <a class="dropdown-item" href="#"
                            onclick="document.getElementById('myForm').submit();">Logout</a>
                    </li>
                </ul>
            </div>

            <!-- Burger button responsive -->
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </div>
    </div>
</div>
