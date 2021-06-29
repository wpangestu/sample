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
                      <label for="">Tanggal Order</label>
                      <input type="text" readonly value="{{ $order->created_at }}" class="form-control"> 
                    </div>                    
                  </div>                
                </div>

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
                      @if($order->order_status === "payment_success"||$order->order_status === "waiting_order")
                      <div class="form-group">
                        <label for="">Aksi</label><br>
                        <button class="btn btn-sm btn-info" id="btn_search_technician"><i class="fa fa-search"></i> Mencari Teknisi</button>
                          <!-- <a onclick="return confirm('Apa anda yakin?')" href="#" class="btn btn-sm btn-info"><i class="fa fa-search"></i> Cari Teknisi</a> -->
                      </div>
                      @endif
                      @if($order->order_status === "accepted")
                      <div class="form-group">
                      <!-- <label for="">Diterima Teknisi</label><br> -->
                        <!-- <a onclick="return confirm('Apa anda yakin?')" href="{{ route('service_order.process_decline',['orderid'=>$order->order_number,'status'=>'processed']) }}" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i> Proses Order</a>
                        <a onclick="return confirm('Apa anda yakin?')" href="{{ route('service_order.process_decline', ['orderid'=>$order->order_number,'status'=>'decline']) }}" class="btn btn-sm btn-danger"><i class="fa fa-times-circle"></i> Tolak Order</a> -->
                      </div>
                      @endif
                      @if($order->order_status === "processed")
                      <div class="form-group">
                      <!-- <label for="">Sedang diproses Teknisi</label><br> -->
                        <!-- <label for="">Aksi</label><br>
                        <a onclick="return confirm('Apa anda yakin?')" href="{{ route('service_order.process_decline',['orderid'=>$order->order_number,'status'=>'take-away']) }}" class="btn btn-sm btn-info"><i class="fa fa-hands-helping"></i> Extend</a>
                        <a onclick="return confirm('Apa anda yakin?')" href="{{ route('service_order.process_decline', ['orderid'=>$order->order_number,'status'=>'done']) }}" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i> Selesai</a>
                        <a onclick="return confirm('Apa anda yakin?')" href="{{ route('service_order.process_decline', ['orderid'=>$order->order_number,'status'=>'canceled']) }}" class="btn btn-sm btn-danger"><i class="fa fa-times-circle"></i> Dibatalkan</a> -->
                      </div>
                      @endif
                      @if($order->order_status === "extend")
                      <div class="form-group">
                      <!-- <label for="">Pengerjaan di perpanjang</label><br> -->
                        <!-- <label for="">Aksi</label><br>
                        <a onclick="return confirm('Apa anda yakin?')" href="{{ route('service_order.process_decline', ['orderid'=>$order->order_number,'status'=>'done']) }}" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i> Selesai</a>
                        <a onclick="return confirm('Apa anda yakin?')" href="{{ route('service_order.process_decline', ['orderid'=>$order->order_number,'status'=>'canceled']) }}" class="btn btn-sm btn-danger"><i class="fa fa-times-circle"></i> Dibatalkan</a> -->
                      </div>
                      @endif
                      @if($order->order_status === "done")
                        <div class="form-group">
                          <img style="
                          width: 50%;
                          height: 200px;
                          object-fit: cover;
                          object-position: center;
                          " src="{{ $order->photo }}" class="img-fluid">
                          <br>
                          <label for="">Review Customer</label><br>
                        @if($order->review()->exists())
                            <i class="text-warning fa fa-star"></i> {{$order->review->ratings}}<br>
                            <label class="text-muted">Likes</label>
                            <select class="form-control select2" multiple>
                            @if (is_array($order->review->liked))
                              @foreach ($order->review->liked as $val)
                                <option selected>{{ $val }}</option>                                
                              @endforeach
                            @endif
                            </select>
                            <label class="text-muted">Komentar</label>
                            <textarea rows="3" class="form-control" readonly>{{ $order->review->description??'-' }}</textarea>
                          @else
                          <span>Belum Ada Review</span> [<a href="{{ route('review_service.create',[$order->id]) }}">Tambahkan</a>]
                        @endif
                        </div>
                      @endif
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="">Customer</label>
                        <input type="text" readonly value="{{ $order->customer->userid." - ".$order->customer->name }}" class="form-control"> 
                      </div>
                      <div class="form-group">
                        <label for="">Alamat Posisi Customer</label>
                        @php $address = json_decode($order->address) @endphp
                        <textarea type="text" readonly class="form-control">{{ $address->description??'-' }} | Lat: {{ $address->latitude??'-' }} | Lng: {{ $address->longitude??'-' }} | Note: {{ $address->notes??'-' }} </textarea> 
                      </div>
                      <hr>
                      @if($order->engineer()->exists())
                      <div class="form-group">
                        <label for="">Teknisi</label>
                        <input type="text" readonly value="{{ $order->engineer->userid." - ".$order->engineer->name }}" class="form-control"> 
                      </div>
                      <div class="form-group">
                        <label for="">Alamat Teknisi</label>
                        <textarea type="text" readonly class="form-control">{{ $order->origin??'-' }}</textarea> 
                      </div>
                      @endif
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
                            @php
                              $fee = 0;
                            @endphp
                            @foreach($order->order_detail as $val)
                              @php
                                $fee += $val->base_service->price_receive;
                              @endphp
                              <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $val->name }}</td>
                                <td>{{ $val->qty }}</td>
                                <td class="text-right">{{ rupiah($val->price) }}</td>
                              </tr>
                            @endforeach
                              <tr>
                                <td class="text-right" colspan="3">Ongkos Kirim</td>
                                <td class="text-right">{{ rupiah($order->shipping) }}</td>
                              </tr>
                              <tr>
                                <td class="text-right" colspan="3">Unique Code</td>
                                <td class="text-right">{{ rupiah($order->convenience_fee) }}</td>
                              </tr>
                              <tr>
                                <td class="text-right" colspan="3">Total</td>
                                <td class="text-right">{{ rupiah($order->total_payment) }}</td>
                              </tr>
                              <tr>
                                <td class="text-right" colspan="3">Fee Teknisi</td>
                                <td class="text-right">{{ rupiah($fee) }}</td>
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

      $('.select2').select2("readonly", true);

      $('#btn_search_technician').click(function(){

        // fetch("{{ route('service_order.update_waiting_order',$order->id) }}")
        fetch("{{ route('service_order.update_waiting_order',$order->id) }}")
          .then( result => console.log(result.json))
          // .then( text => console.log(text))
          .catch(error => alert(error));

        $.blockUI.defaults.baseZ = 9999999;
        $.blockUI({
            message: '<i class="fa fa-sync fa-spin"></i> Loading <br>(Tunggu 30 Detik)',
            timeout: 30000, //unblock after 2 seconds
            overlayCSS: {
                backgroundColor: '#FFF',
                opacity: 0.8,
                cursor: 'wait'
            },
            css: {
                border: 0,
                padding: 0,
                color: '#333',
                backgroundColor: 'transparent'
            },
            onUnblock: function() {
              location.reload();
            }
        });
      });

    });
</script>

@endsection