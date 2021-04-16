@extends('layouts.app_layout')
@section('title','Saldo Pelanggan')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Saldo</h1>
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
                <h3 class="card-title">Saldo Pelanggan Detail : <a href="#">{{$user->name}}</a></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <!-- <a href="{{route('customer.create')}}" class="btn btn-primary mb-3"><i class="fa fa-plus"></i> Tambah</a>
                    <a href="{{route('customer.import')}}" class="btn bg-teal mb-3 float-right">Import</a> -->
                  </div>
                </div>
                
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
                <div class="table-resonsive">
                    <div class="col-12 col-sm-4">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                                <span class="info-box-text text-center text-muted"><i class="fa fa-wallet"></i> Saldo</span>
                                <span class="info-box-number text-center text-muted mb-0">Rp 250.000</span>
                            </div>
                        </div>
                    </div>

                    <h4>Riwayat Saldo</h4>
                    
                    <table id="table_datatable_1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pelanggan ID</th>
                                <th>Nama</th>
                                <th>Saldo</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
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

  <!-- Model -->

  <div class="modal fade" id="modal_change_balance">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ubah Saldo Pelanggan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('balance.update') }}" method="post">
                @csrf 
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="inputName" class="col-sm-4 col-form-label">Nama</label>
                        <div class="col-sm-8">
                            <input type="text" name="name" class="form-control" readonly id="inputName" placeholder="Nama">
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label for="inputUserId" class="col-sm-4 col-form-label">ID Customer</label>
                        <div class="col-sm-8">
                            <input type="text" name="userid" class="form-control" readonly id="inputUserId" placeholder="Nama">
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label for="inputSaldo" class="col-sm-4 col-form-label">Saldo</label>
                        <div class="col-sm-8">
                            <input type="number" min="0" name="balance" class="form-control" id="inputSaldo" placeholder="Nama">
                        </div>
                    </div>                    
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
   </div>
    <!-- /.modal -->

@endsection

@section('scripts')

<script>

    $(document).ready(function(){

        $(document).on('click','.btn_change',function(){
            let name = $(this).data('name');
            let userid = $(this).data('id');
            let saldo = $(this).data('balance');
            $('#modal_change_balance #inputName').val(name)
            $('#modal_change_balance #inputUserId').val(userid)
            $('#modal_change_balance #inputSaldo').val(saldo)
            $('#modal_change_balance').modal('show');
        })

      $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

        let table = $('#table_datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('balance.customer.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false,width:'5%'},
                {data: 'userid', name: 'userid'},
                {data: 'name', name: 'name'},
                {data: 'balance', name: 'balance'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('body').on('click', '.btn_delete', function () {
            const url = $(this).data('url');

            if (confirm("Apakah anda yakin?") == true) {
                // ajax
                $.ajax({
                    type:"POST",
                    url: url,
                    dataType: 'json',
                    success: function(res){
                        var oTable = $('#table_datatable').dataTable();
                        oTable.fnDraw(false);
                    }
                });
            }

        })



    });
</script>

@endsection