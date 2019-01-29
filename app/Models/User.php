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
        'name' ,'email' , 'phone' ,
    ];
    protected $rules = [
        'name' => "required|unique:admin_users,name,2,status",
        'email' => "required|unique:admin_users,email,2,status",
    ];
    protected $messages = [
        'name.unique' => '用户已存在',
        'email.unique' => '邮箱已经存在',
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
        $query->where(['status' => 1]);
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
     * 批量数据添加
     * @param type $data
     */
    public function addAll($data) {
        $dataError = [];
        foreach ($data as $k => $v) {
            $arr = array_combine($this->fields, $v);
            $schoolId = $this->schoolSelect($arr['applySchool']);
            if (!$schoolId) {
                $dataError[$k] = $arr;
                continue;
            }
            $arr['applySchool'] = $schoolId;
            $dataError[$k] = $this->getAdd($arr);
        }
        return $dataError;
    }

    public function schoolSelect($applySchool) {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $data = DB::table('school')->where(['name' => $applySchool])->first();
        if($data['id']){
            return $data['id'];
        }
        return false;
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
            return $data;
        } else {
            $data['created_at']=date('Y-m-d H:i:s');
            DB::table('admin_users')->insert($data);
        }
    }

    /**
     * 
     * @param type $data
     * @param type $id修改
     */
    public function getSave($data, $id) {
        //验证唯一性 排除本身
        $rules = [
            'name' => 'required|unique:info_users,identityNum,' . $id . ',id,status,1',
            'email' => 'required|unique:info_users,examineeNum,' . $id . ',id,status,1',
        ];
        $validator = Validator::make($data, $rules, $this->messages);
        $dataArr = $this->getFind($id);
        if ($validator->fails()) {
            return $validator->errors()->getMessages();
        }
        $this->where(['id' => $id])->update($data);
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
        return $this->where(['id' => $id])->update(['status' => 2]);
    }

}
