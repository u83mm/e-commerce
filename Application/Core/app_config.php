<?php			
	/** Define site root */	
	define("SITE_ROOT", $_SERVER['DOCUMENT_ROOT']);

	/** Define URL */
	define('URL', $_SERVER['REQUEST_URI']);		

	require_once(SITE_ROOT . '/../vendor/autoload.php');

	/** Configure directories to load their classes */
	//\Application\model\classes\Loader::init(SITE_ROOT . "/..");
		
	/** Define path to save uploaded images files */
	define('STORAGE_IMAGES_PATH', SITE_ROOT . "/uploads/images");		
?>