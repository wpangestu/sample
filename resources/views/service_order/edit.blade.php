@extends('layouts.app_layout')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Service Order</h1>
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
                <h3 class="card-title">Ubah Pesanan Jasa</h3>
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
                        <form action="{{route('service_order.update',$service_order->id)}}" enctype="multipart/form-data" method="post">
                            @method('put')
                            @csrf
                            <div class="form-group">
                                <label for="inputCustomer" class="col-form-label">ID</label>
                                <input type="text" readonly class="form-control" value="{{ $service_order->serviceorder_id }}">
                            </div>
                            <div class="form-group">
                                <label for="inputCustomer" class="col-form-label">Pelanggan</label>
                                <select class="form-control" name="customer_id" id="inputCustomer">
                                    <option value="">PILIH</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $customer->id==$service_order->customer_id?'selected':'' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputEngineer" class="col-form-label">Teknisi</label>
                                <select class="form-control" name="engineer_id" id="inputEngineer">
                                    <option value="">PILIH</option>
                                    @foreach($engineers as $engineer)
                                        <option value="{{ $engineer->id }}" {{ $engineer->id == $service_order->engineer_id?'selected':'' }}>{{ $engineer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputService" class="col-sm-2 col-form-label">Service</label>
                                <select class="form-control" name="service_id" id="inputService">
                                    <option value="">--PILIH--</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ $service->id==$service_order->service_id?'selected':'' }}>{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputStatus" class="col-sm-2 col-form-label">Status</label>
                                <select class="form-control" name="status" id="inputStatus">
                                    <option value="">--PILIH--</option>
                                    @foreach($status as $s)
                                        <option value="{{ $s }}" {{ $s==$service_order->status?'selected':'' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputDescription" class="col-form-label">Deskripsi</label>
                                <textarea name="description" class="form-control" id="inputDescription" rows="5">{{ $service_order->description }}</textarea>
                            </div>
                    </div>
                    <div class="col-md-6">

                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label for="inputStatus" class="col-form-label"></label>
                        <button class="btn btn-primary">Ubah</button>
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
