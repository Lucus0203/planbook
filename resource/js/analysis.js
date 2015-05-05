$(function () {
	$('div[id^=container]').each(function(i){
		var title='';//$('#quetitle'+i).val();
		var anscount=$('#anscount'+i).val();
		var subtitle='答卷人数:'+anscount;
		if($('#quetype'+i).val()==1){
			var option=createOption(i,title,subtitle,anscount,'pie');
		}else if($('#quetype'+i).val()==2){
			var option=createOption(i,title,subtitle,anscount,'bar');
		}else if($('#quetype'+i).val()==3){
			var option=createOption(i,title,subtitle,anscount,'bar');
		}
		$(this).highcharts(option);
	});
});

//构造option i第几个,标题,副标题,图形类型
function createOption(i,title,subtitle,anscount,type){
	var option = (type=='pie') ? getPieOption() : getBarOption();
	option.title.text=title;
	option.subtitle.text=subtitle;
	option.chart.type=type;
	var optdata=new Array();
	$('.options'+i).each(function(opi){
		var opttitle=$(this).text().toString();
		var optcount=$('.counts'+i).eq(opi).text().toString()*1;
		if(type=='pie'){
			optcount = optcount/anscount*100;
			optdata[opi]=[opttitle, optcount];
		}else if(type=='bar'){
			option.xAxis.categories[opi]=opttitle;
			optdata[opi]=optcount;
		}
	});
	option.series[0]={
        name: '选中人数',
        data: optdata
    };
	return option;
}
//饼形图
function getPieOption(){
	var option={
	        chart: {
	            plotBackgroundColor: null,
	            plotBorderWidth: null,//null,
	            plotShadow: false
	        },
	        colors:['#22b5c3', '#a3be57', '#ff9c9c', '#48cfef', '#25bf6e', '#ea5f35', '#7e85e0', '#f2bd7c', '#bbbbba', '#7257a2'],
	        title: {
	            text: '选项标题',
	            style:{
	                fontFamily:'Microsoft YaHei'
	            }
	        },
	        subtitle: {
	            text: '副标题',
	            style:{
	                fontFamily:'Microsoft YaHei'
	            }
	        },
	        tooltip: {
	            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
	        },
	        plotOptions: {
	            pie: {
	                allowPointSelect: true,
	                cursor: 'pointer',
	                dataLabels: {
	                    enabled: true,
	                    format: '{point.name}: {point.percentage:.1f} %',
	                    style: {
	                    	fontFamily:'Microsoft YaHei',
	                    	fontSize:'8px'
	                    }
	                }
	            }
	        },
	        series: [{}]
	    };
	return option;
}

//条形图
function getBarOption(){
	var option={
	        chart: {
	            type: 'bar'
	        },
	        title: {
	            text: '选项标题'
	        },
	        subtitle: {
	            text: '副标题'
	        },
	        xAxis: {
	            categories: [],
	            title: {
	                text: null
	            }
	        },
	        yAxis: {
	            min: 0,
	            title: {
	            	text: '',
	                style:{fontFamily:'Microsoft YaHei'}
	            }
	        },
	        tooltip: {
	        	style:{
	                color:'#888888',
	                fontFamily:'Microsoft YaHei'
	              },
		          formatter: function() {
		            return this.y.toFixed(2)+'%';
		          }
	        },
	        plotOptions: {
	            bar: {
	                dataLabels: {
	                    enabled: true
	                }
	            }
	        },
	        legend: {
	            layout: 'vertical',
	            align: 'right',
	            verticalAlign: 'top',
	            x: -40,
	            y: 100,
	            floating: true,
	            borderWidth: 1,
	            backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
	            shadow: true
	        },
	        credits: {
	            enabled: false
	        },
	        series: [{}]
	    };
	return option;
}