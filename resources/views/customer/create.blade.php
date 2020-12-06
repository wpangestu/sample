@extends('layouts.app_layout')
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
                <h3 class="card-title">Tambah Pelanggan</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{route('customer.store')}}" enctype="multipart/form-data" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="inputName" class="col-form-label">Nama</label>
                                <input type="text" name="name" class="form-control" id="inputName" placeholder="Nama" value="{{ old('name') }}">
                            </div>
                            <div class="form-group">
                                <label for="inputEmail" class="col-form-label">Email</label>
                                <input type="email" name="email" class="form-control" id="inputEmail" value="{{ old('email') }}" placeholder="Email">
                            </div>
                            <div class="form-group">
                                <label for="inputPhone" class="col-sm-2 col-form-label">No Hp</label>
                                <input type="text" name="phone" class="form-control" id="inputPhone" placeholder="No hp" value="{{ old('phone') }}">
                            </div>
                            <div class="form-group">
                                <label for="inputPassword" class="col-form-label">Password</label>
                                <input type="password" name="password" class="form-control" id="inputPassword">
                            </div>
                            <div class="form-group">
                                <label for="inputConfirmPassword" class="col-form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" id="inputConfirmPassword">
                            </div>
                            <div class="form-group">
                                <label for="inputStatus" class="col-form-label">Status</label>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" name="active" type="checkbox" id="inputActive" value="1" checked>
                                    <label for="inputActive" class="custom-control-label">Aktif</label>
                                </div>
                            </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="inputAddress" class="col-form-label">Photo Profil</label>
                            <input type="file" class="form-control" name="photo">
                            <span class="text-muted">format:jpeg,png,jpg|max:2048kb</span>
                        </div>
                        <div class="form-group">
                            <label for="inputAddress" class="col-form-label">Alamat</label>
                            <textarea name="address" class="form-control" id="inputAddress" rows="5">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label for="inputStatus" class="col-form-label"></label>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </div>
                </form>
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
