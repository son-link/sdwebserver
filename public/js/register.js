//profile image preview
function readURL(input) {

	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$("#profile-img").attr('src', e.target.result);
		}

		reader.readAsDataURL(input.files[0]);
	}
}

$("#imginput").on('change', function() {
	readURL(this);
});

//flag image preview
$("#flaginput").on('change', function() {
	var newsrc = `${base_url}/img/flags/flags_medium/`+this.value.replace(' ','_')+'.png';
	$("#flagimg").attr('src', newsrc);
});

$('#refresh-captcha').on('click', function() {
	$().ajax(
		`${base_url}/register/new_captcha`,
		{
			'dataType': 'text',
			'success': function(res) {
				$("#captcha").attr('src', res);
			}
		}
	);
});

$('#reg-form > form').on('submit', function(e) {
	e.preventDefault();
	// Check if the password math
	const passwd = $('#password').val();
	const passwdcheck = $('#passwordcheck').val();
	$('#register-error').hide();
	$('#passwd-error').hide();
	
	if (passwd.normalize() != passwdcheck.normalize()) {
		$('#passwd-error').show();
	} else {
		const form_data = new FormData(this);

		$().ajax(`${base_url}/register/newuser`, {
			type: 'POST',
			data: form_data,
			success: (resp) => {
				if (resp.ok) {
					location.href = `${base_url}/register/ok`;
				} else {
					$('#register-error').text(resp.msg);
					$('#register-error').show();
				}
			}
		});
	}
});

$('input').on('keyup', function() {
	const input = $(this);
	const inputID = '#' + input.attr('id');
	$(inputID + ' + .input-error').hide();

	if (input.val() && input.is(':invalid')) $(inputID + ' + .input-error').show();
	if (input.val() && (inputID == '#password' || inputID == '#passwordcheck')) {
		const passwd = $('#password').val();
		const passwdcheck = $('#passwordcheck').val();
		if (passwd.normalize() != passwdcheck.normalize()) $('#passwd-error').show();
		else $('#passwd-error').hide();
	}
});

$("#flaginput").trigger('change');
