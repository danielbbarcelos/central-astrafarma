
@if(Session::has('log'))
    @foreach(Session::get('log') as $key => $value)
        @foreach($value as $type => $message)
            @if($type == 'error')
                <script>
                    Materialize.toast('{!! $message !!}', 5000, 'red');
                </script>
            @elseif($type == 'success')
                <script>
                    Materialize.toast('{!! $message !!}', 5000, 'green');
                </script>
            @elseif($type == 'warning')
                <script>
                    Materialize.toast('{!! $message !!}', 5000, 'orange');
                </script>
            @elseif($type == 'info')
                <script>
                    Materialize.toast('{!! $message !!}', 5000, '');
                </script>
            @endif
        @endforeach
    @endforeach
@endif
