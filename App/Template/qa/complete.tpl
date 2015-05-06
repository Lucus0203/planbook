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
    </div>
    <div class="wj_m">
    	<p>1.长按二维码，自动识别关注 <font color="red">“<strong>繁昌县规划局</strong>”</font> <strong><span class="weixin">微信</span>  &nbsp;服务号</strong></p>
    	<p>2.在<font color="red">微信公众号</font>中，搜索<font color="red">“<strong>繁昌县规划局</strong>”</font>关注。</p>
	</div>
    <div class="wj_m">
    	<p><img src="{$smarty.const.SITE}resource/images/fanchanglogo.jpg" width="100%" /></p>
    </div>
    </form>
</div>

</body>
</html>
