$('#menu-select').on('change', function() {
	location.href = this.value;
});

if (typeof period !== 'undefined') $(`a#${period}`).addClass('selected')