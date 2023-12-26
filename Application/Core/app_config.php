<?php	

	define("SITE_ROOT", $_SERVER['DOCUMENT_ROOT']);
	define('URL', $_SERVER['REQUEST_URI']);		

	require_once(SITE_ROOT . '/../vendor/autoload.php');	

	session_start();
	session_regenerate_id();
?>
