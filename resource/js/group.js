$(function(){
	//添加图文摘要
	$('#addtr').click(function(){
		$('#addtr').before('<tr><td style="text-align:center;">分组名</td>'+
                '<td><input type="text" name="name[]" /><a class="del" href="#">删除</a></td></tr>');
	});
	
	$('.delgroup').click(function(){
		if(confirm('确定删除吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	})

	$('.del').live('click',function(){
		$(this).parent().parent().remove();
		return false;
	});

});