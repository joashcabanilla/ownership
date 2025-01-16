<?php

namespace App\Classes;
use Illuminate\Support\Facades\Auth;

//Model
use App\Models\User;
use App\Models\MemberModel;

class ReportClass
{

    protected $userModel, $memberModel, $giveawayModel, $timedepositModel;

    function __construct()
    {
        $this->userModel = new User();
        $this->memberModel = new MemberModel();
    }

    function generateReport($data){
        $data = (object) $data;
        switch($data->report){
        }
    }
}
