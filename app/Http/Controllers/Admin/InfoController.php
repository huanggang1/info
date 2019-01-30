<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Info as Info;
use App\Models\School as School;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Excel;
use PDO;
use Storage;

class InfoController extends Controller {

    protected $info = null;
    protected $school = null;

    public function __construct() {
        $this->info = new Info();
        $this->school = new School();
    }

    /**
     * 列表展示
     * @param Request $request
     * @return type
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
            $data = $this->info->selectAll($param, $start, $length, $columns, $order);
            foreach ($data['data'] as $k => $v) {
                $data['data'][$k]['applySchool'] = $this->school->getSchool($v['applySchool'])['name'];
                $data['data'][$k]['key'] = $k + 1;
            }
            return response()->json($data);
        }
        return view('admin.info.index');
    }

    /**
     * 添加列表展示
     * @return type
     */
    public function create(Request $request) {
        $data = $this->school->getSelect();
        foreach ($this->info->fields as $v) {
            $infoData[$v] = "";
        }
        return view('admin.info.create')->with('data', $data)->with('infoData', $infoData)->with("readonly", "");
    }

    /**
     * 添加入库
     * @param Request $request
     * @return type
     */
    public function store(Request $request) {
        $param = Input::all();
        $data = [];
        foreach ($this->info->fields as $k => $v) {
            $data[$v] = $param[$v];
        }
        $return = $this->info->getAdd($data);
        if (isset($return)) {
            writeLog($request, "考生号“ " . $data['examineeNum'] . " ”添加失败");
            return redirect()->back()->withErrors('身份证或考生号或学号已存在');
        }
        writeLog($request, "考生号“ " . $data['examineeNum'] . " ”添加成功");
        return redirect('/admin/info/index')->withSuccess('添加成功！');
    }

    /**
     * 编辑展示页面
     * @param type $id
     * @return type
     */
    public function edit($id) {
        $schoolData = $this->school->getSelect();
        $infoData = $this->info->getFind($id);
        return view('admin.info.edit')->with('data', $schoolData)->with('infoData', $infoData)->with("readonly", "");
        ;
    }

    /**
     * 编辑
     * @param Request $request
     * @param type $id
     * @return type
     */
    public function update(Request $request, $id) {
        $param = Input::all();
        $data = [];
        foreach ($this->info->fields as $k => $v) {
            $data[$v] = $param[$v];
        }
        $return = $this->info->getSave($data, $id);
        if (isset($return)) {
            writeLog($request, "考生号“ " . $data['examineeNum'] . " ”修改失败");
            return redirect()->back()->withErrors('身份证或考生号或学号已存在');
        }
        writeLog($request, "考生号“ " . $data['examineeNum'] . " ”修改成功");
        return redirect('/admin/info/index')->withSuccess('修改成功！');
    }

    /**
     * 查看
     * @param type $id
     * @return type
     */
    public function detail(Request $request) {
        $id = $request->get('id');
        $schoolData = $this->school->getSelect();
        $infoData = $this->info->getFind($id);
        return view('admin.info.detail')->with('data', $schoolData)->with('infoData', $infoData)->with("readonly", "disabled");
    }

    /**
     * 删除
     * @param type $id
     * @return type
     */
    public function destroy(Request $request, $id) {
        $data = $this->info->getFind($id);
        $tag = $this->info->getDelete($id);
        if ($tag) {
            writeLog($request, "考生号“ " . $data['examineeNum'] . " ”删除成功");
            return redirect()->back()->withSuccess("删除成功");
        } else {
            writeLog($request, "考生号“ " . $data['examineeNum'] . " ”删除失败");
            return redirect()->back()->withErrors("删除失败");
        }
    }

    /**
     * 导出
     * @param Request $request
     */
    public function export(Request $request) {
        $param = Input::all();
        $dataArr = $data = [];
        $data = $this->info->export($param);
        foreach ($data as $k => $v) {
            $data[$k]['sex'] = $v['sex'] == 0 ? "男" : "女";
            $data[$k]['addPoints'] = $v['addPoints'] == 0 ? "不加分" : "加分";
            $data[$k]['fullCost'] = $v['fullCost'] == 0 ? "不是" : "是";
            $data[$k]['applySchool'] = $this->school->getSchool($v['applySchool'])['name'];
        }
        $i = 0;
        $headerArr = [
            '姓名', '性别', '民族', '政治面貌', '身份证号', '工作单位', '手机号', '备用电话',
            '预留字段', '年级', '考生号', '成绩', '学号', '报名日期', '初始学校', '层次', '学习形式', '报考学校', '报考专业',
            '核对地址', '是否加分', '预留字段', '个人履历', '报名费', '收款人', '总费用', '是否全费', '预留字段',
            '第一年', '第二年', '第三年', '预留字段', '负责人', '介绍人', '备注',
        ];
        $dataArr[$i] = $headerArr;
        foreach ($data as $k => $v) {
            $i++;
            $dataArr[$i] = array_values($v);
        }
        writeLog($request, "导出信息管理操作");
        Excel::create(iconv('UTF-8', 'GBK', '信息管理'), function($excel) use ($dataArr) {
            $excel->sheet('score', function($sheet) use ($dataArr) {
                $sheet->rows($dataArr);
            });
        })->export('xls');
    }

    /**
     * 导入
     * @param Request $request
     */
    public function import(Request $request) {
        $return = [];
        if ($request->hasFile('file')) {
            // 获取后缀名
            $ext = $request->file('file')->getClientOriginalExtension();
            // 新的文件名
            $newFile = time() . mt_rand(0, 9999) . "." . $ext;
            // 上传文件操作
            $request->file('file')->move('Uploads/', $newFile);
        }

        Excel::load("Uploads/" . $newFile, function($reader) use ($newFile, &$return) {
            $data = $reader->get()->toArray();
            unset($data[0]);
            unlink("Uploads/" . $newFile);
            $return = $this->info->addAll($data);
//            echo json_encode($return);
        });
        if ($return['code'] == 1) {
            writeLog($request, $return['msg']);
            return redirect('admin/info/index')->withSuccess($return['msg']);
        } else {
            writeLog($request, $return['msg']);
            return redirect()->back()->withErrors($return['msg']);
        }
    }

}
