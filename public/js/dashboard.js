$('#login-form').on('submit', function(e) {
	e.preventDefault();
	const form_data = new FormData(this);
	$('#login-error').hide();
	
	$().ajax(`${base_url}/dashboard/login`, {
		type: 'POST',
		data: form_data,
		success: (resp) => {
			if (resp.ok) {
				location.href = `${base_url}/dashboard`;
			} else {
				$('#login-error').show();
			}
		}
	});
});