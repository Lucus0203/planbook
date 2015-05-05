<script type="text/javascript" src="{$smarty.const.SITE}resource/js/add.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">发起演示文稿</div>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="add" />
         <input id="questionnum" type="hidden" name="questionnum" value="0" />
         <input id="abstractnum" type="hidden" name="abstractnum" value="1" />
         <table style="display:none;" class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">问卷收集设置</td>
             </tr>
             <tr>
                 <td style="text-align:center;">回答设置</td>
                 <td>
                 	<input type="checkbox" name="data[isonceip]" value="1" />&nbsp;每个IP只能答一次<br/>
                 	<input type="checkbox" name="data[ispassword]" value="1" />&nbsp;启用密码访问<input type="text" name="data[password]" value="" />
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">何时结束</td>
                 <td><input type="checkbox" />&nbsp;收集<input class="cz_input" style="width:30px;" type="text" name="data[over_num]" value="{$data.over_num}">份数据时结束<br/>
                 	 <input type="checkbox" />&nbsp;到<input class="cz_input" type="text" name="data[over_date]" value="{$data.over_date}">日结束
                 </td>
             </tr>
         </table>
         
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">问卷内容</td>
             </tr>
             <tr>
                 <td style="text-align:center;">标题</td>
                 <td><input id="questionnaire_title" name="data[title]" type="text" value="" style="width:700px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">作者</td>
                 <td><textarea id="author" name="data[author]" style="width:700px;height:50px;"></textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">关键词</td>
                 <td><textarea id="keywords" name="data[keywords]" style="width:700px;height:50px;"></textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">摘要1</td>
                 <td><textarea name="abstract1[content]" style="width:700px;height:150px;"></textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">图片1</td>
                 <td><input name="abstractimg1" type="file" style="width:700px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">图片信息1</td>
                 <td><textarea name="abstract1[imginfo]" style="width:700px;height:50px;"></textarea></td>
             </tr>
             <tr id="addAbsTr">
             	<td colspan="2">
             		<input id="addAbstract" value="添加图文摘要" type="button" />
             	</td>
             </tr>
         </table>
         <table style="margin-top:20px" class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">问卷选项</td>
             </tr>
             <tr>
                 <td style="text-align:center;">问题介绍</td>
                 <td><textarea id="questionnaire_description" name="data[description]" style="width:700px;height:150px;">{$data.description}</textarea></td>
             </tr>
             <tr id="addtr">
             	<td colspan="2">
             		<input id="addSingle" value="添加单选" type="button" />
             		<input id="addMultiple" value="添加多选" type="button" />
             		<input id="addOpenness" value="添加开放性" type="button" />
             		<input id="addFill" value="添加填空" type="button" />
             	</td>
             </tr>
             <tr>
                 <td colspan="2" style="text-align:center;"><input type="submit" value=" 确定发起问卷 "></td>
             </tr>
         </table>
         </form>
 	</div>       
 </td>