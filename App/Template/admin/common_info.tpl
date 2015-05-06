<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">管理问卷公共信息</div>
         <p style="text-align:left;margin-left:20px;color:red;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data" >
         <input type="hidden" name="act" value="update" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td style="text-align:center;">问卷结束答题信息</td>
                 <td><textarea name="complete_msg" style="width:500px;">{$admin.complete_msg}</textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">问卷签名文字信息</td>
                 <td><textarea name="sign_text" style="width:500px;height:100px;">{$admin.sign_text}</textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">问卷签名图</td>
                 <td><input name="file" type="file">
                 	{if $admin.sign_img neq ''}
                 	<br/><img src="{$admin.sign_img}" height="200" />
                 	{/if}
                 </td>
             </tr>
             <tr>
                 <td colspan="2" style="text-align:center;"><input type="submit" value=" 确认修改 "></td>
             </tr>
         </table>
         </form>
 	</div>       
 </td>