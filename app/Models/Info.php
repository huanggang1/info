<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use PDO;
use Validator;

class Info extends Model {

    protected $table = 'info_users';
    protected $fields = [
        'name', 'sex', 'nation', 'politicalStatus', 'identityNum', 'nativePlace', 'workUnit',
        'poorCounty', 'phone', 'remarksPhone', 'qqNumber', 'weChat', 'reservedFields', 'examineeNum',
        'achievement', 'applyTime', 'initialSchool', 'level', 'studyForm', 'applySchool',
        'applyProfession', 'checkAddress', 'unit', 'addPoints', 'province', 'crossProfession',
        'enterFIeld', 'enrollFee', 'payee', 'totalCost', 'costFieldsOne', 'yearOne',
        'yearTwo', 'yearTree', 'costFieldsTwo', 'person', 'introducer', 'remarks'
    ];
    protected $rules = [
        'identityNum' => 'required|unique:info_users',
        'examineeNum' => 'required|unique:info_users',
    ];
    protected $messages = [
        'identityNum.unique' => '身份证号已存在',
        'examineeNum.unique' => '考生号已经存在',
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
        if (isset($param['btName'])) {
            $query->where('name', 'like', '%' . $param['btName'] . '%');
        }
        if (isset($param['btNumber'])) {
            $query->where('examineeNum', 'like', '%' . $param['btNumber'] . '%');
        }
        if (isset($param['btPhone'])) {
            $query->where('identityNum', 'like', '%' . $param['btPhone'] . '%');
        }
        if (isset($param['btSchool'])) {
            $query->where('applySchool', 'like', '%' . $param['btSchool'] . '%');
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
        $i = $j = 0;
        foreach ($data as $k => $v) {
            $arr = array_combine($this->fields, $v);
            //验证唯一性
            $validator = Validator::make($arr, $this->rules, $this->messages);
            if ($validator->fails()) {
                $dataError[$i] = $v;
                $i++;
            } else {
                if ($this->getAdd($arr)) {
                    $j++;
                }
            }
        }
        return $dataError['msg'] = $j;
    }

    /**
     * 入库
     * @param type $data
     * @return type
     */
    public function getAdd($data) {
        return DB::table('info_users')->insertGetId($data);
    }

}
