@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Статистика</div>

                <div class="panel-body">
                    <ul>
                        <li><a href="{{ route('total') }}">Общая статистика</a></li>
                        <li>По страницам:</li>
                            <ul>
                            @foreach ($uris as $uri)
                                <li><a href="{{ route('uri', ['uri' => $uri]) }}">{{ $uri }}</a></li>
                            @endforeach
                            </ul>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
