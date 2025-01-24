<?php

namespace App\Classes;
use Illuminate\Support\Facades\Auth;

//Model
use App\Models\User;
use App\Models\MemberModel;
use App\Models\scheduleModel;

class ReportClass
{
    protected $userModel, $memberModel, $scheduleModel;

    function __construct()
    {
        $this->userModel = new User();
        $this->memberModel = new MemberModel();
        $this->scheduleModel = new scheduleModel();
    }

    function generateReport($data){
        $data = (object) $data;
        switch($data->report){
            case "registeredMembersList":
                $this->registeredMembersList();
            break;
            case "staffRegisteredMembersList":
                $this->registeredMembersList(Auth::user()->id);
            break;
        }
    }

    private function registeredMembersList($staffId = ""){
        $data = $registeredList = $userList = $summaryList = $listPerDay = array();
        
        $users = $this->userModel->getUser();
        foreach($users as $user){
            $userList[$user->id] = strtoupper($user->name);
        }

        $members = !empty($staffId) ? $this->memberModel->where("updated_by",$staffId)->get() : $this->memberModel->whereNotNull("updated_by")->get();

        foreach($members as $member){
            $firstname = !empty($member->firstname) ? $member->firstname : "";
            $middlename = !empty($member->middlename) ? $member->middlename : "";
            $lastname = !empty($member->lastname) ? $member->lastname : ""; 
            $registeredList[] = [
                "memid" => $member->memid,
                "pbno" => $member->pbno,
                "name" => $firstname." ".$middlename." ".$lastname,
                "branch" => $member->branch,
                "updated_by" => $userList[$member->updated_by],
                "received_at" => date("m/d/Y h:i A", strtotime($member->received_at))
            ];

            $date = date("Y-m-d",strtotime($member->received_at));
            $time = date("A",strtotime($member->received_at));
            $summaryList[$member->branch][$date][$time][] = $member->id;
            $listPerDay[$date][$time][] = $member->id;
        }

        $branchList = $this->memberModel->branchList();
        $scheduleList = $this->scheduleModel->scheduleList();
        ksort($branchList);
        foreach($branchList as $branch){
            foreach($scheduleList as $date => $timeData){
                foreach($timeData as $time => $day){
                    $time = date("A",strtotime($time));

                    $data["totalPerDay"][$day][$time] = isset($listPerDay[$date][$time]) ? count($listPerDay[$date][$time]): 0;
                    
                    $data["summaryList"][$branch][$day][$time] = isset($summaryList[$branch][$date][$time]) ? count($summaryList[$branch][$date][$time]) : 0;
                }
            }
        }

        $data["scheduleList"] = $scheduleList;
        $data["title"] = "Registered Members List";
        $data["registeredList"] = $registeredList;
        return response()->make(view("Report.RegisteredMemberList",$data), '200');
    }
}
