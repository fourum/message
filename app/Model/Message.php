<?php

namespace Fourum\Message\Model;

use Fourum\Notification\NotifierInterface;
use Fourum\Reporting\ReportableInterface;
use Fourum\Repository\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class Message extends Model implements NotifierInterface, RepositoryInterface, ReportableInterface
{
    protected $guarded = array('id');

    /**
     * @return mixed
     */
    public function getAll()
    {
        return self::all();
    }

    /**
     * @param array $input
     * @return mixed
     */
    public function createAndSave(array $input)
    {
        return self::create($input);
    }

    public function getId()
    {
        return $this->id;
    }

    public function get($id)
    {
        return self::find($id);
    }

    public function getName()
    {
        return 'message';
    }

    public function getContent()
    {
        return $this->content;
    }

    public function markAsRead()
    {
        $this->read = 1;
        $this->save();
    }

    public function isRead()
    {
        return (bool) $this->read;
    }

    public function getAuthor()
    {
        return $this->user()->first();
    }

    public function user()
    {
        return $this->belongsTo('Fourum\Model\User', 'from_user_id');
    }

    public function getForeignKey()
    {
        return 'message_id';
    }

    public function getEntityName()
    {
        return 'message';
    }

    public function getUrl()
    {
        return "/messages/view/{$this->getAuthor()->getUsername()}";
    }
}
