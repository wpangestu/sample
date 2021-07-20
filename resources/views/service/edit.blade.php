@extends('layouts.app_layout')
@section('title','Ubah Jasa')
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
              <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Jasa</a></li>
              <li class="breadcrumb-item active">Ubah Jasa</li>
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
                <h3 class="card-title">Ubah Jasa</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                      <div class="form-group row">
                          <label for="inputCatSer" class="col-sm-3 col-form-label">Kategori</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" readonly value="{{ $service->base_service->service_category->name }}">
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="inputName" class="col-sm-3 col-form-label">Nama Jasa</label>
                          <div class="col-sm-9">
                              <input type="text" readonly class="form-control" id="inputName" value="{{ $service->base_service->name }}" placeholder="Nama">
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="inputPrice" class="col-sm-3 col-form-label">Harga</label>
                          <div class="col-sm-9">
                              <input type="text" readonly name="price" value="{{ rupiah($service->base_service->price) }}" class="form-control" id="inputPrice" placeholder="">
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="inputPrice" class="col-sm-3 col-form-label">Fee Teknisi</label>
                          <div class="col-sm-9">
                              <input type="text" readonly name="price" value="{{ rupiah($service->base_service->price_receive) }}" class="form-control" id="inputPrice" placeholder="">
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="inputImage" class="col-sm-3 col-form-label">Gambar Jasa</label>
                          <div class="col-sm-9">
                            <img height="170px" src="{{ $service->base_service->image }}" alt="">
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="inputDesc" class="col-sm-3 col-form-label">Deskripsi</label>
                          <div class="col-sm-9">
                              <textarea class="form-control" name="description" readonly id="inputDesc" cols="30" rows="5">{{ $service->base_service->description }}</textarea>
                          </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <form action="{{route('services.update', $service->id)}}" method="post" enctype="multipart/form-data">
                              @method('put')
                              @csrf
                        <div class="form-group row">
                            <label for="inputEngineer" class="col-sm-3 col-form-label">Teknisi*</label>
                            <div class="col-sm-9">
                              <input type="text" readonly class="form-control" value="{{ $service->engineer->name }}">
                            </div>
                        </div>
                        <div class="form-group row">
                          <label for="inputImage" class="col-sm-3 col-form-label">Gambar Sertifikat Jasa</label>
                          <div class="col-sm-9">
                            <img height="170px" src="{{ $service->sertification_image }}" alt="">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="inputImageSertificate" class="col-sm-3 col-form-label">Ubah Gambar Sertifikat Jasa*</label>
                          <div class="col-sm-9">
                              <input type="file" name="sertification_image" id="inputImageSertificate"><br>
                              <span class="text-muted text-sm"><i>format: jpeg, png, jpg | max: 2048kb</i></span>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="inputSkill" class="col-sm-3 col-form-label">Skill Teknisi</label>
                          <div class="col-sm-9">
                            <!-- <input type="text" name="skill" id="inputSkill" class="form-control"> -->
                            <select name="skill[]" id="inputSkill" class="form-control" multiple>
                              @foreach($service->skill as $val)
                                  <option value="{{ $val }}" selected>{{ $val }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="inputSkill" class="col-sm-3 col-form-label">Status</label>
                          <div class="col-sm-9">
                            <input type="checkbox" name="status" {{ $service->status==true?'checked':'' }}> Aktif
                          </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputStatus" class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                                <button class="btn btn-primary">Ubah</button>
                                <a href="{{ route('services.index') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </div>
                      </form>
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
      $('#inputCatSer').select2();

      $('#inputSkill').select2({
        tags : true
      })
    })
  </script>

@endsection()
