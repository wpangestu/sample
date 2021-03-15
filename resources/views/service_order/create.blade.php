@extends('layouts.app_layout')
@section('title','Tambah Pesanan Jasa')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Service Order</h1>
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
                <h3 class="card-title">Tambah Pesanan Jasa</h3>
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
                        <form action="{{route('service_order.store')}}" enctype="multipart/form-data" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="inputCustomer" class="col-form-label">Pelanggan</label>
                                <select class="form-control" name="customer_id" id="inputCustomer">
                                    <option value="">PILIH</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputCustomer" class="col-form-label">Pilih Lokasi Pelanggan</label>
                                <div class="input-group">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text">Cari Alamat</span>
                                  </div>
                                  <input type="text" id="address-input" name="map_address" class="form-control map-input">
                                      <input type="hidden" class="form-control" readonly name="address_latitude" id="address-latitude" value="0" />
                                      <input type="hidden" class="form-control" readonly name="address_longitude" id="address-longitude" value="0" />
                                </div>
                                <div id="address-map-container" style="width:100%;height:250px; ">
                                    <div style="width: 100%; height: 100%" id="address-map"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                      <label for="">Lat</label>
                                      <input type="text" class="form-control" readonly name="latitude" id="inputLat" value="0" />
                                    </div>
                                    <div class="col-md-6">
                                      <label>Lng</label>
                                      <input type="text" class="form-control" readonly name="longitude" id="inputLng" value="0" />
                                    </div>
                                  </div>
                            </div>

                            <div class="form-group">
                                <label for="inputCatSer" class="col-form-label">Kategori Service</label>
                                <select class="form-control" name="category_service" id="inputCatSer">
                                    <option value="">PILIH</option>
                                    @foreach($category_services as $val)
                                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputService" class="col-sm-2 col-form-label">Service</label>
                                <select class="form-control" name="service_id[]" id="inputService">
                                    <option value="">--PILIH--</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id."_".$service->price }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputService" class="">Harga</label>
                                <input type="text" name="price" id="price" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="inputService" class="">Ongkos Kirim</label>
                                <input type="number" name="shipping" id="shipping" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="inputTotal" class="">Total</label>
                                <input type="number" name="total" id="inputTotal" readonly class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="inputDescription" class="col-form-label">Deskripsi</label>
                                <textarea name="description" class="form-control" id="inputDescription" rows="5"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="payment_method" class="">Metode Pembyaran</label>
                                <select name="payment_method" class="form-control" id="payment_method">
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="payment_gateway" class="">Pilih Bank</label>
                                <select name="payment_gateway" class="form-control" id="payment_gateway">
                                    <option value="BCA">BCA</option>
                                    <option value="BRI">BRI</option>
                                    <option value="MANDIRI">MANDIRI</option>
                                </select>
                            </div>
                    </div>
                    <div class="col-md-6">

                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label for="inputStatus" class="col-form-label"></label>
                        <button class="btn btn-primary">Simpan</button>
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

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB980FhrGUf3mBrp9eRFzpqJaC-g6ExNco&libraries=places&callback=initialize" async defer></script>
<script type="text/javascript">      

  function initialize() {

    $('.map-input').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    const locationInputs = document.getElementsByClassName("map-input");

    const autocompletes = [];
    const geocoder = new google.maps.Geocoder;
    for (let i = 0; i < locationInputs.length; i++) {
        
        const input = locationInputs[i];
        console.log(input);
        const fieldKey = input.id.replace("-input", "");
        const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey + "-longitude").value != '';

        const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || -7.4181887466077265;
        const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || 109.22154831237727;

        const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
            center: {lat: latitude, lng: longitude},
            zoom: 13
        });
        const marker = new google.maps.Marker({
            map: map,
            position: {lat: latitude, lng: longitude},
            draggable:true,
        });

        marker.setVisible(isEdit);

        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.key = fieldKey;
        autocompletes.push({input: input, map: map, marker: marker, autocomplete: autocomplete});

        google.maps.event.addListener(marker, 'dragend', function() {
            geocodePosition(marker.getPosition());
        });

        function geocodePosition(pos) 
        {
          geocoder.geocode({
                latLng: pos
          }, 
            function(results, status) 
            {
                if (status == google.maps.GeocoderStatus.OK) 
                {
                    $(".map-input").val(results[0].formatted_address);
                    const lat = results[0].geometry.location.lat();
                    const lng = results[0].geometry.location.lng();
                    console.log(lat);
                    $('#inputLat').val(lat)
                    $('#inputLng').val(lng)
                    // $("#mapErrorMsg").hide(100);
                } 
                // else 
                // {
                //     $("#mapErrorMsg").html('Cannot determine address at this location.'+status).show(100);
                // }
            }
          );
        }
    }

    for (let i = 0; i < autocompletes.length; i++) {
        const input = autocompletes[i].input;
        const autocomplete = autocompletes[i].autocomplete;
        const map = autocompletes[i].map;
        const marker = autocompletes[i].marker;

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            marker.setVisible(false);
            const place = autocomplete.getPlace();

            geocoder.geocode({'placeId': place.place_id}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    const lat = results[0].geometry.location.lat();
                    const lng = results[0].geometry.location.lng();
                    $('#inputLat').val(lat)
                    $('#inputLng').val(lng)
                    // setLocationCoordinates(autocomplete.key, lat, lng);
                }
            });

            if (!place.geometry) {
                window.alert("No details available for input: '" + place.name + "'");
                input.value = "";
                return;
            }

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

        });
    }
  }
</script>

  <script>
    $(document).ready(function(){
      $('#inputCustomer').select2();
      $('#inputEngineer').select2();
      $('#inputService').select2();

      $('#inputService').change(function (params) {
        const val = $(this).val();
        const arr = val.split('_');

        $('#price').val(arr[1]);
        // $('#shipping').val(12000);

      })

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
      }
    });

    $('#inputCatSer').change(function(){
      $('#inputService').html('');
      const category_id = $(this).val();
      $.ajax({
          type: 'post',
          url: '{{ route("service.by_category.ajax") }}',
          data: {
            category_id : category_id
          },
          dataType: 'json',
          success: function (data) {
            console.log(data);
            $('#inputService').html('');
            let option = '';
            option += `
              <option value="">== Pilih ==</option>
            `;
            data.data.forEach(function(d,index){
              option += `
              <option value="${d.id+'_'+d.price}">${d.name} | Teknisi: ${d.engineer.name}</option>
              `
            });
            $('#inputService').html(option);
          },
          error: function (data) {
              console.log(data);
          }
      });
    })

    $('#shipping').blur(function(){
      const shipping = $(this).val();
      const sub_total = $("#price").val();
      const total = Number(shipping) + Number(sub_total);

      $('#inputTotal').val(total);
    })

    });
  </script>

@endsection