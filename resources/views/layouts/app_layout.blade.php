<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | Benerin.id</title>
    <!-- Scripts -->
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <!-- Datatable -->
    <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
      <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.css') }}">
     <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    <link rel="shortcut icon" type="image/jpg" href="{{ asset('logo_app.png') }}"/>


    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    @yield('css')
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

<div class="wrapper">
    @include('include.navbar')
    @include('include.sidebar')

    @yield('content')

    @include('include.footer')
</div>

<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.js')}}"></script>
<!-- DataTables -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

<script src="{{ asset('js/fcm.js') }}"></script>

<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-messaging.js"></script>


<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->

<script>
  console.log('{{ asset("logo_app.png") }}')
  // Your web app's Firebase configuration
  var firebaseConfig = {
    apiKey: "AIzaSyBd-hpKWU00hnmJq9HaEzVbWQwXnbiF0F8",
    authDomain: "tesfirebase-cf18a.firebaseapp.com",
    databaseURL: "https://tesfirebase-cf18a.firebaseio.com",
    projectId: "tesfirebase-cf18a",
    storageBucket: "tesfirebase-cf18a.appspot.com",
    messagingSenderId: "285443598816",
    appId: "1:285443598816:web:7dfa6d01d2bff7ea176385"
  };
  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
</script>

<script>
  const messaging = firebase.messaging();

  // Add the public key generated from the console here.
  messaging.getToken({vapidKey: "BOmVU6h5pVaHiZisObJ5lxlshdfApMR5aH0xPNnCNCLW2dLk2DIjg21pVtlJ7bmEAqbKptT06i8GAfniQr9FiiE"});

  function sendTokenToServer(token){
    console.log(token);
    $.ajax({
        url: "{{ route('notofication.update.token') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            "token" : token,
        },
        success: function(response) {
          if(response.success){
            console.log(response.message);
          }else{
            console.log(response);
          }
        }
    });
  }

  function retreiveToken(){

      messaging.getToken({vapidKey: 'BOmVU6h5pVaHiZisObJ5lxlshdfApMR5aH0xPNnCNCLW2dLk2DIjg21pVtlJ7bmEAqbKptT06i8GAfniQr9FiiE'}).then((currentToken) => {
          if (currentToken) {
            sendTokenToServer(currentToken);
            // updateUIForPushEnabled(currentToken);
          } else {
            // Show permission request.
            alert('You should allow notification!');
            // console.log('No registration token available. Request permission to generate one.');
            // Show permission UI.
            // updateUIForPushPermissionRequired();
            // setTokenSentToServer(false);
          }
      }).catch((err) => {
            console.log('An error occurred while retrieving token. ', err);
            // showToken('Error retrieving registration token. ', err);
            // setTokenSentToServer(false);
      });
  }

  retreiveToken();
  messaging.onTokenRefresh(()=>{
      retreiveToken();
  });

  messaging.onMessage((payload) => {
    console.log('Message received. ', payload);
    const type = payload.data.role;
    const url = window.location.href;
    if(url.includes("{{ route('chat.index.engineer') }}")){

      $.ajax({
            url: "{{ route('ajax.chat.update.list_user') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                "type" : type
            },
            success: function(response) {
              const list_user_chat = update_list_user_chat(response);
              $('#list_user_chat').html(list_user_chat);
            }
        });

    }else{
      console.log('its not match');
    }
    toastr.info('Notifikasi baru')
    // ...
  });
</script>

@yield('scripts')
</body>
