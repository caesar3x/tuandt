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
    /**
     * Date picker
     */
    $( ".datepicker" ).datepicker();
});	