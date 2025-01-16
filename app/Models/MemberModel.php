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
        if($member->birthdate == $data->birthdate){
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
}