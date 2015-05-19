<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="width=device-width, initial-scale= 1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" name="viewport" id="viewport" />
<meta name="format-detection" content="telephone=no"/>
<title>{$questionnaire.title}</title>
<link href="{$smarty.const.SITE}resource/css/qa.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/jQuery.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/as.js?0507"></script>
</head>

<body>

<div class="wj_box">
	<form action="{url controller=As action=Submit}" method="post">
	<input type="hidden" name="act" value="submit" />
	<input type="hidden" name="questionnaire_unit" value="{$questionnaire.id}" />
	<input type="hidden" id="questionnaire_status" value="{$questionnaire.status}" />
	<input type="hidden" name="quesnum" id="quesnum" value="1" />
	<input type="hidden" id="time" name="time" value="0" />
	<div class="wj_t">
		<h1>{$questionnaire.title}</h1>
    	{if $questionnaire.author neq ''}<p>{$questionnaire.author|replace:" ":"&nbsp;"|nl2br}</p>{/if}
    	{if $questionnaire.keywords neq ''}<p><span class="keyword">{$questionnaire.keywords_subtitle}:</span>{$questionnaire.keywords|replace:" ":"&nbsp;"|nl2br}</p>{/if}
    	{if $abstract[0].content neq ''}
	    	{section name=abs loop=$abstract}
	    	<h4>{$abstract[abs].title}:</h4>
	    	<p>{$abstract[abs].content|replace:" ":"&nbsp;"|nl2br}</p>
	    	{if $abstract[abs].img neq ''}
	    	<a href="{$smarty.const.SITE}{$abstract[abs].img}"><img src="{$smarty.const.SITE}{$abstract[abs].img}" /></a>
	    	{/if}
	    	<p class="imginfo">{$abstract[abs].imginfo|replace:" ":"&nbsp;"|nl2br}</p>
	    	{/section}
    	{/if}
    </div>
    <div class="wj_m"><p>{$questionnaire.description}</p></div>
    <div class="wj_m question_list">
    	<p>Q1. {$question.title}
    		{if $question.file neq ''}<br><img src="{$question.file}" />{/if}
    		<input type="hidden" name="questionid0" value="{$question.id}"  />
    	</p>
        <div class="wj_mm ansqe">
        <input class="qetype" type="hidden" value="{$question.type}" />
        	{section name=opt loop=$question.option}
        		<label>{if $question.type eq '1'}<input type="radio" name="check0" value="{$question.option[opt].id}">
        		{elseif $question.type eq '2'}<input type="checkbox" name="check0[]" value="{$question.option[opt].id}">
        		{elseif $question.type eq '3'}<input type="radio" name="check0" value="{$question.option[opt].id}">
        		{elseif $question.type eq '4'}<input type="hidden" name="check0" value="{$question.option[opt].id}">
        		{/if}
        			{$question.option[opt].content|replace:'@text':'<br><input type="text" class="anstext" value="" style="font-size:16px;width:80%;height:30px;" /><br>'}<input type="hidden" name="anscontent{$question.option[opt].id}" value="" /></label>
        	{/section}
        </div>
    </div>
    <div id="qepath" style="display:none;">
	    {section name=sec loop=$qes}
	   	<ul class="step">
	   		<li id="qestion_id{$qes[sec].question.id}">
	   			<input type="hidden" value="{$qes[sec].question.id}" class="qeid" />
	   			<input type="hidden" value="{$qes[sec].probability}" class="probability" />
	   			<input type="hidden" value="{$qes[sec].aslimit}" class="aslimit" />
	   			<input type="hidden" value="{$qes[sec].flag_over}" class="flag_over" />
	   			<input type="hidden" value="{$qes[sec].question.title}" class="qetitle" />
	   			<input type="hidden" value="{$qes[sec].question.type}" class="qetype" />
	   			<input type="hidden" value="{$qes[sec].question.file}" class="qefile" />
	   			{section name=oec loop=$qes[sec].question.option}
	   			<input type="hidden" value="{$qes[sec].question.option[oec].id}" class="opid" />
	   			<input type="hidden" value="{$qes[sec].question.option[oec].content}" class="opcontent" />
	   			{/section}
	   		</li>
	   	</ul>
	    {/section}
	    <ul class="qecon">
	    {section name=qcn loop=$qecon}
	    	<li>
	   			<input type="hidden" value="{$qecon[qcn].prevqe_id}" class="qecon_prevqe_id" />
	   			<input type="hidden" value="{$qecon[qcn].prevop_id}" class="qecon_prevop_id" />
	   			<input type="hidden" value="{$qecon[qcn].qe_id}" class="qecon_qe_id" />
	   		</li>
	    {/section}
	    </ul>
	    <ul class="questall">
	   		{section name=allec loop=$qesall}
	   		<li id="questall_id{$qesall[allec].question.id}">
	   			<input type="hidden" value="{$qesall[allec].question.id}" class="qeid" />
	   			<input type="hidden" value="{$qesall[allec].probability}" class="probability" />
	   			<input type="hidden" value="{$qesall[allec].aslimit}" class="aslimit" />
	   			<input type="hidden" value="{$qesall[allec].flag_over}" class="flag_over" />
	   			<input type="hidden" value="{$qesall[allec].question.title}" class="qetitle" />
	   			<input type="hidden" value="{$qesall[allec].question.type}" class="qetype" />
	   			<input type="hidden" value="{$qesall[allec].question.file}" class="qefile" />
	   			{section name=oec loop=$qesall[allec].question.option}
	   			<input type="hidden" value="{$qesall[allec].question.option[oec].id}" class="opid" />
	   			<input type="hidden" value="{$qesall[allec].question.option[oec].content}" class="opcontent" />
	   			{/section}
	   		</li>
	   		{/section}
	    </ul>
	</div>
    <div class="wj_btn">
    	<input class="wj_in_btn" type="submit" id="submit" value="中止答题并提交">
    </div>
    </form>
    <div class="wj_t"></div>
    
    {if $signinfo.sign_text neq ''}
    	<p>{$signinfo.sign_text|replace:" ":"&nbsp;"|nl2br}</p>
    {else}
	    <p><a href="{$smarty.const.SITE}{url controller=Answer action=Index qid=$questionnaire.id}" id="copyQurl">复制问卷链接</a></p>
	    <div class="wj_m">
	    	<p>1.长按二维码，自动识别关注 <font color="red">“<strong>繁昌县规划局</strong>”</font> <strong><span class="weixin">微信</span>  &nbsp;服务号</strong></p>
	    	<p>2.在<font color="red">微信公众号</font>中，搜索<font color="red">“<strong>繁昌县规划局</strong>”</font>关注。</p>
		</div>
    {/if}
    <div class="wj_m">
    	{if $signinfo.sign_img neq ''}
    	<img src="{$smarty.const.SITE}{$signinfo.sign_img}" width="100%" />
    	{else}
    	<p><img src="{$smarty.const.SITE}resource/images/planbook.jpg" width="100%" /></p>
    	{/if}
    </div>
</div>

</body>
</html>
