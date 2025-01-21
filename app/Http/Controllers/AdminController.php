<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

//Classes
use App\Classes\DataTableClass;
use App\Classes\ReportClass;

//Model
use App\Models\User;
use App\Models\MemberModel;
use App\Models\ScheduleModel;

class AdminController extends Controller
{
    protected $data, $datatable, $userModel, $memberModel, $scheduleModel, $reportClass;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data = array();
        $this->userModel = new User();
        $this->datatable = new DataTableClass();
        $this->memberModel = new MemberModel();
        $this->scheduleModel = new ScheduleModel();
        $this->reportClass = new ReportClass();
    }

    function Users(){
        $this->data["titlePage"] = "OWNERSHIP | Users";
        $this->data["tab"] = "users"; 
        return view('Components.Users',$this->data);
    }

    function Maintenance(){
        $this->data["titlePage"] = "OWNERSHIP | Maintenance";
        $this->data["tab"] = "maintenance";

        $tableArray = $this->datatable->getAllDatabaseTable();
        $tableList = array();
        foreach($tableArray as $table){
            foreach($table as $tablename){
                $tableList[] = trim($tablename);
            }
        }
        $this->data["tables"] = $tableList;

        $this->data['reportList'] = [
        ];

        $userList = $this->userModel->getUser();
        foreach($userList as $user){
            $this->data['userList'][$user->id] = $user->name;
        }
        
        return view('Components.Maintenance',$this->data);
    }

    function Dashboard(){
        $this->data["titlePage"] = "OWNERSHIP | Dashboard";
        $this->data["tab"] = "dashboard"; 
        $this->data["scheduleList"] = $this->scheduleModel->scheduleList();
        $this->data["data"] = $this->memberModel->getDashboardData($this->data["scheduleList"]);
        return view('Components.Dashboard',$this->data);
    }

    function Logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response('logout',200); 
    }

    function UserTable(Request $request){
        return $this->datatable->userTable($request->all());
    }

    function createUpdateUser(Request $request){
        return $this->userModel->createUpdateUser($request->all());
    }

    function getUser(Request $request){
        return $this->userModel->getUser($request->id);
    }

    function deactivateUser(Request $request){
        if(!empty($request->status)){
            return $this->userModel->deactivateUser($request->id, $request->status);
        }else{
            return $this->userModel->deactivateUser($request->id);
        }
    }

    function batchInsertData(Request $request){
        $table = $request->table;
        $data = $request->insert;
        $result = array();
    
        if(!empty($data)){
            foreach($data as $rowData){
                foreach($rowData as $key => $row){
                    $dbData[trim($key)] = !empty($row) ? trim($row) : NULL;
                }
                $insertData[] = $dbData;
            }
            $dbInsert = DB::table(trim($table))->insert($insertData);
            if($dbInsert){
                $result["status"] = "success";
            }else{
                $result["status"] = "failed";
                $result["error"] = $insertData;
            }
        }else{
            $result["status"] = "failed";
            $result["error"] = $data;
        }

        return $result;
    }
    
    function createUpdateMember(Request $request){
        return $this->memberModel->createUpdateMember($request->all());
    }

    function generateReport(Request $request){
        return $this->reportClass->generateReport($request->all());
    }

    function RegisterQrcode(Request $request){
        $this->data["titlePage"] = "OWNERSHIP | Register";
        $this->data["member"] = $this->memberModel->getQrCodeRegistration($request->qrcode); 
        return view('Components.QrCodeForm',$this->data);
    }

    function registerMember(Request $request){
        return $this->memberModel->registerMember($request->memberId);
    }

    function getDashboardData(){
        $this->data["scheduleList"] = $this->scheduleModel->scheduleList();
        $this->data["data"] = $this->memberModel->getDashboardData($this->data["scheduleList"]);
        return $this->data;
    }

    function Members(){
        $this->data["titlePage"] = "OWNERSHIP | Members";
        $this->data["tab"] = "members"; 
        $branchList = $this->memberModel->branchList();
        ksort($branchList);
        $this->data["branchList"] = $branchList;
        return view('Components.Members',$this->data);
    }

    function memberTable(Request $request){
        return $this->datatable->memberTable($request->all());
    }
}
