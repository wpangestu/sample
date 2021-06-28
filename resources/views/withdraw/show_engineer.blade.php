@extends('layouts.app_layout')
@section('title','Detail Withdraw')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Withdraw</h1>
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
                <h3 class="card-title">Detail Penarikan Teknisi</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
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
                  <dt class="col-sm-3">Status</dt>
                  <dd class="col-sm-8">: 
                    @if($data->status === 'pending')
                        <span class="badge badge-warning">Menunggu Konfirmasi</span>
                    @elseif($data->status === 'decline')
                        <span class="badge badge-danger">Ditolak</span>
                    @elseif($data->status === 'success')
                        <span class="badge badge-success">Sukses</span>
                    @else
                        -
                    @endif
                  </dd>

                  <dt class="col-sm-3">Teknisi</dt>
                  <dd class="col-sm-8">: {{$data->user->name}}</dd>
                  <dt class="col-sm-3">Id Withdraw</dt>
                  <dd class="col-sm-8">: {{$data->withdraw_id}}</dd>
                  <dt class="col-sm-3">Total Penarikan</dt>
                  <dd class="col-sm-8">: {{ rupiah($data->amount??0) }}
                  <dt class="col-sm-3">Note</dt>
                  <dd class="col-sm-8">: {{ $data->note }}
                  <hr>
                  </dd>
                  <dt class="col-sm-3">Saldo Sebelum Penarikan </dt>
                  <dd class="col-sm-8">: {{ rupiah($data->user->balance+$data->amount??0) }}</dd>
                  <dt class="col-sm-3">Saldo Setelah Penarikan </dt>
                  <dd class="col-sm-8">: {{ rupiah($data->user->balance??0) }}
                    <hr>
                  
                  </dd>
                  <dt class="col-sm-3">Di buat</dt>
                  <dd class="col-sm-8">: {{ $data->created_at }}
                  </dd>
                  <dt class="col-sm-3">Di Update</dt>
                  <dd class="col-sm-8">: {{ $data->updated_at }}

                    <hr>

                  @if($data->status==="success" || $data->status==="decline")
                  <dt class="col-sm-3">Verifikasi By</dt>
                  <dd class="col-sm-8">: {{ $data->verified->name??'-' }}
                  <dt class="col-sm-3">Verifikasi Time</dt>
                  <dd class="col-sm-8">: {{ $data->verified_at }}
                  @endif

                  <dt class="col-sm-3"></dt>
                  <dd class="col-sm-8">
                  @if($data->status==="pending")
                    <a href="{{ route('withdraw.confirm.accept', $data->id) }}" onclick="return confirm('Apakah anda yakin ? ')" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i> Diterima</a>
                    <a href="{{ route('withdraw.confirm.decline', $data->id) }}" onclick="return confirm('Apakah anda yakin ? ')" class="btn btn-sm btn-danger"><i class="fa fa-times-circle"></i> Ditolak</a>
                  @endif
                    <a href="{{ route('withdraw.technician.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
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