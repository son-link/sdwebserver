const url = new URL(window.location)
const username = url.pathname.split('/').slice(-1);
console.log(url, username)

const dt_user_races = new MiniDT({
	target: 'last_user_races',
	url: `${base_url}/api/user_races`,
	params: {
		username: username
	},
	cols:  [
		{
			title: 'Session ID',
			col: 'id',
			render: (row) => {
				return linkTag(row.id, 'race', row.id)
			}
		},
		{
			title: 'Type',
			col: 'type',
			render: (row) => {
				return racetype(row.type)
			}
		},
		{
			title: 'Started on',
			col: 'timestamp',
			render: (row) => {
				const date = new Date(row.timestamp)
				return date.toLocaleString('en-US', { timeZone: 'Europe/Madrid' })
			}
		},
		{
			title: 'Track',
			col: 'track_name',
			render: (row) => {
				return linkTag(row.track_id, 'track', row.track_name)
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
			col: 'endposition',
			render: (row) => {
				let position = 'Retired/Not finished'
				const startposition = parseInt(row.startposition)
				const endposition = parseInt(row.endposition)

				if (endposition > 0) {
					position = endposition
					const gain = startposition - endposition;

					const color = (row.gain >= 0) ? 'green' : 'red'
					position += ` <sup style='color:${color};'>(+${gain})</sup>`;
				}

				return position
			}
		},
	],
})
