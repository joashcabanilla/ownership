<?php

namespace App\Classes;
use Illuminate\Support\Facades\DB;

//Model
use App\Models\User;
use App\Models\MemberModel;

class DataTableClass
{
    protected $userModel, $memberModel;

    function __construct()
    {
        $this->userModel = new User();
        $this->memberModel = new MemberModel();
    }

    function getAllDatabaseTable(){
        $tables = DB::select('SHOW TABLES');
        return $tables;
    }
    
    function processTable($param){
        $final_query = $param['sql'];
        $columns = $param['columns'];
        $result['iTotalRecords'] = 0;
        $param['union'] = !empty($param['union']) ? $param['union'] : array() ;
        $counter = 0;
   
        if(isset($param['group'])&&$param['group']):
            $result["iTotalRecords"] = count($param['sql']->groupBy($param['group'])->distinct($param['group'])->get());
        elseif(isset($param['having'])&&$param['having']):
            $result["iTotalRecords"] = count($param['sql']->having($param['having'][0][0],$param['having'][0][1],$param['having'][0][2])->get());
        elseif(isset($param['distinct'])&&$param['distinct']):
            if(isset($param['union']) && $param['union']):
                if(count($param['union'])>0):
                    foreach($param['union'] as $unions):
                        $counter++;
   
                        $result["iTotalRecords"] += $unions->distinct($param['distinct'])->count();
                        if($counter!=1):
                            $final_query = $final_query->unionAll($unions);
                        endif;
                    endforeach;
                endif;
            else:
                $result["iTotalRecords"] = $param['sql']->distinct($param['distinct'])->count();
            endif;
   
        else:
            $result["iTotalRecords"] = $param['sql']->count();
        endif;
        if( $param['var']->length > 0 ){
            $final_query = $final_query->skip(intval($param['var']->start))->take(intval($param['var']->length));
        }
   
        $result["iTotalDisplayRecords"] = $result["iTotalRecords"];
   
        if(isset($param['group'])&&$param['group']):
            $tmpgroup = is_array($param['group'])?$param['group']:[$param['group']];
            $final_query = call_user_func_array([$final_query,'groupBy'],$tmpgroup);
        endif; 
        if(isset($param['having'])&&$param['having']):
            foreach ($param['having'] as $con):
                $final_query = call_user_func_array([$final_query,'having'],$con);
            endforeach;
        endif;
        if(isset($param['distinct'])&&$param['distinct']) $final_query->distinct();
   
   
        $result["aaData"] = array();
        $count = intval($param['var']->start?$param['var']->start:0);
        
        foreach ($final_query->get() as $finres){
            $count ++;
            $isAModel = is_a($finres,'Illuminate\Database\Eloquent\Model');
            $mrow = $isAModel ? $finres : (array) $finres;
   
            $tmpr = array();
            foreach ($columns as $cc=>$cval) {
                $val = $mrow[ $cval['db'] ];
   
                if(isset($cval['sortnum'])&&$cval['sortnum']){
                    $tmpr[] = $count;
                }else if ( isset( $cval['formatter'] ) ) {
                    $tmpr[] = $cval['formatter']( $val, $mrow);
                }else {
                    $tmpr[] = $val;
                }
            }
            $result["aaData"][] = $tmpr;
        }
   
        echo json_encode($result);
    }

    function userTable($data){
        $var = (object) $data;
        $query = $this->userModel->userTable($var);
        $columns = [
            ['db' => 'id', 'dt' => 0,'orderable' => false, 'sortnum'=>true],

            ['db' => 'user_type', 'dt' => 1,'formatter' => function($d){
                return strtoupper($d);
            }],

            ['db' => 'name', 'dt' => 2,'formatter' => function($d){
                return strtoupper($d);
            }],

            ['db' => 'deleted_at', 'dt' => 3,'formatter' => function($d){
                $status = !empty($d) ? "DEACTIVATED" : "ACTIVE";
                $color =   $status != "ACTIVE" ? "border border-danger text-danger" : "border border-success text-success";
                return "<p style='font-size: 0.9rem !important;' class='text-center font-weight-bold m-0 p-1 rounded-lg elevation-1 ".$color."'>".$status."</p>";
            }],

            ['db' => 'last_login', 'dt' => 4,'formatter' => function($d){
                return !empty($d) ? date("m/d/Y h:i A", strtotime($d)) : "";
            }],

            ['db' => 'last_ip', 'dt' => 5],

            ['db' => 'id', 'dt' => 6, 'formatter' => function($d, $userData){
                if(!empty($userData->deleted_at)){
                    $deactivate = "<a class='dropdown-item activateBtn' style='cursor:pointer;' data-id='".$d."'><i class='fas fa-user-check'></i> Activate</a>";
                }else{
                    $deactivate = "<a class='dropdown-item deactivateBtn' style='cursor:pointer;' data-id='".$d."'><i class='fas fa-user-lock'></i> Deactivate</a>";
                }

                return "<div class='btn-group'>
                <button type='button' class='btn btn-sm' data-toggle='dropdown'><i class='fas fa-ellipsis-h'></i>
                </button>
                <div class='dropdown-menu dropdown-menu dropdown-menu-left'>
                  <a class='dropdown-item editBtn' style='cursor:pointer;' data-id='".$d."'><i class='fas fa-edit'></i> Edit</a>
                  ".$deactivate."
                </div>
              </div>";
            }]
        ];

        $params = array(
            "var" => $var,
            "columns" => $columns,
            "sql" => $query  
        );
        
        return $this->processTable($params);
    }

    function memberTable($data){
        $var = (object) $data;
        $userList = array();
        $query = $this->memberModel->memberTable($var);
        $users = $this->userModel->get();
        foreach($users as $user){
            $userList[$user->id] = strtoupper($user->name);
        }
        $columns = [
            ['db' => 'id', 'dt' => 0,'orderable' => false, 'sortnum'=>true],
            ['db' => 'memid', 'dt' => 1],
            ['db' => 'pbno', 'dt' => 2],
            ['db' => 'name', 'dt' => 3,'formatter' => function($name){
                return strtoupper($name);
            }],
            ['db' => 'branch', 'dt' => 4,'formatter' => function($branch){
                return strtoupper($branch);
            }],
            ['db' => 'received_at', 'dt' => 5,'formatter' => function($d){
                return !empty($d) ? date("m/d/Y h:i:s A", strtotime($d)) : "";
            }],
            ['db' => 'updated_by', 'dt' => 6,'formatter' => function($d) use($userList){
                return !empty($d) ? $userList[$d]: "";
            }],
            ['db' => 'id', 'dt' => 7, 'formatter' => function($d,$drow){
                if(empty($drow["updated_by"])){    
                    return "<button type='submit' class='btn btn-sm btn-primary elevation-1 editBtn' data-id='".$d."'><i class='fas fa-edit' aria-hidden='true'></i></button>";   
                }
                
                return "<p style='font-size: 0.9rem !important;' class='text-center font-weight-bold m-0 p-1 rounded-lg elevation-1 border border-success text-success'>REGISTERED</p>";
            }],
        ];

        $params = array(
            "var" => $var,
            "columns" => $columns,
            "sql" => $query  
        );
        
        return $this->processTable($params);
    }
}
