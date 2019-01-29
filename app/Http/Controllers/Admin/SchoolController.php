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
}
