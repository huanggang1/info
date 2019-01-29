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
    public function moudleName($name) {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        return DB::table('permissions')->where(['name' => $name])->first(['display_name'])['display_name'];
    }

    /**
     * 查询数据库
     * @param type $param
     * @param type $start
     * @param type $length
     * @param type $columns
     * @param type $order
     * @return type
     */
    public function selectAll($param, $start, $length, $columns, $order) {
        $data['recordsFiltered'] = $this->where(function ($query) use ($param) {
                    $this->getWhere($param, $query);
                })->join('admin_users', 'userId', '=', 'admin_users.id')->count();
                        
        $data['data'] = $this->where(function ($query) use ($param) {
                    $this->getWhere($param, $query);
                })
                ->join('admin_users', 'userId', '=', 'admin_users.id')
                ->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->get();
        return $data;
    }

    /**
     * 查询条件处理
     * @param type $param
     * @param type $query
     */
    private function getWhere($param, $query) {
        if (isset($param['startTime']) && !empty($param['startTime'])) {
            $query->whereDate('created_at', ">=", $param['startTime']);
        }
        if (isset($param['endTime']) && !empty($param['endTime'])) {
            $query->whereDate('created_at', "<=", $param['endTime']);
        }
    }
}
