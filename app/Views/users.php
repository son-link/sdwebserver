<div class="container">
	<div class="cards">
	<?php foreach ($users as $user): ?>
		<a href="<?= base_url() ?>user/<?= $user->username ?>" class="user-card">
			<div class="card-head">
				<div class="card-title">
					<?= $user->username ?>
				</div>
				<div class="card-img">
					<img src="<?=base_url()?>img/users/<?=$user->img?>">
				</div>
			</div>
			<div class="card-body">
				<div class="body-title">
					Member since:
				</div>
				<div>
					<?= date_format(new DateTime($user->registrationdate), 'd M Y @ H:i') ?>
				</div>
				<div class="body-title">
					Latest activity:
				</div>
				<div>
					<?php
						if($user->sessiontimestamp>0)
						{
							$date1 = new DateTime("");
							$date2 = new DateTime($user->sessiontimestamp);
							$interval = $date1->diff($date2);
							$ago='';
							$years = $interval->y;
							$months = $interval->m;
							$days = $interval->d;
							$hours = $interval->h;
							$minutes = $interval->i;
							$done = false;
							//echo $interval->d;

							if($years > 0)
							{
								$ago .= $years." years ";
								$done = true;
							}

							if ($months > 0 && !$done)
							{
								$ago .= $months." months ";
								$done = true;
							}
 
							if ($days > 0 && !$done)
							{
								$ago .= $days." days ";
								$done = true;
							}

							if ($hours > 0 && !$done)
							{
								$ago .= $hours." hours ";
								$done = true;
							}

							if ($minutes > 0 && !$done)
							{
								$ago .= $minutes." minutes ";
							}

							if ($years < 1 && $months < 1 && $days < 1 && $hours < 1 && $minutes < 1)
							{
								$ago = 'Now';	
							}
							else
							{
								$ago .= 'ago';
							}
						}
						else
						{
							$ago = 'Never been active';
						}
					?>
					<?= $ago ?>
				</div>
				<div class="body-title">
					Country:
				</div>
				<div>
					<?php $flag = str_replace(' ', '_', $user->nation) ?>
					<img src="<?=base_url()?>/img/flags/flags_small/<?=$flag?>.png" alt="<?=$user->nation?>" title="<?=$user->nation?>"/> <?=$user->nation?>
				</div>
			</div>
		</a>
	<?php endforeach; ?>
</div>