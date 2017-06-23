<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CronLog extends Model
{
    //
    protected $table = 'cron_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['log_name', 'content', 'created_at', 'updated_at'];



}
