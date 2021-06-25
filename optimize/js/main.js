;(function($) {

	var chartStartDate, chartEndDate, linechart;
	var monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			web_item = $('.web-item-wrapper').clone(),
			web_item_id = 1;
	var url = window.location.href;
	var fullDates = [];

	// Random tools
	var Utils = {
		randomValue: function(min, max) {
			return Math.round(Math.random() * (max - min) + min);
		},
		generateRandomData: function(length) {
			var data = [];
			for (var i=0; i<length; i++) {
				data[i] = Utils.randomValue(20, 240);
			}
			return data;
		},
		getDateDiff: function(from, to) {
			var timeDiff = Math.abs((from.getTime()) - to.getTime());
			return Math.ceil(timeDiff / (1000 * 3600 * 24));
		},
		getDateInterval: function(from, to) {
			var days = [];
			fullDates = [];
			var diff = Utils.getDateDiff(from,to);
			if (diff <= 1) {
				var d = new Date();
				to = (diff == 0) ? d.getHours() : 24;
				for (var i = 0; i < to; i++) {
					days.push(i + ':00');
				}
			} else {
				while (from <= to) {
					days.push(from.getDate());
					var d =  from.getDate().toString() + ' ' + monthNames[from.getMonth()] + ' ' + from.getFullYear();
    			fullDates.push(d);
					from.setDate(from.getDate() + 1);
				}
			}
			return days;
		},
		transparentize: function(color, opacity) {
			var alpha = opacity === undefined? 0.5 : 1 - opacity;
			return Chart.helpers.color(color).alpha(alpha).rgbString();
		},
		isMobile: function() {
			return (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent));
		}
	}

	$(window).on('resize', toggleNavbar);  // Hide bar for small screens
	$(document).on('click', '.navbar a', pushStateHandler);

	// Toggle tabs
	$(document).on('click', '.tabs li', toggleTabs);

	// Popups
	$(document).on('click', '.popup-trigger', openPopup);
	$(document).on('click', '.popup-wrapper, .popup-wrapper .close-btn', closePopup);
	$(document).on('click', '.popup-wrapper .btn-copy', copyCodeTag);
	$(document).on('click', '.popup-wrapper .add-web-btn', addWebItem);
	$(document).on('click', '.popup-wrapper .rm-web-btn', removeWebItem);

	// Colapse sidebar on click
	$(document).on('click', '.burger', colapseSidebar);


	// Abrir panel
	$(document).on('click', '.dropdown, .slide-panel', openDropdown);
	// Desplegar menus y ocultar los no activos
	$(document).on('click', '.dropdown:not(.just-info) a', openDropdownMenus);
	// Cerrar dropdowns & slide-panels cuando se pincha fuera
	$(document).on('click', closeDropdown);
	// Prevent slide-panel to close when click on the options
	$(document).on('click', '.slide-panel .slide-menu', function(e){ e.stopPropagation() });

	$(document).on('click', '.form-submit', checkFormValues);
	$(document).on('click', '#reportrange', activateDatepicker);
	$(document).on('click', '.bell-trigger', openNotifications);
	$(document).on('click', '.table-ntfs tr', goTonNotificationSingle);
	$(document).on('click','.panel-header .toggle', toggleDropdowns);
	$(document).on('click', '.help-triggers', changeHelpPanel);

	//$(document).on('keyup', '.search-input', searchFilters);

	$(document).on('click', '.fd-btn', toggleReportFilterDropdown);
	$(document).on('keyup', '.filter-dropdown .search-input input', showResWrapper);
	$(document).on('click', '.filter-dropdown .single-res', chooseResOption);
	$(document).on('click', '.filter-dropdown .rm-box-btn', removeResOption);
	$(document).on('click', '.filter-dropdown .new-filter', addFilter);
	$(document).on('click', '.filter-dropdown .rm-btn', removeFilter);
	$(document).on('change', '.filter-dropdown .check-option input', countFilters);
	$(document).on('click', '.filter-dropdown .clear-search', clearFilterInput);
	$(document).on('click', '.filter-dropdown .main-mf', openMoreFilters);


	toggleNavbar(); // Hide bar for small screens

	// Hide bar for small screens
	function toggleNavbar(){
		if ($('body').hasClass('collapsed')) return;
		if ($(window).width() <= 1024) {
			$('body').addClass('navbar-collapse');
		} else {
			$('body').removeClass('navbar-collapse');
		}
	}


	function colapseSidebar() {
		$('body').toggleClass('navbar-collapse').toggleClass('collapsed', $('body').hasClass('navbar-collapse'));
	}

	function pushStateHandler(e){
		var href = $(this).attr('href');
		// Update navbar active item

		if (!$(this).hasClass('popup-trigger')) {
			$(this).parent().toggleClass('active');
		}
		// $(this).parent().addClass('active').siblings().removeClass('active');
		if (!$(this).parent().hasClass('has-sub')) {
			toggleNavbar();
		}

		if (href == 'index.html') {
			initDatepicker();
		}
	}

	function toggleTabs(e) {
		$(this).addClass('active').siblings().removeClass('active');
		updateHandler();
	}

	var filter_counter;
	var template_filter = $('.template-filter').clone();
	template_filter.removeClass('template-filter')
	$('.template-filter').remove();

	function toggleReportFilterDropdown() {
		$(this).closest('.filter-dropdown').toggleClass('open');
		var filter_top = $(this).closest('.filter-dropdown').offset().top,
			filter_left = $(this).closest('.filter-dropdown').offset().left,
			filter_add_left = 50;
		$(this).closest('.filter-dropdown').find('.fd-menu').css('top', filter_top - $(window).scrollTop() + 45);
		if (filter_left < 350 || $(window).width() <= 1260){
			filter_add_left = 0;
		}
		$(this).closest('.filter-dropdown').find('.fd-menu').css('left', filter_left - filter_add_left);
		countFilters(this);
	}

	function closeReportFilterDropdown(e) {
		if (!e.target.closest('.filter-dropdown')) {
			$('.filter-dropdown').removeClass('open')
		}
	}

	function countFilters(_this) {
		filter_counter = $(_this).closest('.filter-dropdown').find(':checked');
		if (_this.type == 'change') {
			filter_counter = $(_this.target).closest('.filter-dropdown').find(':checked');
			$(_this.target).closest('.filter-dropdown').find('.fd-btn').text('Filters ('+ filter_counter.length +')');
			return;
		}

		$(_this).closest('.filter-dropdown').find('.fd-btn').text('Filters ('+ filter_counter.length +')')
	}

	function showResWrapper() {
		var search_val = $(this).val(),
			target_res_wrapper = $(this).closest('.filter-wrapper').find('.search-res-wrapper'),
			clear_btn = $(this).closest('.filter-wrapper').find('.clear-search')
		//console.log(search_val);
		//console.log($(this).closest('.filter-name').html());
		var filter = $(this).parents('.filter-wrapper').find('.left .filter-name').html();
		//console.log(filter);
		$.ajax({
		  method: "POST",
		  url: "acom.php",
		  data: { search: search_val, filter: filter }
		}).done(function( res ) {
		    //console.log(res);
		    
		    if (search_val.length >= 2) {
				target_res_wrapper.addClass('active');
				target_res_wrapper.html(res);
				clear_btn.show();
			} else {
				target_res_wrapper.removeClass('active');
				clear_btn.hide();
			}
		    
		});		
	}

	function chooseResOption() {
		var chosen_option = $(this).text(),
			target_res_wrapper = $(this).closest('.filter-wrapper').find('.search-res-wrapper'),
			search_field = $(this).closest('.filter-wrapper').find('.search-input input'),
			filter_options = $(this).closest('.filter-wrapper').find('.flex-top')
			rm_box_wrapper = $(this).closest('.filter-wrapper').find('.rm-box-wrapper'),
			in_ex_class = $(this).closest('.filter-wrapper').find('.th-s-panel button');

		search_field.val(chosen_option);
		target_res_wrapper.removeClass('active');
		filter_options.addClass('active');
		var ie_class = 'include';
		if(in_ex_class.hasClass('exclude')){
			ie_class = 'exclude'
		}
		rm_box_wrapper.append('<p class="rm-box ' + ie_class + '">' + chosen_option + '<span class="rm-box-btn"></span></p>')

		$(this).closest('.filter-wrapper').find('.rm-btn').css('display', 'inline-block');
	}

	function removeResOption(e) {
		e.stopPropagation()
		var target = $(this).parent(),
			target_res_wrapper = $(this).closest('.filter-wrapper').find('.search-res-wrapper'),
			filter_options = $(this).closest('.filter-wrapper').find('.flex-top'),
			rm_boxes = $(this).closest('.filter-wrapper').find('.rm-box');

		target.remove();
		if (rm_boxes.length == 1) {
			filter_options.removeClass('active');
			$(this).closest('.filter-wrapper').find('.rm-btn').hide();
		}
	}

	function addFilter(e) {
		e.stopPropagation()
		var filter_name = $(this).text(),
			filter_list = $(this).closest('.filter-dropdown').find('.fdm-body'),
			new_filter = template_filter.clone(),
			other_class;

		if ($(this).hasClass('other')){
			other_class = 'other';
		}

		$(this).remove();
		new_filter.addClass(other_class);
		new_filter.find('.filter-name').text(filter_name)
		new_filter.find('.check-option input').attr('id', 'fc-' + filter_name);
		new_filter.find('.check-option input').attr('name', 'fc-' + filter_name);
		new_filter.find('.check-option label').attr('for', 'fc-' + filter_name);
		filter_list.append(new_filter);
		$('.filter-dropdown').find('.mf-inner').hide();
		countFilters(new_filter);
	}

	function removeFilter(e) {
		e.stopPropagation()
		var target_filter = $(this).closest('.filter-wrapper'),
			filter_name = target_filter.find('.filter-name').text(),
			filters_footer = $(this).closest('.filter-dropdown').find('.fdm-footer'),
			filters_other_footer = $(this).closest('.filter-dropdown').find('.mf-inner');

		target_filter.remove();
		if (target_filter.hasClass('other')){
			filters_other_footer.append('<a href="javascript:void(0)" class="new-filter other">' + filter_name + '</a>');
		} else {
			filters_footer.append('<a href="javascript:void(0)" class="new-filter">' + filter_name + '</a>');
		}
		$('.filter-dropdown').find('.mf-inner').hide();
		countFilters(filters_footer);
	}

	function clearFilterInput() {
		$(this).parent().find('input').val('');
		$(this).closest('.filter-wrapper').find('.search-res-wrapper').removeClass('active');
	}

	function openMoreFilters() {
		if ($(this).parent().find('.mf-inner').is(":visible")){
			$(this).parent().find('.mf-inner').hide()
		} else {
			$(this).parent().find('.mf-inner').css('display', 'block');
		}
	}


	function openPopup(e) {
		e.preventDefault();
		e.stopPropagation();
		var popup_target = $(this);
		
		if($(this).hasClass('btn-delete-rule')){
			$('.popup-delete-rule').addClass('active');	
			$('#del-rule-id').attr('href', $(this).attr('data-value') );
		}else if($(this).hasClass('btn-delete-tag')){
			$('.popup-delete-tag').addClass('active');	
			$('#del-tag-id').attr('href', $(this).attr('data-value') );
		}else if($(this).hasClass('btn-delete-group')){
			$('.popup-delete-group').addClass('active');	
			$('#del-group-id').attr('href', $(this).attr('data-value') );
		}else if($(this).hasClass('btn-edit-tag')){
			$('.popup-edit2').addClass('active');	
			var thisTagid = $(this).attr('data-value');
			$('#idtag').val( thisTagid );
			
			$('#edit_description').val( $('#tagname' + thisTagid ).html() );
			$('#edit_tagid').val( $('#tagid' + thisTagid ).html() );
			$('#edit_listid').val( $('#taglist' + thisTagid ).html() );
			$('#edit_fill').val( $('#tagfill' + thisTagid ).html() );
			$('#edit_requests').val( $('#tagreq' + thisTagid ).html() );
			
		}else if($(this).hasClass('btn-edit-tago')){
			$('.popup-edit3').addClass('active');	
			var thisTagid = $(this).attr('data-value');
			$('#idtago').val( thisTagid );
			
			$('#edit_descriptiono').val( $('#tagname' + thisTagid ).html() );
			$('#edit_tagido').val( $('#tagid' + thisTagid ).html() );			
		}else{
			$('.popup-edit').addClass('active');	
		}

		$('.popup-wrapper').addClass('active');		
	}
	function closePopup(e) {
		if (e.target === this) {
			$('.popup-wrapper, .popup-container').removeClass('active');
		}
	}
	function forceClosePopup() {
		$('.popup-wrapper, .popup-container').removeClass('active');
	}
	function copyCodeTag() {
		var target_field = $(this).closest('.form-field');

		target_field.find('.copy-target').select();
		document.execCommand("copy");
	}

	function addWebItem() {
		var new_web_item = web_item.clone();
		web_item_id = web_item_id +=1;
		new_web_item.find('.required').remove();
		new_web_item.find('.web-name').attr('name', 'web-page'+ web_item_id).attr('id', 'web-page'+ web_item_id).addClass('not-required');
		new_web_item.find('label').attr('for', 'web-page'+ web_item_id);
		new_web_item.appendTo('.web-pages-container');
		return false;
	}

	function removeWebItem() {
		var web_target = $(this).closest('.web-item-wrapper');
		web_target.remove();
	}

	// Dropdown componentes
	function toggleDropdowns() {
		var closest_panel = $(this).closest('.panel');
		if (closest_panel.hasClass('panel-help') && $(this).hasClass('max')) {
			$('.panel-body').slideUp('fast');
			$('.panel-header .toggle').addClass('max');
			$(this).removeClass('max');
		} else {
			$(this).toggleClass('max');
		}
		closest_panel.find('.panel-body').slideToggle('fast');
	};


	// Abrir panel
	function openDropdown(e){
		e.stopPropagation();

		if ($(this).closest('.nested').length = 0) {
			// Cerramos el resto de dropdowns y slide-menus
			$('.open').not(this).removeClass('open');
		}
		if ($(e.target).hasClass('search-input')) {
			return;
		}
		// Despliega el menú
		$(this).toggleClass('open');
		
		//console.log('SI');
	}

	// Cerrar dropdowns & slide-panels cuando se pincha fuera
	function closeDropdown(e){
		$('.dropdown.open, .slide-panel.open, .notifications-dropdown.open').each(function(){
			if (e.target !== this)
				$('.open').removeClass('open');
		})

		if (!e.target.closest('.filter-dropdown')) {
			$('.filter-dropdown').removeClass('open')
		}
	}

	function openNotifications(e) {
		e.stopPropagation();
		$(this).removeClass('new');
		$('.notifications-dropdown').toggleClass('open');
	}

	// Desplegar menus y ocultar los no activos
	function openDropdownMenus(e){
		e.preventDefault();
		if ($(this).hasClass('back-btn')){
			$(this).closest('.nested').toggleClass('open').siblings().removeClass('open');
			$('.nested').show();
			return false;
		}
		if ($(this).parent().hasClass('nested')) {
			$(this).parent().toggleClass('open').siblings().removeClass('open');
			$('.nested:not(.open)').hide();
			return false;
		}
		var $button = $(this).closest('.dropdown');
		var $hidden = $button.find('input.hiddenv');
		// Marcar el nuevo seleccionado
		$(this).closest('.dropdown').find('.selected').removeClass('selected')
		$(this).parent().toggleClass('selected');
		// Actualizar el valor
		$button.find('button').html($(this).html());
		$button.val($(this).data('value'));
		$hidden.val($(this).data('value'));
		
		
		console.log($hidden);
	}

	function searchFilters(th) {
		
		var search_val = th.val(),
			search_items = th.closest('.slide-menu').find('.inner-list label');
		console.log(search_val);
		if (search_val.length >= 1) {
			console.log('123');
			th.closest('.slide-menu').find('.inner-list li').hide();
			for (var i = 0; i < search_items.length; i++) {
				if ($(search_items[i]).text().toLowerCase().indexOf(search_val) > -1) {
					console.log($(search_items[i]).text())
					$(search_items[i]).closest('li').show();
				}
			}
		} else {
			$(this).closest('.slide-menu').find('.inner-list li').show();
		}
	}

	// Checkboxes
	$('.select-all').on('change', function(e){
		var $container = $(this).closest('.checkboxes');
		$container.find('.check-option.hidden').toggleClass('off');
		$container.find('[type=checkbox]').prop('checked', $(this).prop('checked'));
	});

	$('.checkboxes [type=checkbox]').on('change', function(){
		var $this = $(this);
		var $container = $this.closest('.checkboxes');
		$container.find('.select-all').prop('checked', function(){
			return ($(this).prop('checked') && $this.prop('checked')) ||  ($container.find(':checked:not(.select-all)').length ==  $container.find('[type=checkbox]:not(.select-all)').length);
		});
		var value = ($container.find('.select-all').prop('checked')) ? 'All' : $container.find(':checked').length;
		$container.prev('button:not(.inner-text)').html('Selected: ' + value);
		$container.prev('button.inner-text, .button.inner-text').find('.text').html('(' + value + ')');

		if ($container.closest('.nested').length > 0) {
			var nested_counter = $container.closest('.dropdown').find('button.inner-text:first .text'),
				nested_containers = $container.closest('.dropdown-menu').find(':checked:not(.select-all)').length;

			nested_counter.text('(' + nested_containers + ')')
		}
	});


	// Init datepickers

	function initDatepicker() {

	    var start = moment();
	    var end = moment();

	    function cb(start, end) {
					$('#reportrange .from span').html(start.format('DD/MM/YYYY'));
	        $('#reportrange .to span').html(end.format('DD/MM/YYYY'));
					chartStartDate = start._d;
					chartEndDate = end._d;
					if (linechart != undefined) {
						updateHandler()
						updateMonthNames()
					} else {
						initializeChart();
					}
	    }

	    $('#reportrange').daterangepicker({
	        startDate: start,
	        endDate: end,
	        ranges: {
	           'Hoy': [moment(), moment()],
	           'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	           'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
	           'Mes actual': [moment().startOf('month'), moment().endOf('month')],
	           'Mes pásado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	        },
					locale: {
				    "customRangeLabel": "Fecha personalizada",
				  },
					linkedCalendars: false,
					alwaysShowCalendars: true
	    }, cb);

	    cb(start, end);

	};

	if ($('body').hasClass('home') || $('body').hasClass('reports')) {
		initDatepicker();
	}

	function updateHandler(e) {

		// Update the labels and data with the new interval
		var chartData = linechart.config.data;
		var interval = Utils.getDateInterval(chartStartDate, chartEndDate);
		chartData.labels = interval;
		for (var i = 0; i < chartData.datasets.length; i++) {
			chartData.datasets[i].data = Utils.generateRandomData(interval.length);
		}
		linechart.update();
		// Update month names
		return false;
	}

	function updateMonthNames() {
		$('.chart-months .from-month').html(monthNames[chartStartDate.getMonth()]);
		$('.chart-months .to-month').html(monthNames[chartEndDate.getMonth()]);
	}

	// Initialize the chart
	function initializeChart() {
		updateMonthNames()

		if ($('#chart').length) {
			// Set the initial data for Publishers
			var pubColors = ['#6335d2', '#ff4e6a'];
			var r = ($(window).width() < 1024) ? 4 : 6;
			var pubData = {
				labels: Utils.getDateInterval(chartStartDate, chartEndDate),
				datasets: [
					{
						label: 'Impressions',
						data: [80, 135, 145, 130, 140, 220, 240, 230, 205, 135, 230, 210, 127, 140, 122, 80, 135, 145, 130, 140, 220, 240, 230, 205, 135, 230, 210, 127, 140, 122],
						fill: true,
						lineTension: 0.3,
						backgroundColor: Utils.transparentize(pubColors[0], 0.65),
						borderColor: pubColors[0],
						pointRadius: r/1.5,
						borderWidth: 1.5,
						pointBorderColor: 'transparent',
						pointBackgroundColor: pubColors[0],
						pointBorderWidth: r/r,
						pointHoverRadius: r,
						pointHoverBackgroundColor: '#FFF',
						pointHoverBorderWidth: r/2,
						pointHoverBorderColor: pubColors[0]
					},
					{
						label: 'Clicks',
						data: [30, 203, 150, 210, 120, 130, 205, 150, 130, 100, 207, 230, 220, 200, 150, 30, 203, 150, 210, 120, 130, 205, 150, 130, 100, 207, 230, 220, 200, 150],
						fill: true,
						lineTension: 0.3,
						backgroundColor: Utils.transparentize(pubColors[1], 0.65),
						borderColor: pubColors[1],
						pointRadius: r/1.5,
						borderWidth: 1.5,
						pointBorderColor: 'transparent',
						pointBackgroundColor: pubColors[1],
						pointBorderWidth: r/r,
						pointHoverRadius: r,
						pointHoverBackgroundColor: '#FFF',
						pointHoverBorderWidth: r/2,
						pointHoverBorderColor: pubColors[1]
					}
				]
			};
			var w = ($(window).width() < 1024) ? 10 : 56;
			var l_bottom = ($(window).width() < 1024) ? 25 : 0;
			var y_axes = ($(window).width() < 1024) ? false : true;
			var chartOptions = {
				defaults: {
					defaultFontColor: '#424853',
					defaultFontSize: '14',
					defaultFontFamily: 'Roboto'
				},
				layout: {
            padding: {
                left: 0,
                right: w,
                top: 0,
                bottom: l_bottom
            }
        },
				scales: {
					xAxes: [{
						display: y_axes,
						scaleLabel: {
							display: true
						},
						ticks: {
							fontSize: 13,
							fontColor: '#424853',
							padding: 10
						}
					}],
					yAxes: [{
						display: true,
						ticks: {
							fontColor: '#424853',
							fontSize: 14,
							padding: 10,
							max: 250,
							beginAtZero: true,
							// Include a dollar sign in the ticks
              callback: function(value, index, values) {
                  return '$' + value;
              }
						}
					}],
				},
				animation: false,
				spanGaps: true,
				maintainAspectRatio: false,
				hover: {
					mode: 'nearest',
					intersect: true,
					onHover: function(e, el) {
						$("#chart").css("cursor", el[0] ? "pointer" : "default");
					}
				},
				legend: {
					display: false
				},
				legendCallback: function(chart) {
					var legend = [],
						datasets = linechart.data.datasets;
					legend.push('<ul class="legend-' + linechart.id + '">');
					for (var i=0; i < datasets.length; i++) {
						if (datasets[i].label) {
							legend.push('<li data-index="' + i + '">' + datasets[i].label + '</li>');
						}
					}
					legend.push('</ul>');
					return legend.join("");
				},
				tooltips: {
					yPadding: 15,
					xPadding: 15,
					displayColors: false,
					backgroundColor: '#fff',
					titleFontSize: 14,
					titleFontStyle: 'normal',
					titleFontColor: '#424853',
					footerFontColor: '#424853',
					footerFontStyle: 'normal',
					bodyFontColor: '#ff166b',
					bodyFontSize: 22,
					cornerRadius: 2,
					borderWidth: 1,
					borderColor: '#ebeff6',
					callbacks: {
		        title: function(tooltipItem, data) {
							// console.log(tooltipItem)
							var title;
							if (tooltipItem[0].datasetIndex === 0) {
								title = data['datasets'][1]['label'];
		            return title;
			        } else if (tooltipItem[0].datasetIndex === 1) {
								title = data['datasets'][0]['label'];
								return title;
			        }
		        },
		        label: function(tooltipItem, data) {
							if (tooltipItem.datasetIndex === 0) {
		            return data['datasets'][0]['data'][tooltipItem['index']];
			        } else if (tooltipItem.datasetIndex === 1) {
								return data['datasets'][1]['data'][tooltipItem['index']];
			        }
		        },
						footer: function(tooltipItem, data) {
							var fullDateToShow = tooltipItem[0].index;
							return fullDates[fullDateToShow];
		        }
		      },
				}
			}
			linechart = new Chart($('#chart'), {
				type: 'line',
				data: $('.panel-chart').hasClass('panel-publishers') ? pubData : advData,
				options: chartOptions
			});

			// Generate html labels
			$('.chart-legend').html(linechart.generateLegend());

			function updateChartColors(i, index, colors, meta){
				// Check if there's any active label
				var off = !$('.chart-legend li').hasClass('active');
				// Calculate the new colors
				var bg = (!off && i == index) ? Utils.transparentize(colors[i], 0.75) : 'transparent';
				var base = (off || i == index) ? colors[i] : Utils.transparentize(colors[i], 0.75);
				var border = (off || i == index) ? '#FFF' : base;
				var poingBG = (off || i == index) ? colors[i] : 'transparent';

				meta.dataset.custom = meta.dataset.custom || {};
				// Set the line with the new color
				meta.dataset.custom.borderColor = base;
				// Set the background
				meta.dataset.custom.backgroundColor = bg;
				// Set the points with the new colors
				var points = meta.data;
				for (var j = 0; j < points.length; j++) {
					points[j].custom = points[j].custom || {};
					points[j].custom.borderColor = border;
					points[j].custom.backgroundColor = poingBG;
				}
			}
		}
	}


	// Sort tables

	if (jQuery().stupidtable) {
		$('table').stupidtable({
			'date': function(a,b){
				// validate date
				var aNum = a.split('-');
				var bNum = b.split('-');
				return new Date(aNum[2], aNum[1]-1, aNum[0]).getTime() < new Date(bNum[2], bNum[1]-1, bNum[0]).getTime();
			},
			'currency': function(a,b){
				// validate currency?
				var a = parseFloat(a.replace(/\u20ac /g,"").replace(/[,.]/g,""));
				var b = parseFloat(b.replace(/\u20ac /g,"").replace(/[,.]/g,""));
				return a - b;
			},
			'ratio': function(a,b){
				// revisar si orden correcto!
				var aNum = a.split(':');
				aNum = aNum[0] / aNum[1];
				var bNum = b.split(':');
				bNum = bNum[0] / bNum[1];
				return aNum - bNum;
			}
		});
	}

	function checkFormValues(e) {
		e.preventDefault();
		var empty_value = $(this).closest('form').find(".form-field>input:not(.not-required)").filter(function() {
			if (this.value === "") {
				$(this).addClass('error');
			} else {
				$(this).removeClass('error');
			}
			return this.value === "";
		});
		var empty_dropdown = $(this).closest('form').find(".dropdown").filter(function() {
				if ($(this).find('.selected').length == 0) {
					$(this).addClass('error');
					return 1;
				} else {
					$(this).removeClass('error');
					return 0;
				}
		});
		var empty_checklist = $(this).closest('form').find(".checkboxes").filter(function() {
				var isChecked = $(this).find('input[type=checkbox]').is(':checked');
				if (isChecked == false) {
					$(this).closest('.slide-panel').find('button').addClass('error');
				} else {
					$(this).closest('.slide-panel').find('button').removeClass('error');
				}
				return isChecked;
    });
    if ($(this).closest('form').find('.error').length > 1) {
        $(this).closest('form').find(".check-message").text('Ops, revisa los campos marcados en rojo por favor...').addClass('error');
				return;
    } else {
			$(this).closest('form').find(".check-message").text('').removeClass('error');
			showActionModal('success');
			forceClosePopup();
			
			if($(this).hasClass('new-group')){
		        $.post( "handelform.php", $( "#newgroup" ).serialize(), function(data){ window.location.href = 'https://reports.vidoomy.com/optimize/group.php?idg=' + data; } );
	        }else if($(this).hasClass('new-tag')){
		        $.post( "handelform.php", $( "#newtag" ).serialize(), function(){ location.reload(); } );
	        }else if($(this).hasClass('new-rule')){
		        var idform = $(this).attr('data-value');
		        $.post( "handelform.php", $( "#newrule" + idform ).serialize(), function(){ location.reload(); } );
	        }else if($(this).hasClass('save-tag')){
		        $.post( "handelform.php", $( "#savetag" ).serialize(), function(){ location.reload(); } );
	        }else if($(this).hasClass('save-tago')){
		        $.post( "handelform.php", $( "#savetago" ).serialize(), function(){ location.reload(); } );
	        }
			
		}
	}

	function showActionModal(modal) {
		$('.action-modals.' + modal).addClass('active');
		setTimeout(function() {
			$('.action-modals').removeClass('active');
		}, 2000);
	}

	function activateDatepicker() {
		$('#reportrange').toggleClass('active');
		if ($('body').hasClass('reports')) {
			var left = $('#reportrange').offset().left;

			$('.daterangepicker').css('left', left + 'px');
		}
	}

	function getRandomColor() {
	  var letters = '0123456789ABCDEF';
	  var color = '#';
	  for (var i = 0; i < 6; i++) {
	    color += letters[Math.floor(Math.random() * 16)];
	  }
	  return color;
	}

	if ($('body').hasClass('user-admins')) {
		$('.user-img').filter(function() {
			$(this).css('backgroundColor', getRandomColor());
		});
	}

	function goTonNotificationSingle() {
		window.location = 'notification-single.html';
	}

	function changeHelpPanel() {
		$('.help-triggers').removeClass('active');
		$(this).addClass('active');
	}

})(jQuery);
