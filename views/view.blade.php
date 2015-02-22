@include('meta')
@include('header')

<div class="row">
    <div class="col-md-12">
        <h3>Conversation with {{ $sender->getUsername() }}</h3>
    </div>
</div>

<div class="row">
    <div class="col-md-12 text-center">
        <a href="">Show older messages</a>
    </div>
</div>

@foreach($messages as $message)
<div class="row post">
    <div class="col-md-1">
        {{ Gravatar::image($message->getAuthor()->getEmail(), '', array('width' => 50, 'height' => 50)) }}
    </div>
    <div class="col-md-11">
        <div class="row post-content-container">
            <div class="col-md-12 post-content">
                {{ $message->getContent() }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-skinny">
                <div class="btn-group btn-group-sm post-controls">
                    @if($message->getAuthor()->getId() != $user->getId())
                        <a href="{{ url('/report/message/' . $message->getId()) }}" class="btn btn-default">Report</a>
                    @else
                        <a href="{{ url('/messages/delete/' . $message->getId()) }}" class="btn btn-default">Delete</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<div class="row">
    <div class="col-md-12">
        @include('message::quickform')
    </div>
</div>

@include('footer')
