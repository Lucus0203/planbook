<script type="text/javascript" src="{$smarty.const.SITE}resource/js/qsadd.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">发起专业问卷调查</div>
         <form action="" enctype="multipart/form-data" method="post" >
         <input type="hidden" name="act" value="add" />
         <input type="hidden" class="selectedstep" value="0" />
         <input id="abstractnum" type="hidden" name="abstractnum" value="1" />
         <input id="stepnum" type="hidden" name="stepnum" value="1" />
         <input type="hidden" id="getQeByGroupUrl" value="{url controller=Qs action=GetQeByGroup}" />
         <input type="hidden" id="getOpsByQeUrl" value="{url controller=Qs action=GetOpsByQe}" />
         <input type="hidden" id="getBeforeConUrl" value="{url controller=Qs action=GetBeforeCon}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">问卷内容</td>
             </tr>
             <tr>
                 <td style="text-align:center;">标题</td>
                 <td><input id="qs_title" name="data[title]" type="text" value="" style="width:700px;"></td>
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
         <table style="margin-top:20px;" class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">问卷选项</td>
             </tr>
             <tr>
                 <td colspan="2">
		        		<div class="main_ta_box">
		                   <div class="tk">
		                   	<div class="tk_l">
		                      		<div class="tk_lm tk_lmon">
		                           	<ul></ul>
		                               <div class="cls"></div>
		                           </div>
		                           <div class="tk_lm wx">
		                           	<ul></ul>
		                               <div class="cls"></div>
		                           </div>
		                           <input type="button" id="addpath" value="添加" />
		                       </div>
		                       <div class="tk_r">
		                       	{section name=gec loop=$groups}
		                       	<h1>
		                       	{$groups[gec].name}
		                       	<select class="searchCategory" style="font-size:14px;padding:0;">
		                       		<option value="">类型筛选</option>
		                       		{section name=cat loop=$category}
		                       		<option value="{$category[cat].id}">{$category[cat].name}</option>
		                       		{/section}
		                       	</select>
		                       	<input type="text" class="searchTitle" style="width:100px;padding:0;" />
		                       	<input class="searchQeBtn" type="button" value="查找" >
		                       	<input class="searchAll" type="button" value="全部" >
		                       	</h1>
		                       		<div class="tk_rm1">
			                           	<ul>
		                           		{section name=qec loop=$groups[gec].qes}
		                               	<li class="icon{$groups[gec].id mod 7} tooltip" title="{$groups[gec].qes[qec].title}">
		                               		{$groups[gec].qes[qec].no}
		                               		<input type="hidden" class="qeid" value="{$groups[gec].qes[qec].id}" />
		                               		<input type="hidden" class="qetitle" value="{$groups[gec].qes[qec].title}" />
		                               		<input type="hidden" class="qetype" value="{$groups[gec].qes[qec].type}" />
		                               		<input type="hidden" class="no" value="{$groups[gec].qes[qec].no}" />
		                               		<input type="hidden" class="qecate" value="{$groups[gec].qes[qec].category_id}" />
		                               	</li>
		                               	{/section}
		                               </ul>
	                               <div class="cls"></div>
	                           </div>
	                           {/section}
		                       </div>
		                       <div class="cls"></div>
		                   </div>
		           	</div>
<!--弹出框-->
 	<div class="tck_box" style="display:none;">
	<div class="tck_box_bg"></div>
	<div class="sxhf">
    	<p><a id="tck_close" href="javascript:void(0);">X</a></p>
        <div class="cls"></div>
        <div class="sxhf_m">
        	<table border="0" cellpadding="0" cellspacing="1">
            	<tr>
                	<td class="td_right">问题</td>
                    <td id="tc_qeTitle"></td>
                </tr>
                <tr>
                	<td class="td_right">单选题</td>
                    <td>
                    	<div class="dxt">
                        	<ul id="tc_ulops">
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                	<td class="td_right">前置条件</td>
                    <td class="qeBeforeCon">
                    </td>
                </tr>
                <tr>
                	<td class="td_right">概率系数(整数)</td>
                    <td><input type="text" class="qeProbability" value="1">无前置条件时有效</td>
                </tr>
                <tr>
                	<td class="td_right">答案上限</td>
                    <td><input type="text" class="qeAslimit" value="1">多选题有效</td>
                </tr>
                <tr>
                	<td class="td_right">触发结束</td>
                    <td><label><input type="radio" class="qeFlagOver" name="flag_over" value="2" checked >否</label><label><input type="radio" class="qeFlagOver" name="flag_over" value="1" >是</label></td>
                </tr>
                <tr>
                    <td colspan="2" class="tck_btn"><input id="addCondition" type="button" value="添加条件" /><input id="subCondition" type="button" value="确定"><input type="button" id="removeQe" value="移除"></td>
                </tr>
            </table>
        </div>
    </div>
</div>      
                 </td>
             </tr>
             <tr>
                 <td colspan="2" style="text-align:center;"><input type="submit" value=" 确定发起问卷 "></td>
             </tr>
         </table>
         </form>
 	</div> 

 </td>