@extends('layouts.app_layout')
@section('title','Manajemen Akun')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Pengaturan</h1>
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
                <h3 class="card-title">Manajemen Akun</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <a href="{{ route('manajement_account.create') }}" class="btn btn-primary mb-3">Tambah</a>
                
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
                <div class="table-responsive">
                  <table id="table_data" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                      <th>No</th>
                      <th>UserId</th>
                      <th>Nama</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th>Status</th>
                      <th>Last Login</th>
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

        let table = $('#table_data').DataTable({
            processing: true,
            serverSide: true,
            order: [[ 6, "desc" ]],
            ajax: "{{ route('manajement_account.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false,width:'5%'},
                {data: 'userid', name: 'userid'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'role', name: 'role'},
                {data: 'status', name: 'status',width:'10%',class:'text-center'},
                {data: 'created_at', name: 'created_at',width:'10%',class:'text-center'},
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
                        var oTable = $('#table_data').dataTable();
                        oTable.fnDraw(false);
                    }
                });
            }

        })

    });
</script>

@endsection