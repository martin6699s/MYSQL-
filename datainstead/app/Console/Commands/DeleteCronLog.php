<?php

namespace App\Console\Commands;

use App\CronLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteCronLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronlog:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $randomId = CronLog::where('updated_at','<=',Carbon::now())->max('id');

        if($randomId > 10){
            $success = CronLog::find(rand((($randomId%10) +  rand(0,$randomId/10))  ,$randomId))->delete();
            if(!$success){
                // 容错
                CronLog::find(rand(($randomId/10),$randomId))->delete();
            }
        } else{
            CronLog::find($randomId)->delete();
        }



    }
}
