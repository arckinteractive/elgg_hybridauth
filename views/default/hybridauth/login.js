define(function(require) {
	var $ = require("jquery");
	
	$('a.hybridauth-start-authentication').click(function(){
		var form = $(this).parents('form:first');
		if (form.length === 1 && $("input[type='checkbox'][name='persistent']", form).prop('checked')) {
			var href = $(this).prop('href');
			href = href.concat("&persistent=true");
			$(this).prop('href', href);
		}
	});
});