@extends('layouts.app_layout')
@section('title','Detail Service')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Service</h1>
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
                <h3 class="card-title">Detail Konfirmasi Service</h3>
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

                <dl class="row">
                  <dt class="col-sm-3">Status</dt>
                  <dd class="col-sm-8">: 
                    @if($service->status==="review")
                        <span class="badge badge-warning">Menunggu Kofirmasi</span>
                    @else
                        <span class='badge badge-danger'>Ditolak</span>
                    @endif
                  </dd>
                  <dt class="col-sm-3">Nama Teknisi</dt>
                  <dd class="col-sm-8">: <a href="{{ route('engineer.show',$service->engineer->userid) }}" target="_blank">{{ $service->engineer->name??'-' }}</a></dd>
                  <dt class="col-sm-3">Kategori Jasa</dt>
                  <dd class="col-sm-8">: {{ $service->service_category->name??'-' }}</dd>
                  <dt class="col-sm-3">Nama Jasa</dt>
                  <dd class="col-sm-8">: {{ $service->base_service->name??'-' }}</dd>
                  <dt class="col-sm-3">Harga (IDR)</dt>
                  <dd class="col-sm-8">: {{ rupiah($service->base_service->price) }}</dd>
                  <dt class="col-sm-3">Deskripsi</dt>
                  <dd class="col-sm-8"><textarea class="form-control" readonly rows="4">{{ $service->description }}</textarea></dd>
                  <dt class="col-sm-3">Skill</dt>
                  <dd class="col-sm-8">
                    <select multiple class="form-control" id="skill">
                        @foreach($service->skill as $item)
                            <option selected>{{ $item }}</option>
                        @endforeach
                    </select>
                  </dd>

                  <dt class="col-sm-3">Gambar Jasa</dt>
                  <dd class="col-sm-8"><img src="{{ $service->image }}" height="180px"></dd>
                  <dt class="col-sm-3">Gambar Sertifikat Jasa</dt>
                  <dd class="col-sm-8"><img src="{{ $service->sertification_image }}" height="180px"></dd>

                  <dt class="col-sm-3">Di buat</dt>
                  <dd class="col-sm-8">: {{ $service->created_at }}
                  </dd>
                  <dt class="col-sm-3">Di Update</dt>
                  <dd class="col-sm-8">: {{ $service->updated_at }}
                  <dt class="col-sm-3"></dt>
                  <dd class="col-sm-8">
                  @if($service->status==="review")
                    <a href="{{ route('services.confirmation.accept', $service->id) }}" onclick="return confirm('Apakah anda yakin ? ')" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i> Diterima</a>
                    <a href="{{ route('services.confirmation.danied', $service->id) }}" onclick="return confirm('Apakah anda yakin ? ')" class="btn btn-sm btn-danger"><i class="fa fa-times-circle"></i> Ditolak</a>
                  @endif
                    <a href="{{ route('services.confirmation') }}" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                  </dd>
                </dl>

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
        $('#skill').select2('readonly', true);
    });
</script>

@endsection