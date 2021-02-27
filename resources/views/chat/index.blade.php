@extends('layouts.app_layout')
@section('title','Chat Teknisi')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Chat Teknisi</h1>
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
              <h3 class="card-title">List Teknisi</h3>
            </div>
            <div class="card-body p-0 overflow-auto" id="list_user_chat" style="height:400px">
              <ul class="nav nav-pills flex-column">
              @foreach($new_chatroom_data as $value)
                <li class="nav-item active">
                  <a href="#" data-user_id="{{ $value['user_id'] }}" data-userid="{{ $value['userid'] }}" class="nav-link engineer_list">
                    <i class="fas fa-user"></i> {{$value['user_name']}}
                    @if($value['unread_count'] > 0 )
                      <span class="badge bg-primary float-right">{{ $value['unread_count'] }}</span>
                    @endif
                  </a>
                </li>
              @endforeach
              @foreach($engineers as $engineer)
                <li class="nav-item active">
                  <a href="#" data-user_id="{{ $engineer->id }}" data-userid="{{ $engineer->userid }}" class="nav-link engineer_list">
                    <i class="fas fa-user"></i> {{$engineer->name}}
                  </a>
                </li>
              @endforeach
              </ul>
            </div>
            <!-- /.card-body -->
          </div>
          </div>
          <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chat Messages <span id="user_name">{{ isset($user->name)?' - '.$user->name:'' }}</span></h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <!-- Conversations are loaded here -->
                    <div class="direct-chat-messages" id="message_user" style="height:300px">
                      @if(!(isset($user)))
                      <p>Silahkan pilih user untuk memulai chat</p>
                      @else
                        @if(!empty($chat))
                          @foreach($chat->reverse() as $value)
                          <div class="direct-chat-msg @if($loop->first) first @endif {{$value->user_from->hasRole(['admin', 'cs']) ?'right':''}}" data-chat_id="{{$value->id}}">
                              <div class="direct-chat-infos clearfix">
                                <span class="direct-chat-name float-{{ $value->user_from->hasRole(['admin', 'cs']) ?'right':'left'}}"> {{ $value->user_from->name }} @if($value->user_from->hasRole('admin')) (admin) @elseif($value->user_from->hasRole('cs')) (cs) @else (user) @endif</span>
                                <span class="pl-1 pr-1 direct-chat-timestamp float-{{ $value->user_from->hasRole(['admin', 'cs']) ? 'right':'left'}}"> [{{$value->created_at->format('d/m/Y H:i')}}] </span>
                              </div>
                              <div style="width:50%;margin:5px" class="direct-chat-text float-{{ $value->user_from->hasRole(['admin', 'cs']) ?'right':'left'}}">
                                {{ $value->message }}
                              </div>
                          </div>

                          @endforeach
                        @else
                          <p>Tidak ada riwayat chat</p>
                        @endif
                      @endif
                    </div>
                </div>
                <!-- /.card-body -->
                <div id="form-send-message-layout" class="card-footer" {{ isset($user)?'':'hidden' }}>
                    <form action="{{ route('post.chat.user') }}" method="post" id="form_post_message">
                        <div class="input-group">
                            <input required type="text" name="message" id="input_message" placeholder="Type Message ..." class="form-control">
                            <input type="hidden" name="user_id" value="{{ isset($user)?$user->id:'' }}" id="input_hidden_user_id">
                            <span class="input-group-append">
                            <button type="submit" class="btn btn-primary">Send</button>
                            </span>
                        </div>
                    </form>
                </div>
                <!-- /.card-footer-->
                <div id="card-refresh-layout" class="overlay" hidden>
                  <i class="fas fa-2x fa-sync-alt fa-spin"></i>
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

<script>

    $(document).ready(function(){


      var contend = document.getElementById('message_user');
      contend.addEventListener('scroll', function(event) {
          if (event.target.scrollTop === 0) {
            const first = $('.direct-chat-msg.first').data('chat_id');
            // alert(first);
          }
      }, false);


      var objDiv = document.getElementById("message_user");
      objDiv.scrollTop = objDiv.scrollHeight;

      $('.engineer_list').click(function () {

        let user_id = $(this).data('user_id');
        let userid = $(this).data('userid');

        $('#card-refresh-layout').attr('hidden',false);

        // For change URL
        history.pushState({}, "", "{{ route('chat.engineer.show') }}/"+userid)

        // location.replace("/"+user_id);
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
              $("#form-send-message-layout").attr('hidden',false);
              $('#card-refresh-layout').attr('hidden',true);
              var objDiv = document.getElementById("message_user");
              objDiv.scrollTop = objDiv.scrollHeight;

              // $('#form_post_message').attr('action','{{ route("post.chat.user",'+user_id+') }}');
            }
        });

      })

      function template_message_user(data)
      {
        let template = '';

        // console.log(data.chat)

        if(data.chat.length > 0){
          data.chat.slice().reverse().forEach((d,i) => {
            template += `
                  <div class="direct-chat-msg ${ i===0?'first ':'' } ${ d.admin_cs ?'right':''}" data-chat_id="${d.id}">
                      <div class="direct-chat-infos clearfix">
                        <span class="direct-chat-name float-${ d.admin_cs ?'right':'left'}"> ${d.name} ${d.role!==""?"("+d.role+")":""}</span>
                        <span class="pl-1 pr-1 direct-chat-timestamp float-${ d.admin_cs ?'right':'left'}"> [${d.created_at}] </span>
                      </div>
                      <div style="width:50%;margin:5px" class="direct-chat-text float-${ d.admin_cs ?'right':'left'}">
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

