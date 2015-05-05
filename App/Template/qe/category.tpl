<script type="text/javascript" src="{$smarty.const.SITE}resource/js/category.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">题库类型</div>
 		 <p style="color:red;">{$msg}</p>
         <form action="" method="post" >
         <input type="hidden" name="act" value="add" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">题库类型</td>
             </tr>
             {section name=sec loop=$category}
             <tr>
                 <td style="text-align:center;">{$category[sec].id}<input type="hidden" name="ids[]" value="{$category[sec].id}" /></td>
                 <td><input type="text" name="name[]" value="{$category[sec].name}" /><a class="delgroup" href="{url controller=Qe action=DelCategory id=$category[sec].id}">删除</a></td>
             </tr>
             {/section}
             <tr>
                 <td style="text-align:center;">类型名</td>
                 <td><input type="text" name="name[]" /><a class="del" href="#">删除</a></td>
             </tr>
             <tr id="addtr">
             	<td colspan="2">
             		<input id="addGroup" value="添加" type="button" />
             	</td>
             </tr>
             <tr>
                 <td colspan="2" style="text-align:center;"><input type="submit" value=" 确定 "></td>
             </tr>
         </table>
         </form>
 	</div>       
 </td>