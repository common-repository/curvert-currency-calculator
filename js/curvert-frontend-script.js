jQuery(document).ready(function($) {

	$('body').on('blur', '#curvert-amount', function(){
		if($('#curvert-amount').val() > 0){
			makeAPICall();
		}
	});

	$('body').on('change', '#curvert-from-currency,#curvert-to-currency', function(){
		if($('#curvert-amount').val() > 0){
			makeAPICall();
		}
	});

	$('body').on('click', '#curvert-swap-currencies', function(){
		var f = $('#curvert-from-currency').val(),
			t = $('#curvert-to-currency').val();

		$('#curvert-from-currency').val(t);
		$('#curvert-to-currency').val(f);

		if($('#curvert-amount').val() > 0){
			makeAPICall();
		}
	}); 

	function collectCurrencies(){

		var p = {};

		p['amount'] = $('#curvert-amount').val();
		p['from'] = $('#curvert-from-currency').val();
		p['from_type'] = $('#curvert-from-currency option:selected').data('type');
		p['to'] = $('#curvert-to-currency').val();
		p['to_type'] = $('#curvert-to-currency option:selected').data('type');

		return p;
	}

	function makeAPICall(){
		var params = collectCurrencies();

		$.getJSON('https://www.curvert.com/wp-api.php', params, function(json) {
			if(json.status == 'OK'){
				$('#curvert-result-single').text('1 ' + params.from + ' = ' + json.single + ' ' + params.to);
				$('#curvert-result').text('= ' + json.result + ' ' + params.to);
			}
		});
	}
});
