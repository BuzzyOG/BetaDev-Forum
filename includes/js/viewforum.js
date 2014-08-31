$(document).ready(
	function(){
		$(".topic_rows .forum_main a").each(function(){
				var self = $(this)
				self.tooltip({
						position: "center left",
						opacity: 0.85,
						effect: 'slide',
						direction: 'left',
						bounce: true
					}
				).dynamic({
						right:{
							direction: 'right',
							bounce: true
						}
					}
				);
			}
		)
	}
);