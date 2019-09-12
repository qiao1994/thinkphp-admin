//===表单验证js======
function setError(jqobj, msg) {
    jqobj.parent().parent().parent().removeClass('has-success');
    jqobj.next().removeClass('icon-ok-sign');
    jqobj.parent().parent().parent().addClass('has-error');
    jqobj.next().addClass('icon-remove-sign');
    if (jqobj.parent().parent().next(). hasClass('help-block')) {
        jqobj.parent().parent().next().html(msg);
    }
}
function setSuccess(jqobj) {
    jqobj.parent().parent().parent().removeClass('has-error');
    jqobj.next().removeClass('icon-remove-sign');
    if (jqobj.parent().parent().next(). hasClass('help-block')) {
        jqobj.parent().parent().next().html('');
    }
    jqobj.parent().parent().parent().addClass('has-success');
    jqobj.next().addClass('icon-ok-sign');
}
function  setReset(jqobj) {
    jqobj.parent().parent().parent().removeClass('has-success');
    jqobj.next().removeClass('icon-ok-sign');
    jqobj.parent().parent().parent().removeClass('has-error');
    jqobj.next().removeClass('icon-remove-sign');
    if (jqobj.parent().parent().next(). hasClass('help-block')) {
        jqobj.parent().parent().next().html('');
    }
}
function validateInput(jqobj) {
    var requiredAttr = jqobj.attr('validate-required');
    if (!requiredAttr) {
        return true;
    }
    var reqArr = requiredAttr.split(":");
    if (reqArr[0] == 'required') {
        if (jqobj.val() == '') {
            setError(jqobj, reqArr[1]);
        } else {
            setSuccess(jqobj);
        }
    } else {
        setSuccess(jqobj);
    }
}
function inputReset(jqobj) {
    setReset(jqobj);
}
$("input,textarea").blur(function(){
    validateInput($(this));
});
$("form").submit(function(){
    var submitFlag = true;
    //每个input都验证一遍
    $(this).find('input,textarea').each(function(){
        validateInput($(this));
        if ($(this).parent().parent().next().hasClass('help-block') && ($(this).parent().parent().next().html() != '')) {
            //有错误
            $(this).focus();
            submitFlag = false;
        }
    });
    return submitFlag;
});
$(".reset").click(function(){
    $('input,textarea').each(function(){
        inputReset($(this));
    });
});
//!===表单验证结束======

//===清空查询输入框======
function clearFind() {
    //清空输入框内容
    $('.search-input').each(function() {
        $(this).val('');
    });
    //清空下拉框选中默认值
    $('.search-select').each(function() {
        $(this).val("");
    });
    $("#search-form").submit();
}
//===导出当前查询条件下的数据======
function exportExcel() {
    var url = '?';
    $('.search-input').each(function() {
        url = url+$(this).attr('name')+'='+$(this).val()+'&';
    });
    $('.search-select').each(function() {
        url = url+$(this).attr('name')+'='+$(this).val()+'&';
    });
    $('.sort-str').each(function() {
        url = url+$(this).attr('name')+'='+$(this).val()+'&';
    });

    var href = $('#export-btn').attr('href');
    $('#export-btn').attr('href', href+url);
    return true;
}
//===删除操作======
$(document).ready(function() {
    $('.delete-btn').click(function() {
        if (!confirm('是否确定要删除?')) {
            return false;
        }
        var id = $(this).attr('id');
        var url = $('#controller').val()+'/delete';
        $.post(url, {id: id}, function(data, status) {
            if (data.status == 1) {
                alert(data.info);
                location=location;
            } else {
                alert('请求出错!');
            }
        });

    });
    $("#checkall").click(function(){
        if(this.checked){
            $("input[name='chkid']").prop("checked", true);
        }else{
            $("input[name='chkid']").removeAttr("checked");
        }
    });

    $("#delall").click(function(){
        var arrChk = $("input[name='chkid']");
        var idArr=new Array();
        arrChk.each(function(){
            if ($(this).prop("checked")) {
                idArr.push(this.value);
            }
        });
        if (idArr.length != 0) {
            if (!confirm("是否要批量删除？")) {
                return false;
            }
        } else {
            alert("请选择要删除的项!");
            return false;
        }
        var controller = $("#controller").val();
        var idStr = idArr.join(",");
        var url = $('#controller').val()+'/delete';
        $.post(url, {id: idStr}, function(data, status) {
            if (data.status == 1) {
                alert(data.info);
                location=location;
            } else {
                alert('请求出错!');
            }
        });
    });
    $('.return').click(function(event) {
        history.go(-1);
    });
    $('.list-update-btn').click(function(){
        var dataField = $(this).attr('data-field');
        var dataId = $(this).attr('data-id');
        var updateEleId = dataField+'-'+dataId;
        //隐藏显示div
        $('#list-show-div-'+updateEleId).hide();
        //展示select
        $('#list-update-div-'+updateEleId).show();
        //增加焦点
        $('#list-update-div-'+updateEleId).children().focus();
    });
    $('.list-update-select,.list-update-input').blur(function(){
        var dataModel = $(this).attr('data-model');
        var dataField = $(this).attr('data-field');
        var updateEleId = dataField+'-'+dataId;
        var dataId = $(this).attr('data-id');
        var dataNewValue = $(this).val();
        //ajax修改
        $.post("/Admin/System/updateFromList",{
            dataModel:dataModel, 
            dataField:dataField, 
            dataId:dataId, 
            dataNewValue:dataNewValue
        },
        function(result){
            if (result.status === 0) {
                alert(result.info);
            } else {
                location.reload();
            }
        });        
    });
    $('.sort-btn').click(function(){
        var sortStr = $(this).attr('data-new-value');
        $(this).removeClass('sort-btn');
        $('.sort-btn').each(function(){
            sortStr += ','+$(this).attr('data-value');
        });
        $('#sort-str').val(sortStr);
        $('#search-form').submit();
    });
    $('.operation-btn').click(function(){
        var arrChk = $("input[name='chkid']");
        var idArr=new Array();
        arrChk.each(function(){
            if ($(this).prop("checked")) {
                idArr.push(this.value);
            }
        });
        if (idArr.length != 0) {
            if (!confirm("是否要批量操作？")) {
                return false;
            }
        } else {
            alert("请选择要操作的项!");
            return false;
        }
        var controller = $("#controller").val();
        var idStr = idArr.join(",");
        var operationKey = $(this).attr('data-operation-id');
        var url = $('#controller').val()+'/batchOperation';
        $.post(url, {id: idStr, operationKey: operationKey}, function(data, status) {
            if (data.status == 1) {
                alert(data.info);
                location=location;
            } else {
                alert('请求出错!');
            }
        });
    });
    //验证码点击
    $('#identify-code-img').click(function(){
        $(this).attr('src', $(this).attr('src'));
    });
});
