<?php

namespace App\Classes;
use Illuminate\Support\Facades\Auth;

//Model
use App\Models\User;
use App\Models\MemberModel;
use App\Models\GiveawayModel;
use App\Models\TimedepositModel;

class ReportClass
{

    protected $userModel, $memberModel, $giveawayModel, $timedepositModel;

    function __construct()
    {
        $this->userModel = new User();
        $this->memberModel = new MemberModel();
        $this->giveawayModel = new GiveawayModel();
        $this->timedepositModel = new TimedepositModel();
    }

    function generateReport($data){
        $data = (object) $data;
        switch($data->report){
            case "staffShareCapitalGiveaway":
                return $this->shareCapitalGiveaway("sharecapital",$data);
            break;

            case "staffTimeDepositGiveaway":
                return $this->shareCapitalGiveaway("timedeposit",$data);
            break;

            case "sharecapitalsummary":
                return $this->giveawaySummary("sharecapital",$data);
            break;

            case "timedepositsummary":
                return $this->giveawaySummary("timedeposit",$data);
            break;
        }
    }

    private function shareCapitalGiveaway($category, $data, $all = false, $giveawayItems = array()){
        $giveawayList = $this->giveawayModel->where("category",$category);

        if(!empty($giveawayItems)){
            $giveawayList =  $giveawayList->whereIn("description", $giveawayItems);
        }

        if($all){
            $giveawayList = $giveawayList->get();
        }else{
            $giveawayList = $giveawayList->where("updated_by",Auth::user()->id)->get();
        }
        
        $userIdList = $memberIdList = $users = $members = $timedeposits = array();
        foreach($giveawayList as $giveaway){
            $userIdList[$giveaway->updated_by] =  $giveaway->updated_by;
            $memberIdList[$giveaway->userid] = $giveaway->userid;
        }

        $userList = $this->userModel->whereIn("id",$userIdList)->get();
        foreach($userList as $user){
            $users[$user->id] = strtoupper(strtolower($user->name)); 
        }

        $memberList = $this->memberModel->whereIn("id",$memberIdList)->get();
        foreach($memberList as $member){
            $members[$member->id] = $member;
        }

        $timeDepositList = $this->timedepositModel->whereIn("id",$memberIdList)->get();
        foreach($timeDepositList as $timedeposit){
            $timedeposits[$timedeposit->id] = $timedeposit;
        }

        $summary = $giftCheckSummary = array();
        foreach($giveawayList as $giveaway){
            $description = $giveaway->description == "giftcheck" ? $giveaway->description . " " . $giveaway->amount : $giveaway->description;

            $summary[$giveaway->category."-".$giveaway->userid."-".$giveaway->description."-".$giveaway->amount."-".$giveaway->quantity] = [
                "memid" => $giveaway->memid,
                "pbno" => $giveaway->pbno,
                "name" => strtoupper(strtolower($giveaway->name)),
                "branch" => strtoupper($giveaway->branch),
                "status" => $giveaway->status,
                "sharecapital" => !isset($members[$giveaway->userid]) ?: number_format($members[$giveaway->userid]["sharecapital"], 2, '.', ','),
                "timedeposit" => !isset($timedeposits[$giveaway->userid]) ?: number_format($timedeposits[$giveaway->userid]["timedeposit"], 2, '.', ','),
                "giveaway" => $description,
                "quantity" => $giveaway->quantity,
                "updatedBy" => $users[$giveaway->updated_by],
                "dataReceived" => date("m/d/Y h:i A", strtotime($giveaway->received_at))
            ];

            $giftCheckSummary[date("m/d/Y", strtotime($giveaway->received_at))][$description][$giveaway->category."-".$giveaway->userid."-".$giveaway->description."-".$giveaway->amount."-".$giveaway->quantity] = $giveaway->quantity;  
        }
        
        $var = (array) $data;
        $var["title"] = $category == "sharecapital" ? "Share Capital Giveaway" : "Time Deposit Giveaway";
        $var["giveawayList"] = $summary; 
        $var["giftCheckSummary"] = $giftCheckSummary; 
        return response()->make(view("Report.ShareCapitalGiveaway",$var), '200'); 
    }

    private function giveawaySummary($category,$data){
        $giveawayList = $this->giveawayModel->where("category",$category)->get();

        $userIdList = $memberIdList = $users = $members = $timedeposits = array();
        foreach($giveawayList as $giveaway){
            $userIdList[$giveaway->updated_by] =  $giveaway->updated_by;
            $memberIdList[$giveaway->userid] = $giveaway->userid;
        }

        $userList = $this->userModel->whereIn("id",$userIdList)->get();
        foreach($userList as $user){
            $users[$user->id] = strtoupper(strtolower($user->name)); 
        }

        $memberList = $this->memberModel->whereIn("id",$memberIdList)->get();
        foreach($memberList as $member){
            $members[$member->id] = $member;
        }

        $timeDepositList = $this->timedepositModel->whereIn("id",$memberIdList)->get();
        foreach($timeDepositList as $timedeposit){
            $timedeposits[$timedeposit->id] = $timedeposit;
        }

        $summary = $overall = $descList = $branch = array();
        foreach($giveawayList as $giveaway){
            $description = $giveaway->description == "giftcheck" ? $giveaway->description . " " . $giveaway->amount : $giveaway->description;

            $summary[$giveaway->category."-".$giveaway->userid."-".$giveaway->description."-".$giveaway->amount."-".$giveaway->quantity] = [
                "memid" => $giveaway->memid,
                "pbno" => $giveaway->pbno,
                "name" => strtoupper(strtolower($giveaway->name)),
                "branch" => strtoupper($giveaway->branch),
                "status" => $giveaway->status,
                "sharecapital" => !isset($members[$giveaway->userid]) ?: number_format($members[$giveaway->userid]["sharecapital"], 2, '.', ','),
                "timedeposit" => !isset($timedeposits[$giveaway->userid]) ?: number_format($timedeposits[$giveaway->userid]["timedeposit"], 2, '.', ','),
                "giveaway" => $description,
                "quantity" => $giveaway->quantity,
                "updatedBy" => $users[$giveaway->updated_by],
                "dataReceived" => date("m/d/Y", strtotime($giveaway->received_at))
            ];
            
            $overall[date("m/d/Y", strtotime($giveaway->received_at))][$giveaway->branch][$description][$giveaway->category."-".$giveaway->userid."-".$giveaway->description."-".$giveaway->amount."-".$giveaway->quantity] = $giveaway->quantity;

            $descList[$description] = $description;
            
            $branch[$giveaway->branch][date("m/d/Y", strtotime($giveaway->received_at))][$giveaway->name] = $giveaway->name;
            
            $dateList[date("m/d/Y", strtotime($giveaway->received_at))] = date("m/d/Y", strtotime($giveaway->received_at));
        }
        
        $totalMigs = $this->memberModel->where("status", "MIGS")->count();
        $totalTD = $this->timedepositModel->count();

        $var = (array) $data;
        $var["title"] = $category == "sharecapital" ? "Share Capital Giveaway" : "Time Deposit Giveaway";
        $var["giveawayList"] = $summary;
        $var["summaryList"] = $overall;
        $var["descList"] = $descList;
        $var["totalMigs"] = $totalMigs;
        $var["totalTD"] = $totalTD;
        $var["branchList"] = $branch;
        $var["dateList"] = $dateList;

        return response()->make(view("Report.GiveawaySummary",$var), '200');
    }
}
