<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="width=device-width, initial-scale= 1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" name="viewport" id="viewport" />
<meta name="format-detection" content="telephone=no"/>
<title>{$questionnaire.title}</title>
<link href="{$smarty.const.SITE}resource/css/qa.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/jQuery.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/highcharts/highcharts.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/highcharts/modules/exporting.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/analysis.js"></script>
</head>

<body>

<div class="wj_box">
	<div class="wj_t">
		<h1>{$questionnaire.title}</h1>
    </div>
    {section name=sec loop=$question}
    {if $question[sec].type neq 4}
	<div class="wj_m">
         <p>Q{$smarty.section.sec.index+1}:{$question[sec].title}</p>
      	 <input type="hidden" id="quetitle{$smarty.section.sec.index}" value="{$question[sec].title}" />
      	 <input type="hidden" id="quetype{$smarty.section.sec.index}" value="{$question[sec].type}" />
      	 <input type="hidden" id="anscount{$smarty.section.sec.index}" value="{$question[sec].times|@count}" />
         <div class="wj_mm">
         	<div id="container{$smarty.section.sec.index}" style="min-width: 310px; height: 305px; max-width: 90%; margin: 0 auto 10px;"></div>
         	<table class="optionTab" border="0" cellpadding="1" cellspacing="1" width="70%" style="margin:0 auto;">
     		<tr><td>答案选项</td><td style="word-break:keep-all">回复情况</td></tr>
     		{section name=sop loop=$question[sec].option}
     		<tr>
     			<td class="options{$smarty.section.sec.index}">{$question[sec].option[sop].content|replace:'@text':'______'}</td>
     			<td class="counts{$smarty.section.sec.index}">{$question[sec].option[sop].count}</td>
     		</tr>
     		{/section}
     		<tr><td colspan="2">答题人数:{$question[sec].times|@count}</td></tr>
     		</table>
         </div>
    </div>
    {/if}
    {/section}
    <div class="wj_t"></div>
    <a href="{url controller=AsOut action=QuestionarePdf qnnaid=$questionnaire.id}" target="_blank" style="margin-right:20px;">打印pdf</a>
    <a href="{url controller=AsOut action=QuestionareExc qnnaid=$questionnaire.id}">导出excel</a>
    <div id="answerList" class="wj_t" style="margin-top:40px;overflow-x: scroll;">
    	<table class="optionTab" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="5%">
				<col width="10%">
				<col width="5%">
    			{section name=sec loop=$question}
				<col width="15%">
    			{/section}
				<col width="7%">
			</colgroup>
             <tr>
                 <th>答题序号</th>
                 <th>答题时间</th>
                 <th>答题时长</th>
                 <th>答题内容</th>
    			{section name=sec loop=$question}
    			<th style="min-width:100px;">{$question[sec].title}</th>
    			{/section}
             </tr>
             {section name=sec loop=$answerlist}
             <tr>
                 <td style="text-align:center;"><div style="width:80px;">{$answerlist[sec].num}</div></td>
                 <td style="text-align:center;"><div style="width:90px;">{$answerlist[sec].created}</div></td>
                 <td style="text-align:center;"><div style="width:90px;">{$answerlist[sec].pass_time}</div></td>
                 <td style="text-align:center;"><div style="width:90px;"><a href="{url controller=As action=Preview qid=$answerlist[sec].id}" target="_blank">查看</a></div></td>
                 {section name=ans loop=$answerlist[sec].answer}
		             <td>
		             	<div {if $answerlist[sec].answer[ans].qetype eq '1'}style="width:100px;"
		             		{elseif $answerlist[sec].answer[ans].qetype eq '2'}style="width:100px;"
		             		{elseif $answerlist[sec].answer[ans].qetype eq '3'}style="width:400px;"
		             		{elseif $answerlist[sec].answer[ans].qetype eq '4'}style="width:400px;"
		             		{/if}>
		             	{$answerlist[sec].answer[ans].content}</div><br/>
	                 </td>
                 {/section}
             </tr>
             {/section}
         </table>
         {$page}
    </div>
    <div class="wj_m">
    	<p><a href="http://weixin.qq.com/r/fUO2rsHEh5z4rYXN9xZv" target="_blank">【Click here】</a>To add the <a href="http://weixin.qq.com/r/fUO2rsHEh5z4rYXN9xZv" target="_blank"><font color="red">“<strong>Planbook</strong>”</font></a> <strong><span class="weixin">WECHAT</span>  &nbsp;service account</strong></p>
	</div>
    <div class="wj_m">
    	<p><img src="{$smarty.const.SITE}resource/images/planbook.jpg" width="280" /></p>
    </div>
</div>

</body>
</html>
