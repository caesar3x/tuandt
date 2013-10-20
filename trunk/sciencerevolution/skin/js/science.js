jQuery(function($) {
    /*var reSetHeight = function(){
        var lefth = $(".col-left").height();
        var mainh = $(".col-main").height();
        var righth = $(".col-right").height();
        var max = lefth;
        if(max < mainh)
            max = mainh;
        if(max < righth)
            max = righth;

        $(".col-left").css("height",max);
        $(".col-main").css("height",max);
        $(".col-right").css("height",max);
    }*/
    /*$(window).load(function(){
        reSetHeight();
    });*/
	$(".dropdown" ).each( function(){
		var _this = this
		$(".expand", this).click(function(){
		if($('.dropdown-wrapper', _this).css('display') == 'none'){
			$(_this ).addClass("focus");
			$('.dropdown-wrapper', _this).slideDown(200);
			$('.expand', _this).text("-");	
		}else{
			$(_this ).removeClass("focus");
			$('.dropdown-wrapper', _this).slideUp(200);
			$('.expand', _this).text("+");	
		}
		});	
	});
	
	$(".checkbox").click(function(){
		var _this = this;
		if($(this).hasClass("checked")){
			$(this).removeClass("checked");
			$('input',_this).removeAttr('checked');
		}else{
			$(this).addClass("checked");
			$('input',_this).attr('checked','checked');
		}
	});


    /**
     * Date picker
     */
    $( ".datepicker" ).datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "1930:2000"
    });
    /**
     * Click checkbox billing box
     */
    var checkBillingBox = function(elm){
        if(elm.is(":checked")){
            $("#billing-box").css("opacity","0.5");
            $("#billing-box").find("input,select").attr('disabled', "disabled");
        }else{
            $("#billing-box").css("opacity","1");
            $("#billing-box").find("input,select").removeAttr("disabled");
        }
    };
    if($("#billing-box").length > 0){
        /*$('#billing-residental').prop( "checked", true );*/
        checkBillingBox($('#billing-residental'));
    }
    $(document).on('click','#billing-residental',function(e){
        e.stopPropagation();
        checkBillingBox($(this));
    });
    /**
     * Process location
     */
    var loadCities = function(countryElm,stateElm,cityElm){
        var countryVal = countryElm.val();
        var stateVal = stateElm.val();
        if(typeof  countryVal != 'undefined' && typeof  stateVal != 'undefined' && countryVal != '0' && countryVal != '' && stateVal != '0' && stateVal != ''){
            if(countryVal != '0' && countryVal != ''){
                $.get(siteurl+'ajaxload?country='+countryVal+'&state='+stateVal).done(function(data){
                    cityElm.html(data);
                    cityElm.trigger('click');
                });
            }
        }
    };
    var loadStates = function(countryElm,stateElm,cityElm,loadcities){
        var countryVal = countryElm.val();
        if(typeof  countryVal != 'undefined' && countryVal != '0' && countryVal != ''){
            $.get(siteurl+'ajaxload?country='+countryVal).done(function(data){
                stateElm.html(data);
                if(loadcities == 'yes'){
                    loadCities(countryElm,stateElm,cityElm);
                }
                stateElm.trigger('click');
            });
        }
    };
    if($("#select-country").length > 0){
        loadStates($("#select-country"),$("#select-state"),$("#select-city"),'yes');
        loadCities($("#select-country"),$("#select-state"),$("#select-city"));
        $("#select-country").change(function(e){
            e.stopPropagation();
            loadStates($(this),$("#select-state"),$("#select-city"),'yes');
            e.preventDefault();
        });
        $("#select-state").change(function(e){
            e.stopPropagation();
            loadCities($("#select-country"),$(this),$("#select-city"));
            e.preventDefault();
        });
    }
    if($("#billing-country").length > 0){
        loadStates($("#billing-country"),$("#billing-state"),$("#billing-city"),'yes');
        loadCities($("#billing-country"),$("#billing-state"),$("#billing-city"));
        $("#billing-country").change(function(e){
            e.stopPropagation();
            loadStates($(this),$("#billing-state"),$("#billing-city"),'yes');
            e.preventDefault();
        });
        $("#billing-state").change(function(e){
            e.stopPropagation();
            loadCities($("#billing-country"),$(this),$("#billing-city"));
            e.preventDefault();
        });
    }
    /**
     * Check agrre
     */
    $(document).on("submit","#signup-form",function(){
        var accept = $("#accept").is(":checked");
        if(!accept){
            alert('Please accept our policy');
            return false;
        }
    });
    /**
     * Validate form
     */
    if(typeof $.fn.validate != 'undefined'){
        $(".validate").validate({
            rules: {
                password: {
                    required: true,
                    minlength: 5
                },
                confirm_password: {
                    required: true,
                    minlength: 5,
                    equalTo: "#password"
                },
                email: {
                    required: true,
                    email: true
                },
                "billing:email" : {
                    required: true,
                    email: true
                },
            },
            messages: {
                password: {
                    minlength: "Your password must be at least 5 characters long"
                },
                confirm_password: {
                    minlength: "Your password must be at least 5 characters long",
                    equalTo: "Please enter the same password as above"
                },
                email: "Please enter a valid email address",
                "billing:email": "Please enter a valid email address"
            }
        });
    }
});