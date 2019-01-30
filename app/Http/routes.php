<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
//Route::get('/',function(){
//    return view('home');
//});
//
//Route::get('/home',function(){
//    return view('welcome');
//});

Route::get('admin/index', ['as' => 'admin.index', 'middleware' => ['auth','menu'], 'uses'=>'Admin\\IndexController@index']);

$this->group(['namespace' => 'Admin','prefix' => '/admin',], function () {
    Route::auth();
});

$router->group(['namespace' => 'Admin', 'middleware' => ['auth','authAdmin','menu']], function () {
    //权限管理路由
    Route::get('admin/permission/{cid}/create', ['as' => 'admin.permission.create', 'uses' => 'PermissionController@create']);
    Route::get('admin/permission/{cid?}', ['as' => 'admin.permission.index', 'uses' => 'PermissionController@index']);
    Route::post('admin/permission/index', ['as' => 'admin.permission.index', 'uses' => 'PermissionController@index']); //查询
    //会员管理
    Route::get('admin/member/index', ['as' => 'admin.member.index', 'uses' => 'MemberController@index']); //查询
    Route::get('admin/member/merage', ['as' => 'admin.member.merage', 'uses' => 'MemberController@index']); //查询

    Route::resource('admin/permission', 'PermissionController');
    Route::put('admin/permission/update', ['as' => 'admin.permission.edit', 'uses' => 'PermissionController@update']); //修改
    Route::post('admin/permission/store', ['as' => 'admin.permission.create', 'uses' => 'PermissionController@store']); //添加


    //角色管理路由
    Route::get('admin/role/index', ['as' => 'admin.role.index', 'uses' => 'RoleController@index']);
    Route::post('admin/role/index', ['as' => 'admin.role.index', 'uses' => 'RoleController@index']);
    Route::resource('admin/role', 'RoleController');
    Route::put('admin/role/update', ['as' => 'admin.role.edit', 'uses' => 'RoleController@update']); //修改
   
    Route::post('admin/role/store', ['as' => 'admin.role.create', 'uses' => 'RoleController@store']); //添加


    //用户管理路由
    Route::get('admin/user/index', ['as' => 'admin.user.manage', 'uses' => 'UserController@index']);  //用户管理
    Route::post('admin/user/index', ['as' => 'admin.user.index', 'uses' => 'UserController@index']);
    Route::get('admin/user/export', ['as' => 'admin.user.export', 'uses' => 'UserController@export']); //导出
    Route::get('admin/user/detail', ['as' => 'admin.user.detail', 'uses' => 'UserController@detail']); //查看
    Route::post('admin/user/import', ['as' => 'admin.user.import', 'uses' => 'UserController@import']); //导入
    Route::resource('admin/user', 'UserController');
    Route::put('admin/user/update', ['as' => 'admin.user.edit', 'uses' => 'UserController@update']); //修改
     Route::get('admin/user/edit', ['as' => 'admin.user.edit', 'uses' => 'UserController@edit']); //修改
    Route::get('admin/user/create', ['as' => 'admin.user.create', 'uses' => 'UserController@create']); //添加
    Route::post('admin/user/store', ['as' => 'admin.user.store', 'uses' => 'UserController@store']); //添加
    
    //信息管理路由
    Route::get('admin/info/index', ['as' => 'admin.info.index', 'uses' => 'InfoController@index']);  //用户管理
    Route::post('admin/info/index', ['as' => 'admin.info.index', 'uses' => 'InfoController@index']);
    Route::get('admin/info/export', ['as' => 'admin.info.export', 'uses' => 'InfoController@export']); //导出
    Route::get('admin/info/detail', ['as' => 'admin.info.detail', 'uses' => 'InfoController@detail']); //查看
    Route::post('admin/info/import', ['as' => 'admin.info.import', 'uses' => 'InfoController@import']); //导入
    Route::resource('admin/info', 'InfoController');
    Route::put('admin/info/update', ['as' => 'admin.info.update', 'uses' => 'InfoController@update']); //修改
    Route::get('admin/info/edit', ['as' => 'admin.info.edit', 'uses' => 'InfoController@edit']); //修改
    Route::get('admin/info/create', ['as' => 'admin.info.create', 'uses' => 'InfoController@create']); //添加
    Route::post('admin/info/store', ['as' => 'admin.info.store', 'uses' => 'InfoController@store']); //添加

     //院校管理路由
    Route::get('admin/school/index', ['as' => 'admin.school.index', 'uses' => 'SchoolController@index']);  //用户管理
    Route::post('admin/school/index', ['as' => 'admin.school.index', 'uses' => 'SchoolController@index']);
    Route::get('admin/school/export', ['as' => 'admin.school.export', 'uses' => 'SchoolController@export']); //导出
    Route::post('admin/school/import', ['as' => 'admin.school.import', 'uses' => 'SchoolController@import']); //导入
    Route::resource('admin/school', 'SchoolController');
    Route::put('admin/school/update', ['as' => 'admin.school.update', 'uses' => 'SchoolController@update']); //修改
    Route::put('admin/school/edit', ['as' => 'admin.school.edit', 'uses' => 'SchoolController@edit']); //修改
    Route::get('admin/school/create', ['as' => 'admin.school.create', 'uses' => 'SchoolController@create']); //添加
    Route::post('admin/school/store', ['as' => 'admin.school.store', 'uses' => 'SchoolController@store']); //添加
    Route::DELETE('admin/school/delete/{id}', ['as' => 'admin.school.delete', 'uses' => 'SchoolController@delete']); //删除
    //日志管理
    Route::get('admin/operationLog/index', ['as' => 'admin.operationLog.index', 'uses' => 'operationLogController@index']);  //日志
    Route::post('admin/operationLog/index', ['as' => 'admin.operationLog.index', 'uses' => 'operationLogController@index']);
    Route::resource('admin/operationLog', 'operationLogController');

});

Route::get('admin', function () {
    return redirect('/admin/index');
});

Route::auth();



