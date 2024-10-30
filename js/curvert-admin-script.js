jQuery(document).ready(function($) {
	$('body').on("change", "#curvert_borderthicknes", function(){
		var bt = $(this).val();
		$('.border-thickness-preview').text( bt );
	});

	$('body').on("change", "#curvert_bpadding", function(){
		var bp = $(this).val();
		$('.bpadding_preview').text(bp);
	});
});
  