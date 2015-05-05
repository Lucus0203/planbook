<script type="text/javascript" src="{$smarty.const.SITE}resource/js/highcharts/highcharts.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/highcharts/modules/exporting.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/analysis.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">统计分析《{$questionnaire.title}》</div>
        <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <tr>
                 <td class="hd_ta_t">
                 <a class="left" href="{url controller=Questionnaire action=Index}">
                 	返回问卷列表
                 </a>
                 <a class="right" href="{url controller=ResultAnalysis action=List qnnaid=$questionnaire.id}">
                 	<img src="resource/images/list_icon.png" style="vertical-align:middle;" />
                 	&nbsp;答卷详情
                 </a></td>
             </tr>
            {section name=sec loop=$question}
        	<tr>
                 <td style="background-color:#fafafa;">Q{$smarty.section.sec.index+1}:{$question[sec].title}
                 <!-- {if $question[sec].type eq 1}(单选题){elseif $question[sec].type eq 2}(多选题){/if} -->
             	 <input type="hidden" id="quetitle{$smarty.section.sec.index}" value="{$question[sec].title}" />
             	 <input type="hidden" id="quetype{$smarty.section.sec.index}" value="{$question[sec].type}" />
             	 <input type="hidden" id="anscount{$smarty.section.sec.index}" value="{$questionnaire.anscount}" />
             	 </td>
            </tr>
             <tr>
                 <td style="text-align:center;">
                 	<div id="container{$smarty.section.sec.index}" style="min-width: 310px; height: 305px; max-width: 100%; margin: 0 auto"></div>
                 	<table class="optionTab" border="0" cellpadding="0" cellspacing="1" width="70%">
             		<tr><td>答案选项</td><td style="word-break:keep-all">回复情况</td></tr>
             		{section name=sop loop=$question[sec].option}
             		<tr>
             			<td class="options{$smarty.section.sec.index}">{$question[sec].option[sop].content|replace:'@text':'______'}</td>
             			<td class="counts{$smarty.section.sec.index}">{$question[sec].option[sop].count}</td>
             		</tr>
             		{/section}
             		<tr><td colspan="2">答卷人数:{$questionnaire.anscount}</td></tr>
             		</table>
                 
                 </td>
             </tr>
             {/section}
         </table>
         
 	</div>       
 </td>

