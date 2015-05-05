<script type="text/javascript" src="{$smarty.const.SITE}resource/js/list.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">被删除的问卷<a style="float:right;font-size:14px;" href="{url controller=Questionnaire action=Index}">问卷列表</a></div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="Questionnaire" />
         <input type="hidden" name="action" value="Index" />
         <div class="hd_t1">查找问卷<input class="cz_input" type="text" name="title" value="{$pageparm.title}"><input class="cz_btn" type="submit" value="查找"></div>
         </form>
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="5%">
				<col width="25%">
				<col width="20%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
				<col width="">
			</colgroup>
             <tr>
             	 <th>序号</th>
                 <th>二维码</th>
                 <th>标题</th>
                 <th>提交份数</th>
                 <th>问卷链接</th>
                 <th>统计分析</th>
                 <th>问卷状态</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td>{$list[sec].id}</td>
                 <td><img src="{$list[sec].qrcode}" width="250px" /></td>
                 <td class="hd_td_l">{$list[sec].title}</td>
                 <td>{$list[sec].num}</td>
                 <td><a href="{url controller=Answer action=Index qid=$list[sec].id}" target="_blank">查看问卷</a></td>
                 <td><a class="f18" href="{url controller=Answer action=Analysis qnnaid=$list[sec].id}" target="_blank">统计分析</a></td>
                 <td>已删除</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=Questionnaire action=Recover id=$list[sec].id}">恢复</a>
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>