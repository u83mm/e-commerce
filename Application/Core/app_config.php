<?php			
	/** Define site root */	
	define("SITE_ROOT", $_SERVER['DOCUMENT_ROOT']);

	/** Define URL */
	define('URL', $_SERVER['REQUEST_URI']);			

	/** Configure directories to load their classes */
	\Application\model\classes\Loader::init(SITE_ROOT . "/..");
		
	/** Define path to save uploaded images files */
	define('STORAGE_IMAGES_PATH', SITE_ROOT . "/uploads/images");
	
	/** Define constants to save documents */
	define('STORAGE_DOCUMENTS_PATH', SITE_ROOT . "/uploads/documents");	
	define('MAX_FILE_SIZE', 3 * 1024 * 1024); // 3 MB
	
	session_start();
	session_regenerate_id();
?>