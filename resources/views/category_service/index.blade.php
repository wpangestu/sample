@extends('layouts.app_layout')
@section('title','Kategori Jasa')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Kategori Jasa</h1>
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
                <h3 class="card-title">Daftar Kategori Jasa</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <a href="{{route('service_category.create')}}" class="btn btn-primary mb-3">Tambah</a>
                
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
                  <table id="table_category_service" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama</th>
                      <th>Ikon</th>
                      <th>Status</th>
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

  <x-modal id="md_delete" title="Hapus Data Kategori Jasa">
    <p>Apa anda yakin?</p>
    <form action="" method="post">
      @csrf
      @method('delete')
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

        let table = $('#table_category_service').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('service_category.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false,width:'5%'},
                {data: 'name', name: 'name'},
                {data: 'icon', name: 'icon'},
                {data: 'status', name: 'status',width:'10%',class:'text-center'},
                {data: 'action', name: 'action', width:'5%', orderable: false, searchable: false, class:'text-center'},
            ]
        });

        $('body').on('click', '.btn_delete', function () {
            const url = $(this).data('url');

            $('#md_delete').modal('show');
            $('#md_delete').find('form').attr('action',url);

            // const url = $(this).data('url');

            // if (confirm("Apakah anda yakin?") == true) {
            //     // ajax
            //     $.ajax({
            //         type:"POST",
            //         url: url,
            //         dataType: 'json',
            //         success: function(res){

            //             var oTable = $('#table_category_service').dataTable();
            //             oTable.fnDraw(false);
            //         }
            //     });
            // }

        })

    });
</script>

@endsection