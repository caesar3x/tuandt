jQuery(function($) {	
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
    $(window).load(function(){
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
    });
    /**
     * Date picker
     */
    $( ".datepicker" ).datepicker();
});	