<?php

namespace App\Console\Commands;

use App\CronLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InsertCronLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronlog:insert';

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

        for($i = 0; $i < 10; $i++){
            $time = Carbon::now();

            $cronLog = new CronLog();

            $cronLog->Log_name = $i . 'log test ' . rand();

            $cronLog->content = '假设你有一个 Eloquent 模型的实例，则可以通过相对应的属性来访问模型的字段值。
        例如，让我们遍历查找所返回的每个 Flight 实例，并且输出 name 字段的值' . $time;

            $cronLog->save();
        }

    }
}
