<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use PDO;
class OperationLog extends Model {

    public $timestamps = true;

    protected $table = "operation_log";

    protected $primaryKey = "id";
    
    /**
     * 查询访问模块名称
     * @param type $name
     */
    public function moudleName($name){
        return DB::table('permissions')->where(['name'=>$name])->first(['display_name'])->display_name;
    }
    

}
