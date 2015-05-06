<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="width=device-width, initial-scale= 1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" name="viewport" id="viewport" />
<meta name="format-detection" content="telephone=no"/>
<title>{$questionnaire.title}</title>
<link href="{$smarty.const.SITE}resource/css/qa.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/jQuery.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/qa.js"></script>
</head>

<body>

<div class="wj_box">
	<form action="{url controller=Answer action=Submit}" method="post">
	<input type="hidden" name="act" value="submit" />
	<input type="hidden" name="questionnaire_unit" value="{$questionnaire.id}" />
	<input type="hidden" id="questionnaire_status" value="{$questionnaire.status}" />
	<input type="hidden" id="time" name="time" value="0" />
	<div class="wj_t">
		<h1>{$questionnaire.title}</h1>
    	{if $questionnaire.author neq ''}<p>{$questionnaire.author|replace:" ":"&nbsp;"|nl2br}</p>{/if}
    	{if $questionnaire.keywords neq ''}<p><span class="keyword">关键词:</span>{$questionnaire.keywords|replace:" ":"&nbsp;"|nl2br}</p>{/if}
    	{if $abstract[0].content neq ''}
	    	<h4>摘要:</h4>
	    	{section name=abs loop=$abstract}
	    	<p>{$abstract[abs].content|replace:" ":"&nbsp;"|nl2br}</p>
	    	{if $abstract[abs].img neq ''}
	    	<a href="{$smarty.const.SITE}{$abstract[abs].img}"><img src="{$smarty.const.SITE}{$abstract[abs].img}" /></a>
	    	{/if}
	    	<p class="imginfo">{$abstract[abs].imginfo|replace:" ":"&nbsp;"|nl2br}</p>
	    	{/section}
    	{/if}
    </div>
    <div class="wj_m"><p>{$questionnaire.description}</p></div>
    {section name=que loop=$question}
    <div class="wj_m">
    	<p>Q{$smarty.section.que.index+1}. {$question[que].title}
    		<!-- {if $question[que].type eq 1}[单选题]{elseif $question[que].type eq 2}[多选题]{elseif $question[que].type eq 3}[开放性问题]{/if} -->
    		<input type="hidden" name="questionid{$smarty.section.que.index}" value="{$question[que].id}"  />
    	</p>
        <div class="wj_mm">
        <input class="qetype" type="hidden" value="{$question[que].type}" />
        	{section name=opt loop=$question[que].option}
        		<label>{if $question[que].type eq '1'}<input type="radio" name="check{$smarty.section.que.index}" value="{$question[que].option[opt].id}">
        		{elseif $question[que].type eq '2'}<input type="checkbox" name="check{$smarty.section.que.index}[]" value="{$question[que].option[opt].id}">
        		{elseif $question[que].type eq '3'}<input type="radio" name="check{$smarty.section.que.index}" value="{$question[que].option[opt].id}">
        		{elseif $question[que].type eq '4'}<input type="hidden" name="check{$smarty.section.que.index}" value="{$question[que].option[opt].id}">
        		{/if}
        			{$question[que].option[opt].content|replace:'@text':'<br><input type="text" class="anstext" value="" style="font-size:16px;width:80%;height:30px;" /><br>'}<input type="hidden" name="anscontent{$question[que].option[opt].id}" value="" /></label>
        	{/section}
        </div>
    </div>
    {/section}
    
    <div class="wj_btn">
    	<input class="wj_in_btn" type="submit" id="submit" value="答题结束并提交">
    </div>
    </form>
    <div class="wj_t"></div>
    
    <p><a href="{$smarty.const.SITE}{url controller=Answer action=Index qid=$questionnaire.id}" id="copyQurl">复制问卷链接</a></p>
    <div class="wj_m">
    	<p>1.长按二维码，自动识别关注 <font color="red">“<strong>繁昌县规划局</strong>”</font> <strong><span class="weixin">微信</span>  &nbsp;服务号</strong></p>
    	<p>2.在<font color="red">微信公众号</font>中，搜索<font color="red">“<strong>繁昌县规划局</strong>”</font>关注。</p>
	</div>
    <div class="wj_m">
    	<p><img src="{$smarty.const.SITE}resource/images/fanchanglogo.jpg" width="100%" /></p>
    </div>
</div>

</body>
</html>
