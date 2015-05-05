<script type="text/javascript" src="{$smarty.const.SITE}resource/js/list.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">答卷详情《{$questionnaire.title}》</div>
         <div class="hd_t1">
         	<a href="{url controller=Questionnaire action=Index}">返回问卷列表</a>
         </div>
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="20%">
				<col width="20%">
				<col width="20%">
				<col width="20%">
				<col width="10%">
				<col width="">
			</colgroup>
             <tr>
                 <th>答题序号</th>
                 <th>答题时间</th>
                 <th>答题时长</th>
                 <th>答题内容</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td>{$list[sec].num}</td>
                 <td>{$list[sec].created}</td>
                 <td>{$list[sec].pass_time}</td>
                 <td><a href="{url controller=Answer action=Preview qid=$list[sec].id}" target="_blank">查看</a></td>
                 <td style="word-break:keep-all;">
                 	<a class="delBtn" href="{url controller=ResultAnalysis action=DelAnswer id=$list[sec].id}">删除</a>
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>