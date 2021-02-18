@extends('layouts.app_layout')
@section('title','Tambah Ulasan Pelanggan')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Manajemen Akun</h1>
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
                <h3 class="card-title">Tambah Akun</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
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
                <form action="{{route('manajement_account.store')}}" enctype="multipart/form-data" method="post">
                <div class="row">
                    <div class="col-md-6"> 
                            @csrf
                        <div class="form-group">
                            <label for="inputNama" class="col-form-label">Nama</label>
                            <input type="text" class="form-control" value="{{ old('name') }}" name="name" id="inputNama">
                        </div>
                        <div class="form-group">
                            <label for="inputEmail" class="col-form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" id="inputEmail">
                        </div>
                        <div class="form-group">
                            <label for="inputPhone" class="col-form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" value="{{old('phone')}}" id="inputPhone">
                        </div>
                        <div class="form-group">
                            <label for="inputAddress" class="col-form-label">Alamat</label>
                            <textarea name="address" class="form-control" id="inputAddress" cols="30" rows="3">{{ old('address') }}</textarea>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="inputPhoto" class="col-form-label">Photo</label>
                            <input type="file" class="form-control" name="photo" id="inputPhoto">
                            <span class="text-muted rext-sm">format:jpeg,png,jpg|max:2048kb</span>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword" class="col-form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="inputPassword">
                        </div>
                        <div class="form-group">
                            <label for="inputConfirmPassword" class="col-form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" name="password_confirmation" id="inputConfirmPassword">
                        </div>
                        <div class="form-group">
                            <label for="inputPhone" class="col-form-label">Role</label>
                            <div class="icheck-primary">
                                <input name="role[]" type="checkbox" value="admin" id="cb_role_admin"/>
                                <label for="cb_role_admin">admin</label>
                            </div>
                            <div class="icheck-primary">
                                <input name="role[]" type="checkbox" value="cs" id="cb_role_cs" />
                                <label for="cb_role_cs">customer service</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label for="inputStatus" class="col-form-label"></label>
                        <a href="{{ route('manajement_account.index') }}" class="btn btn-secondary">Kembali</a>
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


@section('scripts')

  <script>
    $(document).ready(function(){
      // $('#inputCustomer').select2();
      // $('#inputEngineer').select2();
      // $('#inputService').select2();
    });
  </script>

@endsection