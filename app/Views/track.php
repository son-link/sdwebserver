<?php use App\Models\CarsModel; ?>
<div class="container">
	<h1><?= $track->name; ?></h1>
	<div class="ct-info">
		<div class="ct-img">
			<?= $track->imgTagFull() ?>
		</div>
		<div class="ct-info-body">
			<div class="ct-info-row">
				<div class="ct-info-title">
					Author:
				</div>
				<div>
					<?= $track->author; ?>
				</div>
			</div>
			<div class="ct-info-row">
				<div class="ct-info-title">
					Description:
				</div>
				<div>
					<?= $track->description; ?>
				</div>
			</div>
		</div>
	</div>
	<h3>Bests laps:</h3>
	<table class="fullPage responsive">
		<thead>
			<tr>
				<th>Racer</th>
				<th>Time</th>
				<th>Car</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($bestLaps as $bl): ?>
				<?php $car = new CarsModel(getCar($bl->car_id)) ?>
				<tr>
					<td data-title="Racer">
						<?= $bl->username ?>
					</td>
					<td data-title="Time">
						<?= formatLaptime($bl->laptime) ?>
					</td>
					<td data-title="Car">
						<?= $car->clickableName() ?>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>