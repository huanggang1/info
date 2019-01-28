@extends('admin.layouts.base')

@section('title','控制面板')

@section('pageHeader','控制面板')

@section('pageDesc','DashBoard')

@section('content')
<div class="main animsition">
    <div class="container-fluid">

        <div class="row">
            <div class="">

                @include('admin.partials.errors')
                @include('admin.partials.success')
                <form class="form-horizontal" role="form" method="POST" action="/admin/info/store">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">基本信息</h3>
                        </div>
                        @include('admin.info._form_one')
                        <div class="panel-heading">
                            <h3 class="panel-title">报考信息</h3>
                        </div>
                        @include('admin.info._form_two')
                        <div class="panel-heading">
                            <h3 class="panel-title">缴费信息</h3>
                        </div>
                        @include('admin.info._form_tree')
                         <div class="panel-heading">
                            <h3 class="panel-title">负责人信息</h3>
                        </div>
                        @include('admin.info._form_four')
                        <div class="panel-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="cove_image"/>

                            <div class="form-group">
                                <div class="col-md-7 col-md-offset-3">
                                    <button type="submit" class="btn btn-primary btn-md">
                                        <i class="fa fa-plus-circle"></i>
                                        添加
                                    </button>
                                </div>
                            </div>

                            </form>

                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@stop