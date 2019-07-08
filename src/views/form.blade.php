@extends('easy-crud::layouts.form')

@section('formTitle')
    {{ $pageTitle }}
@endsection

@section('blockTitle')
    {{ $pageTitle }}
@endsection

@section('form')
    {{ Form::open(['route' => [$formActionRoute, $formActionId], 'method' => $formActionMethod, 'files' => true]) }}
    <div class="card-body">
        @foreach($formItems as $item)
            {!! getInputField($item, @$data) !!}
        @endforeach
    </div>
    
    <div class="card-footer">
        <a href="{{ route($routePrefix.'.index', ["query" => request()->query('query'), "page" => request()->query('page')]) }}">
            <button type="button" class="btn btn-default">Cancel</button>
        </a>
        
        <button type="submit" class="btn btn-info pull-right">Submit</button>
    </div>
    {{ Form::close() }}
@endsection

@include('partials.select2')
@include('partials.froalaEditor')