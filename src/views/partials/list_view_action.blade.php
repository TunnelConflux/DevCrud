<td style="min-width: 105px">
    @if (@$routePrefix)
        <form method="POST" action="{{ route($routePrefix.'.delete', [$item->id]) }}" accept-charset="UTF-8"
              onsubmit="return confirm('Are you sure you want to delete this?')">
            <input name="_method" type="hidden" value="DELETE">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <input name="query" type="hidden" value="{{ request()->query('query') }}">
            <input name="page" type="hidden" value="{{ request()->query('page') }}">
            
            @if(@$isViewable)
                <a href="{{ route($routePrefix.'.view',[$item->id, "query" => request()->query('query'), "page" => request()->query('page')]) }}" title="View"
                   class="btn btn-info btn-sm">
                    <i class="fa fa-fw fa-plus"></i>
                </a>
            @endif
            
            @if(@$isEditable)
                <a href="{{ route($routePrefix.'.edit', [$item->id, "query" => request()->query('query'), "page" => request()->query('page')]) }}" title="Edit"
                   class="btn btn-info btn-sm">
                    <i class="fa fa-fw fa-edit"></i>
                </a>
            @endif
            
            @if(@$isDeletable)
                <button class="btn btn-sm btn-danger" type="submit"><i class="fa fa-fw fa-trash"></i></button>
            @endif
        </form>
    @else
        <a href="{{ url("") }}" title="No action (Go home)"
           class="btn btn-info btn-sm">
            <i class="fa fa-fw fa-ban"></i>
        </a>
    @endif
</td>