(function ($) {

	/****** Init Datepicker ******/
	$.fn.initPicker = function () {
		var picker = $('.date');
		if( picker.length > 0 ){

            $("form:not(.edit-form) .registerDatepicker").datetimepicker({
                format: "YYYY/MM/DD HH:mm:00",
                locale: 'ja',
                stepping: 10,
                minDate: new Date(),
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down",
                    previous: 'fa fa-chevron-circle-left ',
                    next: 'fa fa-chevron-circle-right'
                }
            });
            $(".edit-form .registerDatepicker").datetimepicker({
                format: "YYYY/MM/DD HH:mm:00",
                locale: 'ja',
                stepping: 10,
                useCurrent: false,
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down",
                    previous: 'fa fa-chevron-circle-left ',
                    next: 'fa fa-chevron-circle-right'
                }
            });
            $(".registerDatepicker#datepicker1").on('dp.change', function(e){
                var minD = $(".registerDatepicker#datepicker1 input").val();
                $(".registerDatepicker#datepicker2").data("DateTimePicker").minDate(minD);
            });


            // $(".date:not(.time)").datetimepicker({
			// 	format: "YYYY/MM/DD",
			// 	locale: 'ja',
			// 	useCurrent: false,
			// 	icons: {
			// 		time: "fa fa-clock-o",
			// 		date: "fa fa-calendar",
			// 		up: "fa fa-arrow-up",
			// 		down: "fa fa-arrow-down",
			// 		previous: 'fa fa-chevron-circle-left ',
			// 		next: 'fa fa-chevron-circle-right'
			// 	},
			// });
            $("form:not(.edit-form) .date.registerDateEvent1").datetimepicker({
                format: "YYYY/MM/DD",
                locale: 'ja',
                minDate: new Date(),
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down",
                    previous: 'fa fa-chevron-circle-left ',
                    next: 'fa fa-chevron-circle-right'
                }
            });
            $(".edit-form .date.registerDateEvent1").datetimepicker({
                format: "YYYY/MM/DD",
                locale: 'ja',
                useCurrent: false,
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down",
                    previous: 'fa fa-chevron-circle-left ',
                    next: 'fa fa-chevron-circle-right'
                }
            });
            $(".date.registerDateEvent2").datetimepicker({
                format: "YYYY/MM/DD",
                locale: 'ja',
                minDate: new Date(),
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down",
                    previous: 'fa fa-chevron-circle-left ',
                    next: 'fa fa-chevron-circle-right'
                }
            });
            $(".registerDateEvent1#datepicker1").on('dp.change', function(e){
                var minD1 = $(".registerDateEvent1#datepicker1 input").val();

                $(".registerDateEvent1#datepicker2").data("DateTimePicker").minDate(minD1);
            });
            $(".registerDateEvent2#datepicker4").on('dp.change', function(e){
                var minD2 = $(".registerDateEvent2#datepicker4 input").val();
                $(".registerDateEvent2#datepicker5").data("DateTimePicker").minDate(minD2);
            });
			$(".date.time:not(.registerDatepicker)").datetimepicker({
				format: "YYYY/MM/DD HH:mm:ss",
				locale: 'ja',
				useCurrent: false,
				icons: {
					time: "fa fa-clock-o",
					date: "fa fa-calendar",
					up: "fa fa-arrow-up",
					down: "fa fa-arrow-down",
					previous: 'fa fa-chevron-circle-left ',
					next: 'fa fa-chevron-circle-right'
				}
			});
			// picker.find("input").on("click", function(e){
			// 	$(this).data("DateTimePicker").show();
			// });
			picker.find("input").on('keyup', function (e) {
                if( e.which === 8 ){ return false; }
                e.preventDefault();
                return false;
			});


            var regisPicker = $('.date.registerDatepicker');

            regisPicker.find("input").on('keyup keydown keypress', function (e) {
                if( e.which === 8 ){ return false; }
                e.preventDefault();
                return false;
            });

			if(($("#datepicker1 input").val() != "") && ($("#datepicker2 input").val() != "")){
				$("#datepicker2").data("DateTimePicker").minDate($("#datepicker1").data("DateTimePicker").date());
			}
			$("#datepicker1").on('dp.change', function(e){
				var minD = moment(e.date).startOf("day");

				if(($("#datepicker1 input").val() != "") && ($("#datepicker2 input").val() != "")){
					if($(this).data("DateTimePicker").date() > $("#datepicker2").data("DateTimePicker").date()){
						$("#datepicker2").data("DateTimePicker").date(null);
					}
				}

                $("#datepicker2").data("DateTimePicker").minDate(minD);
			});
			$("#datepicker2").on('dp.show', function(e){
				if(($("#datepicker1 input").val() == "") && ($("#datepicker2 input").val() == "")){
					$("#datepicker2").data("DateTimePicker").date(null);
				}
                //  var currentHour  = $("#datepicker1 input").val().split(' ')[1];
                // $("#datepicker2").data("DateTimePicker").format('YYYY/MM/DD '+currentHour);
			});

            if(($("#datepicker4 input").val() != undefined) && ($("#datepicker5 input").val() != undefined)){
                $("#datepicker5").data("DateTimePicker").minDate($("#datepicker4").data("DateTimePicker").date());
            }
            $("#datepicker4").on('dp.change', function(e){
                var minD = moment(e.date).startOf("day");

                if(($("#datepicker4 input").val() != "") && ($("#datepicker5 input").val() != "")){
                    if($(this).data("DateTimePicker").date() > $("#datepicker5").data("DateTimePicker").date()){
                        $("#datepicker5").data("DateTimePicker").date(null);
                    }
                }

                $("#datepicker5").data("DateTimePicker").minDate(minD);
            });
            $("#datepicker5").on('dp.show', function(e){
                if(($("#datepicker4 input").val() == "") && ($("#datepicker5 input").val() == "")){
                    $("#datepicker5").data("DateTimePicker").date(null);
                }
            });
		}
	};
	/****** Reset Form ******/
	$.fn.resetForm = function (){
		return this.each(function () {
			var self = $(this);
			self.on("click", function(e){
				var form = self.closest(".custom-form");
				form.find('select').val(-1);
				form.find('.selectpicker').selectpicker('refresh');
				form.find("input").val('');
				form.find("input[type=checkbox]").checked = false;
				form.find("input[type=radio]").checked = false;
				var picker = form.find('.date');
				if( picker.length > 0 ){
					$("#datepicker1").data("DateTimePicker").date(null);
					$("#datepicker2").data("DateTimePicker").date(null);
				}

			});
		});

	};
	/****** Error Message ******/
	$.fn.eventError = function() {
		return this.each(function() {
			var self = $(this);
			self.on("keyup",".has-error input, .has-error textarea", function(e){
				$(this).parents(".has-error").removeClass("has-error");
				$(this).siblings(".with-errors").remove();
				e.preventDefault();
			});
			self.on('change', ".has-error select", function() {
				if($(this).find("option:selected").val() != ""){
					$(this).parents(".has-error").removeClass("has-error");
					$(this).siblings(".with-errors").remove();
				}
			});
			self.on('change', ".has-error input[type='checkbox']", function() {
				$(this).closest(".has-error").removeClass("has-error");
				$(this).closest(".form-block").find('.with-errors').remove();
			});
			//var codeInput = self.find(".input-code");
			//codeInput.on('keyup', function () {
            //
			//	if ($(this).val().length == $(this).attr('maxLength')) {
			//		$(this).next('input').focus();
			//	}
			//});
		});
	};
	/****** Toggle Search ******/
	$.fn.toggleSearch = function(options) {
        var config = $.extend({}, {
            href: "",
        }, options);


		return this.each(function() {

			var obj = $(this);
			var toggle = obj.find("#toggleSearch");
			var top = $(".top-search");
			var panel = $("#searchPanel");
			var hPanel;
			obj.on("click", '.panel-heading', function (e) {
				if(obj.hasClass("open")){
                    hPanel=  $("#searchPanel .panel-body").height() + 40;
                    panel.find('.panel-body').css({'opacity': '0', 'height':'0'});
                    removeStorage('page');
					panel.addClass('collapse');
                    setTimeout(function(){
                        panel.css({"height": '0'});
                        panel.css({"opacity": '0'});
                        obj.removeClass("open");
						panel.hide();
                    }, 50);
				}else{
					obj.addClass("open");
					panel.addClass('in');
					panel.show();
					panel.find('.panel-body').attr("style","");
					panel.css({"opacity": '1'});
                    hPanel=  $("#searchPanel .panel-body").height() + 40;
					panel.animate({"height": hPanel},50,"linear");
					setTimeout(function() {

						panel.find('.panel-body').css({"opacity": '1'});
					},50);
					addStorage('page', config.href);


				}
			});
		});
	};
})(jQuery);

$(function () {
	var href = window.location.href.split('?')[0]+"";
	const STORAGE_HREF = 'page';
	if(searchOpening(STORAGE_HREF,href)) {
		$(".panel-search").addClass('open');

	}else{
		$("#searchPanel").addClass('collapse');
	}
	$(".table-responsive.table-over").on('click', "tbody tr:last-child():not(:first-child()) .bootstrap-select", function() {
		$(this).addClass("dropup");
	});
	
	$(".navbar-toggle, .user-name").on("click", function(e){
		switch ($(e.currentTarget).attr('class')){
			case 'pull-right dropdown-toggle pr-0 user-name':
				$("#myNavbar").removeClass("in");
				break;
			case 'navbar-toggle':
				$(".wrapper").removeClass("mobile-nav-open");
				break;
		}
	});

	$(".form-date").initPicker();
	$(".btn-reset").resetForm();
	// $(".custom-form").eventError();
    $(".panel-search").toggleSearch({href: href});

    document.addEventListener('touchmove', function(event) {
        event = event.originalEvent || event;
        if (event.scale !== 1) {
            event.preventDefault();
        }
    }, false);
    $(window).on("resize", function () {
        var height = $(window).height();
        $('.full-height').css('height', (height));
        $('.page-wrapper').css('min-height', (height));

		//update height of search field
		if($(".panel-search").hasClass("open")){
			$("#searchPanel").css({"height": ($("#searchPanel .panel-body").height() + 40)});
		}
    }).resize();

    var $wrapper = $(".wrapper");
    $(document).on('click', '#toggle_mobile_nav', function (e) {
        $wrapper.toggleClass('mobile-nav-open').removeClass('open-right-sidebar');
        return;
    });
	$('.cb-modal').change(function(e) {
		if(this.checked) {
			$("#modalTicketApproval").modal("show");
		}else{
			this.checked = true;
			e.preventDefault();
		}
	});
	$("#csvUpload").on("change", function(){
		$('.file-status').html('');
        $('#modalCsvUpload .has-error').removeClass("has-error");
        $('#modalCsvUpload .with-errors').text('');
		$.each(this.files, function() {
			readURL(this, $('.file-status'));
		});
		$(".csv-submit").fadeIn(300);
        $('#csv-submit').show();
	});

	//Config slide
    var slickSlide1 = $('.gatepass-sliders');

    initSlider(slickSlide1);
    if (slickSlide1 != null) {
        slickSlide1.slick({
            infinite: true,
            dots: false,
            slidesToShow: 8,
            swipe: false,
            slidesToScroll: 1,
            prevArrow: $('.gatepass-prev'),
            nextArrow: $('.gatepass-next'),
            responsive: [
                {
                    breakpoint: 1440,
                    settings: {
                        slidesToShow: 7,
                        slidesToScroll: 1,
                        infinite: true,
                    }
                },
                {
                    breakpoint: 1279,
                    settings: {
                        slidesToShow: 5,
                        slidesToScroll: 1,
                        infinite: true,
                    }
                },
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        infinite: true,

                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        infinite: true,

                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        infinite: true
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        infinite: true
                    }
                }
            ]
        });
    }

    /**
     *  Add promoter code before submit
     *
     *  @author <hoangnq@nal.vn>
     */
    $("#add-promoter").submit( function(eventObj) {
        var $checked = $("input[name='radio_add']:checked").val();

        $('<input />').attr('type', 'hidden')
            .attr('name', "promoter")
            .attr('value', $checked)
            .appendTo('#add-promoter');

        return true;
    });

    /**
     *  Add promoter code before submit
     *
     *  @author <hoangnq@nal.vn>
     */
    $("#link_event").submit( function(eventObj) {
        var $checked = $("input[name='radio_add']:checked").val();

        $('<input />').attr('type', 'hidden')
            .attr('name', "event_code")
            .attr('value', $checked)
            .appendTo('#link_event');

        return true;
    });

    /**
     *  Delete promoter code before submit
     *
     *  @author <hoangnq@nal.vn>
     */
    $("#delete-promoter").submit( function(eventObj) {
        var $checkeds = $('input[name*="checkbox_detele"]:checked');

        for (var i=0; i < $checkeds.length; i++) {
            $('<input />').attr('type', 'hidden')
                .attr('name', "promoters[]")
                .attr('value', $checkeds[i].value)
                .appendTo('#delete-promoter');
        }

        return true;
    });

    /**
     *  Delete promoter code before submit
     *
     *  @author <hoangnq@nal.vn>
     */
    $("#unlink_event").submit( function(eventObj) {
        var $checkeds = $('input[name*="checkbox_detele"]:checked');

        for (var i=0; i < $checkeds.length; i++) {
            $('<input />').attr('type', 'hidden')
                .attr('name', "event_code[]")
                .attr('value', $checkeds[i].value)
                .appendTo('#unlink_event');
        }

        return true;
    });

	/**
	 *  Add limit parameter in search
	 *
	 *  @author <hoangnq@nal.vn>
	 */
	$("#searchPanel form.form-search").submit( function(eventObj) {
		var searchParams = new URLSearchParams(window.location.search);
		if (searchParams.get('limit') !== null) {
			$('<input />').attr('type', 'hidden')
				.attr('name', "limit")
				.attr('value', searchParams.get('limit'))
				.appendTo('#searchPanel form.form-search');
		}
		return true;
	});

    /**
     *  enable submit button when choose value radio
     *
     *  @author <hoangnq@nal.vn>
     */
    $(document).on('click', 'input[name*="radio_add"]', function (e) {
        $('#add-promoter button[type="submit"]').prop('disabled', false);
    });

    /**
     *  enable submit button when choose value radio
     *
     *  @author <hoangnq@nal.vn>
     */
    $(document).on('click', 'input[name*="radio_add"]', function (e) {
        $('#link_event button[type="submit"]').prop('disabled', false);
    });

    /**
     *  Enable submit button when choose value checkbox
     *
     *  @author <hoangnq@nal.vn>
     */
    $(document).on('click', 'input[name*="checkbox_detele"]', function (e) {
        var $checkeds = $('input[name*="checkbox_detele"]:checked');

        $('#delete-promoter button[type="submit"]').prop('disabled', $checkeds.length <= 0);
    });

    /**
     *  Enable submit button when choose value checkbox
     *
     *  @author <hoangnq@nal.vn>
     */
    $(document).on('click', 'input[name*="checkbox_detele"]', function (e) {
        var $checkeds = $('input[name*="checkbox_detele"]:checked');

        $('#unlink_event button[type="submit"]').prop('disabled', $checkeds.length <= 0);
    });

    $('.date input').on('keydown', function (e) {
    	if (e.keyCode == 8) {
    		$(this).val('');
		}
	});

});

function readURL(file, status) {
	var reader = new FileReader();
	reader.onload = function(e) {
		status.html(file.name);
	};
	reader.readAsDataURL(file);
}
function initSlider(obj){
	if (obj.hasClass('slick-initialized')) {
		obj.slick('removeSlide', null, null, true);
		obj.slick('destroy');
	}
}

var build_query = function (obj, num_prefix, temp_key) {

	var output_string = [];

	Object.keys(obj).forEach(function (val) {

		var key = val;

		num_prefix && !isNaN(key) ? key = num_prefix + key : '';

		var key = encodeURIComponent(key);
		temp_key ? key = temp_key + '[' + key + ']' : '';

		if (typeof obj[val] === 'object') {
			var query = build_query(obj[val], null, key);
			output_string.push(query)
		} else {
			var value = encodeURIComponent(obj[val]);
			output_string.push(key + '=' + value)
		}

	});

	return output_string.join('&')
};

function searchOpening(name, value){
	if (getStorage(name) == value){
		return true;
	}
	removeStorage(name);
	return false;
}
function addStorage(name, value){
	if (typeof(Storage) !== "undefined") {
		localStorage.setItem(name,value);
	}
}
function removeStorage(name){
	if (typeof(Storage) !== "undefined") {
		localStorage.removeItem(name);
	}
}
function getStorage(name){
	if (typeof(Storage) !== "undefined") {
		return localStorage.getItem(name);
	}
	return null;
}

function txt_kogyo_code(str) {
    var txt_kogyo_code = $('.txt_kogyo_code');
    txt_kogyo_code.val(str || '');
    txt_kogyo_code.focus();
}

function txt_kogyo_sub_code(str) {
    console.log('txt_kogyo_sub_code');
    var txt_kogyo_sub_code = $('.txt_kogyo_sub_code');
    txt_kogyo_sub_code.val(str || '');
    txt_kogyo_sub_code.focus();
}

function txt_koen_code(str, forcus) {
    var txt_koen_code = $('.txt_koen_code');
    txt_koen_code.val(str || '');
    txt_koen_code.focus();
}

function change_all_txt_by_event_code(array_event_code) {
    if (!Array.isArray(array_event_code)) {
        return false;
    }
    var kogyo_code = array_event_code[0] || '';
    var kogyo_sub_code = array_event_code[1] || '';
    var koen_code = array_event_code[2] || '';

    txt_kogyo_code(kogyo_code.substr(0, 6));
    txt_kogyo_sub_code(kogyo_sub_code.substr(0, 4));
    txt_koen_code(koen_code.substr(0, 3));
}

$(document).ready(function () {
	$('.txt_kogyo_code').on('keyup', function (e) {
		var value_text = $(this).val();
		var value_explode = value_text.split('-');
        if (value_explode.length > 1) {
            change_all_txt_by_event_code(value_explode);
			return;
        }

        if (value_text.length >= 6) {
            txt_kogyo_sub_code('');
        }
        $(this).val(value_text.substr(0, 6))
	});

    $('.txt_kogyo_sub_code').on('keyup', function (e) {
        var value_text = $(this).val();
        var value_explode = value_text.split('-');

        if (value_explode.length > 1) {
            change_all_txt_by_event_code(value_explode);
            return;
        }

        if (value_text.length >= 4) {
            txt_koen_code('');
        }

        $(this).val(value_text.substr(0, 4))
    });

    $('.txt_koen_code').on('keyup', function (e) {
        var value_text = $(this).val();
        var value_explode = value_text.split('-');

        if (value_explode.length > 1) {
            change_all_txt_by_event_code(value_explode);
            return;
        }
        console.log(value_text.substr(0, 3));
        $(this).val(value_text.substr(0, 3))
    });

});





