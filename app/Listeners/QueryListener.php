<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class QueryListener
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
     * @param  \Illuminate\Database\Events\QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        if (env('SQL_DEBUG',false) ) {
            try{
                if($event->sql){
                    $sql = str_replace("?", "'%s'", $event->sql);
                    $log = vsprintf($sql, $event->bindings);
                    Log::channel('sql')->info("[{$event->time}] {$log}");
                }
            }catch (\Throwable $e){

            }

        }
    }
}
