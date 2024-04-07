<?php
	$P = percentStr($timeontrackPractice, $timeontrack);
	$Q = percentStr($timeontrackQualify, $timeontrack);
	$R = percentStr($timeontrackRace, $timeontrack);
?>
<div class="container">
	<div id="user-header">
		<div id="user-img">
			<img class="avatar" src="<?= base_url("img/users/{$user->img}") ?>" alt="<?= $user->username ?>">
		</div>
		<div id="user-info">
			<div>
				<span class="user-info-title">Name:</span> <?= $user->username ?>
			</div>
			<div>
				<span class="user-info-title">Country:</span>
				<img src="<?=base_url("img/flags/flags_small/<?=$user->flag")?>" alt="<?=$user->nation?>" > <?=$user->nation ?>
			</div>
			<div>
				<span class="user-info-title">Total time:</span><?= secondsToTime(round($timeontrack, 0)) ?>
			</div>
			<div>
				<span class="user-info-title">Total races:</span><?= count($userRaces) ?>.
			</div>
			<div>
				<span class="user-info-title">Wins:</span><?= (int) $racesWon ?>
			</div>
			<div>
				<span class="user-info-title">Second:</span><?= (int) $racespodiums->totalSilver ?>
			</div>
			<div>
				<span class="user-info-title">Third:</span><?= (int) $racespodiums->totalBronze ?>
			</div>
			<div>
				<span class="user-info-title">Favorite car:</span>
				<?php if ($mostusedcar->car) echo clickableName($mostusedcar->car->id, 'car', $mostusedcar->car->name); ?>
			</div>
			<div>
				<span class="user-info-title">Favorite track:</span>
				<?php if ($mostusedtrack->track) echo clickableName($mostusedtrack->track->id, 'track', $mostusedtrack->track->name); ?>
			</div>
		</div>
	</div>
	<h1>User Stats</h1>
	<table id="user-stats" class="responsive">
		<thead>
			<tr>
				<th>
					Race time
				</th>
				<th>
					Qualify Sessions
				</th>
				<th>
					Qualify Time
				</th>
				<th>
					Practice Sessions
				</th>
				<th>
					Practice Time:
				</th>
				<th>
					Retired / Not finished
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td data-title="Race Time">
					<?= secondsToTime(round($timeontrackRace)) . "<br />($R)" ?>
				</td>
				<td data-title="Qualify Sessions">
					<?= $qualifiescount ?>
				</td>
				<td data-title="Qualify Time">
					<?= secondsToTime(round($timeontrackQualify)) . "<br />($Q)" ?>
				</td>
				<td data-title="Practice Sessions">
					<?= $practicescount ?>
				</td>
				<td data-title="Practice Time">
					<?= secondsToTime(round($timeontrackPractice)) . "<br />($P)" ?>
				</td>
				<td data-title="Retired / Not Finished">
					<?= "$racesretired<br />($racesretiredpercent)" ?>
				</td>
			</tr>
		</tbody>
	</table>

	<!-- all races from this driver -->
	<h1>Latest Races</h1>
	<table class="responsive">
		<thead>
			<tr>
				<th>Session ID</th>
				<th>Type</th>
				<th>Started on</th>
				<th>Track</th>
				<th>Car</th>
				<th>Finish Position<br>(gain/loss)</th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($raceSessions as $race):
		?>
			<tr>
				<td data-title="Session ID">
					<a href="<?=base_url("race/{$race->id}")?>"><?=$race->id?></a>
				</td>
				<td data-title="Type">
					<?= racetype($race->type) ?>
				</td>
				<td data-title="Started on">
					<?= date_format(new DateTime($race->timestamp), 'd M Y @ H:i') ?>
				</td>
				<td data-title="Track">
					<?= $race->track_name ?>
				</td>
				<td data-title="Car">
					<?= $race->car_name ?>
				</td>
				<td data-title="Finish Position">
				<?php
					if ($race->endposition > 0)
					{
						echo $race->endposition;
						$gain = $race->startposition - $race->endposition;

						if ($gain >= 0) echo " <sup style='color:green;'>(+$gain)</sup>";
						else echo "<sup style='color:red;'>($gain)</sup>";
					}
					else echo 'Retired/Not finished';
				?>
				</td>
			</tr>
		<?php
		endforeach;
		?>
		</tbody>
	</table>
</div>