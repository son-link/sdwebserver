//repeat password check
//update passwordcheck validation pattern to match password value when this change
$("#password").on('change', function() {
	$("#passwordcheck").pattern = this.value;
});

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
	var newsrc='./img/flags/flags_medium/'+this.value.replace(' ','_')+'.png';
	$("#flagimg").attr('src', newsrc);
});

$('#refresh-captcha').on('click', function() {
	$().ajax(
		'/register/new_captcha',
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
	let passwd = $('#password').val();
	let passwdcheck = $('#passwordcheck').val();
	$('#register-error').hide();
	$('#passwd-error').hide();
	if (passwd != passwdcheck) {
		$('#passwd-error').show();
	} else {
		form_data = new FormData(this);

		$().ajax('/register/newuser', {
			type: 'POST',
			data: form_data,
			success: (resp) => {
				if (resp.ok) {
					location.href = '/register/ok';
				} else {
					$('#register-error').text(resp.msg);
					$('#register-error').show();
				}
			}
		});
	}
});

$('input').on('keyup', function() {
	input = $(this);
	inputID = '#' + input.attr('id');
	$(inputID + ' + .input-error').hide();
	if (input.val() && input.is(':invalid')) $(inputID + ' + .input-error').show();
	if (input.val() && inputID == '#password') $('#passwordcheck').attr('pattern', input.val())
});

$("#flaginput").trigger('change');
