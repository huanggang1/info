<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use PDO;
use Validator;
use Auth;

class Info extends Model {

    public $timestamps = true;
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
        'identityNum' => "required|unique:info_users,identityNum,2,status",
        'examineeNum' => "required|unique:info_users,examineeNum,2,status",
        'studentNum' => "required|unique:info_users,studentNum,2,status",
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
        if (Auth::guard('admin')->user()->id != 1) {
            $query->where(['uid' => Auth::guard('admin')->user()->id]);
        }
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
        foreach ($data as $k => $v) {
            if (count($this->fields) != count($v)) {
                return ['code' => 0, 'msg' => "导入信息模板错误"];
                continue;
            }
            $arr = array_combine($this->fields, $v);
            $schoolId = $this->schoolSelect($arr['applySchool']);
            if (!$schoolId) {
                $dataError[$k] = $arr['examineeNum'];
                continue;
            }
            $arr['applySchool'] = $schoolId;
            $arr['uid'] = Auth::guard('admin')->user()->id;
            $dataError[$k] = $this->getAdd($arr);
        }
        if (count($dataError) > 0) {
            return ['code' => 0, 'msg' => "导入信息考生号“ " . implode(',', $dataError) . " ”失败"];
        }
        return ['code' => 1, 'msg' => "导入成功"];
    }

    public function schoolSelect($applySchool) {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $data = DB::table('school')->where(['name' => $applySchool])->first();
        if ($data['id']) {
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
            return $data['examineeNum'];
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['uid'] = Auth::guard('admin')->user()->id;
            DB::table('info_users')->insert($data);
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
            'identityNum' => 'required|unique:info_users,identityNum,' . $id . ',id,status,1',
            'examineeNum' => 'required|unique:info_users,examineeNum,' . $id . ',id,status,1',
            'studentNum' => 'required|unique:info_users,studentNum,' . $id . ',id,status,1',
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
        return Info::find($id);
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
