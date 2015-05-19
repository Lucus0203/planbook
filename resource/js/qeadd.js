$(function(){
	//添加选项
	var num=1;
	$('#addSingle').click(function(){
		$('.atr').hide();
		$('#singlebox').show();
	});
	$('#addMultiple').click(function(){
		$('.atr').hide();
		$('#multiplebox').show();
	});
	$('#addOpenness').click(function(){
		$('.atr').hide();
		$('#opennessbox').show();
	});
	$('#addFill').click(function(){
		$('.atr').hide();
		$('#fillbox').show();
	});
	$('.delQuestion').live('click',function(){
		$(this).parent().parent().next().remove();
		$(this).parent().parent().remove();
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
	
});