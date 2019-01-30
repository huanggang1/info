@extends('admin.layouts.base')

@section('title','控制面板')

@section('pageHeader','控制面板')

@section('pageDesc','DashBoard')

@section('content')
<div id="acc_sc" style="display: none">
    <form  id="formSubmit" class="reasonContent2"  action="/admin/user/import" method="post" enctype="multipart/form-data"> 
        <input type="file" name="file" id="file" multiple class="ph08" />
         <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit"  id="submit" value="导入"/>
    </form>
</div>  
<div class="row page-title-row" style="margin:5px;">
    <div class="col-md-6">
    </div>
    <div class="col-md-6 text-right">
        <a href="/admin/user/create" class="btn btn-success btn-md">
            <i class="fa fa-plus-circle"></i> 添加用户
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
                <label class="lable"  for="tag" style="margin-bottom: 20px">姓名：</label><input type="text"  name="name" id="btName" value="" autofocus>
                <label class="lable"  for="tag" style="margin-bottom: 20px">邮箱：</label><input type="text"  name="email" id="btEmail" value="" autofocus>
                <label class="lable"  for="tag" style="margin-bottom: 20px">手机号：</label><input type="text"  name="phone"  id="btPhone" value="" autofocus>
                <button  class="btn btn-success" id="submitSearch" style="float: right;margin-right: 30px">搜索</button>
                <div class="box-header">
                    <a class="btn btn-success"  id="btnExport">导出</a>
                    <a class="btn btn-success" id="btnImport">导入</a>
                </div>
                <table id="tags-table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">用户名</th>
                            <th class="hidden-sm">邮箱</th>
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
                    确认要删除这个用户吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="deleteForm" method="POST" action="/admin/user">
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


                var table = $("#tags-table").DataTable({
//                    "bFilter": false,
                    "searching": false,
                    oLanguage: {
                        "sProcessing": "处理中...",
                        "sLengthMenu": "显示 _MENU_ 项结果",
                        "sZeroRecords": "没有匹配结果",
                        "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                        "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
                        "sInfoFiltered": "",
                        "sInfoPostFix": "",
//                        "sSearch": "搜索:",
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
                        url: '/admin/user/index',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                         data: function (d) {
                                                     d.btName = $("#btName").val();
                                                     d.btPhone = $("#btPhone").val();
                                                     d.btEmail = $("#btEmail").val();
                        },
                    },
                    "columns": [
                        {"data": "key"},
                        {"data": "name"},
                        {"data": "email"},
                        {"data": "phone"},
                        {"data": "action"}
                    ],
                    columnDefs: [
                        {
                            'targets': -1, "render": function (data, type, row) {
                                 var caozuo = '<a style="margin:3px;" href="/admin/user/detail?id=' + row['id'] + '" class="X-Small btn-xs text-success "><i class="fa fa-street-view"></i> 查看</a>';
                                caozuo += '<a style="margin:3px;" href="/admin/user/' + row['id'] + '/edit" class="X-Small btn-xs text-success "><i class="fa fa-edit"></i> 编辑</a>';
                                if (row['id'] != 1) {
                                    caozuo += '<a style="margin:3px;" href="#" attr="' + row['id'] + '" class="delBtn X-Small btn-xs text-danger "><i class="fa fa-times-circle-o"></i> 删除</a>';
                                }
                                return caozuo;
                            }
                        }
                    ]
                });
                $("#submitSearch").click(function () {
                    $("#tags-table").dataTable().fnDraw(false);
                });
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
                    $('.deleteForm').attr('action', '/admin/user/' + id);
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
                    var btPhone = $("#btPhone").val();
                    var btEmail = $("#btEmail").val();
                    location.href = "/admin/user/export?btName=" + btName + "&btEmail=" + btEmail + "&btPhone=" + btPhone;
                });
//                $(document).on('click', '#submit', function () {
//                    var fd = new FormData(document.querySelector("#formSubmit"));
//                    $.ajax({
//                        //几个参数需要注意一下
//                        type: "POST", //方法类型
//                        url: "/admin/user/import", //url
//                        dataType: 'json',
//                        data: fd,
//                        processData: false, // 不处理数据
//                        contentType: false, // 不设置内容类型
//                        headers: {
//                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
//                        },
//                        success: function (result) {
//                            console.log(result);
//                            layer.close(indexColse);
//                        },
//                    });
//                })

            });
        </script>
        @stop