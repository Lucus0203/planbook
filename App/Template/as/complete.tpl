<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" name="viewport" id="viewport" />
<meta name="format-detection" content="telephone=no"/>
<title>{$msg}</title>
<link href="{$smarty.const.SITE}resource/css/qa.css" rel="stylesheet" type="text/css">
</head>

<body>

<div class="wj_box">
	<form action="{url controller=Answer action=Submit}" method="post">
	<input type="hidden" name="act" value="submit" />
	<input type="hidden" name="questionnaire_unit" value="{$questionnaire.id}" />
	<input type="hidden" id="time" name="time" value="0" />
	<div class="wj_t">
		<h1>{$msg}</h1>
    	<p>This research is produced by </p>
    </div>
    <div class="wj_m">
    	<p><a href="http://weixin.qq.com/r/fUO2rsHEh5z4rYXN9xZv" target="_blank">【点击这里】</a>关注 <a href="http://weixin.qq.com/r/fUO2rsHEh5z4rYXN9xZv" target="_blank"><font color="red">“<strong>Planbook</strong>”</font></a> <strong><span class="weixin">微信</span>  &nbsp;服务号</strong></p>
	</div>
    <div class="wj_m">
    	<p><img src="{$smarty.const.SITE}resource/images/planbook.jpg" width="100%" /></p>
    </div>
    </form>
</div>

</body>
</html>
