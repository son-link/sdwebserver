<div class="container">
	<h1>
		<?= $car->name; ?>
	</h1>
	<div class="ct-info">
		<div class="ct-img">
			<?= imgTagFull($car->img, 'car', $car->name) ?>
		</div>
		<div class="ct-info-body">
			<div class="ct-info-row">
				<div class="ct-info-title">
					Category:
				</div>
				<div>
					<?= $car->category; ?>
				</div>
			</div>
			<div class="ct-info-row">
				<div class="ct-info-title">
					Engine:
				</div>
				<div>
					<?= $car->engine; ?>
				</div>
			</div>
			<div class="ct-info-row">
				<div class="ct-info-title">
					Drivetrain:
				</div>
				<div>
					<?= $car->drivetrain; ?>
				</div>
			</div>
			<div class="ct-info-row">
				<div class="ct-info-title">
					Width:
				</div>
				<div>
					<?= $car->width; ?>
				</div>
			</div>
			<div class="ct-info-row">
				<div class="ct-info-title">
					Length:
				</div>
				<div>
					<?= $car->length; ?>
				</div>
			</div>
			<div class="ct-info-row">
				<div class="ct-info-title">
					Fuel tank:
				</div>
				<div>
					<?= $car->fueltank; ?>
				</div>
			</div>
		</div>
	</div>
</div>