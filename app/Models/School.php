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
        'name' => 'required|unique:school,name,2,status'
    ];
    protected $messages = [
        'name.required' => '学校名称必填项!',
        'name.unique' => '学校名称已存在!',
        'identityNum.unique' => '学校名称已存在',
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
                ->get(array('id', 'name'));
        foreach ($data['data'] as $key => $val) {
            $data['data'][$key]['key'] = $key + 1;
        }
        return $data;
    }

    /**
     * 查询条件处理
     * @param type $param
     * @param type $query
     */
    private function getWhere($param, $query) {
        $query->where('status','=',1);
        if (isset($param['schoolName']) && $param['schoolName']!='') {
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
                })->orderBy('id','desc')->get($this->fields);
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

    public function getSchool($id) {
//        $info = DB::table('school')->where('id','=',$id)->get();
        $info = School::find($id);
        return $info['attributes'];
    }

    /**
     * 查询列表
     */
    public function getSelect() {
        return $this->where(['status' => 1])->get(['id', 'name']);
    }

    /*
     * 修改学校名称
     */

    public function updateInfo($data) {
        $rules = ['name' => 'required|unique:school,name,'.$data['id'].',id,status,1'];
        $validator = $this->validator($data,$rules);
        if(!empty($validator)) {
            return ['code' => 0, 'errmsg' => $validator['errmsg']];
        } else {
            $result = $this->updateData($data['id'], ['name'=>$data['name']]);
            if( $result ){
                return ['code' => 1, 'msg' => '编辑成功!'];
            }else{
                return ['code' => 0, 'errmsg' => '编辑失败或当前学校名称无修改!'];
            }
        }
    }
    /*
     * 编辑数据
     */
    public function updateData($id,$data){
        return DB::table('school')->where(function ($query) use ($id) {
                    $query->where('id','=',$id);
                })->update($data);
    }
    /*
     * 验证
     */
    public function validator($data,$rules=null) {
        $error = [];
        if( is_null($rules) ){
            $validator = Validator::make($data, $this->rules, $this->messages);
        }else{
            $validator = Validator::make($data, $rules, $this->messages);
        }
        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();
            $error['errmsg'] = $errors['name'][0];
        }
        return $error;
    }
    /*
     * 创建院校
     */
    public function creatInfo( $data ){
        $validator = $this->validator($data);
        if(!empty($validator)) {
            return ['code' => 0, 'errmsg' => $validator['errmsg']];
        } else {
            $info=['name'=>$data['name'],'created_at'=>date('Y-m-d H:i:s')];
            $result = $this->getAdd($info);
            if( $result ){
                return ['code' => 1, 'msg' => '添加成功!'];
            }else{
                return ['code' => 0, 'errmsg' => '添加失败!'];
            }
        }
    }
    /*
     * 删除数据
     * 修改 status 为 2
     */
    public function deleteInfo($id){
        $result = $this->updateData($id, ['status'=>2]);
        if( $result ){
            return ['code' => 1, 'msg' => '删除成功!'];
        }else{
            return ['code' => 0, 'errmsg' => '删除失败!'];
        }
    }
    /*
     * 批量添加导入数据
     */
    public function creatBatch( $data ){
        $result = ['code'=>1];
        $errStr='';
        $dataInfo=[];
        foreach($data as $k=>$v){
            //是否符合验证规则
            $arr = array_combine($this->fields, $v);
            $validator = $this->validator($arr);
            if( !empty($validator) ){
                $errStr.=$arr['name'].',';
            }else{
                $arr['created_at']= date('Y-m-d H:i:s');
                $dataInfo[]=$arr;
            }
        }
        $add = DB::table('school')->insert($dataInfo);
        if( $errStr!='' ){
            $result=['code'=>0,'errmsg'=>'导入失败 '.rtrim($errStr,',').',学校名称已存在!'];
        }elseif(!$add){
            $result=['code'=>0,'errmsg'=>'导入失败,操作数据库错误!'];
        }else{
            $result['msg']='导入成功!';
        }
        return $result;
    }
}
