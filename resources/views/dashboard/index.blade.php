@extends('layouts.app_layout')
@section('title','Dashboard')

@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
@endsection()

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
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

        <!-- Info boxes -->
        <div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-tags"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Kategori Jasa</span>
                <span class="info-box-number">
                  {{ $numCategoryServices }}
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-briefcase"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Jasa</span>
                <span class="info-box-number">{{ $numServices }}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-address-book"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Pelanggan</span>
                <span class="info-box-number">{{ $numCustomer }}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-tools"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Teknisi</span>
                <span class="info-box-number">{{ $numEngineer }}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
          <div class="col-md-12">
            <div class="card">

              <div class="card-header border-0">
                <h3 class="card-title">Grafik Pendaftaran Teknisi</h3>
                <div class="card-tools">
                </div>
              </div>

              <div class="card-body">
                <div class="btn-group">
                  <button type="button" onclick="return  get_statistik_engineer('day')" class="btn btn-info">Harian</button>
                  <button type="button" onclick="return  get_statistik_engineer('month')" class="btn btn-info">Bulanan</button>
                  <button type="button" onclick="return  get_statistik_engineer('year')" class="btn btn-info">Tahunan</button>
                </div>
                <div class="mt-3">
                  <canvas class="chart" id="line-chart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>                
                </div>
              </div>
            </div>
          </div>
          <!-- /.col-md-6 -->
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card">

              <div class="card-header border-0">
                <h3 class="card-title">Grafik Pendaftaran Pelanggan</h3>
                <div class="card-tools">
                </div>
              </div>

              <div class="card-body">
                <div class="btn-group">
                  <button type="button" onclick="return  get_statistik_customer('day')" class="btn btn-info">Harian</button>
                  <button type="button" onclick="return  get_statistik_customer('month')" class="btn btn-info">Bulanan</button>
                  <button type="button" onclick="return  get_statistik_customer('year')" class="btn btn-info">Tahunan</button>
                </div>
                <div class="mt-3">
                  <canvas class="chart" id="line-register-customer" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>                
                </div>
              </div>
            </div>
          </div>
          <!-- /.col-md-6 -->
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card">

              <div class="card-header border-0">
                <h3 class="card-title">Grafik Jumlah Order</h3>
                <div class="card-tools">
                </div>
              </div>

              <div class="card-body">
                <div class="btn-group">
                  <button type="button" onclick="return  get_statistik_order('day')" class="btn btn-info">Harian</button>
                  <button type="button" onclick="return  get_statistik_order('month')" class="btn btn-info">Bulanan</button>
                  <button type="button" onclick="return  get_statistik_order('year')" class="btn btn-info">Tahunan</button>
                </div>
                <div class="mt-3">
                  <canvas class="chart" id="line-order" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>                
                </div>
              </div>
            </div>
          </div>
          <!-- /.col-md-6 -->
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Maps Teknisi</h5>
                <div id="map" style="width:100%;height:450px;">
                    {!! Mapper::render() !!}
                </div>
              </div>
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

    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB980FhrGUf3mBrp9eRFzpqJaC-g6ExNco&libraries=places&callback=initialize" async defer></script> -->
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>

    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>

    <script type="text/javascript">   

      // function initialize() {

      //   $('form').on('keyup keypress', function(e) {
      //       var keyCode = e.keyCode || e.which;
      //       if (keyCode === 13) {
      //           e.preventDefault();
      //           return false;
      //       }
      //   });
      //   const locationInputs = document.getElementsByClassName("map-input");

      //   const autocompletes = [];
      //   const geocoder = new google.maps.Geocoder;
      //   for (let i = 0; i < locationInputs.length; i++) {

      //       const input = locationInputs[i];
      //       const fieldKey = input.id.replace("-input", "");
      //       const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey + "-longitude").value != '';

      //       const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || -33.8688;
      //       const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || 151.2195;

      //       const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
      //           center: {lat: latitude, lng: longitude},
      //           zoom: 13
      //       });
      //       const marker = new google.maps.Marker({
      //           map: map,
      //           position: {lat: latitude, lng: longitude},
      //       });

      //       marker.setVisible(isEdit);

      //       const autocomplete = new google.maps.places.Autocomplete(input);
      //       autocomplete.key = fieldKey;
      //       autocompletes.push({input: input, map: map, marker: marker, autocomplete: autocomplete});
      //   }

      //   for (let i = 0; i < autocompletes.length; i++) {
      //       const input = autocompletes[i].input;
      //       const autocomplete = autocompletes[i].autocomplete;
      //       const map = autocompletes[i].map;
      //       const marker = autocompletes[i].marker;

      //       google.maps.event.addListener(autocomplete, 'place_changed', function () {
      //           marker.setVisible(false);
      //           const place = autocomplete.getPlace();

      //           geocoder.geocode({'placeId': place.place_id}, function (results, status) {
      //               if (status === google.maps.GeocoderStatus.OK) {
      //                   const lat = results[0].geometry.location.lat();
      //                   const lng = results[0].geometry.location.lng();
      //                   console.log(lat);
      //                   setLocationCoordinates(autocomplete.key, lat, lng);
      //               }
      //           });

      //           if (!place.geometry) {
      //               window.alert("No details available for input: '" + place.name + "'");
      //               input.value = "";
      //               return;
      //           }

      //           if (place.geometry.viewport) {
      //               map.fitBounds(place.geometry.viewport);
      //           } else {
      //               map.setCenter(place.geometry.location);
      //               map.setZoom(17);
      //           }
      //           marker.setPosition(place.geometry.location);
      //           marker.setVisible(true);

      //       });
      //   }
      // }   

    function get_statistik_customer(filter){
      var ticksStyle = {
        fontColor: '#495057',
        fontStyle: 'bold'
      }

      var mode      = 'index'
      var intersect = true
      const salesGraphChartCanvas = $('#line-register-customer').get(0).getContext('2d');

      $.ajax({
          url: "{{ route('dashboard.statistik.customer.register') }}",
          type: "GET",
          data: {
              _token: "{{ csrf_token() }}",
              "filter" : filter,
          },
          success: function(response) {
            if(response.success){

              var visitorsChart  = new Chart(salesGraphChartCanvas, {
                data   : {
                  labels  : response.label,
                  datasets: [{
                    type                : 'line',
                    data                : response.data,
                    backgroundColor     : 'transparent',
                    borderColor         : '#007bff',
                    pointBorderColor    : '#007bff',
                    pointBackgroundColor: '#007bff',
                    fill                : false,
                  }
                    ]
                },
                options: {
                  maintainAspectRatio: false,
                  tooltips           : {
                    mode     : mode,
                    intersect: intersect
                  },
                  hover              : {
                    mode     : mode,
                    intersect: intersect
                  },
                  legend             : {
                    display: false
                  },
                  scales             : {
                    yAxes: [{
                      // display: false,
                      gridLines: {
                        display      : true,
                        lineWidth    : '4px',
                        color        : 'rgba(0, 0, 0, .2)',
                        zeroLineColor: 'transparent'
                      },
                      ticks    : $.extend({
                        beginAtZero : true,
                        suggestedMin : 0,
                        suggestedMax : 10
                      }, ticksStyle)
                    }],
                    xAxes: [{
                      display  : true,
                      gridLines: {
                        display: false
                      },
                      ticks    : ticksStyle
                    }]
                  }
                }
              })

            }else{
              console.log(response);
            }
          }
      });        
    }

    function get_statistik_engineer(filter){
      var ticksStyle = {
        fontColor: '#495057',
        fontStyle: 'bold'
      }

      var mode      = 'index'
      var intersect = true
      const salesGraphChartCanvas = $('#line-chart').get(0).getContext('2d');

      $.ajax({
          url: "{{ route('dashboard.statistik.engineer.register') }}",
          type: "GET",
          data: {
              _token: "{{ csrf_token() }}",
              "filter" : filter,
          },
          success: function(response) {
            if(response.success){

              var visitorsChart  = new Chart(salesGraphChartCanvas, {
                data   : {
                  labels  : response.label,
                  datasets: [{
                    type                : 'line',
                    data                : response.data,
                    backgroundColor     : 'transparent',
                    borderColor         : '#007bff',
                    pointBorderColor    : '#007bff',
                    pointBackgroundColor: '#007bff',
                    fill                : false,
                  }
                    ]
                },
                options: {
                  maintainAspectRatio: false,
                  tooltips           : {
                    mode     : mode,
                    intersect: intersect
                  },
                  hover              : {
                    mode     : mode,
                    intersect: intersect
                  },
                  legend             : {
                    display: false
                  },
                  scales             : {
                    yAxes: [{
                      // display: false,
                      gridLines: {
                        display      : true,
                        lineWidth    : '4px',
                        color        : 'rgba(0, 0, 0, .2)',
                        zeroLineColor: 'transparent'
                      },
                      ticks    : $.extend({
                        beginAtZero : true,
                        suggestedMin : 0,
                        suggestedMax : 10
                      }, ticksStyle)
                    }],
                    xAxes: [{
                      display  : true,
                      gridLines: {
                        display: false
                      },
                      ticks    : ticksStyle
                    }]
                  }
                }
              })

            }else{
              console.log(response);
            }
          }
      });        
    }

    function get_statistik_order(filter){
      var ticksStyle = {
        fontColor: '#495057',
        fontStyle: 'bold'
      }

      var mode      = 'index'
      var intersect = true
      const salesGraphChartCanvas = $('#line-order').get(0).getContext('2d');

      $.ajax({
          url: "{{ route('dashboard.statistik.order') }}",
          type: "GET",
          data: {
              _token: "{{ csrf_token() }}",
              "filter" : filter,
          },
          success: function(response) {
            if(response.success){

              var visitorsChart  = new Chart(salesGraphChartCanvas, {
                data   : {
                  labels  : response.label,
                  datasets: [{
                    type                : 'line',
                    data                : response.data,
                    backgroundColor     : 'transparent',
                    borderColor         : '#007bff',
                    pointBorderColor    : '#007bff',
                    pointBackgroundColor: '#007bff',
                    fill                : false,
                  }
                    ]
                },
                options: {
                  maintainAspectRatio: false,
                  tooltips           : {
                    mode     : mode,
                    intersect: intersect
                  },
                  hover              : {
                    mode     : mode,
                    intersect: intersect
                  },
                  legend             : {
                    display: false
                  },
                  scales             : {
                    yAxes: [{
                      // display: false,
                      gridLines: {
                        display      : true,
                        lineWidth    : '4px',
                        color        : 'rgba(0, 0, 0, .2)',
                        zeroLineColor: 'transparent'
                      },
                      ticks    : $.extend({
                        beginAtZero : true,
                        suggestedMin : 0,
                        suggestedMax : 10
                      }, ticksStyle)
                    }],
                    xAxes: [{
                      display  : true,
                      gridLines: {
                        display: false
                      },
                      ticks    : ticksStyle
                    }]
                  }
                }
              })

            }else{
              console.log(response);
            }
          }
      });        
    }

    $(document).ready(function(){
      $('.daterange').daterangepicker();
      
      get_statistik_engineer('day');

      get_statistik_customer('day');
      
      get_statistik_order('day');


    });

    </script>
@endsection