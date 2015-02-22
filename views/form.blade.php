@foreach ($errors->all() as $error)
<div class="alert alert-danger">
    {{ $error }}
</div>
@endforeach

{!! Form::open(array('url' => '/messages/create', 'method' => 'post', 'role' => 'form')) !!}

    <div class="form-group">
        {!! Form::label('to', 'To') !!}
        {!! Form::hidden('to', Input::old('to'), array('style' => 'width:100%', 'id' => 'to')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('message', 'Message') !!}
        {!! Form::textarea('message', Input::old('message'), array('class' => 'form-control')) !!}
    </div>

    {!! Form::button('Send', array('class' => 'btn btn-default btn-primary', 'type' => 'submit')) !!}

{!! Form::close() !!}

<script type="text/javascript">
(function($, window, document) {

    $(function() {
        $("#to").select2({
            placeholder: "Find a user",
            minimumInputLength: 1,
            ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                url: "{{ url('/messages/user-search') }}",
                dataType: 'json',
                data: function (term, page) {
                    return {
                        q: term // search term
                    };
                },
                results: function (data, page) { // parse the results into the format expected by Select2.
                    // since we are using custom formatting functions we do not need to alter remote JSON data
                    return {results: data};
                }
            }
        });
    });

}(window.jQuery, window, document));
</script>
