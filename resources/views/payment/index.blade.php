@extends('layouts.app_layout')
@section('title','Pembayaran')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Konfirmasi Pembayaran</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Konfirmasi Pembayaran</li>
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
                <h3 class="card-title">List Pembayaran</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <!-- <a href="{{route('services.create')}}" class="btn btn-primary mb-3">Tambah</a> -->
                
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

                <table id="table-datatables" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>ID Pembayaran</th>
                    <th>Tipe</th>
                    <th>Total</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                  </thead>
                <!-- for sample data -->
                    <tbody>
                        @foreach($payment as $val)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $val->created_at->format('d/m/Y') }}<br>{{ $val->created_at->format('H:i:s') }}</td>
                            <td><a href="#">{{ $val->paymentid }}</a> </td>
                            <td>{{ ucfirst($val->type_payment) }}<br>ref : {{$val->data_id}}</td>
                            <td>{{ rupiah($val->amount+($val->convenience_fee??0)) }}</td>
                            <td>{{ $val->customer->name }}</td>
                            <td>
                              @if($val->status === 'check')
                                <span class="badge badge-info">Menunggu Konfirmasi</span>
                              @elseif($val->status === 'pending')
                                <span class="badge badge-warning">Menunggu Pembayaran</span>
                              @elseif($val->status === 'decline')
                                <span class="badge badge-danger">Ditolak</span>
                              @elseif($val->status === 'success')
                                <span class="badge badge-success">Sukses</span>
                              @else
                                -
                              @endif
                            </td>
                            <td>
                              <button type="button" class="btn btn-xs btn-secondary dropdown-toggle" data-toggle="dropdown">
                                  Aksi
                              </button>
                              <ul class="dropdown-menu">
                                  <li class="dropdown-item"><a href="{{ route('payment.order.detail',$val->id) }}" title="Detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                                  <!-- <li class="dropdown-item"><a href="'.route('payment.order.edit',$row->payment_id??'#').'" data-original-title="Buat Pembayaran"><i class="fa fa-money"></i> Buat Pembayaran</a></li> -->
                              </ul>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                <!-- endsample -->
                </table>
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

        $('#table-datatables').DataTable();

      $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

        // let table = $('#table-datatables').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     ajax: "{{ route('services.index') }}",
        //     columns: [
        //         {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false,width:'5%'},
        //         {data: 'name', name: 'name'},
        //         {data: 'service_category_id', name: 'service_category_id'},
        //         {data: 'price', name: 'price'},
        //         {data: 'action', name: 'action', orderable: false, searchable: false},
        //     ]
        // });

        $('body').on('click', '.btn_delete', function () {
            const url = $(this).data('url');

            if (confirm("Apakah anda yakin?") == true) {
                // ajax
                $.ajax({
                    type:"POST",
                    url: url,
                    dataType: 'json',
                    success: function(res){
                        var oTable = $('#table-datatables').dataTable();
                        oTable.fnDraw(false);
                    }
                });
            }

        })

    });
</script>

@endsection