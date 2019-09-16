@extends('dev-crud::layouts.info')

@section('blockTitle')
    {{ $pageTitle }}
@endsection

@section('dataBlock')
    <router-view></router-view>
    
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead>
            <tr class="text-capitalize">
                <th style="width: 25px">SL</th>
                @foreach($listColumns as $item)
                    <th>{{$item}}</th>
                @endforeach
                @if(@$isCreatable || @$isEditable  || @$isViewable  || @$isDeletable )
                    <th style=" min-width: 152px; ">Action</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @forelse($data as $key => $item)
                <tr id="sim-{{ $item->id }}">
                    <td>{{ $key + 1 + (($data->currentPage() <= 1) ? 0 : (($data->currentPage() - 1) * $data->perPage())) }}</td>
                    
                    @foreach($listColumns as $k => $v)
                        @if (in_array($k, ['status', 'is_active']))
                            <td>{{ \TunnelConflux\DevCrud\Helpers\DevCrudHelper::getStatus(($item->{$k}?:0))}}</td>
                        @elseif (in_array($v, $item->inputTypes[\TunnelConflux\DevCrud\Models\Enums\InputTypes::IMAGE]))
                            <td><img style="max-height: 80px;max-width: 120px" src="{{ \TunnelConflux\DevCrud\Helpers\DevCrudHelper::getFileUrl($item, $item->{$v}) }}" /></td>
                        @elseif (is_object($item->{$k}) && (isset($item->{$k}->title) || isset($item->{$k}->name)))
                            <td>{{ $item->{$k}->title??$item->{$k}->name }}</td>
                        @else
                            @if(@$item->{$k})
                                <td>{!! $item->{$k} !!}</td>
                            @else
                                <td>N/A</td>
                            @endif
                        @endif
                    @endforeach
                    
                    @include('dev-crud::partials.list_view_action')
                </tr>
            @empty
                <tr>
                    <td colspan="50" class="text-center"><b>No record found</b></td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    
    @if ($data->lastPage() > 1)
        <div class="card-footer clearfix text-right">
            {{ $data->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
