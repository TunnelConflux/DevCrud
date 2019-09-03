@extends('dev-crud::layouts.info')

@section('blockTitle')
    {{ $pageTitle }}
@endsection

@section('dataBlock')
    <div class="card-body text-justify">
        <div class="col-md-6">
            @foreach($infoItems as $val)
                @if ($val == 'status')
                    <label>{{ ucwords(str_replace('_', ' ', $val)) }}: </label>
                    {{ \TunnelConflux\DevCrud\Helpers\DevCrudHelper::getStatus(($data->{$val}?:0)) }} <br>
                @elseif(!in_array(@$infoItems[$val][1], ['image', 'file', 'video']) && $val != 'password')
                    @php
                        $tempKey = str_replace(['_id', '_'], ['',' '], $val);
                    @endphp
                    <label>{{ ucwords($tempKey) }}: </label>
                    @if (is_iterable($data->{$tempKey}))
                        @foreach($data->{$tempKey} as $k => $v)
                            @if (isset($v->title))
                                <br /> <i class="fa fa-angle-right"></i> {{ $v->title }}
                            @elseif (isset($v->display_name))
                                <br /> <i class="fa fa-angle-right"></i> {{ $v->display_name }}
                            @elseif (isset($v->name))
                                <br /> <i class="fa fa-angle-right"></i> {{ $v->name }}
                            @endif
                        @endforeach
                        <br />
                    @elseif (is_object($data->{$tempKey}))
                        @if (isset($data->{$tempKey}->title))
                            <br /> <i class="fa fa-angle-right"></i> {{ $data->{$tempKey}->title }}
                        @elseif (isset($data->{$tempKey}->display_name))
                            <br /> <i class="fa fa-angle-right"></i> {{ $data->{$tempKey}->display_name }}
                        @elseif (isset($data->{$tempKey}->name))
                            <br /> <i class="fa fa-angle-right"></i> {{ $data->{$tempKey}->name }}
                        @endif
                        <br />
                    @else
                        @if (preg_match("/(content|description)/i",$val))
                            <p>{{ $data->{$val}?strip_tags($data->{$val}):"N/A" }}</p>
                        @else
                            {{ $data->{$val}?:"N/A" }} <br>
                        @endif
                    @endif
                @endif
            @endforeach
        </div>
        <div class="col-md-6">
            @foreach($infoItems as $key => $val)
                @if (@$infoItems[$key][1] == 'image' && $data->{$key})
                    @if(file_exists($uploadPath.'/'.$data->{$key}))
                        <div class="col-md-6 float-left" style="padding: 2px;background: #2d28283d;">
                            <label class="text-center">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                            <img width="100%" src="{{ getFileUrl($uploadPath, $data->{$key}) }}" />
                        </div>
                    @endif
                @endif
            @endforeach
        </div>
    </div>
    <div class="card-footer">
        @include('dev-crud::partials.detail_view_action')
    </div>
@endsection