$(document).ready(
	function(){
		$("input[name='is_cat']").change(
			function(){
				if ($("input[name='is_cat']:checked").val() == "1"){
					$("select[name=parent_id]").attr("disabled", "disabled");
				}else{
					$("select[name=parent_id]").removeAttr("disabled");
				}
			}
		).change();
	}
);

