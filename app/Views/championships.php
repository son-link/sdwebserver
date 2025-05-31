<div class="container">
	<h1>Current week</h1>
	<div class="championship-header">
		<h3>
			Track:
			<br />
			<small>
				<?= ($weeklyData != []) ? $weeklyData->track_id : 'No Data' ?>
			</small>
		</h3>

		<h3>
			Category:
			<br />
			<small>
				<?= (!empty($current) && count($current) > 0) ? $current[0]->category_name : 'No data' ?>
				<?= ($weeklyData != []) ? $weeklyData->car_cat : 'No Data' ?>
			</small>
		</h3>

		<h3>
			Weather:
			<br />
			<small>
				<?= ($weeklyData != []) ? weatherTag($weeklyData->wettness) : 'No Data' ?>
			</small>
		</h3>
	</div>

	<table id="current_week" class="responsive">
		<thead>
			<tr>
				<th>
					Time
				</th>
				<th>
					Racer
				</th>
				<th>
					Car
				</th>
				<th>
					Session
				</th>
				<th>
					Weather
				</th>
			</tr>
		</thead>
			<?php if (!empty($current) && count($current) > 0): ?>
				<?php foreach($current as $c): ?>
					<tr>
						<td data-title="Time">
							<?= formatLaptime($c->laptime) ?>
						</td>
						<td data-title="Racer">
							<?= linkTag($c->username, 'user', $c->username) ?>
						</td>
						<td data-title="Car">
							<?= linkTag($c->car_id, 'car', $c->car_name) ?>
						</td>
						<td data-title="Session">
							<?= linkTag($c->race_id, 'race', "#{$c->race_id}") ?>
						</td>
						<td data-title="Weather">
							<?= weatherTag($c->wettness) ?>
						</td>
					</tr>
				<?php endforeach ?>
			<?php else: ?>
				<td colspan="6" class="mini-dt nodata">
					No data
				</td>
			<?php endif ?>
		</tbody>
	</table>

	<h1>Current week races</h1>
	<div class="championship-header">
		<h3>
			Track:
			<br />
			<small>
				<?= ($weeklyData != []) ? $weeklyData->track_id : 'No Data' ?>
			</small>
		</h3>

		<h3>
			Category:
			<br />
			<small>
				<?= ($weeklyData != []) ? $weeklyData->car_cat : 'No Data' ?>
			</small>
		</h3>

		<h3>
			Laps:
			<br />
			<small>
				<?= ($weeklyData != []) ? $weeklyData->raceLaps : 'No Data' ?>
			</small>
		</h3>

		<h3>
			Min Valid Laps:
			<br />
			<small>
				<?= ($weeklyData != []) ? $weeklyData->minValidLaps : 'No Data' ?>
			</small>
		</h3>
	</div>

	<table id="current_week_races" class="responsive">
		<thead>
			<tr>
				<th>
					Total Time
				</th>
				<th>
					Racer
				</th>
				<th>
					Car
				</th>
				<th>
					Session
				</th>
				<th>
					Valid Laps
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($currentRaces as $c): ?>
				<tr>
					<td data-title="Time">
						<?= formatLaptime($c->total_laptime) ?>
					</td>
					<td data-title="Racer">
						<?= linkTag($c->username, 'user', $c->username) ?>
					</td>
					<td data-title="Car">
						<?= linkTag($c->car_id, 'car', $c->car_name) ?>
					</td>
					<td data-title="Session">
						<?= linkTag($c->race_id, 'race', "#{$c->race_id}") ?>
					</td>
					<td data-title="Valid Laps">
						<?= $c->valid_laps; ?>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>

	<!-- all races from this driver -->
	<h1>Previous weeks</h1>
	<div id="week-selector">
		<select id="select-week">
			<option disabled selected>Select week</option>
			<?php foreach($previous as $p): ?>
				<option value="<?= $p->id ?>">
					<?= "{$p->date_start_conv} - {$p->date_end_conv}" ?>
				</option>
			<?php endforeach ?>
		</select>
	</div>

	<div class="championship-header" id="previous-week-header">
		<h3>
			Track:
			<br />
			<small id="prev_track"></small>
		</h3>

		<h3>
			Category:
			<br />
			<small id="prev_category"></small>
		</h3>
	</div>

	<div class="table-container">
		<table id="previous_weeks" class="responsive cat-table"></table>
	</div>
</div>