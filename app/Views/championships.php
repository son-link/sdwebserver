<div class="container">
	<h1>Current week</h1>
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
			</tr>
		</thead>
		<tbody>
			<?php foreach($current as $c): ?>
				<tr>
					<td data-title="Time">
						<?= formatLaptime($c->laptime) ?>
					</td>
					<td data-title="Racer">
						<?= $c->username ?>
					</td>
					<td data-title="Car">
						<?= $c->car_name ?>
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
					<?= "{$p->date_start} - {$p->date_end}" ?>
				</option>
			<?php endforeach ?>
		</select>
	</div>
	<div class="table-container">
		<table id="previous_weeks" class="responsive cat-table"></table>
	</div>
</div>