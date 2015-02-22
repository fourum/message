@include('meta')
@include('header')

<div class="row">
    <div class="col-md-12">
        <h3>Messages</h3>

        <div class="btn-group btn-group-sm buffer-sm">
            @foreach($menu->getItems() as $item)
                <a href="{{ url($item->getTarget()) }}" class="btn btn-default">{{ ucwords($item->getName()) }}</a>
            @endforeach
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-borderless buffer">
            <tbody>
            @foreach($senders as $user)
                <tr>
                    <td style="width:50px;">
                        <img src="{{ gravatar($user->getEmail(), 50) }}">
                    </td>
                    <td>
                        <h4>
                            <a href="{{ url('/messages/view/' . $user->getUsername()) }}">
                                {{ $user->getUsername() }}
                            </a>
                        </h4>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('footer')
