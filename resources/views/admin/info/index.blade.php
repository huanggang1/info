@extends('admin.layouts.base')

@section('title','控制面板')

@section('pageHeader','控制面板')

@section('pageDesc','DashBoard')

@section('content')
<div id="acc_sc" style="display: none">
    <form  id="formSubmit" class="reasonContent2" action="/admin/info/import" method="post" enctype="multipart/form-data"> 
        <input type="file" name="file" id="file" multiple class="ph08" />
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit"  id="submit" value="导入"/>
        

    </form>
</div>  
<div class="row page-title-row" style="margin:5px;">
    <div class="col-md-6">
    </div>
    <div class="col-md-6 text-right">
        <a href="/admin/info/create" class="btn btn-success btn-md">
            <i class="fa fa-plus-circle"></i> 添加信息
        </a>
    </div>
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
                    <label class="lable"  for="tag" style="margin-bottom: 20px">年级：</label><input type="text"   id="btGrade" value="" autofocus>
                    <label class="lable"  for="tag" >姓名：</label><input type="text"   id="btName" value="" autofocus>
                    <label class="lable"  for="tag" >手机号：</label> <input type="text"   id="btPhone" value="" autofocus><br/>
                    <label class="lable"  for="tag" >学校：</label><input type="text"   id="btSchool" value="" autofocus>
                    <label class="lable"  for="tag" >核对地址：</label><input type="text"    id="checkAddress" value="" autofocus>
                    <!--<label class="lable"  for="tag" >年级：</label><input type="text"    id="btSchool" value="" autofocus>-->
<!--                    是否录取 :<select id="btnFullCost">
                        <option value="-1">--请选择--</option>
                        <option value="1">已录取</option>
                        <option value="0">未录取</option> </select>-->
                    <label class="lable"  for="tag" >是否全费：</label> :<select id="btnFullCost">
                        <option value="-1">--请选择--</option>
                        <option value="1">是</option>
                        <option value="0">否</option>
                    </select>
                    <button class="btn btn-success"  id="submitSearch"style="float: right;margin-right: 30px">搜索</button>
                    <div class="box-header">
                        <a class="btn btn-success"  id="btnExport">导出</a>
                        <a class="btn btn-success" id="btnImport">导入</a>
                    </div>
                </div>
                <table id="tags-table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm">序号</th>
                            <th class="hidden-sm">考生号</th>
                            <th class="hidden-sm">姓名</th>
                            <th class="hidden-md">身份证号</th>
                            <th class="hidden-md">报考专业</th>
                            <th class="hidden-md">报考院校</th>
                            <th class="hidden-md">手机号</th>
                            <th data-sortable="false">操作</th>
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
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    ×
                </button>
                <h4 class="modal-title">提示</h4>
            </div>
            <div class="modal-body">
                <p class="lead">
                    <i class="fa fa-question-circle fa-lg"></i>
                    确认要删除这个信息吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="deleteForm" method="POST" action="/admin/info">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times-circle"></i>确认
                    </button>
                </form>
            </div>
        </div>
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
                        url: '/admin/info/index',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                         data: function (d) {
                                                     d.btName = $("#btName").val();
                                                     d.btGrade = $("#btGrade").val();
                                                     d.checkAddress = $("#checkAddress").val();
                                                     d.btnFullCost = $("#btnFullCost").val();
                                                     d.btPhone = $("#btPhone").val();
                                                     d.btSchool = $("#btSchool").val();
                        },
                    },
                    "columns": [
                        {"data": "key"},
                        {"data": "examineeNum"},
                        {"data": "name"},
                        {"data": "identityNum"},
                        {"data": "applyProfession"},
                        {"data": "applySchool"},
                        {"data": "phone"},
                        {"data": "action"}
                    ],
                    columnDefs: [
                        {
                            'targets': -1, "render": function (data, type, row) {
                                var caozuo = '<a style="margin:3px;" href="/admin/info/detail?id=' + row['id'] + '" class="X-Small btn-xs text-success "><i class="fa fa-street-view"></i> 查看</a>';
                                caozuo += '<a style="margin:3px;" href="/admin/info/' + row['id'] + '/edit" class="X-Small btn-xs text-success "><i class="fa fa-edit"></i> 编辑</a>';
                                caozuo += '<a style="margin:3px;" href="#" attr="' + row['id'] + '" class="delBtn X-Small btn-xs text-danger "><i class="fa fa-times-circle-o"></i> 删除</a>';
                                return caozuo;
                            }
                        }
                    ]
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
                $("table").delegate('.delBtn', 'click', function () {
                    var id = $(this).attr('attr');
                    $('.deleteForm').attr('action', '/admin/info/' + id);
                    $("#modal-delete").modal();
                });
                $("#btnImport").click(function () {
                    indexColse = layer.open({
                        type: 1,
                        title: '导入',
                        shadeClose: true,
                        shade: 0.6,
                        area: ['500px', '90%'],
                        content: $("#acc_sc") //"http://127.0.0.1:9501/addUser.html"
                    });
                })
                $("#btnExport").click(function () {
                    var btName = $("#btName").val();
                    var btGrade = $("#btGrade").val();
                    var checkAddress = $("#checkAddress").val();
                    var btnFullCost = $("#btnFullCost").val();
                    var btPhone = $("#btPhone").val();
                    var btSchool = $("#btSchool").val();
                    location.href = "/admin/info/export?btName=" + btName + "&btGrade=" + btGrade + "&checkAddress=" + checkAddress + "&btnFullCost=" + btnFullCost + "&btPhone=" + btPhone + "&btSchool=" + btSchool;
                });
            });
        </script>
        @stop