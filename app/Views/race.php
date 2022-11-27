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
				<?= $track->linkTitleImgTag() ?>
			</td>
		</tr>
		<tr>
			<th>Car:</th>
			<td>
				<?= $car->linkTitleImgTag() ?>
			</td>
		</tr>
		<tr>
			<th>User:</th>
			<td>
				<?= $user->getLink() ?>
			</td>
		</tr>
		<tr>
			<th>Laps completed:</th>
			<td>
				<?= $race->n_laps ?>
			</td>
		</tr>
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
