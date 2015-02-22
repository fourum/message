<?php

namespace Fourum\Message;

use App;
use Carbon\Carbon;
use Event;
use Fourum\Menu\Item\LinkItem;
use Fourum\Menu\SimpleMenu;
use Fourum\Message\Model\Message;
use Fourum\Message\Notification\MessageNotification;
use Fourum\Notification\NotifierInterface;
use Fourum\Notification\NotifiableInterface;
use Illuminate\Support\ServiceProvider;
use Route;

class MessageServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'message');

        $this->registerEvents();
        $this->registerRoutes();
        $this->registerNotifications();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    protected function registerNotifications()
    {
        $factory = App::make('Fourum\Notification\NotificationFactory');
        $repoFactory = App::make('Fourum\Repository\RepositoryFactory');
        $repoRegistry = App::make('Fourum\Repository\RepositoryRegistry');

        $factory->addType(MessageNotification::TYPE_MESSAGE, function (
            NotifierInterface $notifier,
            NotifiableInterface $notifiable,
            $read,
            Carbon $timestamp
        ){
            return new MessageNotification($notifier, $notifiable, $read, $timestamp);
        });

        $repoFactory->addForeignKey('message_id', 'Fourum\Message\Model\Message');
        $repoRegistry->add('message', 'Fourum\Message\Model\Message');
    }

    protected function registerEvents()
    {
        Event::listen('header.menu.loggedin.created', function(SimpleMenu $menu, $user) {
            $count = count(Message::where('user_id', $user->getId())->where('read', 0)->get()->all());
            $countText = '';

            if ($count > 0) {
                $countText = " ({$count})";
            }

            $menu->addItem(
                new LinkItem("messages{$countText}", '/messages')
            );
        });
    }

    protected function registerRoutes()
    {
        Route::get('/messages', 'Fourum\Message\Http\Controllers\MessageController@index');
        Route::get('/messages/create', 'Fourum\Message\Http\Controllers\MessageController@getCreate');
        Route::post('/messages/create', 'Fourum\Message\Http\Controllers\MessageController@postCreate');
        Route::get('/messages/view/{username}', array(
            'as' => 'message.view',
            'uses' => 'Fourum\Message\Http\Controllers\MessageController@view'
        ));
        Route::get('/messages/user-search', 'Fourum\Message\Http\Controllers\MessageController@userSearch');
    }
}
