<div class="container">
	<h1>Current week</h1>
	<div class="championship-header">
		<h3>
			Track:
			<br />
			<small>
				<?= (!empty($current) && count($current) > 0) ? $current[0]->track_name : 'No data' ?>
			</small>
		</h3>

		<h3>
			Category:
			<br />
			<small>
				<?= (!empty($current) && count($current) > 0) ? $current[0]->category_name : 'No data' ?>
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