$(function(){
	//添加图文摘要
	$('#addAbstract').click(function(){
		var num=$('#abstractnum').val();
		++num;
		$('#addAbsTr').before('<tr><td style="text-align:center;">摘要'+num+'</td><td><textarea name="abstract'+num+'[content]" style="width:700px;height:150px;"></textarea></td></tr>'+
            '<tr><td style="text-align:center;">图片'+num+'</td><td><input name="abstractimg'+num+'" type="file" style="width:700px;"></td></tr>'+
            '<tr><td style="text-align:center;">图片信息'+num+'</td><td><textarea name="abstract'+num+'[imginfo]" value="" style="width:700px;height:50px;"></textarea></td></tr>');
		$('#abstractnum').val(num);
	});
	//选中路径
	$('.tk_lm').live('click',function(){
		//当前第几步路径
		$('.selectedstep').val($('.tk_lm').index($(this)));
		$('.tk_lm').removeClass('tk_lmon');
		$(this).addClass('tk_lmon');
	});
	//添加问题到路径中
	$('.tk_rm1 li').click(function(){
		var step=$('.selectedstep').val();
		var no=$(this).find('.no').val();
		var qeid=$(this).find('.qeid').val();
		var qetitle=$(this).find('.qetitle').val();
		//添加路径隐藏属性
		$('.tk_lmon ul').append($(this).clone().addClass('tooltip').attr('title',qetitle).html('<span>R</span><p>'+no+'</p><input type="hidden" name="step[]" class="step" value="'+step+'" /><input type="hidden" name="qeid[]" class="qeid" value="'+qeid+'" /><input type="hidden" name="probability[]" class="probability" value="1" /><input type="hidden" value="2" class="hascon" name="hascon[]" /><input type="hidden" value="1" class="aslimit" name="aslimit[]" /><input type="hidden" value="2" class="flag_over" name="flag_over[]" /><input type="hidden" class="qetitle" value="'+qetitle+'" />'));
		$('.tooltip').tooltipster();
	});
	//移除问题
	$('#removeQe').click(function(){
		if(confirm('是否移除?')){
			$('.tk_lm ul li.selected').remove();
			$('.tck_box').hide();
		}
	});
	//查看详细
	$('.tk_lm ul li').live('click',function(){
		$('.tk_lm ul li').removeClass('selected');
		$(this).addClass('selected');
		var url=$('#getOpsByQeUrl').val();
		var qeid=$(this).find('.qeid').val();
		var step=$(this).find('.step').val();
		var probability=$(this).find('.probability').val();
		var aslimit=$(this).find('.aslimit').val();
		var flag_over=$(this).find('.flag_over').val();
		var beftk=step;
		var beftklmqe=$('.tk_lm:lt('+beftk+')').find('li');
		$.ajax({
			type:'post',
			url:url,
			data:{'qeid':qeid},
			dataType:'json',
			success:function(res){
				//问题详情
				$('#tc_qeTitle').text('').html('<font color="red">'+res.no+'</font>&nbsp;'+res.title);
				$('#tc_ulops').html('');
				for(i in res.op){
					$('#tc_ulops').append('<li>'+res.op[i].content+'</li>');
				}
				$('.qeBeforeCon').html('');
				$('.qeProbability').val(probability);
				$('.qeAslimit').val(aslimit);
				if(flag_over==1){
					$('.qeFlagOver').eq(0).removeAttr('checked');
					$('.qeFlagOver').eq(1).attr('checked','checked');
				}else{
					$('.qeFlagOver').eq(0).attr('checked','checked');
					$('.qeFlagOver').eq(1).removeAttr('checked');
				}
				
				//前置条件
				if(step==0){
					$('.qeBeforeCon').text('无');
					$('#addCondition').attr('disabled','disabled');
				}else{
					$('#addCondition').removeAttr('disabled');
					var strda='';
					$('.tk_lmon li.selected input[name^=consprevqe]').each(function(i){
						strda+='qe[]='+$(this).val()+'&op[]='+$('.tk_lmon li.selected input[name^=consprevop]').eq(i).val()+'&';
					});
					if(strda!=''){//原先有条件的情况
						var getconurl=$('#getBeforeConUrl').val();
						//填充条件
						$.ajax({
							type:'post',
							url:getconurl,
							data:strda,
							dataType:'json',
							success:function(res){
								var str='';
								for(i in res){
									str+='<select class="questionCondition">';
									str+='<option value="">请选择</option>';
									beftklmqe.each(function(tkli){
										var sected=(res[i].qeid==$(this).find('.qeid').val())?'selected':'';
										str+='<option '+sected+' value="'+$(this).find('.qeid').val()+'">'+$(this).find('p').text()+'('+$(this).find('.qetitle').val()+')</option>'
									})
									var opstr='';
									var inputattr='type="checkbox" ';
									if(res[i].qetype=='2'){
										inputattr='type="checkbox" ';
									}
									for(opk in res[i].ops){
										sected='';
										for(s in res[i].opids){
											if(res[i].opids[s]==res[i].ops[opk].id){
												sected='checked';
											}
										}
										opstr+='<li><label><input class="selectop" '+sected+' '+inputattr+' name="radiocon'+i+'" value="'+res[i].ops[opk].id+'">'+res[i].ops[opk].content+'</label></li>';
									}
									
									str+='</select><br><div class="dxt"><ul class="optionCondition">'+opstr+'</ul></div><br>'+'<div class="cls"></div>';
								}
								$('.qeBeforeCon').html(str);
							}
						});
					}else{//默认无值的情况
						var str='<select class="questionCondition">';
						str+='<option value="">请选择</option>';
						beftklmqe.each(function(tkli){
							str+='<option value="'+$(this).find('.qeid').val()+'">'+$(this).find('p').text()+'('+$(this).find('.qetitle').val()+')</option>'
						})
						str+='</select><br><div class="dxt"><ul class="optionCondition"></ul></div><br>';
						$('.qeBeforeCon').html(str+'<div class="cls"></div>');
					}
					
				}
				$('.tck_box').show();
			}
		})
	});
	//条件
	$('.questionCondition').live('change',function(){
		var url=$('#getOpsByQeUrl').val();
		var index=$('.questionCondition').index($(this));
		var options=$('.optionCondition').eq(index);
		var qeid=$(this).val();
		$.ajax({
			type:'post',
			url:url,
			data:{'qeid':qeid},
			dataType:'json',
			success:function(res){
				options.html('');
				var inputattr='type="checkbox" name="radiocon'+index+'"';
				if(res.type=='2'){
					inputattr='type="checkbox" name="radiocon'+index+'"';
				}
				for(i in res.op){
					options.append('<li><label><input class="selectop" '+inputattr+' value="'+res.op[i].id+'">'+res.op[i].content+'</label></li>');
				}
			}
		})
	});
	//添加条件
	$('#addCondition').click(function(){
		var ops=$('.questionCondition').last().clone().html();//前一个问题选项
		var str='<select class="questionCondition">';
		str+=ops;
		str+='</select><br><div class="dxt"><ul class="optionCondition"></ul></div><br>';
		$('.qeBeforeCon').append(str+'<div class="cls"></div>');
	});
	//确认条件
	$('#subCondition').click(function(){
		//问题出现概率
		$('.tk_lmon li.selected input.probability').val($('.qeProbability').val());
		$('.tk_lmon li.selected input.aslimit').val($('.qeAslimit').val());
		$('.tk_lmon li.selected input.flag_over').val($('.qeFlagOver:checked').val());
		
		$('.tk_lmon li.selected input[name^=consprevqe]').remove();
		$('.tk_lmon li.selected input[name^=consprevop]').remove();
		$('.tk_lmon li.selected input[name^=consqeid]').remove();
		$('.tk_lmon li.selected input[name^=constep]').remove();
		$('.questionCondition').each(function(i){
			var qeid=$(this).val();
			$('.optionCondition').eq(i).find('input:checked').each(function(oi){
				$('.tk_lmon li.selected').append('<input type="hidden" name="consprevqe[]" value="'+qeid+'" /><input type="hidden" name="consprevop[]" value="'+$(this).val()+'" /><input type="hidden" name="consqeid[]" value="'+$('.tk_lmon li.selected input.qeid').val()+'" /><input type="hidden" name="constep[]" value="'+$('.tk_lmon li.selected input.step').val()+'" />');
			});
		});
		if($('.optionCondition li').length>0){//有前置条件
			$('.tk_lmon li.selected input.hascon').val(1);
			$('.tk_lmon li.selected span').text('C');
		}else{//无前置条件
			$('.tk_lmon li.selected input.hascon').val(2);
			$('.tk_lmon li.selected span').text('R');
		}
		$('.tck_box').hide();
	});
	//鼠标滚动
	$(window).scroll(function(){
//		if($(window).scrollTop() > $('.tk_l').offset().top){
//			$('.tk_l').css('margin-top',($(window).scrollTop()-$('.tk_l').offset().top)+'px');
//		}
	});
	//弹出框
	$('body').append($('.tck_box'));
	$('#tck_close').click(function(){
		$('.tck_box').hide();
		$('.tk_lm ul li').removeClass('selected');
	});
	//鼠标hover效果
//	$('li[class^=icon]').hover(function(){
//		//alert($(this).find('.qetitle').val())
//	},function(){
//		
//	});
	$('.tooltip').tooltipster();
	//添加路径框
	$('#addpath').click(function(){
		$(this).prev().removeClass('wx');
		$(this).before('<div class="tk_lm wx"><ul></ul><div class="cls"></div></div>');
	});
	//查找题库
	$('.searchCategory').change(function(){
		var cid=$(this).val();
		if(cid==''){
			$(this).parent().next().find('li').show();
			return;
		}
		$(this).parent().next().find('li').each(function(){
			if($(this).find('.qecate').val()==cid){
				$(this).show();
			}else{
				$(this).hide();
			}
		});
	});
	$('.searchQeBtn').click(function(){
		var key=$(this).prev().val();
		if(key==''){
			$(this).parent().next().find('li').show();
			return;
		}
		$(this).parent().next().find('li').each(function(){
			var str=$(this).find('.qetitle').val();
			if(str.indexOf(key)!=-1){
				$(this).show();
			}else{
				$(this).hide();
			}
		});
	});
	$('.searchAll').click(function(){
		$(this).parent().next().find('li').show();
		//$(this).parent().find('.searchTitle').val('');
	});
});


function checkFrom(){
	var flag=true;
	var title=$('#qs_title').val();
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