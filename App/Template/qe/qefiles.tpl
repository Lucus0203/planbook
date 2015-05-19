<script type="text/javascript" src="{$smarty.const.SITE}resource/js/qelist.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">题库附件列表</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="Qe" />
         <input type="hidden" name="action" value="QeFile" />
         <div class="hd_t1">查找题库<input class="cz_input" type="text" name="keyword" value="{$pageparm.keyword}"><input class="cz_btn" type="submit" value="查找"></div>
         </form>
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="20%">
				<col width="20%">
				<col width="20%">
				<col width="20%">
				<col width="5%">
				<col width="5%">
				<col width="20%">
			</colgroup>
             <tr>
                 <th>分组</th>
                 <th>附件</th>
                 <th>问题</th>
                 <th>选项</th>
                 <th>答题数</th>
                 <th>频率</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td class="hd_td_l">{$list[sec].group_name}</td>
                 <td>{if $list[sec].file neq ''}<img src="{$list[sec].file}" width="200" />{/if}</td>
                 <td class="hd_td_l">{$list[sec].title}</td>
                 <td class="hd_td_l">{section name=op loop=$list[sec].ops}
                 	{$list[sec].ops[op].content}<br/>
                 	{/section}</td>
                 <td>{$list[sec].times}</td>
                 <td>{$list[sec].rate}%</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=Qe action=QeEdit qeid=$list[sec].id}">编辑题库</a>
                 	<a class="delBtn" href="{url controller=Qe action=QeDelFile qeid=$list[sec].id}">删除附件</a>
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>