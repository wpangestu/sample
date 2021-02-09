@extends('layouts.app_layout')
@section('title','Detail Teknisi')
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
              <div class="card-header">
                <h3 class="card-title">Detail Teknisi</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">

                  </div>
                </div>
                
                @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <i class="icon fas fa-check"></i>
                    {{ $message }}
                </div>
                @endif
                @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <i class="icon fas fa-times"></i>
                    {{ $message }}
                </div>
                @endif

                <dl class="row">
                  <div class="images">
                  <img src="{{ asset('images/user_profil/',$data->profile_photo_path) }}" alt="">
                  </div>
                  <dt class="col-sm-3">Status Verifikasi</dt>
                  <dd class="col-sm-8">: 
                      @if($data->engineer->status=="pending")
                        <span class="badge badge-warning">Menunggu Konfirmasi</span>
                      @elseif($data->engineer->status=="decline")
                      <span class="badge badge-danger">Varifikasi DiTolak</span>
                      @else
                      <span class="badge badge-success">Varifikasi Berhasil</span>
                      @endif
                  </dd>
                  <dt class="col-sm-3">No KTP</dt>
                  <dd class="col-sm-8">: {{ $data->engineer->id_card_number??'-' }}</dd>
                  <dt class="col-sm-3">Nama</dt>
                  <dd class="col-sm-8">: {{ $data->engineer->name??'-' }}</dd>
                  <dt class="col-sm-3">Phone</dt>
                  <dd class="col-sm-8">: {{ $data->engineer->phone??'-' }}</dd>
                  <dt class="col-sm-3">Email</dt>
                  <dd class="col-sm-8">: {{ $data->engineer->email??'-' }}</dd>
                  <dt class="col-sm-3">Konfirmasi Email</dt>
                  <dd class="col-sm-8">: 
                        @if($data->engineer->is_verified_email===true)
                        <i class='fa fa-check-circle'></i>
                        @else
                        <i class='fa fa-times-circle'></i>
                        @endif
                  </dd>
                  <dt class="col-sm-3">Alamat</dt>
                  <dd class="col-sm-8">: {{ $data->address??'-' }}</span>
                  <!-- <dt class="col-sm-3">Status</dt>
                  <dd class="col-sm-8">: <span class="badge badge-{{ $data->is_active===1?'success':'secondary' }}">{{ $data->is_active===1?'Aktif':'Tidak Aktif' }}</span> -->
                  <dt class="col-sm-3">Foto KTP</dt>
                  <dd class="col-sm-8">: 
                    @if(!is_null($data->engineer->id_card_image))
                    <img height="200px" src="<?= $data->engineer->id_card_image ?>" alt="">
                    @else
                      Tidak tersedia
                    @endif
                  </dd>
                  <dt class="col-sm-3">Foto KTP Selfie</dt>
                  <dd class="col-sm-8">: 
                    @if(!is_null($data->engineer->id_card_selfie_image))
                    <img height="200px" src="<?= $data->engineer->id_card_selfie_image ?>" alt="">
                    @else
                      Tidak tersedia
                    @endif
                  </dd>
                  <dt class="col-sm-3">Foto Formal</dt>
                  <dd class="col-sm-8">: 
                    @if(!is_null($data->profile_photo_path))
                    <img height="200px" src="<?= $data->profile_photo_path ?>" alt="">
                    @else
                      Tidak tersedia
                    @endif
                  </dd>
                  <dt class="col-sm-3">Tanggal Register</dt>
                  <dd class="col-sm-8">: {{ $data->created_at??'-' }}</dd>
                  <dt class="col-sm-3">Tanggal Update</dt>
                  <dd class="col-sm-8">: {{ $data->updated_at??'-' }}
                  <dt class="col-sm-3"></dt>
                  <dd class="col-sm-8">
                  @if($data->engineer->status=="pending")
                    <a onclick="return confirm('Apakah anda yakin?')" href="{{ route('engineer.confirm.accept',$data->userid) }}" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i> Accept</a>
                    <a onclick="return confirm('Apakah anda yakin?')" href="{{ route('engineer.confirm.decline', $data->userid) }}" class="btn btn-sm btn-danger"><i class="fa fa-times-circle"></i> Decline</a>
                  @endif
                    <a href="{{ route('engineer.confirm.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                  </dd>
                </dl>
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