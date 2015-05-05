<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" name="viewport" id="viewport" />
<meta name="format-detection" content="telephone=no"/>
<title>{$questionnaire.title}</title>
<link href="{$smarty.const.SITE}resource/css/qa.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/jQuery.js"></script>
</head>

<body>

<div class="wj_box">
	<div class="wj_t">
		<h1>{$questionnaire.title}</h1>
		<table>
		<tr><td>答题序号：{$answer.num}</td><td>用 户 IP：{$answer.ip}</td><td>答题时长：{$answer.pass_time}</td><td>答题时间：{$answer.created}</td></tr>
		</table>
    </div>
    {section name=que loop=$question}
    <div class="wj_m">
    	<p>Q{$smarty.section.que.index+1}. {$question[que].title}
    		<input type="hidden" name="questionid{$smarty.section.que.index}" value="{$question[que].id}"  />
    	</p>
        <div class="wj_mm">
        	回答:<br/>
        	<label>{$question[que].answer}</label><br>
        </div>
    </div>
    {/section}
    <div class="wj_m">
    	<p><img src="{$smarty.const.SITE}resource/images/planbook.jpg" width="280" /></p>
    </div>
</div>

</body>
</html>
