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

$('#user-edit-form > form').on('submit', function(e) {
	e.preventDefault()

	const formData = new FormData(this);

	axios.post(`${base_url}/dashboard/update_user`, formData)
		.then( resp => {
			if (resp.data) window.location.reload()
		})
})

if (typeof nation !== 'undefined') {
	$('#flaginput > option').each( ele => {
		if (ele.innerText == nation) {
			$(ele).attr('selected', true)
			return true
		}
	})
}

$('#user-passwd-form > form').on('submit', function(e) {
	e.preventDefault()

	$('#passwd-error').hide();
	const formData = new FormData(this);
	if (formData.get('password') != formData.get('passwordcheck')) {
		$('#passwd-error').show();
		return;
	}

	axios.post(`${base_url}/dashboard/change_passwd`, formData)
		.then( resp => {
			if (resp.data) {
				if (resp.data.ok) window.location.reload()
				else alert(resp.data.msg)
			}
		})
})