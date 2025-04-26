<div class="container">
	<table id="race-info">
		<tr>
			<th>Session Type: </th>
			<td>
				<?= racetype($race->type) ?>
			</td>
		</tr>
		<tr>
			<th>Date:</th>
			<td>
				<?= date_format(new DateTime($race->timestamp), 'd M Y @ H:i') ?>
			</td>
		</tr>
		<tr>
			<th>Track:</th>
			<td id="race-track-img">
				<?= linkTitleImgTag($race->track_id, 'track', $race->track_name, $race->track_img) ?>
			</td>
		</tr>
		<tr>
			<th>Car:</th>
			<td>
				<?= linkTitleImgTag($race->car_id, 'car', $race->car_name, $race->car_img) ?>
			</td>
		</tr>
		<tr>
			<th>User:</th>
			<td>
				<?= clickableName($race->username, 'user', $race->username) ?>
			</td>
		</tr>
		<tr>
			<th>Laps completed:</th>
			<td>
				<?= $race->n_laps ?>
			</td>
		</tr>
	</table>

	<h1>Laps</h1>
	<div class="table-container">
		<table id="race-laps" class="responsive">
			<thead>
				<tr>
					<th>Lap</th>
					<th>Laptime</th>
					<th>Position</th>
					<th>Fuel</th>
					<th>Valid</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach(json_decode($laps) as $i => $lap): ?>
					<tr>
						<td>
							<?= $i + 1 ?>
						</td>
						<td>
							<?= formatLaptime($lap->laptime) ?>
						</td>
						<td>
							<?= $lap->position ?>
						</td>
						<td>
							<?= $lap->fuel ?>
						</td>
						<td>
							<?= ($lap->valid == 1) ? '<i class="lap-valid"></i>' : '<i class="lap-invalid"></i>' ?>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>

	<table id="race-laps">
		
	</table>

	<div>
  		<canvas id="chart"></canvas>
	</div>
</div>
<?php if (!empty($laps)): ?>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", () => {
		let lapsData = JSON.parse('<?= $laps ?>');

		let labels = [];
		let positions = [];
		let fuel = [];
		let laptime = []

		for (i=0; i < lapsData.length; i++) {
			labels.push(`Lap ${i+1}`);
			positions.push(parseInt(lapsData[i].position));
			fuel.push(parseFloat(lapsData[i].fuel));
			laptime.push(parseFloat(lapsData[i].laptime));
		}

		const data = {
			labels: labels,
			datasets: [
				{
					label: 'Position',
					backgroundColor: 'rgb(255, 99, 132)',
					borderColor: 'rgb(255, 99, 132)',
					data: positions,
					yAxisID: 'y',
					reverse: true,
				},
				{
					label: 'Fuel',
					backgroundColor: 'rgb(255, 99, 255)',
					borderColor: 'rgb(255, 99, 255)',
					data: fuel,
					yAxisID: 'y1',
				},
				{
					label: 'Time Lap',
					backgroundColor: 'rgb(0, 99, 255)',
					borderColor: 'rgb(0, 99, 255)',
					data: laptime,
					yAxisID: 'y2',
				}
			]
		};

		const config = {
			type: 'line',
			data: data,
			options: {
				responsive: true,
				/*interaction: {
				mode: 'index',
				intersect: false,
				},*/
				stacked: false,
				plugins: {
				title: {
					display: true,
					text: 'Race graph'
				}
				},
				scales: {
				y: {
					type: 'linear',
					display: true,
					position: 'left',
					reverse: true,
					min: 1,
					text: 'Pos.'
				},
				y1: {
					type: 'linear',
					display: true,
					position: 'left',
					max: 100,
					min: 0,
					text: 'Fuel'
				},
				y2: {
					type: 'linear',
					display: true,
					position: 'left',
					text: 'Time'
				},
				}
			}
		};

		const myChart = new Chart(
			document.getElementById('chart'),
			config
		);
	});
	
</script>
<?php endif ?>
