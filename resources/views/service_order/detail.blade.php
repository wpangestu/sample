@extends('layouts.app_layout')
@section('title','Detail Order')
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
                <h3 class="card-title">Detail Order</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <!-- <a href="{{route('service_order.create')}}" class="btn btn-primary mb-3"><i class="fa fa-plus"></i> Tambah</a> -->
                
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
                @php $address = json_decode($order->address) @endphp
                <div class="row">
                    <div class="col-md-6">

                      <div class="form-group">
                        <label for="">Order Number</label>
                        <input type="text" readonly value="{{ $order->order_number }}" class="form-control"> 
                      </div>
                      <div class="form-group">
                        <label for="">Tipe Order</label>
                        <input type="text" readonly value="{{ $order->order_type }}" class="form-control"> 
                      </div>                    
                      <div class="form-group">
                        <label for="">Status Order</label>
                        <div class="status">
                          {!! $status !!}
                        </div>
                      </div>                    
                      <div class="form-group">
                        <label for="">Take Away</label>
                        <input type="text" readonly value="{{ $order->is_take_away?'Ya':'Tidak' }}" class="form-control"> 
                      </div>                    

                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="">Customer</label>
                        <input type="text" readonly value="{{ $order->customer->userid." - ".$order->customer->name }}" class="form-control"> 
                      </div>
                      <div class="form-group">
                        <label for="">Alamat Posisi Customer</label>
                        @php $address = json_decode($order->address) @endphp
                        <textarea type="text" readonly class="form-control">{{ $address->name??'-' }} | Lat: {{ $address->lat??'-' }} | Lng: {{ $address->lng??'-' }}</textarea> 
                      </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <h5>Detail Jasa</h5>
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th width="5px">No</th>
                              <th>Jasa</th>
                              <th>Qty</th>
                              <th>Harga</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($order->order_detail as $val)
                              <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $val->name }}</td>
                                <td>{{ $val->qty }}</td>
                                <td class="text-right">{{ $val->price }}</td>
                              </tr>
                            @endforeach
                              <tr>
                                <td class="text-right" colspan="3">Ongkos Kirim</td>
                                <td class="text-right">{{ $order->shipping }}</td>
                              </tr>
                              <tr>
                                <td class="text-right" colspan="3">Total</td>
                                <td class="text-right">{{ $order->total_payment }}</td>
                              </tr>
                          </tbody>
                        </table>
                        <a href="{{ route('service_order.index') }}" class="btn btn-secondary">Kembali</a>
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



    });
</script>

@endsection