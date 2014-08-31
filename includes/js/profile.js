$(document).ready(
	function(){
		$('.profileright .catrow').not('.noSlide').prepend('<span class="arrow"></span>').click(
			function(){				
				$(this).next().slideToggle('slow');
			}
		);
		$('.profileright .catrow').not(':first-child, .noSlide').next().hide();
	}
);