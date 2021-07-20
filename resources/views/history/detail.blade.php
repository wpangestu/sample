@extends('layouts.app_layout')
@section('title','Ulasan Pelanggan Detail')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">History Log</h1>
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
                <h3 class="card-title">History Log Detail</h3>
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
                <div class="row">
                    <div class="col-md-6"> 
                            <div class="form-group">
                                <label for="inputServiceOrder" class="col-form-label">Tanggal</label>
                                <input type="text" class="form-control" readonly value="{{$data->created_at->format('d-m-Y H:i:s')}}">
                            </div>
                            <div class="form-group">
                                <label for="inputRatings" class="col-form-label">Role</label>

                            </div>
                            <div class="form-group">
                                <label for="inputRatings" class="col-form-label">Nama</label>

                            </div>
                            <div class="form-group">
                                <label for="inputDescription" class="col-form-label">Deskripsi</label>
                                <textarea name="description" readonly class="form-control" id="inputDescription" rows="3"></textarea>
                            </div>
                    </div>
                    <div class="col-md-6">

                        <div class="form-group">
                            <label for="inputDescription" class="col-form-label">Detail</label>
                            <textarea name="description" readonly class="form-control" id="inputDescription" rows="5">
                            @foreach ($data->properties as $key => $value)
                                {{ $key }} => 
                                    @if (is_array($value))
                                        @foreach ($value as $key => $val)
                                            {{ $key }} => {{ $val }}                                           
                                        @endforeach
                                    @else
                                        {{$value}}
                                    @endif
                            @endforeach
                            </textarea>
                        </div>
                        <div class="form-group">
                            <label for="inputDescription" class="col-form-label">Detail</label>
                            <textarea name="description" readonly class="form-control" id="inputDescription" rows="5">
                                {{ var_dump($data->properties) }}
                            </textarea>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="inputDescription" class="col-form-label">Detail</label>
                            <textarea name="description" readonly class="form-control" id="inputDescription" rows="20">
@foreach ($data->changes as $key => $value)
{{ $key }} =>
@if (is_array($value))
    @foreach ($value as $key => $val)
    {{ $key }} => {{$val}}                                    
    @endforeach
@else
    {{$value}}
@endif
====================================================
@endforeach
                            </textarea>
                        </div>
                    </div>
                </div>
                <a class="btn btn-sm btn-secondary" href="{{ route('history.index') }}">Kembali</a>
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