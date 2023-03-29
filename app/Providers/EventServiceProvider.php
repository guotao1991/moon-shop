<?php

namespace App\Providers;

use App\Events\Goods\GoodsAddEvent;
use App\Events\Goods\GoodsEditEvent;
use App\Listeners\Goods\Add;
use App\Listeners\Goods\Edit;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // 新增SqlListener监听QueryExecuted
        'Illuminate\Database\Events\QueryExecuted' => [
            'App\Listeners\SqlListener',
        ],
        GoodsAddEvent::class => [
            Add\CategoryListener::class,
            Add\ColorsListener::class
        ],
        GoodsEditEvent::class => [
            Edit\CategoryListener::class,
            Edit\ColorsListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
