<?php

use App\Models\UsersModel;

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Welcome to CodeIgniter 4!</title>
	<meta name="description" content="The small framework with powerful features">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/png" href="/favicon.ico"/>

	<!-- STYLES -->

	<style {csp-style-nonce}>
		html, body {
			color: rgba(33, 37, 41, 1);
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
			font-size: 16px;
			margin: 0;
			padding: 0;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			text-rendering: optimizeLegibility;
		}
	</style>
</head>
<body>
<table class="fullPage">
		<thead>
			<tr>
				<th colspan="7">Bests lap for each track<br><small><?=$periodString; ?></small></th>
			</tr>
		</thead>
		<tbody>
		<tr>
			<td>Track</td>
			<td>Pilot</td>
			<td>Car</td>
			<td>Laptime</td>
			<td>Weather</td>
			<td>Date</td>
			<td>Session</td>
		</tr>
		<?php
			foreach ($mylaps as $mylap):
				$user = new UsersModel($mylap->user_id);
				$track = $mylap->track_id;
				$car = $mylap->car_id;
		?>
		<tr>
			<td>
				<?= getTrack($track)->clickableName(); ?>
			</td>
			<td>
				<?= $user->getLink(); ?>
			</td>
			<td>
				<?= getCar($car)->clickableName(); ?>
			</td>
			<td>
				<?= formatLaptime($mylap['bestlap']); ?>
			</td>
			<td>
				<?= weatherTag($mylap['wettness']); ?>
			</td>
			<td>
				<?= $mylap['timestamp']; ?>
			</td>
			<td>
				<a href="./race.php?id=<?= $mylap['race_id'] ?>">#<?=$mylap['race_id']?></a>
			</td>
		</tr>
		<?php endforeach ?>
		</tbody>
	</table>
</body>
</html>
