@extends('layouts.app_layout')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <!-- <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Starter Page</li>
            </ol> -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Wellcome</h5>

                <p class="card-text">
                    Hello <span class="bold">{{auth()->user()->name}} ({{auth()->user()->email}})</span><br>
                    Anda memiliki role :
                        @foreach(auth()->user()->roles as $role)
                            {{$role->name}}         
                        @endforeach
                </p>
                <div id="map" style="width:100%;height:380px;"></div>
              </div>
            </div>
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

@endsection

@section('scripts')
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAqK3bZ_zrgDmaMSLFHRBMwZn1HKCpPOp8&callback=initMap"
  type="text/javascript"></script>
    <script type="text/javascript">
      let map;

      function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
          center: { lat: -7.424827456168968, lng: 109.23510945529932 },
          zoom: 15,
        });
      }
    </script>
@endsection