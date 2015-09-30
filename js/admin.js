$(function() {

	/** Close icon on notification messages. */
	$('.close').click(function(){
		$(this).parent().remove();
		tooltip.hide();
	});
	/** Settings panel animation. */
  $('#settings_disp').click(function () {
      var $s_bar = $('#settings_bar');
			var $s_wrap = $('#settings_wrap');
			if (!$s_bar.hasClass("expanded")){
				$s_bar.stop().animate({ width: '430px' }, { duration: 500 });
				$s_bar.addClass("expanded");
			}else{
				$s_bar.stop().animate({ width: '60px' }, { duration: 500 });
				$s_bar.removeClass("expanded");
			}
  });
	/** Settings panel scrollbar */
	$('#settings_wrap').tinyscrollbar({ axis: 'y', sizethumb: 120, wheel: 20});

	function logout_ajax(){
		var dataString = 'action=logout' ;
		$.ajax({
			type: "POST",
			url: "ajax_love.php",
			data: dataString,
			cache: false,
			success: function(data)	{
				if (data.indexOf('<div class="alert error">') != -1){
					$('#login').prepend(data);
				}else{
					location.href = 'index.php';
				}
			}
		});
		return false;
	}

	$('#logout').click(function(){
		logout_ajax();
	});

});
