<span>
    @if(@$routePrefix)
        <form method="POST" action="{{ route($routePrefix.'.delete', [$data->id]) }}" accept-charset="UTF-8"
              onsubmit="return confirm('Are you sure you want to delete this?')">
            <input name="_method" type="hidden" value="DELETE">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            
            <a href="{{ route("{$routePrefix}.index", ["query" => request()->query('query'), "page" => request()->query('page')]) }}" class="btn btn-xs btn-default">
                <i class="fa fa-fw fa-arrow-left"></i> Back
            </a>
            <a href="{{ route("{$routePrefix}.edit",[$data->id]) }}" class="btn btn-xs btn-default">
                <i class="fa fa-fw fa-edit"></i> Edit
            </a>
            <button class="btn btn-xs btn-default" type="submit"><i class="fa fa-fw fa-trash"></i> Delete</button>
        </form>
    @else
        <a href="{{ url("") }}" title="No action">
            <i class="fa fa-fw fa-ban"></i> Go home
        </a>
    @endif
</span>