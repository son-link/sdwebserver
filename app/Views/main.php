<?php
	$menu = '<nav>';
	$menuSelect = '<select id="menu-select">';
	/*################################
	## generate the car category selection menu
	################################
	*/
	foreach ($carCategoriesList as $cat)
	{
		$class = '';
		$selected = '';
		//if the category contain no cars we do no consider it
		//todo: should we display only officially released ones?
		if ($cat->totalCars > 0)
		{
			//if no category has been chosen by the user, used the first valid (non empty) one
			if ($carCatId == '') $carCatId = $cat->id;

			//set a splecial class for the menu item that represent the currently selected class
			if ($carCatId == $cat->id )
			{
				$class = 'class="selected"';
				$selected = 'selected';
			}

			//echo "\n<a href='?cat=".$id."' $class>".$category->name."</a>";
			$url = rewriteUrl('cat', $cat->id);
			$menu .= "<a href=\"$url\" $class>{$cat->name}</a>";
			$menuSelect .= "<option value=\"$url\" $selected>{$cat->name}</option>";
		}
	}
	$menu .= '</nav>';
	$menuSelect .= '</select>';
?>
<div class="col-2 navbar navbar-vert" id="menu">
	<div id="menu-title">Categories:</div>
	<?= $menu ?>
	<?= $menuSelect ?>
</div>
<div class="container">
	<nav id="period">
		<strong>Period:</strong>
		<a id="today" href="<?= rewriteUrl('period','today'); ?>">Today</a>
		<a id="week" href="<?= rewriteUrl('period','week'); ?>">Week</a>
		<a id="month" href="<?= rewriteUrl('period','month'); ?>">Month</a>
		<a id="year" href="<?= rewriteUrl('period','year'); ?>">Year</a>
		<a id="allTime" href="<?= rewriteUrl('period','allTime'); ?>">All Time</a>
	</nav>

	<?php if (!empty($currCat)): ?>
	<h1 id="cat-title">
		<?= $currCat->name ?>
	</h1>
	<?php endif ?>

	<h3>
		Most active users<br />
		<small><?= $periodString; ?></small>
	</h3>
	<table class="fullPage responsive cat-table">
		<thead>
			<tr>
				<th>Pilot</th>
				<th>Races</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($users as $user):
		?>
				<tr>
					<td data-title="Pilot">
						<?= clickableName($user->username, 'user', $user->username) ?>
					</td>
					<td data-title="Races">
						<?= $user->count ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<h3>
		Bests lap for each track<br />
		<small><?=$periodString; ?></small>
	</h3>				
	<table class="fullPage responsive cat-table">
		<thead>
			<tr>
				<th>Track</th>
				<th>Pilot</th>
				<th>Car</th>
				<th>Laptime</th>
				<th>Weather</th>
				<th>Date</th>
				<th>Session</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($mylaps as $mylap):
				$track = $mylap->track_id;
				$car = $mylap->car_id;
		?>
		<tr>
			<td data-title="Track">
				<?= clickableName($mylap->track_id, 'track', $mylap->track_name) ?>
			</td>
			<td data-title="Pilot">
				<?= clickableName($mylap->username, 'user', $mylap->username) ?>
			</td>
			<td data-title="Car">
				<?= clickableName($mylap->car_id, 'car', $mylap->car_name) ?>
			</td>
			<td data-title="Laptime">
				<?= formatLaptime($mylap->bestlap); ?>
			</td>
			<td data-title="Weather">
				<?= weatherTag($mylap->wettness); ?>
			</td>
			<td data-title="Date">
				<?= $mylap->timestamp; ?>
			</td>
			<td data-title="Session">
				<a href="<?= base_url() ?>race/<?= $mylap->race_id ?>">#<?=$mylap->race_id?></a>
			</td>
		</tr>
		<?php endforeach ?>
		</tbody>
	</table>

	<h3>
		Most used Tracks<br />
		<small><?php echo $periodString; ?></small>
	</h3>
	<table class="fullPage responsive cat-table">
		<thead>
			<tr>
				<th>Track</th>
				<th>Races</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($tracks as $race): ?>
			<tr>
				<td data-title="Track">
					<?= clickableName($race->track_id, 'track', $tracksNames[$race->track_id]) ?>
				</td>
				<td data-title="Races">
					<?= $race->count ?>
				</td>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>
	<h3>
		Top cars<br />
		<small><?php echo $periodString; ?></small>
	</h3>
	<table class="fullPage responsive cat-table">
		<thead>
			<tr>
				<th>Car</th>
				<th>Races</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($cars as $car): ?>
			<tr>
				<td data-title="Car">
					<?= clickableName($car->car_id, 'car', $car->name) ?>
				</td>
				<td data-title="Races">
					<?= $car->count ?>
				</td>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>
</div>
<script>
	period = '<?= $period ?>';
</script>