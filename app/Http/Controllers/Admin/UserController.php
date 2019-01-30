<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\AdminUser as User;
use App\Models\User as users;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Excel;
use PDO;
use Storage;

class UserController extends Controller {

    protected $fields = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'roles' => [],
    ];
    protected $user = null;

    public function __construct() {
        $this->user = new users();
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
            $data = $this->user->selectAll($param, $start, $length, $columns, $order);
            return response()->json($data);
        }
        return view('admin.user.index');
    }

    /**
     * 用户添加页面
     * @return type
     */
    public function create() {
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }
        $data['rolesAll'] = Role::all()->toArray();
        return view('admin.user.create', $data)->with("readonly", "");
        ;
    }

    /**
     * 用户添加
     * @param Request $request
     * @return type
     */
    public function store(Request $request) {
        $user = new User();
        foreach (array_keys($this->fields) as $field) {
            $user->$field = $request->get($field);
        }
        if ($request->get('password') != '' && $request->get('repassword') != '' && $request->get('password') == $request->get('repassword')) {
            $user->password = bcrypt($request->get('password'));
        } else {
            writeLog($request, "用户“ " . $user->name . " ”添加失败");
            return redirect()->back()->withErrors('密码或确认密码不能为空！');
        }
        unset($user->roles);
        $user->save();
        if (is_array($request->get('roles'))) {
            $user->giveRoleTo($request->get('roles'));
        }
        writeLog($request, "用户“ " . $user->name . " ”添加成功");
        return redirect('/admin/user')->withSuccess('添加成功！');
    }

    /**
     * 用户编辑页面
     * @param type $id
     * @return type
     */
    public function edit($id) {
        $user = User::find((int) $id);
        if (!$user)
            return redirect('/admin/user')->withErrors("找不到该用户!");
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
        return view('admin.user.edit', $data)->with("readonly", "");
        ;
    }

    /**
     * 用户编辑操作
     * @param Request $request
     * @param type $id
     * @return type
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
                writeLog($request, "用户“ " . $user->name . " ”修改失败");
                return redirect()->back()->withErrors('修改密码时,密码或确认密码不能为空！');
            }
        }
        unset($user->roles);
        $user->giveRoleTo($request->get('roles', []));
        $user->save();
        writeLog($request, "用户“ " . $user->name . " ”修改成功");
        return redirect('/admin/user')->withSuccess('修改成功！');
    }

    /**
     * 查看
     * @param type $id
     * @return type
     */
    public function detail(Request $request) {
        $id = $request->get('id');
        $user = User::find((int) $id);
        if (!$user)
            return redirect('/admin/user')->withErrors("找不到该用户!");
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
        return view('admin.user.edit', $data)->with("readonly", "disabled");
    }

    /**
     * 用户删除
     * @param Request $request
     * @param type $id
     * @return type
     */
    public function destroy(Request $request, $id) {
        $tag = User::find((int) $id);
        if ($tag && $tag->id != 1) {
            if ($this->user->getDelete($id)) {
                writeLog($request, "用户“ " . $tag->name . " ”删除成功");
                return redirect()->back()->withSuccess("删除成功");
            }
        }
        writeLog($request, "用户“ " . $tag->name . " ”删除失败");
        return redirect()->back()->withErrors("删除失败");
    }

    /**
     * 导出
     * @param Request $request
     */
    public function export(Request $request) {
        $param = Input::all();
        $dataArr = $data = [];
        $data = $this->user->export($param);
        $i = 0;
        $headerArr = [
            '姓名', '邮箱', '手机号',
        ];
        $dataArr[$i] = $headerArr;
        foreach ($data as $k => $v) {
            $i++;
            $dataArr[$i] = array_values($v);
        }
        writeLog($request, "导出用户管理操作");
        Excel::create(iconv('UTF-8', 'GBK', '用户管理'), function($excel) use ($dataArr) {
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
        writeLog($request, "导入用户管理操作");
        Excel::load("Uploads/" . $newFile, function($reader) use ($newFile, &$return) {
            $data = $reader->get()->toArray();
            unset($data[0]);
            unlink("Uploads/" . $newFile);
            $return = $this->user->addAll($data);
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
