<?php
if (!$this->session->userdata('loggedIn')) {
	redirect(base_url('login'));
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex">
	<meta name="google-site-verification" content="Pvtsk7aZGnooWc4OLq-h5K2VR12YvfglBTAZSlpMlQo" />
	<title>My Outlet Store</title>
	<link href=" <?php echo base_url('asset/css/styles.css') ?>" rel="stylesheet" type="text/css" />

	<style>
		body {
			background-color: #F2F9FD;
		}

		.row:has(.dataTables_length),
		.dataTables_paginate,
		.box-tools {
			display: none !important;
		}
		div.dataTables_info {
			display: none;
		}


		a {
			text-decoration: none;
		}

		.trev-item {
			display: flex;
   		    justify-content: flex-start;
    		align-items: center;
    		gap: 12px;
    		font-size: 13px;
    		transform: translateX(-1cm);
    		margin: 0%;
    		padding: 5%;
		}

		li.active {
			border: 2px solid #1E272C;
			background-color: #1E272C;
		}

		.skin-blue .main-header .navbar {
			width: 100%;
			border-radius: 0%;
			height: 1cm;
		}

		.right {
			transform: translateX(18cm);
		}
		.nav-item a{
			color: #F2F9FD;
		}
		
		
	</style>


</head>

<body class="skin-blue" style="height: 100%; position: relative;">