<!doctype html>

<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?=$title?></title>
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/css/microcss.css" />
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/css/fontello-embedded.css" />
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/css/sd-icons.css" />
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/css/style.css" />
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/css/dashboard.css" />
</head>
<body class="row">
	<nav id="dashmenu" class="navbar navbar-vert col-2">
		<a href="<?= base_url() ?>/dashboard/user"><i class="sd-account_box"></i>My User</a>
		<!--a href="<?= base_url() ?>/dashboard/users"><i class="sd-groups"></i>Users</a-->
		<a href="<?= base_url() ?>/dashboard/races"><i class="sd-sports"></i>Races</a>
		<a href="<?= base_url() ?>/dashboard/logout"><i class="sd-logout"></i>Logout</a>
	</nav>
	
	<header>
		<h3><?=$title?></h3>
	</header>

	<div id="dashboard" class="col-10">
