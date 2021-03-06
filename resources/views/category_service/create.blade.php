@extends('layouts.app_layout')
@section('title','Tambah Kategori Jasa')
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
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('service_category.index') }}">Kategori Jasa</a></li>
              <li class="breadcrumb-item active">Tambah Kategori Jasa</li>
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
                <h3 class="card-title">Tambah Kategori Jasa</h3>
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
                        <form action="{{route('service_category.store')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-2 col-form-label">Nama *</label>
                                <div class="col-sm-10">
                                    <input type="text" name="name" class="form-control" id="inputName" placeholder="Nama">
                                </div>
                            </div>
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
                                        <input class="custom-control-input" name="active" type="checkbox" id="inputActive" value="1" checked>
                                        <label for="inputActive" class="custom-control-label">Aktif</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputSlug" class="col-sm-2 col-form-label">Slug</label>
                                <div class="col-sm-10">
                                  <input type="text" name="slug" class="form-control" id="inputSlug" placeholder="Nama">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputStatus" class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                    <a href="{{ route('service_category.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
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
      $('#inputName').on('keyup',function(){
        const text = $(this).val().toLowerCase()
                                  .replace(/ /g,'-')
                                  .replace(/[^\w-]+/g,'');
        $('#inputSlug').val(text);
      })
    });
</script>

@endsection
