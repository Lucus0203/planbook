<script type="text/javascript" src="{$smarty.const.SITE}resource/js/qeadd.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">添加题库</div>
         <form action="" method="post" enctype="multipart/form-data">
         <input type="hidden" name="act" value="add" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">添加题库</td>
             </tr>
             <tr>
             	<td style="text-align:center;">分组</td>
             	<td>
             	<select name="qe[group_id]">
             		{section name=grp loop=$group}
             		<option value="{$group[grp].id}">{$group[grp].name}</option>
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
	             		<option value="{$category[cat].id}">{$category[cat].name}</option>
	             		{/section}
	             	</select>
             	</td>
             </tr>
             <tr>
             	<td style="text-align:center;">编号</td>
             	<td>
             		<input type="text" name="qe[no]" value="" />
             	</td>
             </tr>
             <tr id="addtr">
             	<td colspan="2">
             		&nbsp;&nbsp;&nbsp;&nbsp;
             		<label><input id="addSingle" type="radio" name="qe[type]" value="1" checked />单选题</label>
             		<label><input id="addMultiple" type="radio" name="qe[type]" value="2" />多选题</label>
             		<label><input id="addOpenness" type="radio" name="qe[type]" value="3" />开放性</label>
             		<label><input id="addFill" type="radio" name="qe[type]" value="4" />填空题</label>
             	</td>
             </tr>
             <tr class="qtr">
             	<td class="qn" style="text-align:center;">问题</td>
             	<td><input type="text" name="qe[title]" value="" style="width:300px;" /></td>
             </tr>
			<tr class="atr" id="singlebox">
				<td style="text-align:center;">单选题</td>
				<td><input type="radio" name="question">&nbsp;<input type="text" name="op1[content][]" value="" style="width:240px;" />
				<br/><input type="radio" name="question">&nbsp;<input type="text" name="op1[content][]" value="" style="width:240px;" />
				<a href="#" class="addOption">添加选项+</a></td>
			</tr>
			<tr class="atr" id="multiplebox" style="display:none;"><td style="text-align:center;">多选题</td>
				<td><input type="checkbox">&nbsp;<input type="text" name="op2[content][]" value="" style="width:240px;" />
				<br/><input type="checkbox">&nbsp;<input type="text" name="op2[content][]" value="" style="width:240px;" />
				<a href="#" class="addOption">添加选项+</a></td>
			</tr>
			<tr class="atr" id="opennessbox" style="display:none;"><td style="text-align:center;">开放性问题</td>
				<td>(需要答题者填写文字的部分请用特殊符号"@text"代替,如:我认为 @text)<br/>
				<input type="radio" name="question3">&nbsp;<input type="text" name="op3[content][]" value="" style="width:240px;" />
				<br/><input type="radio" name="question3">&nbsp;<input type="text" name="op3[content][]" value="" style="width:240px;" />
				<a href="#" class="addOption">添加选项+</a></td>
			</tr>
			<tr class="atr" id="fillbox" style="display:none;"><td style="text-align:center;">填空题</td>
				<td>(需要答题者填写文字的部分请用特殊符号"@text"代替,如:我认为 @text)<br/>
				<input type="text" name="op4[content][]" value="" style="width:240px;" /></td>
			</tr>
			<tr class="atr" id="singlebox">
				<td style="text-align:center;">附件</td>
				<td><input type="file" name="file"></td>
			</tr>
             <tr>
                 <td colspan="2" style="text-align:center;"><input type="submit" value=" 添加问题 "></td>
             </tr>
         </table>
         </form>
 	</div>       
 </td>