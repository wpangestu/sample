@extends('layouts.app_layout')
@section('title','Tambah Jasa')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Master Jasa</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('base_services.index') }}">Master Jasa</a></li>
              <li class="breadcrumb-item active">Detail Master Jasa</li>
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
                <h3 class="card-title">Detail Master Jasa</h3>
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
                                    <input readonly type="text" class="form-control" value="{{ $data->service_category->name }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-3 col-form-label">Nama Jasa*</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly name="name" class="form-control" id="inputName" value="{{ $data->name }}" placeholder="Nama">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputPrice" class="col-sm-3 col-form-label">Harga*</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly name="price" class="form-control" id="inputPrice" value="{{ rupiah($data->price) }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputPrice" class="col-sm-3 col-form-label">Garansi</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly class="form-control" value="{{ $data->guarantee==1?'Ya':'Tidak' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputLongGuarantee" class="col-sm-3 col-form-label">Lama Garansi</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly name="long_guarantee" class="form-control" value="{{ $data->long_guarantee }}" id="inputLongGuarantee">
                                </div>
                            </div>                            
                            <div class="form-group row">
                                <label for="inputDesc" class="col-sm-3 col-form-label">Deskripsi</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" readonly name="description" id="inputDesc" cols="30" rows="5">{{ $data->description }}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputDesc" class="col-sm-3 col-form-label">Deskripsi</label>
                                <div class="col-sm-9">
                                    @if(!(is_null($data->image)))
                                    <img src="{{$data->image}}" height="150px" alt="">
                                    @else
                                      -
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="input_price_receive" class="col-sm-3 col-form-label">Fee Teknisi*</label>
                                <div class="col-sm-9">
                                    <input type="text" name="price_receive" readonly class="form-control" value="{{ rupiah($data->price_receive) }}" id="input_price_receive">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputStatus" class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <a href="{{ route('base_services.index') }}" class="btn btn-secondary">Kembali</a>
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
    })
  </script>

@endsection()