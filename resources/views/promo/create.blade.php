@extends('layouts.app_layout')
@section('title','Tambah Jasa')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Promo</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('promos.index') }}">Promo</a></li>
              <li class="breadcrumb-item active">Tambah Promo</li>
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
                <h3 class="card-title">Tambah Promo</h3>
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

                        <form action="{{route('promos.store')}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-3 col-form-label">Nama Promo*</label>
                                <div class="col-sm-9">
                                    <input type="text" name="name" class="form-control" id="inputName" value="{{ old('name') }}" placeholder="Nama">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputLongGuarantee" class="col-sm-3 col-form-label">Kode Promo*</label>
                                <div class="col-sm-9">
                                    <input type="text" name="promo_code" class="form-control" value="{{ old('promo_code') }}" id="inputLongGuarantee">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputIcon" class="col-sm-3 col-form-label">Nilai Potongan (IDR)*</label>
                                <div class="col-sm-9">
                                    <input type="number" name="value" class="form-control" value="{{ old('value') }}" id="inputLongGuarantee">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputDesc" class="col-sm-3 col-form-label">Deskripsi</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="description" id="inputDesc" cols="30" rows="5">{{ old('description') }}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputStatus" class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <button class="btn btn-sm btn-primary">Simpan</button>
                                    <a href="{{ route('promos.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                                </div>
                            </div>
                        </form>
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
    //   $('#inputCatSer').select2();
    })
  </script>

@endsection()