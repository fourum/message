<?php

namespace Fourum\Message\Http\Controllers;

use Fourum\Http\Controllers\FrontController;
use Fourum\Menu\Item\LinkItem;
use Fourum\Menu\SimpleMenu;
use Fourum\Message\Model\Message;
use Fourum\Message\Notification\MessageNotification;
use Fourum\Notification\NotificationRepositoryInterface;
use Fourum\Setting\Manager;
use Fourum\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class MessageController extends FrontController
{
    /**
     * @var UserRepositoryInterface
     */
    protected $users;

    /**
     * @param UserRepositoryInterface $users
     * @param Manager $settings
     */
    public function __construct(UserRepositoryInterface $users, Manager $settings)
    {
        parent::__construct($settings);

        $this->users = $users;
    }

    public function index()
    {
        $results = DB::table('messages')
            ->select('from_user_id')
            ->where('user_id', $this->getUser()->getId())
            ->orderBy('created_at', 'desc')
            ->distinct()
            ->get();

        $senders = array();

        foreach ($results as $result) {
            $senders[] = $this->users->get($result->from_user_id);
        }

        $menu = new SimpleMenu(array(
            new LinkItem('conversations', '/messages'),
            new LinkItem('new conversation', '/messages/create')
        ));

        Event::fire('message::menu.created', array($menu));

        $data['senders'] = $senders;
        $data['menu'] = $menu;

        return view('message::index', $data);
    }

    public function getCreate()
    {
        return view('message::create');
    }

    public function postCreate(Request $request, NotificationRepositoryInterface $notifications)
    {
        $to = $request->get('to');
        $message = $request->get('message');

        $user = $this->users->getByUsername(trim($to));

        $message = Message::create(array(
            'from_user_id' => $this->getUser()->getId(),
            'user_id' => $user->getId(),
            'content' => $message,
            'read' => 0
        ));

        $notification = new MessageNotification($message, $user);
        $notifications->createAndSave($notification);

        return redirect('/messages/view/' . $user->getUsername());
    }

    public function view($username)
    {
        $sender = $this->users->getByUsername($username);
        $user = $this->getUser();

        $results = DB::table('messages')
            ->where('from_user_id', $sender->getId())
            ->where('user_id', $user->getId())
            ->orWhere(function($query) use ($sender, $user) {
                $query->where('from_user_id', $user->getId())
                    ->where('user_id', $sender->getId());
            })
            ->take(5)
            ->orderBy('created_at', 'desc')
            ->get();

        $results = array_reverse($results);

        $messages = array();

        foreach ($results as $result) {
            $messages[] = Message::find($result->id);
        }

        foreach ($messages as $message) {
            if ($message->getAuthor()->getId() != $user->getId()) {
                $message->markAsRead();
            }
        }

        $data['messages'] = $messages;
        $data['sender'] = $sender;

        return view('message::view', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userSearch(Request $request)
    {
        $users = $this->users->getLikeUsername($request->get('q'));

        $usernamesToIds = array();

        foreach ($users->all() as $user) {
            $usernamesToIds[] = array(
                'id' => $user->getId(),
                'text' => $user->getUsername()
            );
        }

        return response()->json($usernamesToIds);
    }
}