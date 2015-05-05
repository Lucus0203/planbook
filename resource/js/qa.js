$(function(){
	setInterval(function(){$('#time').val($('#time').val()*1+1)},1000);
	$('#submit').click(function(){
		if($('#questionnaire_status').val()!=1){
			if($('#questionnaire_status').val()==2){
				alert('调查问卷已结束,您不能答题');
			}else{
				alert('调查问卷还没发布,您不能答题');
			}
			return false;
		}else{
			return true;
		}
	});
	$('.anstext').blur(function(){
		var qeobj=$(this).parent().parent();
		var type=qeobj.find('.qetype').val();
		if(type==3){
			var i=qeobj.find('.anstext').index($(this));
			qeobj.find('input[name^=anscontent]').eq(i).val($(this).val());
		}
		if(type==4){
			var str='';
			qeobj.find('.anstext').each(function(i){
				str+=$(this).val()+',';
			});
			qeobj.find('input[name^=anscontent]').val(str.slice(0,-1));
		}
	});
	$('input[type=radio],input[type=checkbox]').change(function(){
		$('input[type=radio],input[type=checkbox]').each(function(i){
			if($(this).attr('checked')){
				$(this).parent().css('background-color','#d5eaff');
			}else{
				$(this).parent().css('background-color','#fff');
			}
		});
	});
	$('#copyQurl').click(function(){
		//window.clipboardData.setData("Text等等", '需要复制的信息');
		//alert('复制成功');
		return false;
	});
});
