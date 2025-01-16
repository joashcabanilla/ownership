<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

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
}