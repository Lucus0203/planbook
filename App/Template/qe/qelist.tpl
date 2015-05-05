<script type="text/javascript" src="{$smarty.const.SITE}resource/js/qelist.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">题库列表</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="Qe" />
         <input type="hidden" name="action" value="QeList" />
         <div class="hd_t1">
         <select name="group" style="font-size:14px;">
         	<option value="">选择分组</option>
         	{section name=gec loop=$group}
         	<option value="{$group[gec].id}" {if $pageparm.group eq $group[gec].id}selected{/if}>{$group[gec].name}</option>
         	{/section}
         </select>
         <select name="category" style="font-size:14px;">
         	<option value="">选择类型</option>
         	{section name=cec loop=$category}
         	<option value="{$category[cec].id}" {if $pageparm.category eq $category[cec].id}selected{/if}>{$category[cec].name}</option>
         	{/section}
         </select>
         标题<input class="cz_input" type="text" name="keyword" value="{$pageparm.keyword}"><input class="cz_btn" type="submit" value="查找">
         </div>
         </form>
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="5%">
				<col width="10%">
				<col width="20%">
				<col width="20%">
				<col width="7%">
				<col width="7%">
			</colgroup>
             <tr>
                 <th>分组</th>
             	 <th>编号</th>
                 <th>问题</th>
                 <th>选项</th>
                 <th>类型</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td>{$list[sec].group_name}</td>
                 <td>{$list[sec].no}</td>
                 <td class="hd_td_l">{$list[sec].title}</td>
                 <td class="hd_td_l">
                 	{section name=op loop=$list[sec].ops}
                 	{$list[sec].ops[op].content}<br/>
                 	{/section}
                 </td>
                 <td>{if $list[sec].type eq 1}单选{elseif $list[sec].type eq 2}多选{elseif $list[sec].type eq 3}开放型{elseif $list[sec].type eq 4}填空题{/if}</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=Qe action=QeEdit qeid=$list[sec].id}">编辑</a>
                 	<a class="delBtn" href="{url controller=Qe action=QeDel id=$list[sec].id}">删除</a>
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>