@extends('layouts.app_layout')
@section('title','Edit Bank')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Pengaturan Bank</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('banks.index') }}">Master Bank</a></li>
            <li class="breadcrumb-item active">Ubah</li>
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
                <h3 class="card-title">Ubah Bank</h3>
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

                        <form action="{{route('banks.update',$data->id)}}" method="post" enctype="multipart/form-data">
                            @method('put')
                            @csrf
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-3 col-form-label">Nama Bank*</label>
                                <div class="col-sm-9">
                                    <input type="text" name="name" id="inputName" value="{{ old('name',$data->name) }}" class="form-control">
                                </div>
                            </div>
                            @isset($data->logo)
                            <div class="form-group row">
                                <label for="inputNoAccount" class="col-sm-3 col-form-label">Logo Sebelum</label>
                                <div class="col-sm-9">
                                    <!-- <input type="text" name="account_number" value="{{ old('account_number',$data->account_number) }}" id="inputNoAccount" class="form-control"> -->
                                    <img src="{{ $data->logo }}" height="100px" alt="">
                                </div>
                            </div>
                            @endisset
                            <div class="form-group row">
                                <label for="inputIcon" class="col-sm-3 col-form-label">Logo</label>
                                <div class="col-sm-9">
                                    <input type="file" name="logo" id="inputIcon" class="form-control">
                                    <span class="text-muted text-sm"><i>format: jpeg, png, jpg | max: 2048kb</i></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputStatus" class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-9">
                                  <select required name="inputStatus" class="form-control">
                                    <option value="">Pilih</option>
                                    <option value="on" {{ $data->is_active?'selected':'' }}>Aktif</option>
                                    <option value="off" {{ !($data->is_active)?'selected':'' }}>Non Aktif</option>
                                  </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputStatus" class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <button class="btn btn-primary">Simpan</button>
                                    <a href="{{ route('banks.index') }}" class="btn btn-secondary">Kembali</a>
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