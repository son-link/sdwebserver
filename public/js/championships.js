$('#select-week').on('change', function() {
	if (this.value) dt_prev_week.getData({ championship: this.value })
});

const dt_prev_week = new MiniDT({
	target: 'previous_weeks',
	get_data_start: false,
	url: `${base_url}/api/championship_bestlaps`,
	cols:  [
		{
			title: 'Time',
			col: 'laptime',
			render: (row) => {
				return formatLaptime(row.laptime)
			}
		},
		{
			title: 'Racers',
			col: 'username',
		},
		{
			title: 'Car',
			col: 'car_name',
		},
	],
})
