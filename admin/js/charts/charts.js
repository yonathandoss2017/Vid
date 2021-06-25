/* All functions in this file are used only for charts.html */
var Charts = function () {
	
    "use strict";  

    // insert checkboxes 
    var choiceContainer = $("#choices");
    
    // Init Flot Charts Plugin
    var runCharts = function () {
		
        // Add a series of colors to be used in the charts and pie graphs
        var Colors = ['#9de0f5', '#c7b7e5', '#fbb882', '#c6e69c', '#ffac9c'];	
  
		 if ($(".chart-toggle").length) {
			
			var datasets = {
				"Facebook": {
					label: "Facebook",
					data: [[1, 40], [2, 8000], [3, 3400], [4, 6000], [5, 9100], [6, 3500], [7, 16000], [8, 2000], [9, 800], [10, 600]]
				},   
				"Google Plus": {
					label: "Google Plus",
					data: [[1, 0], [2, 12000], [3, 5000], [4, 3000], [5, 12000], [6, 500], [7, 600], [8, 3700], [9, 15800], [10, 2300]]
				},         
				"Twitter": {
					label: "Twitter",
					data: [[1, 40], [2, 100], [3, 200], [4, 1000], [5, 11100], [6, 1500], [7, 2400], [8, 5000], [9, 12000], [10, 24000]]
				},        
				"Pinterest": {
					label: "Pinterest",
					data: [[1, 40], [2, 7000], [3, 1700], [4, 15000], [5, 14400], [6, 9500], [7, 5600], [8, 700], [9, 800], [10, 400]]
				},           
				"Instagram": {
					label: "Instagram",
					data: [[1, 16000], [2, 7000], [3, 200], [4, 3300], [5, 8000], [6, 500], [7, 600], [8, 3700], [9, 5800], [10, 2300]]
				} 
			};
	
			// hard-code color indices to prevent them from shifting as
			// countries are turned on/off
			var i = 0;
			$.each(datasets, function(key, val) {
				val.color = i;
				++i;
			});
	
			var i2 = 0;
			$.each(datasets, function(key, val) {
				choiceContainer.append("<div class='cBox cBox-inline'><input type='checkbox' name='" + key + "' id='" + key + "'/> <label for='" + key + "'>" + val.label + "</label></div>");
			});
			
			choiceContainer.find("input").click(function() {
				plotAccordingToChoices();
            });
			
			var plotAccordingToChoices = function() {
				var data = [];
				choiceContainer.find("input:checked").each(function () {
					var key = $(this).attr("name");
					if (key && datasets[key]) {
						data.push(datasets[key]);
					}
				});
	
				if (data.length > 0) {
					$.plot(".chart-toggle", data, {
						grid: {
							show: true,
							aboveData: true,
							color: "#ccc",
							labelMargin: 5,
							axisMargin: 0,
							borderWidth: 0,
							borderColor: null,
							minBorderMargin: 5,
							clickable: true,
							hoverable: true,
							autoHighlight: true,
							mouseActiveRadius: 20
						},
						series: {
							lines: {
								show: true,
								fill: 0.2,
								lineWidth: 4,
								steps: false
							},
							points: {
								show: true
							}
						},
						yaxis: {
							min: 0
						},
						xaxis: {
							ticks: 11,
							tickDecimals: 0
						},
						colors: Colors,
						shadowSize: 1,
						tooltip: true,
						//activate tooltip
						tooltipOpts: {
							content: "%s : %y.0",
							shifts: {
								x: -30,
								y: -50
							}
						}
					});
				}
			}
			plotAccordingToChoices();
	    }

    }

    // Init Flot Chart Plugins
    var runChartPlugins = function () {

		
		// Flot Tooltip Plugin
		!function(a){var b={tooltip:!1,tooltipOpts:{content:"%s | X: %x | Y: %y",xDateFormat:null,yDateFormat:null,shifts:{x:10,y:20},defaultTheme:!0,onHover:function(){}}},c=function(a){this.tipPosition={x:0,y:0},this.init(a)};c.prototype.init=function(b){function c(a){var b={};b.x=a.pageX,b.y=a.pageY,e.updateTooltipPosition(b)}function d(a,b,c){var d=e.getDomElement();if(c){var f;f=e.stringFormat(e.tooltipOptions.content,c),d.html(f),e.updateTooltipPosition({x:b.pageX,y:b.pageY}),d.css({left:e.tipPosition.x+e.tooltipOptions.shifts.x,top:e.tipPosition.y+e.tooltipOptions.shifts.y}).show(),"function"==typeof e.tooltipOptions.onHover&&e.tooltipOptions.onHover(c,d)}else d.hide().html("")}var e=this;b.hooks.bindEvents.push(function(b,f){if(e.plotOptions=b.getOptions(),e.plotOptions.tooltip!==!1&&"undefined"!=typeof e.plotOptions.tooltip){e.tooltipOptions=e.plotOptions.tooltipOpts;{e.getDomElement()}a(b.getPlaceholder()).bind("plothover",d),a(f).bind("mousemove",c)}}),b.hooks.shutdown.push(function(b,e){a(b.getPlaceholder()).unbind("plothover",d),a(e).unbind("mousemove",c)})},c.prototype.getDomElement=function(){var b;return a("#flotTip").length>0?b=a("#flotTip"):(b=a("<div />").attr("id","flotTip"),b.appendTo("body").hide().css({position:"absolute"}),this.tooltipOptions.defaultTheme&&b.css({background:"#fff","z-index":"100",padding:"0.4em 0.6em","border-radius":"0.5em","font-size":"0.8em",border:"1px solid #111",display:"none","white-space":"nowrap"})),b},c.prototype.updateTooltipPosition=function(b){var c=a("#flotTip").outerWidth()+this.tooltipOptions.shifts.x,d=a("#flotTip").outerHeight()+this.tooltipOptions.shifts.y;b.x-a(window).scrollLeft()>a(window).innerWidth()-c&&(b.x-=c),b.y-a(window).scrollTop()>a(window).innerHeight()-d&&(b.y-=d),this.tipPosition.x=b.x,this.tipPosition.y=b.y},c.prototype.stringFormat=function(a,b){var c=/%p\.{0,1}(\d{0,})/,d=/%s/,e=/%x\.{0,1}(?:\d{0,})/,f=/%y\.{0,1}(?:\d{0,})/,g=b.datapoint[0],h=b.datapoint[1];return"function"==typeof a&&(a=a(b.series.label,g,h,b)),"undefined"!=typeof b.series.percent&&(a=this.adjustValPrecision(c,a,b.series.percent)),"undefined"!=typeof b.series.label&&(a=a.replace(d,b.series.label)),this.isTimeMode("xaxis",b)&&this.isXDateFormat(b)&&(a=a.replace(e,this.timestampToDate(g,this.tooltipOptions.xDateFormat))),this.isTimeMode("yaxis",b)&&this.isYDateFormat(b)&&(a=a.replace(f,this.timestampToDate(h,this.tooltipOptions.yDateFormat))),"number"==typeof g&&(a=this.adjustValPrecision(e,a,g)),"number"==typeof h&&(a=this.adjustValPrecision(f,a,h)),"undefined"!=typeof b.series.xaxis.tickFormatter&&(a=a.replace(e,b.series.xaxis.tickFormatter(g,b.series.xaxis))),"undefined"!=typeof b.series.yaxis.tickFormatter&&(a=a.replace(f,b.series.yaxis.tickFormatter(h,b.series.yaxis))),a},c.prototype.isTimeMode=function(a,b){return"undefined"!=typeof b.series[a].options.mode&&"time"===b.series[a].options.mode},c.prototype.isXDateFormat=function(){return"undefined"!=typeof this.tooltipOptions.xDateFormat&&null!==this.tooltipOptions.xDateFormat},c.prototype.isYDateFormat=function(){return"undefined"!=typeof this.tooltipOptions.yDateFormat&&null!==this.tooltipOptions.yDateFormat},c.prototype.timestampToDate=function(b,c){var d=new Date(b);return a.plot.formatDate(d,c)},c.prototype.adjustValPrecision=function(a,b,c){var d,e=b.match(a);return null!==e&&""!==RegExp.$1&&(d=RegExp.$1,c=c.toFixed(d),b=b.replace(a,c)),b};var d=function(a){new c(a)};a.plot.plugins.push({init:d,options:b,name:"tooltip",version:"0.6.1"})}(jQuery);
		
	}
    return {
        init: function () {
            runCharts();
            runChartPlugins();
            choiceContainer.find("input").click();
        }
    };
}();