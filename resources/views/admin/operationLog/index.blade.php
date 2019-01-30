@extends('admin.layouts.base')

@section('title','控制面板')

@section('pageHeader','控制面板')

@section('pageDesc','DashBoard')

@section('content')
<div id="acc_sc" style="display: none">
    <form  id="formSubmit" class="reasonContent2" onsubmit="return false" action="#" method="post" enctype="multipart/form-data"> 
        <input type="file" name="file" id="file" multiple class="ph08" />
        <input type="submit"  id="submit" value="导入"/>

    </form>
</div>  
<div class="row page-title-row" style="margin:5px;">
    <div class="col-md-6">
    </div>
<!--    <div class="col-md-6 text-right">
        <a href="/admin/info/create" class="btn btn-success btn-md">
            <i class="fa fa-plus-circle"></i> 添加信息
        </a>
    </div>-->
</div>
<div class="row page-title-row" style="margin:5px;">
    <div class="col-md-6">
    </div>
    <div class="col-md-6 text-right">
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            @include('admin.partials.errors')
            @include('admin.partials.success')

            <div class="box-body">
                <div style="margin-bottom:10px">
                    开始时间  : <input type="text"   onclick="WdatePicker()" class="Wdate text"  id="startTime" value="" autofocus>
                    结束时间  : <input type="text"   onclick="WdatePicker()" class="Wdate text"  id="endTime" value="" autofocus>
                    <button id="submitSearch">搜索</button>
                </div>
                <table id="tags-table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm">序号</th>
                            <th class="hidden-sm">模板名称</th>
                            <th class="hidden-sm">操作人</th>
                            <th class="hidden-md">操作内容</th>
                            <th class="hidden-md">操作时间</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<div class="modal fade" id="modal-delete" tabIndex="-1">
    <div class="modal-dialog">
        @stop
        @section('js')
        <script>
            $(function () {

                var indexColse;
                var table = $("#tags-table").DataTable({
                    "searching": false,
                    oLanguage: {
                        "sProcessing": "处理中...",
                        "sLengthMenu": "显示 _MENU_ 项结果",
                        "sZeroRecords": "没有匹配结果",
                        "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                        "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
                        "sInfoFiltered": "",
                        "sInfoPostFix": "",
                        "sUrl": "",
                        "sEmptyTable": "表中数据为空",
                        "sLoadingRecords": "载入中...",
                        "sInfoThousands": ",",
                        "oPaginate": {
                            "sFirst": "首页",
                            "sPrevious": "上页",
                            "sNext": "下页",
                            "sLast": "末页"
                        },
                        "oAria": {
                            "sSortAscending": ": 以升序排列此列",
                            "sSortDescending": ": 以降序排列此列"
                        }
                    },
                    order: [[1, "desc"]],
                    serverSide: true,
                    ajax: {
                        url: '/admin/operationLog/index',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                         data: function (d) {
                                                     d.startTime = $("#startTime").val();
                                                     d.endTime = $("#endTime").val();
                                                    
                        },
                    },
                    "columns": [
                        {"data": "key"},
                        {"data": "moudleName"},
                        {"data": "name"},
                        {"data": "log_content"},
                        {"data": "created_at"},
                    ],
                });
                $("#submitSearch").click(function () {
                    $("#tags-table").dataTable().fnDraw(false);
                });
//                table.fnClearTable(false);
                table.on('preXhr.dt', function () {
                    loadShow();
                });
                table.on('draw.dt', function () {
                    loadFadeOut();
                });
                table.on('order.dt search.dt', function () {
                    table.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
                        cell.innerHTML = i + 1;
                    });
                }).draw();
            });
        </script>
        @stop