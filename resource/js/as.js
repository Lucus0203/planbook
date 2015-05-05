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
	$('.anstext').live('blur',function(){
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
	
	//判断概率出下一题
	$('.ansqe label').live('click',function(){
		if(!$(this).find('input:eq(0)').attr('checked')){
			$(this).find('input:eq(0)').attr('checked','checked');//勾选
		}
		var index=$('.question_list').index($(this).parent().parent());
		$('.question_list:gt('+index+')').remove();
		
		var questionid=$(this).parent().prev().find('input[name^=questionid]').val();
		var optionlist=$(this).parent().find('input:checked');//var optionid=$(this).find('input').val();
		var selectqeid='';
		var selectqeidarr=new Array();
		var step=$('.ansqe').length;
		var qeli=$('#qepath .step').eq(step-1).find('li');
		//找符合前置条件的问题
		var i=0;
		$('.qecon li').each(function(){
			var qecon_prevqe_id=$(this).find('.qecon_prevqe_id').val();
			var qecon_prevop_id=$(this).find('.qecon_prevop_id').val();
			var qecon_qe_id=$(this).find('.qecon_qe_id').val();
			optionlist.each(function(){
				if(qecon_prevqe_id==questionid && qecon_prevop_id==$(this).val()){
					selectqeidarr[i]=qecon_qe_id;
					i++;
				}
			});
		});
		var ind=Math.floor(Math.random()*selectqeidarr.length);
		selectqeid=selectqeidarr[ind]?selectqeidarr[ind]:'';
		//如果没有符合前置条件的问题则随机下一轮,按概率出
		if(selectqeid==''){
			var maxnum=0;
			qeli.each(function(){
				maxnum+=$(this).find('.probability').val()*1;
			});
			var tnum=Math.floor(Math.random()*maxnum+1);
			qeli.each(function(){
				var probability=$(this).find('.probability').val()*1;
				if(tnum<=probability){
					selectqeid=$(this).find('.qeid').val();
					return false;
				}else{
					maxnum-=probability;
					tnum=Math.floor(Math.random()*maxnum+1);
				}
			});
		}
		if(selectqeid!=''){
			var leng=$('.question_list').length;
			//如果通过前置条件找不到后台随机给出的条件,则在所有路径问题中找到问题
			var selectqe=$('li#qestion_id'+selectqeid).length>0?$('li#qestion_id'+selectqeid):$('li#questall_id'+selectqeid).eq(0);
			var qetitle=selectqe.find('.qetitle').val();
			var qetype=selectqe.find('.qetype').val();
			var qefile=(selectqe.find('.qefile').val()!='')?'<br><img src="'+selectqe.find('.qefile').val()+'">':'';
			
			var str='<div class="wj_m question_list">'+
			    	'<p>Q'+(leng+1)+'. '+qetitle+qefile+
					'<input type="hidden" name="questionid'+leng+'" value="'+selectqeid+'"  />'+
				'</p> <div class="wj_mm ansqe">'+
			    '<input class="qetype" type="hidden" value="'+qetype+'" />';
		    selectqe.find('.opid').each(function(i){
		    	var opid=$(this).val();
		    	var opcontent=selectqe.find('.opcontent').eq(i).val();
		    	str+='<label>';
		    	if(qetype == 1){
		    		str+='<input type="radio" name="check'+leng+'" value="'+opid+'">';
		    	}else if(qetype == 2){
		    		str+='<input type="checkbox" name="check'+leng+'[]" value="'+opid+'">';
		    	}else if(qetype == 3){
		    		str+='<input type="radio" name="check'+leng+'" value="'+opid+'">';
		    	}else if(qetype == 4){
		    		str+='<input type="hidden" name="check'+leng+'" value="'+opid+'">';
		    	}
		    	opcontent=opcontent.replace(/@text/g,'<br><input type="text" class="anstext" value="" style="font-size:16px;width:80%;height:30px;" /><br>')
		    	str+=opcontent+'<input type="hidden" name="anscontent'+opid+'" value="" /></label>';
		    });
			 str+='</div></div>';
			$(this).parent().parent().after(str);
			$('#submit').val('中止答题并提交');
		}else{
			$('#quesnum').val($('.question_list').length);
			$('#submit').val('答题结束并提交');
		}
		return false;
	});
	
});
