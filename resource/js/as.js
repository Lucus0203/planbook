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
	
	$('#copyQurl').click(function(){
		//window.clipboardData.setData("Text等等", '需要复制的信息');
		//alert('复制成功');
		return false;
	});
	
	//判断概率出下一题
	$('.ansqe label').live('click',function(){
		//选中高亮切换
		if(!$(this).find('input:eq(0)').attr('checked')){
			//最多选择几项
			var numlimit=$(this).parent().find('.aslimit').val();
			if($(this).find('input:eq(0)').attr('type')=='checkbox'&&$(this).parent().find('input:checked').length>=numlimit){
				alert('本题最多选择'+numlimit+'项答案');
				return false;
			}
			//勾选
			if($(this).find('input:eq(0)').attr('type')=='radio'){
				$(this).parent().find('label').css('background-color','#fff');
			}
			$(this).find('input:eq(0)').attr('checked','checked');//勾选
			$(this).css('background-color','#d5eaff');
		}else{
			//取消
			$(this).find('input:eq(0)').removeAttr('checked','checked');//勾选
			$(this).css('background-color','#fff');
		}
		//如果是触发结束题则不出新的题
		if($(this).parent().find('.flag_over').val()==1){
			return false;
		}
		//移除后面答题内容
		var index=$('.question_list').index($(this).parent().parent());
		$('.question_list:gt('+index+')').remove();
		
		var questionid=$(this).parent().prev().find('input[name^=questionid]').val();
		var optionlist=$(this).parent().find('input:checked');//var optionid=$(this).find('input').val();
		var selectqeid='';
		var selectqeidarr=new Array();
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
			var step=$('.ansqe').length;
			var qeli=$('#qepath .step').eq(step-1).find('li');//某个路径的li
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
			var selectqe=$('li#qestion_id'+selectqeid).length>0?$('li#qestion_id'+selectqeid):$('li#questall_id'+selectqeid).eq(0);//先通过前置条件找,如果没有则在所有路径问题中找到问题
			var qetitle=selectqe.find('.qetitle').val();
			var qetype=selectqe.find('.qetype').val();
			var aslimit=selectqe.find('.aslimit').val();//答题限制
			var flag_over=selectqe.find('.flag_over').val();//1触发结束,2无
			var qefile=(selectqe.find('.qefile').val()!='')?'<br><img src="'+selectqe.find('.qefile').val()+'">':'';
			
			var str='<div class="wj_m question_list">'+
			    	'<p>Q'+(leng+1)+'. '+qetitle+qefile+
					'<input type="hidden" name="questionid'+leng+'" value="'+selectqeid+'"  />'+
				'</p> <div class="wj_mm ansqe">'+
			    '<input class="qetype" type="hidden" value="'+qetype+'" /><input class="aslimit" type="hidden" value="'+aslimit+'" /><input class="flag_over" type="hidden" value="'+flag_over+'" />';
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
			 $(this).parent().parent().after(str);//新加题目
			 if(flag_over==1){
				 $('#quesnum').val($('.question_list').length);
				 $('#submit').val('答题结束并提交');
			 }else{
				 $('#submit').val('中止答题并提交');
			 }
		}else{
			$('#quesnum').val($('.question_list').length);
			$('#submit').val('答题结束并提交');
		}
		return false;
	});
	
});
