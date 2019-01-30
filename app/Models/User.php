<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use PDO;
use Validator;

class User extends Model {

    public $timestamps = true;
    protected $table = 'admin_users';
    protected $fields = [
        'name', 'email', 'phone',
    ];
    protected $rules = [
        'email' => "required|unique:admin_users",
    ];
    protected $messages = [
        'email.unique' => '邮箱已存在',
    ];

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
                })->count();
        $data['data'] = $this->where(function ($query) use ($param) {
                    $this->getWhere($param, $query);
                })
                ->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->get();
        foreach ($data['data'] as $k => $v) {
            $data['data'][$k]['key'] = $k + 1;
        }
        return $data;
    }

    /**
     * 查询条件处理
     * @param type $param
     * @param type $query
     */
    private function getWhere($param, $query) {
        if (isset($param['btName']) && !empty($param['btName'])) {
            $query->where('name', 'like', '%' . $param['btName'] . '%');
        }
        if (isset($param['btPhone']) && !empty($param['btPhone'])) {
            $query->where('phone', 'like', '%' . $param['btPhone'] . '%');
        }
        if (isset($param['btEmail']) && !empty($param['btEmail'])) {
            $query->where('email', 'like', '%' . $param['btEmail'] . '%');
        }
//        $query->where(['status' => 1]);
    }

    /**
     * 导出数据查询
     * @param type $param
     * @return type
     */
    public function export($param) {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $data = DB::table('admin_users')->where(function ($query) use ($param) {
                    $this->getWhere($param, $query);
                })->get($this->fields);
        return $data;
    }

    /**
     * 批量用户添加
     * @param type $data
     */
    public function addAll($data) {
        $dataError = [];
        foreach ($data as $k => $v) {
            if (count($this->fields) != count($v)) {
                return ['code' => 0, 'msg' => "导入信息模板错误"];
                continue;
            }
            $arr = array_combine($this->fields, $v);
            $dataError[$k] = $this->getAdd($arr);
        }
        if (count($dataError) > 0) {
            return ['code' => 0, 'msg' => "导入用户“ " . implode(',', $dataError) . " ”失败"];
        }
        return ['code' => 1, 'msg' => "导入成功"];
    }

    /**
     * 入库
     * @param type $data
     * @return type
     */
    public function getAdd($data) {
        //验证唯一性
        $validator = Validator::make($data, $this->rules, $this->messages);
        if ($validator->fails()) {
            return $data['name'];
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['password'] = bcrypt(config('auth.pwd')); //初始密码
            DB::table('info_users')->insert($data);
        }
    }

    /**
     * 查询单条数据
     * @param type $id
     */
    public function getFind($id) {
        return $this->where(['id' => $id])->first();
    }

    /**
     * 删除操作
     * @param type $id
     * @return type
     */
    public function getDelete($id) {
        return $this->where(['id' => $id])->delete();
    }

}
