<?php
	declare(strict_types=1);

	use Application\model\classes\Loader;
	use Core\App;	

	require_once($_SERVER['DOCUMENT_ROOT'] . "/../Application/Core/aplication_fns.php");

	$loader = new Loader([$_SERVER['DOCUMENT_ROOT'] . "/.."]);	
	
	$app = new App();
	$app->loadController();