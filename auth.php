<?php

class Auth
{
	private static $key = "653ac8a32dfaa09881d77b31c03a9872e6091edd26a597193182d7085c91dd6829b426e447df6be063a4139f0af52c4aa6d636de6ca44f40543dac3a958dcdc0";
	private static $value = "db9e9432a3aedfd1dcd1cf82b4c75a74ad06b7613525afde1a39076519f94ea998ebcc299d8efdba5d2259f332526c03083b9554ee9f3798072356752570cc6b";



	public static function auth()
	{
		if (hash("sha512", filter_input(INPUT_GET, self::$key)) !== self::$value) {
			self::error();
		}
	}

	public static function error()
	{
		header("HTTP/1.0 404 Not Found");
		echo "
			<head>
				<title>404 Not Found</title>
			</head>
			<body>
				<h1>Not Found</h1>
				<p>The requested URL was not found on this server.</p>
				<hr>
				<address>Apache Server at azubiwerk-muenchen.de Port 443</address>
			</body>
		";
		die();
	}
}

Auth::auth();
