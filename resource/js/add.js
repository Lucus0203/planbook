$(function(){
	//添加图文摘要
	$('#addAbstract').click(function(){
		var num=$('#abstractnum').val();
		++num;
		$('#addAbsTr').before('<tr><td style="text-align:center;"><input name="abstract'+num+'[title]" type="text" value="摘要'+num+'" style="width:50px;padding:0;" /></td><td><textarea name="abstract'+num+'[content]" style="width:700px;height:150px;"></textarea></td></tr>'+
            '<tr><td style="text-align:center;">图片'+num+'</td><td><input name="abstractimg'+num+'" type="file" style="width:700px;"></td></tr>'+
            '<tr><td style="text-align:center;">图片信息'+num+'</td><td><textarea name="abstract'+num+'[imginfo]" value="" style="width:700px;height:50px;"></textarea></td></tr>');
		$('#abstractnum').val(num);
	});
	//添加选项
	$('#addSingle').click(function(){
		var num=$('#questionnum').val();
		$('#addtr').before('<tr class="qtr"><td class="qn" style="text-align:center;"></td><td>'+
            '<input type="text" name="question'+num+'[title]" value="" style="width:300px;" /><a style="float:right;" href="#" class="delQuestion">删除</a></td></tr>'+
            '<tr class="atr"><td style="text-align:center;">单选题<input type="hidden" name="question'+num+'[type]" value="1" /><br>'+
            '<input type="button" class="mvDown" value="下移" /><input type="button" class="mvUp" value="上移" /></td>'+
            '<td><input type="radio" name="question'+num+'">&nbsp;<input type="text" name="question'+num+'[content][]" value="" style="width:240px;" />'+
            '<br/><input type="radio" name="question'+num+'">&nbsp;<input type="text" name="question'+num+'[content][]" value="" style="width:240px;" />'+
		'<a href="#" class="addOption">添加选项+</a></td></tr>');
		$('#questionnum').val((num*1+1));
		resetQ();
	});
	$('#addMultiple').click(function(){
		var num=$('#questionnum').val();
		$('#addtr').before('<tr class="qtr"><td class="qn" style="text-align:center;"></td><td>'+
             '<input type="text" name="question'+num+'[title]" value="" style="width:300px;" /><a style="float:right;" href="#" class="delQuestion">删除</a></td></tr>'+
             '<tr class="atr"><td style="text-align:center;">多选题<input type="hidden" name="question'+num+'[type]" value="2" /><br>'+
             '<input type="button" class="mvDown" value="下移" /><input type="button" class="mvUp" value="上移" /></td>'+
             '<td><input type="checkbox">&nbsp;<input type="text" name="question'+num+'[content][]" value="" style="width:240px;" />'+
             '<br/><input type="checkbox">&nbsp;<input type="text" name="question'+num+'[content][]" value="" style="width:240px;" />'+
             '<a href="#" class="addOption">添加选项+</a></td></tr>');
		$('#questionnum').val((num*1+1));
		resetQ();
	});
	$('#addOpenness').click(function(){
		var num=$('#questionnum').val();
		$('#addtr').before('<tr class="qtr"><td class="qn" style="text-align:center;"></td><td>'+
            '<input type="text" name="question'+num+'[title]" value="" style="width:300px;" /><a style="float:right;" href="#" class="delQuestion">删除</a></td></tr>'+
            '<tr class="atr"><td style="text-align:center;">开放性问题<input type="hidden" name="question'+num+'[type]" value="3" /><br>'+
            '<input type="button" class="mvDown" value="下移" /><input type="button" class="mvUp" value="上移" /></td>'+
            '<td>(需要答题者填写文字的部分请用特殊符号"@text"代替,如:我认为@text)<br/>'+
            '<input type="radio" name="question'+num+'">&nbsp;<input type="text" name="question'+num+'[content][]" value="" style="width:240px;" />'+
            '<br/><input type="radio" name="question'+num+'">&nbsp;<input type="text" name="question'+num+'[content][]" value="" style="width:240px;" />'+
			'<a href="#" class="addOption">添加选项+</a></td></tr>');
		$('#questionnum').val((num*1+1));
		resetQ();
	});
	$('#addFill').click(function(){
		var num=$('#questionnum').val();
		$('#addtr').before('<tr class="qtr"><td class="qn" style="text-align:center;"></td><td>'+
            '<input type="text" name="question'+num+'[title]" value="" style="width:300px;" /><a style="float:right;" href="#" class="delQuestion">删除</a></td></tr>'+
            '<tr class="atr"><td style="text-align:center;">填空题<input type="hidden" name="question'+num+'[type]" value="4" /><br>'+
            '<input type="button" class="mvDown" value="下移" /><input type="button" class="mvUp" value="上移" /></td>'+
            '<td>(需要答题者填写文字的部分请用特殊符号"@text"代替,如:姓名是@text)<br/>'+
            '<input type="text" name="question'+num+'[content][]" value="" style="width:240px;" /></td></tr>');
		$('#questionnum').val((num*1+1));
		resetQ();
	});
	$('.delQuestion').live('click',function(){
		if(confirm('是否删除问题')){
			$(this).parent().parent().next().remove();
			$(this).parent().parent().remove();
			resetQ();
		}
		
		return false;
	});
	$('.addOption').live('click',function(){
		var optcheck=$(this).prev().prev().clone().removeAttr('checked');
		var opt=$(this).prev().clone().val('');
		$(this).before('<br/>');
		$(this).before(optcheck);
		$(this).before('&nbsp;');
		$(this).before(opt);
		return false;
	});
	//下移
	$('input.mvDown').live('click',function(){
		var thisaobj=$(this).parent().parent();
		var thisqobj=thisaobj.prev();
		var index=$('tr.atr').index(thisaobj);
		if((index+1)<=$('tr.atr').length){
			$('tr.atr').eq(index+1).after(thisqobj);
			thisqobj.after(thisaobj);
			resetQ();
		}
	});
	//上移
	$('input.mvUp').live('click',function(){
		var thisaobj=$(this).parent().parent();
		var thisqobj=thisaobj.prev();
		var index=$('tr.atr').index(thisaobj);
		if((index-1)>=0){
			$('tr.qtr').eq(index-1).before(thisaobj);
			thisaobj.before(thisqobj);
			resetQ();
		}
	});
	
});

//重新初始化问题序号
function initQuestionNum(){
	$('tr.qtr').each(function(i){
		$(this).find('input').each(function(){
			if($(this).attr('name')){
				var strname=$(this).attr('name')+'';
				strname = strname.replace(/question\d+/g,'question'+i);
				$(this).attr('name',strname+'');
			}
		});
		$(this).next().find('input').each(function(){
			if($(this).attr('name')){
				var strname=$(this).attr('name')+'';
				strname = strname.replace(/question\d+/g,'question'+i);
				$(this).attr('name',strname+'');
			}
		});
	});
}

//重置Q序号
function resetQ(){
	$('.qn').each(function(i){
		$(this).html('Q'+(i+1));
	});
	initQuestionNum();
}

function checkFrom(){
	var flag=true;
	var title=$('#questionnaire_title').val();
	var status=$('#questionnaire_status').val();
	var msg='';
	if($.trim(title)==''){
		msg+='请填写问卷标题\n';
		flag=false;
	}
	if($.trim(status)=='1'){
		msg+='问卷已发布不可编辑\n';
		flag=false;
	}
	if($.trim(status)=='2'){
		msg+='问卷已结束不可编辑\n';
		flag=false;
	}
	if(!flag){
		alert(msg);
	}
	return flag;
}