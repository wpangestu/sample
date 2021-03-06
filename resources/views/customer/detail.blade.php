@extends('layouts.app_layout')
@section('title','Detail Pelanggan')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Pelanggan</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('customer.index') }}">Pelanggan</a></li>
              <li class="breadcrumb-item active">Detail</li>
            </ol>
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
                <h3 class="card-title">Detail Pelanggan</h3>
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
                  <dt class="col-sm-3">User ID</dt>
                  <dd class="col-sm-8">: {{ $data->userid??'-' }}</dd>
                  <dt class="col-sm-3">Nama</dt>
                  <dd class="col-sm-8">: {{ $data->name??'-' }}</dd>
                  <dt class="col-sm-3">Email</dt>
                  <dd class="col-sm-8">: {{ $data->email??'-' }}</dd>
                  <dt class="col-sm-3">Phone</dt>
                  <dd class="col-sm-8">: {{ $data->phone??'-' }}</dd>
                  <dt class="col-sm-3">Alamat</dt>
                  <dd class="col-sm-8">: {{ $data->address??'-' }}</span>
                  <dt class="col-sm-3">Status</dt>
                  <dd class="col-sm-8">: <span class="badge badge-{{ $data->is_active===1?'success':'secondary' }}">{{ $data->is_active===1?'Aktif':'Tidak Aktif' }}</span>
                  <dt class="col-sm-3">Rekening Customer</dt>
                  <dd class="col-sm-8">
                      <ul class="inline">
                         @forelse ($bank_accounts as $val)
                            <li>{{ $val->bank->name??'' }} {{$val->account_number??0}} - {{$val->account_holder??''}}</li>
                            @empty
                            -
                        @endforelse
                      </ul>                    
                        <hr>
                  </dd>
                  <dt class="col-sm-3">Di buat</dt>
                  <dd class="col-sm-8">: {{ $data->created_at??'-' }}</dd>
                  <dt class="col-sm-3">Di Update</dt>
                  <dd class="col-sm-8">: {{ $data->updated_at??'-' }}
                  <dt class="col-sm-3"></dt>
                  <dd class="col-sm-8"><a href="{{ route('customer.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-circle-left"></i> Kembali</a></dd>
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