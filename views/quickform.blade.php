@foreach ($errors->all() as $error)
<div class="alert alert-danger">
    {{ $error }}
</div>
@endforeach

{!! Form::open(array('url' => '/messages/create', 'method' => 'post', 'role' => 'form')) !!}

    <div class="form-group">
        {!! Form::hidden('to', $sender->getUsername(), array('class' => 'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('message', 'Message') !!}
        {!! Form::textarea('message', Input::old('message'), array('class' => 'form-control', 'size' => '50x5')) !!}
    </div>

    {!! Form::button('Send', array('class' => 'btn btn-default btn-primary', 'type' => 'submit')) !!}

{!! Form::close() !!}
