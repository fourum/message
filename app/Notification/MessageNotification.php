<?php

namespace Fourum\Message\Notification;

use Carbon\Carbon;
use Fourum\Notification\NotificationInterface;
use Fourum\Notification\NotifierInterface;
use Fourum\Notification\NotifiableInterface;

class MessageNotification implements NotificationInterface
{
    const TYPE = 'message';

    protected $notifier;
    protected $notifiable;
    protected $read;
    protected $timestamp;

    public function __construct(NotifierInterface $notifier, NotifiableInterface $notifiable, $read = false, Carbon $timestamp = null)
    {
        $this->notifier = $notifier;
        $this->notifiable = $notifiable;
        $this->read = $read;
        $this->timestamp = $timestamp;
    }

    public function getDescription()
    {
        return "<a href=\"".url("/user/{$this->notifier->getAuthor()->getUsername()}")."\">{$this->notifier->getAuthor()->getUsername()}</a> sent you a <a href=\"{$this->notifier->getUrl()}\">{$this->notifier->getEntityName()}</a>";
    }

    public function getUrl()
    {
        return $this->notifier->getUrl();
    }

    public function getNotifiable()
    {
        return $this->notifiable;
    }

    public function getNotifier()
    {
        return $this->notifier;
    }

    public function getType()
    {
        return self::TYPE;
    }

    public function isRead()
    {
        return (bool) $this->read;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function markAsRead()
    {
        $this->read = true;
    }
}
