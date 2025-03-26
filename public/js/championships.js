$('#select-week').on('change', async function() {
	if (this.value) await dt_prev_week.getData({ championship: this.value })
	if (!!dt_prev_week.rows.length > 0) {
		$('#prev_track').text(dt_prev_week.rows[0].track_name)
		$('#prev_category').text(dt_prev_week.rows[0].category_name)
	}
});

$('#prev_track').text('Not selected')
$('#prev_category').text('Not selected')

const dt_prev_week = new MiniDT({
	target: 'previous_weeks',
	get_data_start: false,
	url: `${base_url}/api/championship_bestlaps`,
	show_pagination: false,
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
			render: (row) => {
				return linkTag(row.username, 'user', row.username)
			}
		},
		{
			title: 'Car',
			col: 'car_name',
			render: (row) => {
				return linkTag(row.car_id, 'car', row.car_name)
			}
		},
		{
			title: 'Session',
			col: 'race_id',
			render: (row) => {
				return linkTag(row.race_id, 'race', row.race_id)
			}
		},
	],
})
