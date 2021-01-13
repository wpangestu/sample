@extends('layouts.app_layout')
@section('title','Term of Service')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Pengaturan</h1>
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
                <h3 class="card-title">Term of Service</h3>
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

                    @if(empty($termOfService))
                        <div class="alert alert-info"><b><i class="fa fa-info-circle"></i> Term of Service</b> belum tersedia silahkan menambahkan</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                <div class="row">
                    <div class="col-md-12">
                        @php 
                            if(empty($termOfService)){
                                $route = route('setting.term_of_service.store');
                            }else{
                                $route = route('setting.term_of_service.update', $termOfService->id);
                            }
                        @endphp
                        <form action="{{ $route }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="inputTitle" class="col-form-label">Judul</label>
                                <input type="text" class="form-control" name="title" id="inputTitle" value="{{ $termOfService->title??'' }}">
                            </div>
                            <div class="form-group">
                                <label for="inputContent" class="col-form-label">Konten</label>
                                <textarea name="content" id="inputContent" rows="5">{{ $termOfService->content??'' }}</textarea>
                            </div>
                    </div>
                    <div class="col-md-6">

                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label for="inputStatus" class="col-form-label"></label>
                        <button class="btn btn-primary">{{ empty($termOfService)?'Simpan':'Ubah' }}</button>
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
        $('#inputContent').summernote();
    });
</script>

@endsection
