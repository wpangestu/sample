@extends('layouts.app_layout')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Ulasan Pelanggan</h1>
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
                <h3 class="card-title">Tambah Ulasan Pelanggan</h3>
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
                        <form action="{{route('review_service.store')}}" enctype="multipart/form-data" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="inputServiceOrder" class="col-form-label">Pilih Service Order</label>
                                <select class="form-control" name="service_order_id" id="inputServiceOrder">
                                    <option value="">PILIH</option>
                                    @foreach($service_orders as $service_order)
                                        <option value="{{ $service_order->id }}">{{ $service_order->serviceorder_id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputRatings" class="col-form-label">Rating</label>
                                <input type="number" class="form-control" name="ratings" min="0" max="5" id="inputRatings">
                            </div>
                            <div class="form-group">
                                <label for="inputDescription" class="col-form-label">Deskripsi</label>
                                <textarea name="description" class="form-control" id="inputDescription" rows="5"></textarea>
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