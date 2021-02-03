@extends('layouts.app_layout')
@section('title','Detail Kategori Jasa')
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
                <h3 class="card-title">Detail Kategori Jasa</h3>
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
                <!-- <button class="btn btn-sm btn-secondary"><i class="fa fa-arrow-circle-left"></i> Kembali</button> -->
                <dl class="row">
                  <dt class="col-sm-3">Nama</dt>
                  <dd class="col-sm-8">: {{ $categoryService->name??'-' }}</dd>
                  <dt class="col-sm-3">Ikon</dt>
                  <dd class="col-sm-8">: 
                    @if(!(is_null($categoryService->icon)))
                    <img src="{{$categoryService->icon}}" height="50px" alt="">
                    @else
                      -
                    @endif
                  </dd>
                  <dt class="col-sm-3">Status</dt>
                  <dd class="col-sm-8">: <span class="badge badge-{{ $categoryService->status===1?'success':'secondary' }}">{{ $categoryService->status===1?'Aktif':'Tidak Aktif' }}</span>
                  <dt class="col-sm-3">Di buat</dt>
                  <dd class="col-sm-8">: {{ $categoryService->created_at??'-' }}
                  </dd>
                  <dt class="col-sm-3">Di Update</dt>
                  <dd class="col-sm-8">: {{ $categoryService->updated_at??'-' }}
                  <dt class="col-sm-3"></dt>
                  <dd class="col-sm-8"><a href="{{ route('service_category.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-circle-left"></i> Kembali</a></dd>
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