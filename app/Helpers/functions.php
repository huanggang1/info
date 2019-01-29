<?php
use Auth;
use Route;
function writeLog($request,$data) { //$request->route()[1]['as']
    $log = new \App\Models\OperationLog();
    $log->setAttribute('userId',Auth::guard('admin')->user()->id);
    $log->setAttribute('moudleName', $log->moudleName(Route::currentRouteName()));
    $log->setAttribute('ipAddress', $request->ip());
    $log->setAttribute('log_content', $data);
    $log->save();
}
