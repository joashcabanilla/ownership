<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleModel extends Model
{
    use HasFactory;
    protected $table = 'schedule';
    protected $primaryKey = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'description',
        'date',
        'time'
    ];
    
    function scheduleList(){
        $result = array();
        foreach($this->orderBy('id', 'asc')->get() as $schedule){
            $result[$schedule->date][$schedule->time] = $schedule->description;
        }
        return $result;
    }
}