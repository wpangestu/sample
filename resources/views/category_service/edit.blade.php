@extends('layouts.app_layout')
@section('title','Ubah Kategori Jasa')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Kategori Jasa</h1>
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
                <h3 class="card-title">Ubah Kategori Jasa</h3>
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

                        <form action="{{route('service_category.update',$data->id)}}" method="post" enctype="multipart/form-data">
                            @method('put')
                            @csrf
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-2 col-form-label">Nama*</label>
                                <div class="col-sm-10">
                                    <input type="text" name="name" class="form-control" id="inputName" value="{{$data->name}}" placeholder="Nama">
                                </div>
                            </div>
                            @if(!(is_null($data->icon)))
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-2 col-form-label">Ikon Sebelum</label>
                                <div class="col-sm-10">
                                  <img src="{{ $data->icon }}" alt="" height="50px">
                                </div>
                            </div>
                            @endif
                            <div class="form-group row">
                                <label for="inputIcon" class="col-sm-2 col-form-label">Ikon</label>
                                <div class="col-sm-10">
                                    <input type="file" name="icon" id="icon" class="form-control">
                                    <span class="text-muted text-sm"><i>format: jpeg, png, jpg | max: 2048kb</i></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputStatus" class="col-sm-2 col-form-label">Status</label>
                                <div class="col-sm-10">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" name="active" type="checkbox" id="inputActive" value="1" {{ $data->status==1?'checked':'' }}>
                                        <label for="inputActive" class="custom-control-label">Aktif</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputStatus" class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-10">
                                    <button class="btn btn-info">Ubah</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">

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
