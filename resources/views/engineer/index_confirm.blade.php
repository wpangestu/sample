@extends('layouts.app_layout')
@section('title','Konfirmasi Teknisi')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Teknisi</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Konfirmasi Teknisi</li>
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
                <h3 class="card-title">Konfirmasi Teknisi</h3>
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
                <div class="table-resonsive">
                    <table id="table_datatable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Teknisi ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No Hp</th>
                                <th>Status</th>
                                <th>Update</th>
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

@endsection

@section('scripts')

<script>

    $(document).ready(function(){

      $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

        let table = $('#table_datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('engineer.confirm.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false,width:'5%'},
                {data: 'userid', name: 'userid'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'status', name: 'status'},
                {data: 'updated_at', name: 'updated_at'},
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