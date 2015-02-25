<?php

namespace Fourum\Message;

use App;
use Event;
use Fourum\Menu\Item\LinkItem;
use Fourum\Message\Model\Message;
use Fourum\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
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

        if (! Schema::hasTable('messages')) {
            Schema::create('messages', function ($table) {
                $table->engine = "InnoDb";

                $table->increments('id')->unsigned();
                $table->integer('from_user_id')->unsigned()->index();
                $table->integer('user_id')->unsigned()->index();
                $table->text('content');
                $table->tinyInteger('read');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        $this->registerEvents();
        $this->registerRoutes();
        $this->registerNotifications();
        $this->registerRepository();
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

    protected function registerRepository()
    {
        $this->setupRepository('message', 'message_id', 'Fourum\Message\Model\Message');
    }

    protected function registerNotifications()
    {
        $this->setupNotifications('Fourum\Message\Notification\MessageNotification');
    }

    protected function registerEvents()
    {
        Event::listen('header.menu.loggedin.created', function($menu, $user) {
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
