<?php
	require_once (__DIR__.'classes/class.php');
	$req = new Parser('!ayat alfatihah:1');
	echo $req.parser();