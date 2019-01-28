<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use PDO;
use Validator;

class Info extends Model {

    protected $table = 'info_users';
    public $fields = [
        'name', 'sex', 'nation', 'politicalStatus', 'identityNum', 'workUnit',
        'phone', 'remarksPhone', 'reservedFields', 'grade', 'examineeNum',
        'achievement', 'studentNum', 'applyTime', 'initialSchool', 'level', 'studyForm',
        'applySchool', 'applyProfession', 'checkAddress', 'addPoints', 'enterFIeld',
        'personalResume', 'enrollFee', 'payee', 'totalCost', 'fullCost', 'costFieldsOne',
        'yearOne', 'yearTwo', 'yearTree', 'costFieldsTwo', 'person', 'introducer', 'remarks'
    ];
    protected $rules = [
        'identityNum' => 'required|unique:info_users,identityNum,status,1',
        'examineeNum' => 'required|unique:info_users,examineeNum,status,1',
        'studentNum' => 'required|unique:info_users,studentNum,status,1',
    ];
    protected $messages = [
        'identityNum.unique' => '身份证号已存在',
        'examineeNum.unique' => '考生号已经存在',
        'studentNum.unique' => '学号已经存在',
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
        if (isset($param['btSchool']) && !empty($param['btSchool'])) {
            $query->where('applySchool', 'like', '%' . $param['btSchool'] . '%');
        }
        if (isset($param['checkAddress']) && !empty($param['checkAddress'])) {
            $query->where('checkAddress', 'like', '%' . $param['checkAddress'] . '%');
        }
        if (isset($param['btGrade']) && !empty($param['btGrade'])) {
            $query->where('grade', 'like', '%' . $param['btGrade'] . '%');
        }
        if (isset($param['btGrade']) && $param['btnFullCost'] != "-1") {
            $query->where(['fullCost' => $param['btnFullCost']]);
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
        $data = DB::table('info_users')->where(function ($query) use ($param) {
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
        $i = 0;
        foreach ($data as $k => $v) {
            $arr = array_combine($this->fields, $v);
            $dataError[$i] = $this->getAdd($arr, $i);
            $i++;
        }
        return $dataError;
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
            DB::table('info_users')->insertGetId($data);
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
            'identityNum' => 'required|unique:info_users,identityNum,' . $id . ',status,0',
            'examineeNum' => 'required|unique:info_users,examineeNum,' . $id . ',status,0',
            'studentNum' => 'required|unique:info_users,studentNum,' . $id . ',status,0',
        ];
        $validator = Validator::make($data, $rules, $this->messages);
        $dataArr = $this->getFind($id);
        if ($validator->fails()) {
            return true;
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
        return $this->where(['id' => $id])->update(['status' => 0]);
    }

}
