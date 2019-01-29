<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\OperationLog as OperationLog;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Excel;
use PDO;
use Storage;

class OperationLogController extends Controller {

    protected $OperationLog = null;

    public function __construct() {
        $this->OperationLog = new OperationLog();
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
            $data = $this->OperationLog->selectAll($param, $start, $length, $columns, $order);
            return response()->json($data);
        }
        return view('admin.operationLog.index');
    } 
}
