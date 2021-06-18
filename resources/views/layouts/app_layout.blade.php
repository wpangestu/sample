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

<!-- Sweet Alert -->
@include('sweetalert::alert')
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
<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-analytics.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-messaging.js"></script>

<script>
  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
  var firebaseConfig = {
    apiKey: "AIzaSyCIFe2fIgT8U2S5HtThUKB6-hRGiOXIc5o",
    authDomain: "zippy-world-298704.firebaseapp.com",
    projectId: "zippy-world-298704",
    storageBucket: "zippy-world-298704.appspot.com",
    messagingSenderId: "652852667154",
    appId: "1:652852667154:web:88b2b19d35eb9c345ec9cb",
    measurementId: "G-SNT2TZQ2SL"
  };
  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
  firebase.analytics();
</script>

<script>
  const messaging = firebase.messaging();
  // var admin = require('firebase-admin');
  // import * as admin from 'firebase-admin';

  // Add the public key generated from the console here.
  messaging.getToken({vapidKey: "BPq75jHjVcvsnTpujR2Quw1UQ1JLVLvNDLNSSrOJSM_PoINHRq1Lrzk1yjSk2DL5ByrkctWRW9qKbjNyG8zbLjQ"});

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

    let registrationTopic = [token];

    // subscribeTopic(registrationTopic,'testing')
    
  }

  function subscribeTopic(token,topic){
      admin.messaging().subscribeToTopic(token, topic)
                        .then((response) => {
                          // See the MessagingTopicManagementResponse reference documentation
                          // for the contents of response.
                          console.log('Successfully subscribed to topic:', response);
                        })
                        .catch((error) => {
                          console.log('Error subscribing to topic:', error);
                        });
  }

  function retreiveToken(){

      messaging.getToken({vapidKey: 'BPq75jHjVcvsnTpujR2Quw1UQ1JLVLvNDLNSSrOJSM_PoINHRq1Lrzk1yjSk2DL5ByrkctWRW9qKbjNyG8zbLjQ'}).then((currentToken) => {
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

      const url_array = url.split("/");
      const lastItem = url_array[url_array.length - 1];

      if(lastItem == payload.data.userid+"#"){
        const chat = JSON.parse(payload.data.chat);
        let get_new_chat = append_message(chat);

        $('#message_user').append(get_new_chat);
        var objDiv = document.getElementById("message_user");
        objDiv.scrollTop = objDiv.scrollHeight;
      }
    }
    else if(url.includes("{{ route('chat.index.customer') }}")){
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
      const url_array = url.split("/");
      const lastItem = url_array[url_array.length - 1];

      if(lastItem == payload.data.userid+"#"){
        const chat = JSON.parse(payload.data.chat);
        let get_new_chat = append_message(chat);

        $('#message_user').append(get_new_chat);
        var objDiv = document.getElementById("message_user");
        objDiv.scrollTop = objDiv.scrollHeight;
      }
    }
    $(document).Toasts('create', {
      body: payload.notification.body,
      title: payload.notification.title,
      subtitle: '',
      icon: 'fas fa-envelope fa-lg',
    })
    // ...
  });
</script>

@yield('scripts')
</body>
