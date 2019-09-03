@extends('dev-crud::layouts.form')

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
            {!! \TunnelConflux\DevCrud\Helpers\DevCrudHelper::getInputField($item, $data) !!}
        @endforeach
        
        <div id="dynamic-data"></div>
        
    </div>
    
    <div class="card-footer">
        <a href="{{ route("{$routePrefix}.index", ["query" => request()->query('query'), "page" => request()->query('page')]) }}">
            <button type="button" class="btn btn-default">Cancel</button>
        </a>
        
        <button type="submit" class="btn btn-info float-right">Submit</button>
    </div>
    {{ Form::close() }}
    
@endsection

@include('dev-crud::partials.select2')
@include('dev-crud::partials.froalaEditor')
