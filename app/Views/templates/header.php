<!doctype html>

<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?=$title?></title>
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/css/style.css" />
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/font/weather-icons.css" />
</head>
<body>
	<nav class="mainMenu">
		<a href="<?= base_url() ?>">Home</a>
		<a href="<?= base_url('users') ?>">Racers</a>
		<!--<li><a href="./live.php">Live stats</a></li>-->
		<a href="<?= base_url('register') ?>">Register</a>
		<a href="<?= base_url('login') ?>">Login</a>
		<a href="https://speed-dreams.net" target="_black">Blog</a>
	</nav>
	
	<div id="header_logo">
		<img class="logo" src="<?= base_url() ?>/img/sd-flag.png" alt="Speed Dreams logo"/>
	</div>

	<div id="main">
