<?php			
	/** Define site root */	
	define("SITE_ROOT", $_SERVER['DOCUMENT_ROOT']);

	/** Define URL */
	define('URL', $_SERVER['REQUEST_URI']);			
			
	/** Define path to save uploaded images files */
	define('STORAGE_IMAGES_PATH', SITE_ROOT . "/uploads/images");
	
	/** Define constants to save documents */
	define('STORAGE_DOCUMENTS_PATH', SITE_ROOT . "/uploads/documents");	
	define('MAX_FILE_SIZE', 3 * 1024 * 1024); // 3 MB

	/** Define DB config */
	define('DB_CONFIG_FILE', SITE_ROOT . '/../Application/Core/db.config.php');	
	
	session_start();	
?>