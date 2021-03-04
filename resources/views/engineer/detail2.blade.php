@extends('layouts.app_layout')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Teknisi</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">

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
              <div class="card-header">
                <h3 class="card-title">Detail Teknisi</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        
                        <div class="card">
                            <!-- <div class="ribbon-wrapper ribbon-lg">
                                <div class="ribbon bg-danger">
                                 Not Verified
                                </div>
                            </div> -->
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    @if(!is_null($data->profile_photo_path))
                                    <img class="profile-user-img img-fluid img-circle"
                                        src="{{$data->profile_photo_path}}"
                                        alt="User profile picture">
                                    @else
                                    <img class="profile-user-img img-fluid img-circle"
                                        src="../../dist/img/avatar04.png"
                                        alt="User profile picture">
                                    @endif

                                </div>

                                <h3 class="profile-username text-center">{{ $data->name }}</h3>

                                <p class="text-muted text-center">ID : {{ $data->userid }}</p>
                                <div class="text-center">
                                    <span class="badge badge-{{ $data->is_active===1?'success':'secondary' }}">{{ $data->is_active===1?'Aktif':'Tidak Aktif' }}
                                    </span>
                                </div>
                                </ul>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- About Me Box -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Ringkasan</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <strong><i class="fas fa-star"></i> Rating</strong>

                                <p class="text-muted">
                                    4.5
                                </p>

                                <hr>

                                <strong><i class="fas fa-map-marker-alt mr-1"></i> Alamat</strong>

                                <p class="text-muted">
                                    {{ $data->address??'-' }}                                
                                </p>

                                <hr>

                                <strong><i class="fas fa-pencil-alt mr-1"></i> Phone</strong>

                                <p class="text-muted">
                                    {{ $data->phone??'-' }}
                                </p>

                                <hr>

                            </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#info" data-toggle="tab">Data  Diri</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#service_order" data-toggle="tab">Service Order</a></li>
                                    <!-- <li class="nav-item"><a class="nav-link" href="#maps" data-toggle="tab">Lokasi Maps</a></li> -->
                                </ul>
                            </div><!-- /.card-header -->
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="active tab-pane" id="info">
                                        <dl class="row">
                                            <div class="images">
                                                <img src="{{ asset('images/user_profil/',$data->profile_photo_path) }}" alt="">
                                            </div>

                                            <dt class="col-sm-3">User ID</dt>
                                            <dd class="col-sm-8">: {{ $data->userid??'-' }}</dd>
                                            <dt class="col-sm-3">Nama</dt>
                                            <dd class="col-sm-8">: {{ $data->name??'-' }}</dd>
                                            <dt class="col-sm-3">Email</dt>
                                            <dd class="col-sm-8">: {{ $data->email??'-' }}</dd>
                                            <dt class="col-sm-3">Phone</dt>
                                            <dd class="col-sm-8">: {{ $data->phone??'-' }}</dd>
                                            <dt class="col-sm-3">Porvinsi</dt>
                                            <dd class="col-sm-8">: {{ $data->province->name??'-' }}</dd>
                                            <dt class="col-sm-3">Kabupaten</dt>
                                            <dd class="col-sm-8">: {{ $data->regency->name??'-' }}</dd>
                                            <dt class="col-sm-3">Kecamatan</dt>
                                            <dd class="col-sm-8">: {{ $data->district->name??'-' }}</dd>
                                            <dt class="col-sm-3">Desa</dt>
                                            <dd class="col-sm-8">: {{ $data->village->name??'-' }}</dd>
                                            <dt class="col-sm-3">Alamat</dt>
                                            <dd class="col-sm-8">: {{ $data->address??'-' }}</span>
                                            <!-- <dt class="col-sm-3">Status</dt>
                                            <dd class="col-sm-8">: </span> -->
                                            <!-- <dt class="col-sm-3">Di buat</dt>
                                            <dd class="col-sm-8">: {{ $data->created_at??'-' }}</dd>
                                            <dt class="col-sm-3">Di Update</dt>
                                            <dd class="col-sm-8">: {{ $data->updated_at??'-' }} -->
                                            <dt class="col-sm-3">Lat</dt>
                                            <dd class="col-sm-8">: {{ $data->lat??'-' }}</dd>
                                            <dt class="col-sm-3">Lng</dt>
                                            <dd class="col-sm-8">: {{ $data->lng??'-' }}</dd>
                                        </dl>
                                        <div id="map" style="width:100%;height:220px;">
                                            {!! Mapper::render() !!}
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="service_order">
                                        <table id="table-datatables" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>ID</th>
                                                <th>Pelanggan</th>
                                                <th>Service</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no=1; @endphp
                                            @foreach($service_orders as $service_order)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $service_order->created_at }}</td>
                                                <td>{{ $service_order->serviceorder_id }}</td>
                                                <td>{{ $service_order->customer->name }}</td>
                                                <td>
                                                    {{ $service_order->service->name }}
                                                </td>
                                                <td>
                                                @php
                                                if($service_order->status==null){
                                                    echo "-";
                                                }elseif($service_order->status=="pending"){
                                                    echo '<badge class="badge badge-warning">pending</badge>';
                                                }elseif ($service_order->status=="process") {
                                                    echo '<badge class="badge badge-info">process</badge>';
                                                }
                                                elseif($service_order->status=="finish") {
                                                    echo '<badge class="badge badge-success">finish</badge>';
                                                }
                                                @endphp
                                                </td>
                                                <td>-</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <!-- <div class="tab-pane" id="maps">
                                        <h1>d</h1>
                                    </div> -->
                                <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                            </div><!-- /.card-body -->
                        </div>
                        <!-- /.nav-tabs-custom -->
                    </div>
                </div>
              </div>
              <!-- /.card-body -->
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

<script>

    $(document).ready(function(){

      

    });
</script>

@endsection