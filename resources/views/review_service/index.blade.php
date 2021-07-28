@extends('layouts.app_layout')
@section('title','Ulasan Pelanggan')
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
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Ulasan</li>
              <!-- <li class="breadcrumb-item active">Starter Page</li> -->
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
                <h3 class="card-title">List Ulasan Pelanggan</h3>
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

                <table id="table-datatables" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>ID Order</th>
                    <th>Pelanggan</th>
                    <th>Teknisi</th>
                    <th>Bintang</th>
                    <!-- <th>Keterangan</th> -->
                    <th>Aksi</th>
                  </tr>
                  </thead>

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

        // $('#table-datatables').DataTable();

      $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

        let table = $('#table-datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('review_service.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false,width:'5%'},
                {data: 'date', name: 'date'},
                {data: 'service_id', name: 'service_id'},
                {data: 'user_id', name: 'user_id'},
                {data: 'engineer_id', name: 'engineer_id'},
                {data: 'ratings', name: 'ratings'},
                // {data: 'description', name: 'description'},s
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
                        var oTable = $('#table-datatables').dataTable();
                        oTable.fnDraw(false);
                    }
                });
            }

        })

    });
</script>

@endsection