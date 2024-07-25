<?= get_header('404: Page not found') ?>
<div class="container">
	<h1>404 - Page Not Found</h1>

	<img id="img_404" src="<?= base_url('img/404.svg') ?>" />

	<p id="text_404">
		<?php if (!empty($message) && $message !== '(null)') : ?>
			<?= nl2br(esc($message)) ?>
		<?php else : ?>
			Sorry! Cannot seem to find the page you were looking for.
		<?php endif ?>
	</p>
</div>
<?= get_footer() ?>