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

class InfoController extends Controller {

    protected $fields = [
        'name', 'sex', 'nation', 'politicalStatus', 'identityNum', 'nativePlace', 'workUnit',
        'poorCounty', 'phone', 'remarksPhone', 'qqNumber', 'weChat', 'reservedFields', 'examineeNum',
        'achievement', 'applyTime', 'initialSchool', 'level', 'studyForm', 'applySchool',
        'applyProfession', 'checkAddress', 'unit', 'addPoints', 'province', 'crossProfession',
        'enterFIeld', 'enrollFee', 'payee', 'totalCost', 'costFieldsOne', 'yearOne',
        'yearTwo', 'yearTree', 'costFieldsTwo', 'person', 'introducer', 'remarks'
    ];

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
            //查询条件
//            $data['recordsTotal'] =  DB::table()::count();
//            if (strlen($search['value']) > 0) {
            $data['recordsFiltered'] = DB::table('info_users')->where(function ($query) use ($param) {
                        if ($param['btName']) {
                            $query->where('name', 'like', '%' . $param['btName'] . '%');
                        }
                        if ($param['btNumber']) {
                            $query->where('examineeNum', 'like', '%' . $param['btNumber'] . '%');
                        }
                        if ($param['btPhone']) {
                            $query->where('identityNum', 'like', '%' . $param['btPhone'] . '%');
                        }
                        if ($param['btSchool']) {
                            $query->where('applySchool', 'like', '%' . $param['btSchool'] . '%');
                        }
                    })->count();

            $data['data'] = DB::table('info_users')->where(function ($query) use ($param) {
                        if ($param['btName']) {
                            $query->where('name', 'like', '%' . $param['btName'] . '%');
                        }
                        if ($param['btNumber']) {
                            $query->where('examineeNum', 'like', '%' . $param['btNumber'] . '%');
                        }
                        if ($param['btPhone']) {
                            $query->where('identityNum', 'like', '%' . $param['btPhone'] . '%');
                        }
                        if ($param['btSchool']) {
                            $query->where('applySchool', 'like', '%' . $param['btSchool'] . '%');
                        }
                    })
                    ->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
//            } else {
//                $data['recordsFiltered'] = User::count();
//                $data['data'] = User::
//                        skip($start)->take($length)
//                        ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
//                        ->get();
//            }
            return response()->json($data);
        }
        return view('admin.info.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
//        echo 123;die;
        $data = [];
//        foreach ($this->fields as $field => $default) {
//            $data[$field] = old($field, $default);
//        }
//        $data['rolesAll'] = Role::all()->toArray();
        return view('admin.info.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
//        echo 123;die;
//        $user = new User();
//        foreach (array_keys($this->fields) as $field) {
//            $user->$field = $request->get($field);
//        }
//        if ($request->get('password') != '' && $request->get('repassword') != '' && $request->get('password') == $request->get('repassword')) {
//            $user->password = bcrypt($request->get('password'));
//        } else {
//            return redirect()->back()->withErrors('密码或确认密码不能为空！');
//        }
//        unset($user->roles);
//        $user->save();
//        if (is_array($request->get('roles'))) {
//            $user->giveRoleTo($request->get('roles'));
//        }
        return redirect('/admin/info'); //->withSuccess('添加成功！');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
//        echo 123;die;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
//        echo "<pre>";
        $user = User::find((int) $id);

//        print_r($user);
//        if (!$user)
//            return redirect('/admin/user')->withErrors("找不到该用户!");
//       print_r($user->roles);die;
        $roles = [];
        if ($user->roles) {
            foreach ($user->roles as $v) {
                $roles[] = $v->id;
            }
        }
        $user->roles = $roles;
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $user->$field);
        }
        $data['rolesAll'] = Role::all()->toArray();
        $data['id'] = (int) $id;
//        dd($data);
        return view('admin.user.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $user = User::find((int) $id);
        foreach (array_keys($this->fields) as $field) {
            $user->$field = $request->get($field);
        }
        if ($request->get('password') != '' || $request->get('repassword') != '') {
            if ($request->get('password') != '' && $request->get('repassword') != '' && $request->get('password') == $request->get('repassword')) {
                $user->password = bcrypt($request->get('password'));
            } else {
                return redirect()->back()->withErrors('修改密码时,密码或确认密码不能为空！');
            }
        }

        unset($user->roles);

        $user->giveRoleTo($request->get('roles', []));

        return redirect('/admin/user')->withSuccess('添加成功！');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $tag = User::find((int) $id);
        if ($tag && $tag->id != 1) {
            $tag->delete();
        } else {
            return redirect()->back()
                            ->withErrors("删除失败");
        }

        return redirect()->back()
                        ->withSuccess("删除成功");
    }

    /**
     * 导出
     * @param Request $request
     */
    public function export(Request $request) {
        $param = Input::all();
        $dataArr = $data = [];
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $data = DB::table('info_users')->where(function ($query) use ($param) {
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
                })->get($this->fields);

        foreach ($data as $k => $v) {
            $data[$k]['sex'] = $v['sex'] == 0 ? "男" : "女";
            $data[$k]['addPoints'] = $v['addPoints'] == 0 ? "不加分" : "加分";
            $data[$k]['province'] = $v['province'] == 0 ? "是" : "不是";
            $data[$k]['crossProfession'] = $v['crossProfession'] == 0 ? "是" : "不是";
        }
        $i = 0;
        $headerArr = [
            '姓名', '性别', '民族', '政治面貌', '身份证号', '籍贯', '工作单位', '贫困县', '手机号', '备用电话', 'QQ',
            '微信', '预留字段', '考生号', '成绩', '报名日期', '初始学校', '层次', '学习形式', '报考学校', '报考专业',
            '核对地址', '所在单位', '是否加分', '省外', '是否跨专业', '预留字段', '报名费', '收款人', '总费用', '预留字段',
            '第一年', '第二年', '第三年', '预留字段', '负责人', '介绍人', '备注',
        ];
        $dataArr[$i] = $headerArr;
        foreach ($data as $k => $v) {
            $i++;
            $dataArr[$i] = array_values($v);
        }
//        print_r($dataArr);die;
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
    public function import() {
        $filePath = './public/信息管理.xls';
        Excel::load($filePath, function($reader) {
            $data = $reader->get()->toArray();
            unset($data[0]);
//           dd($data);
            $this->addAll($data);
            
        });
    }
    private function addAll($data) {
        foreach($data as $k=>$v){
            $data[$k]=array_combine($this->fields, $v);
        }
        dd($data);
    }

}
