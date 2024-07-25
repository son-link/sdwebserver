	</div><!-- #main -->
	<script type="text/javascript">
		const base_url = '<?= base_url() ?>';
	</script>
	<script type="text/javascript" src="<?=base_url()?>/js/pequejs.js"></script>
	<script type="text/javascript" src="<?=base_url()?>/js/axios.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>/js/app.js"></script>
	<?php if(!empty($custom_js)): ?>
		<?php foreach ($custom_js as $js): ?>
			<script type="text/javascript" src="<?=base_url()."/js/$js"?>"></script>
		<?php endforeach ?>
	<?php endif ?>
</body>
</html>