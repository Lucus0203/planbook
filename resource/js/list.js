$(function(){
	$('a.delBtn').click(function(){
		if(confirm('确定删除吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
	$('a.pubBtn').click(function(){
		if(confirm('确定发布吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
	$('a.overBtn').click(function(){
		if(confirm('确定结束这个问卷吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
	
	$('a.recover').click(function(){
		if(confirm('确定恢复这个问卷吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
});