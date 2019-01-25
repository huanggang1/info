<?php

namespace App\Models;
use Zizaco\Entrust\EntrustPermission;
use Illuminate\Database\Eloquent\Model;
use DB;
use PDO;
use Validator;

class School extends Model {

    protected $table = 'school';
    protected $fields = [
        'name'
    ];
    protected $rules = [
        'name' => 'required|unique:school',
    ];
    protected $messages = [
        'identityNum.unique' => '学校以存在',
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
                ->get(array('id','name'));
        foreach ($data['data'] as $key=>$val){
            $data['data'][$key]['key'] = $key+1;
        }
        return $data;
    }

    /**
     * 查询条件处理
     * @param type $param
     * @param type $query
     */
    private function getWhere($param, $query) {
        if (isset($param['schoolName'])) {
            $query->where('name', 'like', '%' . $param['schoolName'] . '%');
        }
        
    }

    /**
     * 导出数据查询
     * @param type $param
     * @return type
     */
    public function export($param) {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $data = DB::table('school')->where(function ($query) use ($param) {
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
        return DB::table('school')->insertGetId($data);
    }
    public function getSchool($id){
//        $info = DB::table('school')->where('id','=',$id)->get();
        $info = School::find($id);
        return $info;
    }
    /**
     * 查询列表
     */
    public function getSelect(){
       return $this->where(['status'=>1])->get(['id','name']);
    }

}
