<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="{{asset('dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">Admin Repairman</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Alexander Pierce</a>
        </div>
      </div> -->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard')?'active':'' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview {{ request()->routeIs('service_category*')||request()->routeIs('services*')?'menu-open':'' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Master Jasa
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('service_category.index') }}" class="nav-link {{ request()->routeIs('service_category*')?'active':'' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Kategori Jasa</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services*')?'active':'' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List Jasa</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{ route('customer.index') }}" class="nav-link {{ request()->routeIs('customer*')?'active':'' }}">
              <i class="nav-icon fas fa-address-book"></i>
              <p>
                Pelanggan
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('engineer.index') }}" class="nav-link {{ request()->routeIs('engineer*')?'active':'' }}">
              <i class="nav-icon fas fa-tools"></i>
              <p>
                Teknisi
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('service_order.index') }}" class="nav-link {{ request()->routeIs('service_order*')?'active':'' }}">
              <i class="nav-icon fas fa-clipboard-list"></i>
              <p>
                Pesanan Jasa
                <span class="right badge badge-danger">2</span>
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-comments"></i>
              <p>
                Chat
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Chat Pelanggan</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Chat Teknisi</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{ route('review_service.index') }}" class="nav-link {{ request()->routeIs('review_service*')?'active':'' }}">
              <i class="nav-icon fas fa-smile"></i>
              <p>
                Ulasan Pelanggan
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('payment.index') }}" class="nav-link {{ request()->routeIs('payment*')?'active':'' }}">
              <i class="nav-icon fas fa-money-bill-wave"></i>
              <p>
                Konfirmasi Pembayaran
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview {{ request()->routeIs('setting*')?'menu-open':'' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-cog"></i>
              <p>
                Pengaturan
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('setting.privacy_policy') }}" class="nav-link {{ request()->routeIs('setting.privacy_policy')?'active':'' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Privacy Policy</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('setting.term_of_service') }}" class="nav-link {{ request()->routeIs('setting.term_of_service')?'active':'' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Term of Service</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('setting.help') }}" class="nav-link {{ request()->routeIs('setting.help')?'active':'' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Help</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>