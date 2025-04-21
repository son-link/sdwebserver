$('#menu-select').on('change', function() {
	location.href = this.value;
});

if (typeof period !== 'undefined') $(`a#${period}`).addClass('selected')

function formatLaptime(time) {
	const date = new Date(time * 1000);
	const minutes = String(date.getMinutes()).padStart(2, '0')
	const seconds = String(date.getSeconds()).padStart(2, '0')
	const miliseconds = date.getMilliseconds()

	return `${minutes}:${seconds}:${miliseconds}`
}

function weatherTag(weather)
{
	switch(weather)
	{
		case 0:
			return '<i class="wi wi-day-sunny"></i>';
		case 1:
			return '<i class="wi wi-rain" title="Little rain"></i>';
		case 2:
			return '<i class="wi wi-rain" title="Medium rain"></i>';
		case 3:
			return '<i class="wi wi-rain" title="Hard rain"></i>';
		default:
			return ''
	}
}

function linkTag(id, type , content) {
	const url = `${base_url}/${type}/${id}`
	if (type == 'race') content = `#${content}`
	
	return `<a href="${url}">${content}</a>`;
}

function racetype(type) {
	type = parseInt(type)
	
	switch (type) {
		case 0:
			return 'Practice';
		case 1:
			return 'Qualify';
		case 2:
			return 'Race';
	}
}