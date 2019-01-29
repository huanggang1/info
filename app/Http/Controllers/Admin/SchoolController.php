<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\AdminUser as User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use DB;
use Excel;
use PDO;
use App\Models\School as School;

class SchoolController extends Controller {

    protected $fields = [
        'name'
    ];
    protected $School = null;

    public function __construct() {
        $this->School = new School();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
         if ($request->ajax()) {
            $data = $param = [];
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');
            $param = Input::all();
            $data = $this->School->selectAll($param, $start, $length, $columns, $order);
            return response()->json($data);
        }
        return view('admin.school.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $schoolInfo = $this->School->getSchool($id);
        return view('admin.school.edit')->with('data',$schoolInfo);
    }
    /*
     * 修改提交
     */
    public function update(Request $request){
        $param['name'] = $request->get('name');
        $param['id'] = $request->get('id');
        $result = $this->School->updateInfo($param);
        if( $result['code']==1 ){
            writeLog($request,$result['msg']);
            return redirect('admin/school/index')->withSuccess($result['msg']);
        }else{
            writeLog($request,$result['errmsg']);
            return redirect()->back()->withErrors($result['errmsg']);
        }
    }
    /*
     * 创建院校
     */
    public function create(){
        return view('admin.school.creat');
    }
    public function store(Request $request){
        $param['name'] = $request->get('name');
        $result = $this->School->creatInfo($param);
        if( $result['code']==1 ){
            writeLog($request,$result['msg']);
            return redirect('admin/school/index')->withSuccess($result['msg']);
        }else{
            writeLog($request,$result['errmsg']);
            return redirect()->back()->withErrors($result['errmsg']);
        }
    }
    /*
     * 删除
     */
    public function delete(Request $request,$id){
        $result = $this->School->deleteInfo($id);
        if( $result['code']==1 ){
            writeLog($request,$result['msg']);
            return redirect('admin/school/index')->withSuccess($result['msg']);
        }else{
            writeLog($request,$result['errmsg']);
            return redirect()->back()->withErrors($result['errmsg']);
        }
    }
    /*
     * 导入院校
     */
    public function import(Request $request){
        $result=[];
        if ($request->hasFile('file')) {
            // 获取后缀名
            $ext = $request->file('file')->getClientOriginalExtension();
            // 新的文件名
            $newFile = time() . mt_rand(0, 9999) . "." . $ext;
            // 上传文件操作
            $request->file('file')->move('Uploads/', $newFile);
        }
        Excel::load("Uploads/" . $newFile, function($reader) use ($newFile,&$result) {
            $data = $reader->get()->toArray();
            unset($data[0]);
            unlink("Uploads/" . $newFile);
            $result = $this->School->creatBatch($data);
        });
        if( $result['code']==1 ){
            writeLog($request,$result['msg']);
            return redirect('admin/school/index')->withSuccess($result['msg']);
        }else{
            writeLog($request,$result['errmsg']);
            return redirect()->back()->withErrors($result['errmsg']);
        }
    }
    /*
     * 导出院校
     */
    public function export(Request $request){
        $param['schoolName'] = $request->get('name');
        $data = $this->School->export($param);
        
        $i = 0;$dataArr = [];
        $headerArr = ['序号','学校名称'];
        $dataArr[$i] = $headerArr;
        foreach ($data as $k => $v) {
            $i++;
            array_unshift($v,$i);
            $dataArr[$i] = array_values($v);
        }
        Excel::create(iconv('UTF-8', 'GBK', '院校管理'), function($excel) use ($dataArr,$request) {
            writeLog($request,'导出院校管理成功!');
            $excel->sheet('score', function($sheet) use ($dataArr) {
                $sheet->rows($dataArr);
            });
        })->export('xls');
    }
}
