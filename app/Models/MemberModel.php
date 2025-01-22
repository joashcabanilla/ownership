<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MemberModel extends Model
{
    use HasFactory;
    protected $table = 'members';
    protected $primaryKey = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'memid',
        'pbno',
        'firstname',
        'middlename',
        'lastname',
        'birthdate',
        'branch',
        'qrcode',
        'updated_by',
        'received_at'
    ];

    function searchAccount($search){
        $result["status"] = "success";
        $member = $this->where("memid",$search)->orWhere("pbno",$search)->get();
        if(count($member) > 0){
            $result["member"] = $member;
        }else{
            $result["status"] = "failed";
            $result["message"] = "No record was found.";
        }
        return $result;
    }

    function saveQrcode($data){
        $result["status"] = "success";
        $member = $this->find($data->memberId);
        $birthdate = date("Y-m-d", strtotime($data->birthdate));
        if($member->birthdate == $birthdate){
            $qrcode = Crypt::encrypt($member->id);
            $member->update(["qrcode" => $qrcode]);
            $qrCodeOptions = file_get_contents(public_path('data/options.json'));
            $qrCodedata = json_decode($qrCodeOptions, true);
            $qrCodedata["data"] = "https://ownership.novadeci.com/admin/register?qrcode=".$qrcode;
            $result["qrcode"] = [
                "data" => $qrCodedata,
                "name" => !empty($member->memid) ? $member->memid : $member->pbno,
            ];
        }else{
            $result["status"] = "failed";
            $result["message"] = "Incorrect birthdate entered.";
        }
        return $result;
    }

    function getQrCodeRegistration($qrcode){
        $result["status"] = "success";
        $memberId =  Crypt::decrypt($qrcode);
        $member = $this->find($memberId);
        $name = $member->firstname . " " . $member->middlename . " " . $member->lastname;

        if(!empty($member->updated_by)){
            $result["status"] = "failed";
            $result["message"] = $name . " is already registered for the ownership forum.";
        }else{
            $result["memberId"] = $member->id;
            $result["memid"] = $member->memid;
            $result["pbno"] = $member->pbno;
            $result["name"] = $name;
            $result["branch"] = $member->branch;
        }
        return $result;
    }

    function registerMember($id){
        $result["status"] = "success";
        $member = $this->find($id);
        $member->update([
            "updated_by" => Auth::user()->id,
            "received_at" => date("Y-m-d H:i:s")
        ]);
        return $result;
    }

    function getDashboardData($scheduleList){
        $result = $memberList = $listPerDay = array();
        $totalRegistered = $totalMembers = 0;

        $members = $this->get();
        foreach($members as $member){
            $totalMembers++;
            if(!empty($member->updated_by)){
                $date = date("Y-m-d",strtotime($member->received_at));
                $time = date("A",strtotime($member->received_at));
                $memberList[$member->branch][$date][$time][] = $member->id;
                $listPerDay[$date][$time][] = $member->id;
                $totalRegistered++;
            }
        }
                
        $branchList = $this->branchList();
        ksort($branchList);
        $result["branchList"] = $branchList;

        foreach($branchList as $branch){
            foreach($scheduleList as $date => $timeData){
                foreach($timeData as $time => $day){
                    $time = date("A",strtotime($time));
                    
                    $result["totalPerDay"][$day][$time] = isset($listPerDay[$date][$time]) ? count($listPerDay[$date][$time]): 0;

                    $result["totalPerBranch"][$branch][$day][$time] = isset($memberList[$branch][$date][$time]) ? count($memberList[$branch][$date][$time]) : 0;
                } 
            }
        }

        $result["totalMembers"] = number_format($totalMembers,0,".",",");
        $result["totalRegistered"] = number_format($totalRegistered,0,".",",");
        return $result;
    }

    function branchList(){
        $result = array();
        $branchList = $this->select("branch")->distinct()->get();
        if(!empty($branchList)){
            foreach($branchList as $branch){
                $result[$branch->branch] = $branch->branch;
            }
        }
        return $result;
    }

    function memberTable($data){
        $query = $this->select(
            "id",
            "memid",
            "pbno",
            "branch",
            DB::raw("UPPER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(middlename, ''), ' ', COALESCE(lastname, ''))) as name"),
            "updated_by",
            "received_at"
        );

        if(!empty($data->filterSearch)){
            $search = $data->filterSearch;
            $query->where(function($q) use($search){
                $q->orWhereRaw("UPPER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(middlename, ''), ' ', COALESCE(lastname, ''))) LIKE '%".strtoupper($search)."%'");
                $q->orWhereRaw("memid LIKE '%".$search."%'");
                $q->orWhereRaw("pbno LIKE '%".$search."%'");
            });
        }

        $query = !empty($data->filterBranch) ? $query->where("branch", $data->filterBranch) : $query;

        if(!empty($data->filterStatus)){
            if($data->filterStatus == "registered"){
                $query = $query->whereNotNull("updated_by",); 
            }else{
                $query = $query->whereNull("updated_by");
            }
        }

        $query = $query->orderBy("id", "ASC");

        return $query;
    }

    function createUpdateMember($data){
       return $this->updateOrCreate([
            "id" => isset($data["id"]) ? $data["id"] : 0
        ],$data);
    }

    function getMember($id = ""){
        if(!empty($id)){
            $query = $this->find($id);
        }else{
            $query = $this->get();
        }
        return $query;
    }
}