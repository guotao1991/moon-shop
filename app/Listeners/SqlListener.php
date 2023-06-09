<?php

namespace App\Listeners;

use DateTime;
use Exception;
use Illuminate\Database\Events\QueryExecuted;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class SqlListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        try {
            $sessionId = session()->get("sql.log.id");
            if (empty($sessionId)) {
                $sessionId = uniqid();
                session()->put("sql.log.id", $sessionId);
                (new Logger('sql'))->pushHandler(new RotatingFileHandler(storage_path('logs/sql/sql.log')))->info("======================start=============", [$sessionId]);
            }
            $sql = str_replace("?", "'%s'", $event->sql);
            foreach ($event->bindings as $i => $binding) {
                if ($binding instanceof DateTime) {
                    $event->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                } else {
                    if (is_string($binding)) {
                        $event->bindings[$i] = "'$binding'";
                    }
                }
            }
            $log = vsprintf($sql, $event->bindings);
            $log = $log . '  [ RunTime:' . $event->time . 'ms ] ';
            (new Logger('sql'))->pushHandler(new RotatingFileHandler(storage_path('logs/sql/sql.log')))->info($log, [$sessionId]);
        } catch (Exception $exception) {
        }
    }
}
