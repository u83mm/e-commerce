<?php
	declare(strict_types=1);
	
	use Core\App;
	use model\classes\Loader;

	require_once($_SERVER['DOCUMENT_ROOT'] . "/../Application/Core/aplication_fns.php");

	$loader = new Loader();
	$loader->init($_SERVER['DOCUMENT_ROOT'] . "/..");	
	
	$app = new App;
	$app->loadController();