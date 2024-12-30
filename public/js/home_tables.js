const urlParams = new URL(window.location).searchParams
const car_cat = (!!urlParams.get('cat')) ? urlParams.get('cat') : 'TRB1'
const period = (!!urlParams.get('period')) ? urlParams.get('period') : 'today'

const dt_active_users = new MiniDT({
	target: 'most_active_users',
	url: `${base_url}/api/most_active_users`,
	cols:  [
		{
			title: 'Pilot',
			col: 'username',
			render: (row) => {
				return linkTag(row.username, 'user', row.username)
			}
		},
		{
			title: 'Races',
			col: 'count',
		},
	],
	params: {
		period: period,
		car_cat: car_cat
	}
})

const dt_bests_laps = new MiniDT({
	target: 'best_laps',
	url: `${base_url}/api/bests_laps`,
	cols:  [
		{
			title: 'Track',
			col: 'track_name',
			render: (row) => {
				return linkTag(row.track_id, 'track', row.track_name)
			}
		},
		{
			title: 'Pilot',
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
			title: 'Laptime',
			col: 'bestlap',
			render: (row) => {
				return formatLaptime(row.bestlap)
			}
		},
		{
			title: 'Weather',
			col: 'wettness',
			align: 'center',
			render: (row) => {
				return weatherTag(parseInt(row.wettness))
			}
		},
		{
			title: 'Date',
			col: 'timestamp'
		},
		{
			title: 'Session',
			col: 'race_id',
			render: (row) => {
				return linkTag(row.race_id, 'race', row.race_id)
			}
		},
	],
	params: {
		period: period,
		car_cat: car_cat
	}
})