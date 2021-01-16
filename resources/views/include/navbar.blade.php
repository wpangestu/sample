  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          @if(get_all_notification()>0)
            <span class="badge badge-warning navbar-badge">{{ get_all_notification() }}</span>
          @endif
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <!-- <span class="dropdown-item dropdown-header">15 Notifications</span> -->
          <div class="dropdown-divider"></div>
          <a href="{{ route('engineer.confirm.index') }}" class="dropdown-item">
            <i class="fas fa-tools mr-2"></i> {{ get_confirm_engineer() }} Konfirmasi Teknisi
          </a>
          <!-- <div class="dropdown-divider"></div> -->
          <!-- <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 Konfirmasi Top Up -->
            <!-- <span class="float-right text-muted text-sm">12 hours</span> -->
          <!-- </a> -->
          <!-- <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a> -->
          <!-- <div class="dropdown-divider"></div> -->
          <!-- <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a> -->
        </div>
      </li>

      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <!-- <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-info mr-2"></i> Profile
          </a> -->
          <div class="dropdown-divider"></div>
          <form action="{{route('logout')}}" method="post">
            @csrf
            <button type="submit" class="dropdown-item dropdown-footer">Logout</button>
          </form>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->