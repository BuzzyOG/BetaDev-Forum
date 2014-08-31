function redir(url){
	setTimeout(function(){
    		window.location = url;
    	}, 1000);
}
jQuery.fn.fadeToggle = function(speed, easing, callback) {
   return this.animate({opacity: 'toggle'}, speed, easing, callback);

}; 
$(document).ready(
	function(){
		$('a#gotop').click(
			function(event){
				event.preventDefault();
				$('html').animate({scrollTop:0}, 'slow'); 
			}
		);
		$('#showlessonline').show();
		$('#usersonpage').hide();
		$('a#numusersonpage').click(
			function(event){
				$('#shortonline').fadeToggle("slow");
			}
		)
		$('a#showmoreonline').click(
			function(event){
				$(this).parent().fadeToggle("slow");
				$('#usersonpage').show("slow");
			}
		)
		$('#showlessonline').click(
			function(event){
				$(this).parent().hide("slow");
				$('#shortonline').fadeToggle("slow");
			}
		)
	}
);