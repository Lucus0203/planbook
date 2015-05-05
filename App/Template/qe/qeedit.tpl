<script type="text/javascript" src="{$smarty.const.SITE}resource/js/qeadd.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">编辑题库</div>
         <form action="" method="post" enctype="multipart/form-data">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="qe[id]" value="{$qe.id}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">编辑题库</td>
             </tr>
             <tr>
             	<td style="text-align:center;">分组</td>
             	<td>
             	<select name="qe[group_id]">
             		{section name=grp loop=$group}
             		<option value="{$group[grp].id}" {if $qe.group_id eq $group[grp].id}selected{/if}>{$group[grp].name}</option>
             		{/section}
             	</select>
             	</td>
             </tr>
             <tr>
             	<td style="text-align:center;">类型</td>
             	<td>
	             	<select name="qe[category_id]">
	             		<option value="">请选择</option>
	             		{section name=cat loop=$category}
	             		<option value="{$category[cat].id}" {if $qe.category_id eq $category[cat].id}selected{/if}>{$category[cat].name}</option>
	             		{/section}
	             	</select>
             	</td>
             </tr>
             <tr>
             	<td style="text-align:center;">编号</td>
             	<td>
             		<input type="text" name="qe[no]" value="{$qe.no}" />
             	</td>
             </tr>
             <tr id="addtr">
             	<td colspan="2">
             		&nbsp;&nbsp;&nbsp;&nbsp;
             		<label><input id="addSingle" type="radio" name="qe[type]" value="1" checked />单选题</label>
             		<label><input id="addMultiple" type="radio" name="qe[type]" value="2" {if $qe.type eq 2}checked{/if} />多选题</label>
             		<label><input id="addOpenness" type="radio" name="qe[type]" value="3" {if $qe.type eq 3}checked{/if} />开放性</label>
             		<label><input id="addFill" type="radio" name="qe[type]" value="4" {if $qe.type eq 4}checked{/if} />填空题</label>
             	</td>
             </tr>
             <tr class="qtr">
             	<td class="qn" style="text-align:center;">问题</td>
             	<td><input type="text" name="qe[title]" value="{$qe.title}" style="width:300px;" /></td>
             </tr>
			<tr class="atr" id="singlebox" {if $qe.type neq 1}style="display:none;"{/if}>
				<td style="text-align:center;">单选题</td>
				<td>
				{if $qe.type eq 1}
					{section name=op loop=$ops}
					{if $smarty.section.op.index neq 0}<br/>{/if}<input type="radio" name="question">&nbsp;<input type="text" name="op1[content][]" value="{$ops[op].content}" style="width:240px;" />
					{/section}
				{else}
				<input type="radio" name="question">&nbsp;<input type="text" name="op1[content][]" value="" style="width:240px;" />
				<br/><input type="radio" name="question">&nbsp;<input type="text" name="op1[content][]" value="" style="width:240px;" />
				{/if}<a href="#" class="addOption">添加选项+</a></td>
			</tr>
			<tr class="atr" id="multiplebox" {if $qe.type neq 2}style="display:none;"{/if}><td style="text-align:center;">多选题</td>
				<td>
				{if $qe.type eq 2}
					{section name=op loop=$ops}
					{if $smarty.section.op.index neq 0}<br/>{/if}<input type="checkbox">&nbsp;<input type="text" name="op2[content][]" value="{$ops[op].content}" style="width:240px;" />
					{/section}
				{else}<input type="checkbox">&nbsp;<input type="text" name="op2[content][]" value="" style="width:240px;" />
				<br/><input type="checkbox">&nbsp;<input type="text" name="op2[content][]" value="" style="width:240px;" />
				{/if}<a href="#" class="addOption">添加选项+</a></td>
			</tr>
			<tr class="atr" id="opennessbox" {if $qe.type neq 3}style="display:none;"{/if}><td style="text-align:center;">开放性问题</td>
				<td>(需要答题者填写文字的部分请用特殊符号"@text"代替,如:我认为 @text)<br/>
				{if $qe.type eq 3}
					{section name=op loop=$ops}
					{if $smarty.section.op.index neq 0}<br/>{/if}<input type="radio" name="question3">&nbsp;<input type="text" name="op3[content][]" value="{$ops[op].content}" style="width:240px;" />
					{/section}
				{else}<input type="radio" name="question3">&nbsp;<input type="text" name="op3[content][]" value="" style="width:240px;" />
				<br/><input type="radio" name="question3">&nbsp;<input type="text" name="op3[content][]" value="" style="width:240px;" />
				{/if}<a href="#" class="addOption">添加选项+</a></td></tr>
			<tr class="atr" id="fillbox" {if $qe.type neq 4}style="display:none;"{/if}><td style="text-align:center;">填空题</td>
				<td>(需要答题者填写文字的部分请用特殊符号"@text"代替,如:我认为 @text)<br/>
				<input type="text" name="op4[content][]" value="{$ops[0].content}" style="width:240px;" /></td>
			</tr>
			<tr class="atr" id="singlebox">
				<td style="text-align:center;">附件</td>
				<td>{if $qe.file neq ''}<img src="{$qe.file}" width="200"  /><br>{/if}<input type="file" name="file"></td>
			</tr>
             <tr>
                 <td colspan="2" style="text-align:center;"><input type="submit" value=" 确认修改 "></td>
             </tr>
         </table>
         </form>
 	</div>       
 </td>