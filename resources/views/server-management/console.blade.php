@extends('server-management.layout')
@section('title')
    Управление серверами | Консоль
@endsection
@section('secondary_content')
<textarea style="overflow:auto;resize:none" class="w-100" rows="25" readonly="" wrap="off" id="console"></textarea>
<form class="row g-3" name="formcommand" onsubmit="consoleCMD({{$server->id}}, document.formcommand.command.value); return false;">
    <div class="col-auto">
      @csrf
      <input type="hidden" name="id" value="{{$server->id}}">
      <input type="hidden" name="type" value='command'>
      <input type="text" class="form-control" name="command" value="">
    </div>
    <div class="col-auto">
      <button type="button" class="btn btn-primary mb-3" onclick="consoleCMD({{$server->id}}, document.formcommand.command.value); return false;">Выполнить</button>
    </div>
</form>
@push('scripts')
<script type="text/javascript">
    let tell = 0;
    let id = '{{$server->id}}'
    function updateConsole() {
        const elem = document.getElementById('console');
        let xhr = new XMLHttpRequest();
        xhr.open('GET', '/server-management/console-update?tell='+tell+'&id='+id);
        xhr.send();
        xhr.onload = function() 
        {
          if (xhr.status != 200) { 
              console.log(`Ошибка ${xhr.status}: ${xhr.statusText}`);
          } else {
              let obj = JSON.parse(xhr.response);
              let needscroll = elem.scrollTop+elem.clientHeight == elem.scrollHeight
              if (tell > obj.tell)
              {
                elem.value = obj.rows;
              }
              else
              {
                elem.value += obj.rows;
              }
              tell = obj.tell;
              if (needscroll)
              {
                elem.scrollTo(0, elem.scrollHeight);
              }
          }
        };

        xhr.onerror = function() {
            console.log("Запрос не удался");
        };
    }

    setInterval(updateConsole, 1000);

    function consoleCMD(server_id, command)
    {
      var formData = new FormData(document.formcommand);

      let xhr = new XMLHttpRequest();
      xhr.open('POST', '/server-management/console-runcommand');
      xhr.send(formData);
      xhr.onload = function() 
      {
        if (xhr.status != 200) { 
            console.log(`Ошибка ${xhr.status}: ${xhr.statusText}`);
        }
        else
        {
          document.formcommand.command.value = '';
        }
      };
    }
</script>
@endpush
@endsection