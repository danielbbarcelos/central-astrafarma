@extends('layouts.template')

@section('page-title', 'Arquivos de logs')

@section('page-css')

@endsection

@section('page-content')

    <div class="middle padding-top-20 padding-right-20">
        <div class="row">

            @foreach($arquivos as $tipo => $itens)
                <div class="col s12 m6 l6">
                    <div class="card-panel" style="height: 520px; overflow-y: auto">
                        <div class="card-content">
                            <h6 class="card-title font-weight-600 font-size-16 padding-bottom-30">
                                {{$tipo}}: arquivos de logs
                            </h6>

                            <ul class="collapsible" data-collapsible="accordion">
                                @foreach($itens as $filename => $file)
                                    <li>
                                        <div class="collapsible-header row" style="cursor: default">
                                            <i class="material-icons cursor-pointer" onclick="download('{!! $filename !!}')">cloud_download</i>

                                            <h6 style="font-weight: 700;" class="padding-top-10">
                                                {{$filename}} {{--date("d/m/Y - H:i:s",$file->getCTime())--}}

                                                <span style="float: right">{{Helper::formataBytes($file->getSize())}}</span>
                                            </h6>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>





@endsection


@section('page-scripts')

    <script>
        function download(filename)
        {
            window.location = '/equipe-vex/logs/'+filename+'/download'
        }
    </script>

@endsection