<script type="text/javascript" src="{$smarty.const.SITE}resource/js/add.js?0507"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">修改问卷</div>
 		 <p style="color:red;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="edit" />
          <input id="questionnaire_id" type="hidden" name="data[id]" value="{$questionnaire.id}" />
          <input id="questionnaire_status" type="hidden" name="data[status]" value="{$questionnaire.status}" />
          <input type="hidden" name="data[qrcode]" value="{$questionnaire.qrcode}" />
         <input id="questionnum" type="hidden" name="questionnum" value="{$question|@count}" />
         <input id="abstractnum" type="hidden" name="abstractnum" value="{$abstract|@count}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">问卷内容</td>
             </tr>
             <tr>
                 <td style="text-align:center;">标题</td>
                 <td><input id="questionnaire_title" name="data[title]" type="text" value="{$questionnaire.title}" style="width:700px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">档案号</td>
                 <td><input name="data[file_no]" type="text" value="{$questionnaire.file_no}" style="width:700px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;"><input name="data[author_subtitle]" type="text" value="{$questionnaire.author_subtitle}" style="width:50px;padding:0;" /></td>
                 <td><textarea id="author" name="data[author]" style="width:700px;height:50px;">{$questionnaire.author}</textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;"><input name="data[keywords_subtitle]" type="text" value="{$questionnaire.keywords_subtitle}" style="width:50px;padding:0;" /></td>
                 <td><textarea id="keywords" name="data[keywords]" style="width:700px;height:50px;">{$questionnaire.keywords}</textarea></td>
             </tr>
             {section name=sec loop=$abstract}
             <tr>
                 <td style="text-align:center;"><input name="abstract{$smarty.section.sec.index+1}[title]" type="text" value="{$abstract[sec].title}" style="width:50px;padding:0;" /></td>
                 <td><textarea name="abstract{$smarty.section.sec.index+1}[content]" style="width:700px;height:150px;">{$abstract[sec].content}</textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">图片{$smarty.section.sec.index+1}</td>
                 <td><input name="abstractimg{$smarty.section.sec.index+1}" type="file" style="width:700px;">{if $abstract[sec].img neq ''}<img src="{$smarty.const.SITE}{$abstract[sec].img}" />{/if}
                 	<input type="hidden" name="abstract{$smarty.section.sec.index+1}[img]" value="{$abstract[sec].img}" /></td>
             </tr>
             <tr>
                 <td style="text-align:center;">图片信息{$smarty.section.sec.index+1}</td>
                 <td><textarea name="abstract{$smarty.section.sec.index+1}[imginfo]" style="width:700px;height:50px;">{$abstract[sec].imginfo}</textarea></td>
             </tr>
             {/section}
             <tr id="addAbsTr">
             	<td colspan="2">
             		<input id="addAbstract" value="添加图文摘要" type="button" />
             	</td>
             </tr>
         </table>
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">问卷选项</td>
             </tr>
             <tr>
                 <td style="text-align:center;">问题介绍</td>
                 <td><textarea id="questionnaire_description" name="data[description]" style="width:700px;height:150px;">{$questionnaire.description}</textarea></td>
             </tr>
             
		    {section name=que loop=$question}
		    {if $question[que].type eq '1'}
		    <tr class="qtr">
		    	<td style="text-align:center;" class="qn">Q{$smarty.section.que.index+1}</td>
		    	<td><input type="text" style="width:300px;" value="{$question[que].title}" name="question{$smarty.section.que.index}[title]">
		    		<a class="delQuestion" href="#" style="float:right;">删除</a></td>
		    </tr>
		    <tr class="atr">
		    	<td style="text-align:center;">单选题<input type="hidden" value="1" name="question{$smarty.section.que.index}[type]"><br>
		    	<input type="button" class="mvDown" value="下移" /><input type="button" class="mvUp" value="上移" />
		    	</td>
		    	<td>{section name=opt loop=$question[que].option}<br>
		    		<input type="radio" name="question{$smarty.section.que.index}">&nbsp;
		    		<input type="text" style="width:240px;" value="{$question[que].option[opt].content}" name="question{$smarty.section.que.index}[content][]">
		    		{/section}
		    		<a class="addOption" href="#">添加选项+</a></td>
		    </tr>
		    {elseif $question[que].type eq '2'}
		    <tr class="qtr">
		    	<td style="text-align:center;" class="qn">Q{$smarty.section.que.index+1}</td>
		    	<td><input type="text" style="width:300px;" value="{$question[que].title}" name="question{$smarty.section.que.index}[title]">
		    		<a class="delQuestion" href="#" style="float:right;">删除</a></td></tr>
		    <tr class="atr">
		    	<td style="text-align:center;">多选题<input type="hidden" value="2" name="question{$smarty.section.que.index}[type]"><br>
		    	<input type="button" class="mvDown" value="下移" /><input type="button" class="mvUp" value="上移" /></td>
		    	<td>{section name=opt loop=$question[que].option}<br>
		    		<input type="checkbox">&nbsp;<input type="text" style="width:240px;" value="{$question[que].option[opt].content}" name="question{$smarty.section.que.index}[content][]">
					{/section}
					<a class="addOption" href="#">添加选项+</a></td>
			</tr>
		    {elseif $question[que].type eq '3'}
		    <tr class="qtr">
		    	<td style="text-align:center;" class="qn">Q{$smarty.section.que.index+1}</td>
		    	<td><input type="text" style="width:300px;" value="{$question[que].title}" name="question{$smarty.section.que.index}[title]">
		    		<a class="delQuestion" href="#" style="float:right;">删除</a></td></tr>
		    <tr class="atr">
		    	<td style="text-align:center;">开放性问题<input type="hidden" value="3" name="question{$smarty.section.que.index}[type]"><br>
		    	<input type="button" class="mvDown" value="下移" /><input type="button" class="mvUp" value="上移" /></td>
		    	<td>(需要答题者填写文字的部分请用特殊符号"@text"代替,如:我认为@text)
					{section name=opt loop=$question[que].option}<br>
					<input type="radio" name="question{$smarty.section.que.index}">&nbsp;
					<input type="text" style="width:240px;" value="{$question[que].option[opt].content}" name="question{$smarty.section.que.index}[content][]">
					{/section}
					<a class="addOption" href="#">添加选项+</a></td>
			</tr>
			{elseif $question[que].type eq '4'}
		    <tr class="qtr">
		    	<td style="text-align:center;" class="qn">Q{$smarty.section.que.index+1}</td>
		    	<td><input type="text" style="width:300px;" value="{$question[que].title}" name="question{$smarty.section.que.index}[title]">
		    		<a class="delQuestion" href="#" style="float:right;">删除</a></td></tr>
		    <tr class="atr">
		    	<td style="text-align:center;">填空题<input type="hidden" value="3" name="question{$smarty.section.que.index}[type]"><br>
		    	<input type="button" class="mvDown" value="下移" /><input type="button" class="mvUp" value="上移" /></td>
		    	<td>(需要答题者填写文字的部分请用特殊符号"@text"代替,如:我认为@text)
					{section name=opt loop=$question[que].option}<br>
					<input type="text" style="width:240px;" value="{$question[que].option[opt].content}" name="question{$smarty.section.que.index}[content][]">
					{/section}</td>
			</tr>
		    {/if}
		    {/section}
    
             <tr id="addtr">
             	<td colspan="2">
             		<input id="addSingle" value="添加单选" type="button" />
             		<input id="addMultiple" value="添加多选" type="button" />
             		<input id="addOpenness" value="添加开放性" type="button" />
             		<input id="addFill" value="添加填空" type="button" />
             	</td>
             </tr>
             <tr>
                 <td colspan="2" style="text-align:center;"><input type="submit" value=" 确定修改问卷 "></td>
             </tr>
         </table>
         </form>
 	</div>       
 </td>