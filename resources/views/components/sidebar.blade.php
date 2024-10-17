<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#">IDN Bonus</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#">IDN</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class="nav-item">
                <a href="{{route('home')}}"
                    class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a>
            </li>
            <li class="nav-item dropdown">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-map-marker-alt"></i> <span>Upload Excel</span></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('uploadsbonus.index') }}" >Upload Bonus</a></li>
                    <li><a href="{{route('uploadscashback.index')}}">Upload CashBack</a></li>
                    <li><a href="{{route('uploadsrolling.index')}}">Upload Rolling</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-map-marker-alt"></i> <span>Hasil Upload Excel</span></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('hasilbonus.index') }}" >Import Bonus</a></li>
                    <li><a href="{{route('hasilcashback.index')}}">Import CashBack</a></li>
                    <li><a href="{{route('hasilrolling.index')}}">Import Rolling</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('memberdata.index') }}" class="nav-link"><i class="fas fa-fire"></i><span>Cek Member</span></a>
            </li>

            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link"><i class="fas fa-fire"></i><span>Users</span></a>
            </li>

        </ul>

        {{-- <div class="hide-sidebar-mini mt-4 mb-4 p-3">
            <a href="https://getstisla.com/docs"
                class="btn btn-primary btn-lg btn-block btn-icon-split">
                <i class="fas fa-rocket"></i> Documentation
            </a>
        </div> --}}
    </aside>
</div>
