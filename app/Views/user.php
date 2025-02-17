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
				<img src="<?=base_url("img/flags/flags_small/$user->flag")?>" alt="<?=$user->nation?>" > <?=$user->nation ?>
			</div>
			<div>
				<span class="user-info-title">Total time:</span><?= secondsToTime(round($timeontrack, 0)) ?>
			</div>
			<div>
				<span class="user-info-title">Total races:</span><?= $userRaces ?>.
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
				<?php if (!empty($mostusedcar->car)) echo clickableName($mostusedcar->car->id, 'car', $mostusedcar->car->name); ?>
			</div>
			<div>
				<span class="user-info-title">Favorite track:</span>
				<?php if (!empty($mostusedtrack->track)) echo clickableName($mostusedtrack->track->id, 'track', $mostusedtrack->track->name); ?>
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
	<div class="table-container">
		<table id="last_user_races" class="responsive cat-table"></table>
	</div>
</div>