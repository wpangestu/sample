@extends('layouts.app_layout')
@section('title','Buat Pembayaran')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Pembayaran</h1>
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
                <h3 class="card-title">Buat Pembayaran</h3>
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
                        <form action="{{route('payment.order.update',$payment->id)}}" enctype="multipart/form-data" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="order_id" class="col-form-label">ID Payment</label>
                                <input type="text" readonly name="order_id" value="{{ $payment->paymentid }}" class="form-control" id="order_id">
                            </div>
                            <div class="form-group">
                                <label for="total" class="col-form-label">Biaya Service + Jasa Teknisi ke lokasi</label>
                                <input type="number" readonly name="total" value="{{ $payment->amount }}" class="form-control" id="total">
                            </div>
                            <div class="form-group">
                                <label for="unique_number" class="col-form-label">Convenience Fee</label>
                                <input type="number" value="{{$convenience_fee}}" readonly name="unique_number" class="form-control" id="unique_number">
                            </div>
                            <div class="form-group">
                                <label for="total_payment" class="">Total Pembayaran</label>
                                <input type="text" name="price" value="{{ $payment->amount+$convenience_fee }}" id="total_payment" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="payment_method" class="">Metode Pembyaran</label>
                                <select name="payment_method" class="form-control" id="payment_method">
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="payment_gateway" class="">Pilih Bank</label>
                                <select name="payment_gateway" class="form-control" id="payment_gateway">
                                    <option value="BCA" {{ $payment->type==="BCA"?'selected':'' }}>BCA</option>
                                    <option value="BRI" {{ $payment->type==="BRI"?'selected':'' }}>BRI</option>
                                    <option value="MANDIRI" {{ $payment->type==="MANDIRI"?'selected':'' }}>MANDIRI</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="file_images" class="col-form-label">Upload Bukti Pembayaran</label><br>
                                <input type="file" name="image" id="file_images">
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