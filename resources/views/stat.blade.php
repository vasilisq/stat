@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Статистика</div>

                <div class="panel-body">
                    <a href="{{ route('home') }}" class="btn btn-default">
                        <i class="glyphicon glyphicon-circle-arrow-left"></i> Назад
                    </a>
                    @foreach ($stat as $sectionName => $section)
                        <h2>{{ trans('stats.' . $sectionName) }}</h2>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Значение показателя</th>
                                    <th>Хиты</th>
                                    <th>Уники по IP</th>
                                    <th>Уники по Cookies</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($section as $index)
                                <tr>
                                    <td>{{ $index->getIndexValue() }}</td>
                                    <td>{{ $index->getHits() }}</td>
                                    <td>{{ $index->getUniqueIpHits() }}</td>
                                    <td>{{ $index->getUniqueCookieHits() }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
