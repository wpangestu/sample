@extends('layouts.app_layout')
@section('title','Tambah Teknisi')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Teknisi</h1>
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
                <h3 class="card-title">Tambah Teknisi</h3>
              </div>
              <!-- /.card-header -->

            <form action="{{route('engineer.store')}}" enctype="multipart/form-data" method="post">
            @csrf
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
                        <div class="form-group">
                            <label for="inputIdCardNumber" class="col-form-label">No KTP</label>
                            <input type="text" name="id_card_number" class="form-control" id="inputIdCardNumber" placeholder="Id Card" value="{{ old('id_card_number') }}">
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-form-label">Nama</label>
                            <input type="text" name="name" class="form-control" id="inputName" placeholder="Nama" value="{{ old('name') }}">
                        </div>
                        <div class="form-group">
                            <label for="inputPhone" class="col-sm-2 col-form-label">No Hp</label>
                            <input type="text" name="phone" class="form-control" id="inputPhone" placeholder="No hp" value="{{ old('phone') }}">
                        </div>
                        <div class="form-group">
                            <label for="inputEmail" class="col-form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="inputEmail" value="{{ old('email') }}" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="inputPassword" class="col-form-label">Password</label><span class="text-muted text-sm"><i> (minimal 6 karakter)</i></span>
                            <input type="password" name="password" class="form-control" id="inputPassword">
                        </div>
                        <div class="form-group">
                            <label for="inputConfirmPassword" class="col-form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" id="inputConfirmPassword">
                        </div>
                        <div class="form-group">
                            <label for="inputStatus" class="col-form-label">Status</label>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" name="active" type="checkbox" id="inputActive" value="1" checked>
                                <label for="inputActive" class="custom-control-label">Aktif</label>
                            </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                          <label for="inputAddress" class="col-form-label">Photo Profil</label>
                          <input type="file" class="form-control" name="photo">
                          <!-- <input type="file" class="custom-file-input" id="customFile">
                          <label class="custom-file-label" for="customFile">Choose file</label> -->
                          <span class="text-muted rext-sm">format:jpeg,png,jpg|max:2048kb</span>
                      </div>
                      <div class="form-group">
                          <label for="inputAddress" class="col-form-label">Photo KTP</label>
                          <input type="file" class="form-control" name="id_card_image">
                          <span class="text-muted rext-sm">format:jpeg,png,jpg|max:2048kb</span>
                      </div>
                      <div class="form-group">
                          <label for="inputAddress" class="col-form-label">Photo KTP Selfie</label>
                          <input type="file" class="form-control" name="id_card_selfie_image">
                          <span class="text-muted rext-sm">format:jpeg,png,jpg|max:2048kb</span>
                      </div>
                      <div class="form-group">
                          <label for="inputAddress" class="col-form-label">Provinsi</label>
                          <input type="text" class="form-control" name="provinsi_id">
                      </div>
                      <div class="form-group">
                          <label for="inputAddress" class="col-form-label">Kabupaten</label>
                          <input type="text" class="form-control" name="provinsi_id">
                      </div>
                      <div class="form-group">
                          <label for="inputAddress" class="col-form-label">Kecamatan</label>
                          <input type="text" class="form-control" name="provinsi_id">
                      </div>
                      <div class="form-group">
                          <label for="inputAddress" class="col-form-label">Desa</label>
                          <input type="text" class="form-control" name="provinsi_id">
                      </div>
                      <div class="form-group">
                          <label for="inputAddress" class="col-form-label">Alamat Lengkap</label>
                          <textarea name="address" class="form-control" id="inputAddress" rows="4">{{ old('address') }}</textarea>
                      </div>
                      <div class="form-group">
                        <div id="accordion">
                          <!-- we are adding the .class so bootstrap.js collapse plugin detects it -->
                        <div class="card card-primary">
                          <div class="card-header">
                            <h4 class="card-title">
                              <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                Pilih Lokasi Maps
                              </a>
                            </h4>
                          </div>
                          <div id="collapseOne" class="panel-collapse collapse in">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-md-12">
                                  <div id="map" style="width:100%;height:300px;">
                                    <!-- {!! Mapper::render() !!} -->
                                    <div class="form-group row">
                                      <div class="input-group">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text">Cari Alamat</span>
                                        </div>
                                        <input type="text" id="address-input" class="form-control map-input">
                                      </div>
                                        <input type="hidden" name="address_latitude" id="address-latitude" value="0" />
                                        <input type="hidden" name="address_longitude" id="address-longitude" value="0" />
                                    </div>
                                    <div id="address-map-container" style="width:100%;height:250px; ">
                                        <div style="width: 100%; height: 100%" id="address-map"></div>
                                    </div>
                                  </div>                            
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="inputLatitude" class="col-form-label">Latitude</label>
                        <input type="text" name="lat" class="form-control" readonly id="inputLatitude">
                      </div>
                      <div class="form-group">
                        <label for="inpuLongitude" class="col-form-label">Longitude</label>
                        <input type="text" name="lng" class="form-control" readonly id="inpuLongitude">
                      </div>
                    </div>
                </div>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <a href="{{route('engineer.index')}}" class="btn btn-default float-right mr-1">Kembali</a>
                <button type="submit" class="btn btn-info float-right mr-1">Simpan</button>
              </div>
            </form>
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
<!-- <script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script> -->

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
                    $('#inputLatitude').val(lat)
                    $('#inpuLongitude').val(lng)
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
      console.log('cek')
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
                    $('#inputLatitude').val(lat)
                    $('#inpuLongitude').val(lng)
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

<script type="text/javascript">
    function updateLatlang(lat,lng)
    {
      $('#inputLatitude').val(lat)
      $('#inpuLongitude').val(lng)
    }
    // $(document).ready(function () {
    //   bsCustomFileInput.init();
    // });
</script>
@endsection