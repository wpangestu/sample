@extends('layouts.app_layout')
@section('title','List Jasa')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Jasa</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">List Jasa</li>
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
                <h3 class="card-title">List Jasa</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <a href="{{route('services.create')}}" class="btn btn-primary mb-3">Tambah</a>
                
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
                  <table id="table-datatables" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama</th>
                      <th>Kategori</th>
                      <th>Teknisi</th>
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

  <x-modal id="md_delete" title="Hapus Data Service">
    <p>Apa anda yakin?</p>
    <form action="" method="post">
      @csrf
      <div class="text-right">
        <button class="btn btn-primary" type="submit">Ya</button>
        <button class="btn btn-secondary" data-dismiss="modal" type="button">Batal</button>
      </div>
    </form>
  </x-modal>

@endsection

@section('scripts')

<script>

    $(document).ready(function(){

      $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

        let table = $('#table-datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('services.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false,width:'5%'},
                {data: 'name', name: 'name'},
                {data: 'service_category_id', name: 'service_category_id'},
                {data: 'engineer_id', name: 'engineer_id'},
                {data: 'status', name: 'status',class:'text-center'},
                {data: 'updated_at', name: 'updated_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('body').on('click', '.btn_delete', function () {
            const url = $(this).data('url');

            $('#md_delete').modal('show');
            $('#md_delete').find('form').attr('action',url);

            // Swal.fire(
            //   'Good job!',
            //   'You clicked the button!',
            //   'success'
            // )

            // Swal.fire({
            //   title: 'Are you sure?',
            //   text: "You won't be able to revert this!",
            //   icon: 'warning',
            //   showCancelButton: true,
            //   confirmButtonColor: '#3085d6',
            //   cancelButtonColor: '#d33',
            //   confirmButtonText: 'Yes, delete it!'
            // }).then((result) => {
            //   if (result.isConfirmed) {
            //     Swal.fire(
            //       'Deleted!',
            //       'Your file has been deleted.',
            //       'success'
            //     )
            //   }
            // })

            // if (confirm("Apakah anda yakin?") == true) {
            //     // ajax
            //     $.ajax({
            //         type:"POST",
            //         url: url,
            //         dataType: 'json',
            //         success: function(res){
            //             var oTable = $('#table-datatables').dataTable();
            //             oTable.fnDraw(false);
            //         }
            //     });
            // }

        })

    });
</script>

@endsection