@if (session('delete'))
    <?php $sessionData = session('delete'); ?>
    <div class="alert alert-warning">
        <h4>
            You have {{ $sessionData['number'] }} "{{ $sessionData['title'] }}" under this Item !
            Still want to delete ?
            {!! Form::open(['route' => [
                "{$route}.delete",
                $sessionData['id'],
                'delete' => 'delete-anyway',
                ], 'method' => 'DELETE', 'style' => 'display:inline']) !!}
            <a href="{{ route("{$route}.index") }}" class="btn btn-primary"
               style="text-decoration: none">Cancel</a>
            {!! Form::submit('Delete Now', ['class' => 'btn btn-danger',
            'onclick' => 'return confirm("Are you sure?");']) !!}
            {!!  Form::close() !!}
        </h4>
    </div>
    <?php session()->forget('delete') ?>
@endif