@extends('layouts.app_layout')
@section('title','Detail Payment')
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
                <h3 class="card-title">Detail Pembayaran</h3>
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

                <dl class="row">
                  <dt class="col-sm-3">Status</dt>
                  <dd class="col-sm-8">: 
                    @if($payment->status === 'check')
                        <span class="badge badge-info">Menunggu Konfirmasi</span>
                    @elseif($payment->status === 'pending')
                        <span class="badge badge-warning">Menunggu Pembayaran</span>
                    @elseif($payment->status === 'decline')
                        <span class="badge badge-danger">Ditolak</span>
                    @elseif($payment->status === 'success')
                        <span class="badge badge-success">Sukses</span>
                    @else
                        -
                    @endif
                  </dd>

                  <dt class="col-sm-3">Customer</dt>
                  <dd class="col-sm-8">: {{$payment->customer->name}}</dd>
                  <dt class="col-sm-3">Id Pembayaran</dt>
                  <dd class="col-sm-8">: {{$payment->paymentid}}</dd>
                  <dt class="col-sm-3">Total</dt>
                  <dd class="col-sm-8">: {{ rupiah($payment->amount+($payment->convenience_fee??0)) }}</dd>
                  <dt class="col-sm-3">Tipe Pembayaran</dt>
                  <dd class="col-sm-8">: {{ ucfirst($payment->type_payment) }} / (#{{$payment->data_id}})</dd>
                  <dt class="col-sm-3">Pembayaran Tujuan</dt>
                  <dd class="col-sm-8">: {{ $payment->bank->name." (".($payment->bank->account_number??'').")" }}</dd>
                  <dt class="col-sm-3">Rekening Pengirim</dt>
                  <dd class="col-sm-8">: {{ $payment->account_number." (".($payment->account_holder??'-').")" }}</dd>
                  <dt class="col-sm-3">Bukti Struk Pembayaran</dt>
                  <dd class="col-sm-8">
                    @if( !is_null($payment->image) )
                      <img src="{{ $payment->image }}" class="img-fluid">
                    @else
                    -
                    @endif
                  </dd>

                  <dt class="col-sm-3">Di buat</dt>
                  <dd class="col-sm-8">: {{ $payment->created_at }}
                  </dd>
                  <dt class="col-sm-3">Di Update</dt>
                  <dd class="col-sm-8">: {{ $payment->updated_at }}

                    <hr>

                  @if($payment->status==="success" || $payment->status==="decline")
                  <dt class="col-sm-3">Verifikasi By</dt>
                  <dd class="col-sm-8">: {{ $payment->verified_name }}
                  <dt class="col-sm-3">Verifikasi Time</dt>
                  <dd class="col-sm-8">: {{ $payment->verified_at }}
                  @endif

                  <dt class="col-sm-3"></dt>
                  <dd class="col-sm-8">
                  @if($payment->status==="check")
                    <a href="{{ route('payment.order.confirm_acc', $payment->id) }}" onclick="return confirm('Apakah anda yakin ? ')" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i> Diterima</a>
                    <a href="{{ route('payment.order.confirm_dec', $payment->id) }}" onclick="return confirm('Apakah anda yakin ? ')" class="btn btn-sm btn-danger"><i class="fa fa-times-circle"></i> Ditolak</a>
                  @endif
                    <a href="{{ route('payment.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                  </dd>
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
        $('#skill').select2('readonly', true);
    });
</script>

@endsection