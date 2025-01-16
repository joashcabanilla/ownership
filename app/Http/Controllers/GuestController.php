<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//Model
use App\Models\User;
use App\Models\MemberModel;


class GuestController extends Controller
{
    protected $data, $memberModel, $userModel;

    public function __construct()
    {
        $this->middleware('guest');
        $this->data = array();
        $this->memberModel = new MemberModel();
        $this->userModel = new User();
    }

    function Index(){
        $this->data["titlePage"] = "OWNERSHIP | Account";
        return view('Components.Registration',$this->data);
    }

    function Login(){
        $this->data["titlePage"] = "OWNERSHIP | Login";
        return view('Components.Login',$this->data);
    }

    function searchAccount(Request $request){
        return $this->memberModel->searchAccount($request->search);
    }

    function saveQrCode(Request $request){
        return $this->memberModel->saveQrCode($request);
    }

    function userLogin(Request $request){
        return $this->userModel->login($request);
    }
}
