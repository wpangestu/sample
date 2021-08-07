@extends('layouts.app_layout')
@section('title','Ubah Bank Payment')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Pengaturan Bank</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <!-- <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('base_services.index') }}">Master Jasa</a></li>
              <li class="breadcrumb-item active">Tambah Master Jasa</li>
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
                <h3 class="card-title">Ubah Bank Pembayaran</h3>
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

                        <form action="{{route('bank_payments.update',$bankPayment->id)}}" method="post">
                            @method('put')
                            @csrf
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-3 col-form-label">Master Bank*</label>
                                <div class="col-sm-9">
                                  <select class="form-control" name="bank" required id="bank_id">
                                    <option value="">PILIH</option>
                                    @foreach ($banks as $value)
                                      <option value="{{ $value->id }}" {{ old('bank')==$value->id?'selected':($bankPayment->bank_id==$value->id?'selected':'') }}>{{ $value->name }}</option>
                                    @endforeach
                                  </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputIcon" class="col-sm-3 col-form-label">No Rekening *</label>
                                <div class="col-sm-9">
                                  <input type="text" name="account_number" value="{{ old('account_number',$bankPayment->account_number) }}" id="account_number" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputStatus" class="col-sm-3 col-form-label">Status*</label>
                                <div class="col-sm-9">
                                  <select required name="inputStatus" class="form-control">
                                    <option value="">Pilih</option>
                                    <option value="on" {{ $bankPayment->is_active?'selected':'' }}>Aktif</option>
                                    <option value="off" {{ !($bankPayment->is_active)?'selected':'' }}>Non Aktif</option>
                                  </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputStatus" class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <button class="btn btn-primary">Simpan</button>
                                    <a href="{{ route('bank_payments.index') }}" class="btn btn-secondary">Kembali</a>
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
      $('#bank_id').select2();
    })
  </script>

@endsection()