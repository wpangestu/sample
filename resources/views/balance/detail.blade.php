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
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ $user->hasRole('user')?route('balance.customer.index'):route('balance.engineer.index') }}">Saldo {{ $user->hasRole('user')?'Pelanggan':'Teknisi' }}</a></li>
              <li class="breadcrumb-item active">Detail</li>
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
                <h3 class="card-title">Saldo {{ $user->hasRole('user')?'Pelanggan':'Teknisi' }} Detail : <a href="#">{{$user->name}}</a></h3>
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
                <h4>Riwayat Saldo</h4>
                <div class="table-resonsive">
                  
                  <div class="row">
                      <div class="col-12 col-sm-6">
                          <div class="info-box bg-light bg-green">
                              <div class="info-box-content">
                                  <span class="info-box-text text-center text-light"><i class="fa fa-wallet"></i> Saldo</span>
                                  <span class="info-box-number text-center text-light mb-0">{{ rupiah($user->balance) }}</span>
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-sm-6">
                          <div class="info-box bg-light bg-red">
                              <div class="info-box-content">
                                  <span class="info-box-text text-center text-light"><i class="fa fa-money-bill"></i> Saldo ditarik</span>
                                  <span class="info-box-number text-center text-light mb-0">Rp 0</span>
                              </div>
                          </div>
                      </div>
                  </div>

                    <table id="table_datatable_1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="1%">No</th>
                                <th width="5%">Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Jumlah</th>
                                <th>Di Buat<br>Oleh</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach($historyBalance as $value)
                            <tr>
                              <td>{{ $loop->iteration }}</td>
                              <td>{{$value->created_at->format('d/m/Y') }}<br>{{$value->created_at->format('H:i:s') }}</td>
                              <td width="30%">{{ $value->description }}</td>
                              <td class="text-right">{{ rupiah($value->amount) }}</td>
                              <td>{{ $value->admin->name??'-' }}</td>
                              <td class="text-center">
                                  <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                                      Aksi
                                  </button>
                                  <ul class="dropdown-menu">
                                      <li class="dropdown-item"><a href="#" data-toggle="tooltip" 
                                      data-userid="{{ $value->user->userid }}" 
                                      data-date="{{ $value->created_at->format('d/m/Y H:i:s') }}" 
                                      data-id="{{ $value->id }}" 
                                      data-description="{{ $value->description }}" 
                                      data-amount="{{ rupiah($value->amount) }}" 
                                      data-name="{{ $value->user->name }}"
                                      data-original-title="Edit" class="edit btn_update_balance_history_user"><i class="fa fa-edit"></i> Ubah</a></li>

                                      <li class="dropdown-item"><a href="javascript:void(0)" data-toggle="tooltip" data-url="{{ route('balance.delete.history_balance',$value->id) }}" data-original-title="Delete" class="btn_delete"><i class="fa fa-times-circle"></i> Hapus</a></li>
                                  </ul>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
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
                <h5 class="modal-title">Ubah Riwayat Saldo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('balance.update.history_balance') }}" method="post">
                @csrf 
                <div class="modal-body">
                    <input type="hidden" name="id" id="inputId">
                    <div class="form-group row">
                        <label for="inputName" class="col-sm-4 col-form-label">Nama</label>
                        <div class="col-sm-8">
                            <input type="text" name="name" class="form-control" readonly id="inputName">
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label for="inputUserId" class="col-sm-4 col-form-label">ID Customer</label>
                        <div class="col-sm-8">
                            <input type="text" name="userid" class="form-control" readonly id="inputUserId">
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label for="inputSaldo" class="col-sm-4 col-form-label">Tanggal</label>
                        <div class="col-sm-8">
                            <input type="text" name="date" class="form-control" readonly id="inputDate">
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label for="inputSaldo" class="col-sm-4 col-form-label">Saldo</label>
                        <div class="col-sm-8">
                            <input type="text" min="0" name="amount" class="form-control" readonly id="inputAmount">
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label for="inputSaldo" class="col-sm-4 col-form-label">Deskripsi</label>
                        <div class="col-sm-8">
                          <textarea name="description" class="form-control" id="ta_description" rows="3"></textarea>
                        </div>
                    </div>                    
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kembali</button>
                    <button type="submit" class="btn btn-primary">Ubah</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
   </div>
    <!-- /.modal -->

<x-modal id="md_delete" title="Hapus Data">
  <p>Apakah anda yakin menghapus data ini dan mempengaruhi saldo pengguna?</p>
  <form action="#" method="post">
    @csrf
    <div class="text-right">
      <button class="btn btn-primary" type="submit">Ya</button>
      <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>  
    </div>
  </form>
</x-modal>

@endsection

@section('scripts')

<script>

    $(document).ready(function(){

        $('#table_datatable_1').dataTable()

        $(document).on('click','.btn_update_balance_history_user',function(e){
            e.preventDefault();
            let id = $(this).data('id');
            let userid = $(this).data('userid');
            let name = $(this).data('name');
            let amount = $(this).data('amount');
            let description = $(this).data('description');
            let date = $(this).data('date');

            $('#modal_change_balance #inputId').val(id);
            $('#modal_change_balance #inputName').val(name);
            $('#modal_change_balance #inputUserId').val(userid);
            $('#modal_change_balance #inputDate').val(date);
            $('#modal_change_balance #inputAmount').val(amount);
            $('#modal_change_balance #ta_description').val(description);

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
            const modal = $('#md_delete').modal('show');
            modal.find('form').attr('action',url);
            // if (confirm("Apakah anda yakin?") == true) {
            //     // ajax
            //     $.ajax({
            //         type:"POST",
            //         url: url,
            //         dataType: 'json',
            //         success: function(res){
            //             var oTable = $('#table_datatable').dataTable();
            //             oTable.fnDraw(false);
            //         }
            //     });
            // }

        })



    });
</script>

@endsection