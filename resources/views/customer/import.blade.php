@extends('layouts.app_layout')
@section('title','Import Pelanggan')
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
                <h3 class="card-title">Import Pelanggan</h3>
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

                        <form action="{{route('customer.store.import')}}" enctype="multipart/form-data" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="inputName" class="col-form-label">File untuk di import</label>
                                <input type="file" class="form-control" name="excel">
                            </div>
                    </div>
                    <div class="col-md-6">

                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label for="inputStatus" class="col-form-label"></label>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </div>
                <div class="row">
                  <a href="{{ asset('files/import/import_customer.xlsx') }}" class="btn bg-purple"><i class="fa fa-download"></i> Download File Template</a>
                </div>
                </form>
              </div>
              <!-- /.card-body -->
            </div>

          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->

        <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-text-width"></i>
                  Keterangan File Template Import
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <dl>
                  <dt>Nama</dt>
                  <dd>Di isi dengan nama lengkap | contoh : DEDE SUHERMAN | <b>Wajib Diisi</b></dd>
                  <dt>Email</dt>
                  <dd>Di isi dengan email pelanggan | ex: dede@gmail.com | <b>Wajib Diisi</b></dd>
                  <dt>Phone</dt>
                  <dd>Di isi dengan no HP pelanggan | ex: 081313543xxx | <b>Wajib Diisi</b></dd>
                  <dt>Password</dt>
                  <dd>Di isi dengan password pelanggan | ex: dede123 | <b>Wajib Diisi</b></dd>
                  <dt>Alamat</dt>
                  <dd>Di isi dengan alamat pelanggan | ex: Jln. Merdeka No 1 | <b>Wajib Diisi</b></dd>
                  <dt>Aktif</dt>
                  <dd>Di isi untuk menentukan pelanggan itu sudah aktif atau tidak| 0: Tdk Aktif, 1: Aktif | <b>Wajib Diisi</b></dd>
                </dl>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>

      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

@endsection
