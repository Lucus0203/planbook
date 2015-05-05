<script type="text/javascript" src="{$smarty.const.SITE}resource/js/list.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">被删除的调查项目列表<a style="float:right;font-size:14px;" href="{url controller=Qs action=List}">调查咨询项目列表</a></div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="Qs" />
         <input type="hidden" name="action" value="List" />
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
                 <td>{if $list[sec].status eq '0'}未发布{elseif $list[sec].status eq '1'}收集数据中{elseif $list[sec].status eq '2'}结束{/if}</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=Qs action=Edit qnnaid=$list[sec].id}" style="margin-right:25px;">编辑</a><a class="delBtn" href="{url controller=Qs action=Del id=$list[sec].id}">删除</a><br/><br/>
                 	<a href="{url controller=Qs action=RePublic qnnaid=$list[sec].id}">复制一份新问卷</a><br/><br/>
                 	{if $list[sec].status eq '0'}<a class="pubBtn f18" href="{url controller=Qs action=Public id=$list[sec].id}">发布</a>
                 	{elseif $list[sec].status eq '1'}<a class="overBtn f18" href="{url controller=Qs action=End id=$list[sec].id}">结束</a>
                 	{elseif $list[sec].status eq '2'}<a class="f18" href="{url controller=ResultAnalysis action=List qnnaid=$list[sec].id}">答卷详情</a>{/if}
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>