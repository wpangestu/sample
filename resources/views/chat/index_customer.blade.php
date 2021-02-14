@extends('layouts.app_layout')
@section('title','Chat Pelanggan')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Chat Pelanggan</h1>
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
          <div class="col-md-3">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">List Pelanggan</h3>
            </div>
            <div class="card-body p-0 overflow-auto" style="height:400px">
              <ul class="nav nav-pills flex-column">
              @foreach($engineers as $engineer)
                <li class="nav-item active">
                  <a href="#" data-user_id="{{ $engineer->id }}" class="nav-link engineer_list">
                    <i class="fas fa-user"></i> {{$engineer->name}}
                    <!-- <span class="badge bg-primary float-right">12</span> -->
                  </a>
                </li>
              @endforeach
                <!-- <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-envelope"></i> Sent
                  </a>
                </li> -->
              </ul>
            </div>
            <!-- /.card-body -->
          </div>
          </div>
          <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chat Messages  <span id="user_name"></span></h3>
                    <!-- <div class="card-tools">
                    <span data-toggle="tooltip" title="3 New Messages" class="badge badge-light">3</span>
                    <button type="button" class="btn btn-tool" data-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle">
                        <i class="fas fa-comments"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-widget="remove"><i class="fas fa-times"></i>
                    </button> -->
                    <!-- </div> -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <!-- Conversations are loaded here -->
                    <div class="direct-chat-messages" id="message_user" style="height:300px">
                      <p>Silahkan pilih user untuk memulai chat</p>
                      <!-- <div class="direct-chat-msg">
                          <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-left">Alexander Pierce</span>
                            <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                          </div>
                          <div class="direct-chat-text">
                          Is this template really for free? That's unbelievable!
                          </div>
                      </div>

                      <div class="direct-chat-msg right">
                          <div class="direct-chat-infos clearfix">
                          <span class="direct-chat-name float-right">Sarah Bullock</span>
                          <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                          </div>

                          <div class="direct-chat-text">
                          You better believe it!
                          </div>
                      </div> -->
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <form action="{{ route('post.chat.user') }}" method="post" id="form_post_message">
                        <div class="input-group">
                            <input required type="text" name="message" id="input_message" placeholder="Type Message ..." class="form-control">
                            <input type="hidden" name="user_id" value="" id="input_hidden_user_id">
                            <span class="input-group-append">
                            <button type="submit" class="btn btn-primary">Send</button>
                            </span>
                        </div>
                    </form>
                </div>
                <!-- /.card-footer-->
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
      $('.engineer_list').click(function () {
        let user_id = $(this).data('user_id');

        $.ajax({
            url: "{{ route('chat.user') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                "user_id" : user_id
            },
            success: function(response) {
              chat = template_message_user(response);
              
              $('#user_name').html(' - '+response.name);

              $('#message_user').html(chat)
              $('#input_hidden_user_id').val(user_id);

              var objDiv = document.getElementById("message_user");
              objDiv.scrollTop = objDiv.scrollHeight;

              // $('#form_post_message').attr('action','{{ route("post.chat.user",'+user_id+') }}');
            }
        });

      })

      function template_message_user(data)
      {
        let template = '';

        console.log(data.chat.length)

        if(data.chat.length > 0){
          data.chat.slice().reverse().forEach(d => {
            template += `
                  <div class="direct-chat-msg ${ d.from.toString() === "{{ auth()->user()->id }}" ?'right':''}" data-chat_id="${d.id}">
                      <div class="direct-chat-infos clearfix">
                        <span class="direct-chat-name float-${ d.from.toString() === "{{ auth()->user()->id }}" ?'right':'left'}"> ${d.name} </span>
                        <span class="direct-chat-timestamp float-${ d.from.toString() === "{{ auth()->user()->id }}" ?'right':'left'}"> [${d.created_at}] </span>
                      </div>
                      <div style="width:50%;margin:5px" class="direct-chat-text float-${ d.from.toString() === "{{ auth()->user()->id }}" ?'right':'left'}">
                        ${ d.message }
                      </div>
                  </div>
            `
          });
        }else{
          template += `
                  <p>Tidak ada riwayat chat</p>
            `;
        }

        return template;
      }

      $('form#form_post_message').submit(function(e){
        e.preventDefault();
        const user_id = $('#input_hidden_user_id').val();
        const message = $('#input_message').val();
        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                "user_id" : user_id,
                "message" : message
            },
            success: function(response) {
              console.log(response);
              if(response.chat.new){
                $('#message_user').html('');
              }
              const template_post = template_post_message(response.chat);
              $('#message_user').append(template_post);
              var objDiv = document.getElementById("message_user");
              objDiv.scrollTop = objDiv.scrollHeight;
            }
        });
        $('#input_message').val('');
      })

      function template_post_message(params) {
        template = `
                <div class="direct-chat-msg right" data-chat_id="${params.id}">
                    <div class="direct-chat-infos clearfix">
                      <span class="direct-chat-name float-right"> ${params.name} </span>
                      <span class="direct-chat-timestamp float-right"> [${params.created_at}] </span>
                    </div>
                    <div style="width:50%;margin:5px" class="direct-chat-text float-right">
                      ${ params.message }
                    </div>
                </div>
          `
        return template;
      }

    });
</script>

@endsection

