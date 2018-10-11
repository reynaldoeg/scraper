@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div>
                        <table class="table table-striped table-hover table-bordered">
                            <tr>
                                <th>Tienda</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                            </tr>
                            @foreach($products as $prod)
                                <tr>
                                    <td>{{$prod->store}}</td>
                                    <td>{{$prod->name}}</td>
                                    <td>{{$prod->description}}</td>
                                    <td>${{number_format($prod->price, 2)}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
