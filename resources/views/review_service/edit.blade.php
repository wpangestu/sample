@extends('layouts.app_layout')
@section('title','Ubah Ulasan Pelanggan')
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
                <h3 class="card-title">Ubah Ulasan Pelanggan</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
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
                <div class="row">
                    <div class="col-md-6"> 
                        <form action="{{route('review_service.update',[$review->id])}}" enctype="multipart/form-data" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="inputServiceOrder" class="col-form-label">Service Order</label>
                                <select class="form-control" name="orderid" id="inputServiceOrder" readonly>
                                  <option value="{{ $review->id }}">{{ $review->order->order_number }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputRatings" class="col-form-label">Rating/Bintang (1-5)</label>
                                <input required type="number" class="form-control" value="{{$review->ratings}}" name="ratings" min="0" max="5" id="inputRatings">
                            </div>
                            <div class="form-group">
                                <label for="inputDescription" class="col-form-label">Deskripsi</label>
                                <textarea name="description" class="form-control" id="inputDescription" rows="5">{{ $review->description }}</textarea>
                            </div>
                    </div>
                    <div class="col-md-6">

                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label for="inputStatus" class="col-form-label"></label>
                        <button class="btn btn-primary btn-sm">Simpan</button>
                        <a href="{{ route('review_service.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                </div>
                </form>
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
      // $('#inputCustomer').select2();
      // $('#inputEngineer').select2();
      // $('#inputService').select2();
    });
  </script>

@endsection