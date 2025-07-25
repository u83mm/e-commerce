<?php
    declare(strict_types = 1);
    
    namespace App\model\classes;

    use Application\model\classes\interfaces\ValidateInterface;
    use finfo;
    use PDOException;
    use PDO;

    /**
     * Validate inputs
     */
    class Validate implements ValidateInterface
    {
    	private $msg;
    	
        /**
         * Method to validate fields from form
         */
        public function test_input(int|string|float|null $data): int|string|float|null
        {
            if(is_null($data) || ctype_space($data)) return null;            

            if(!is_int($data) && !is_float($data)) {
                $data = htmlspecialchars($data);
                $data = trim($data);
                $data = stripslashes($data);
            }
    
            return $data;
        }
        
        /**
         * Method to validate e-mail fields from form
         */
        public function validate_email(string $email): bool {
			if(preg_match('/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/', $email)) {
				return true;
			}
			else {
				return false;
			}
		}
		
		/**
         * Método para validar entradas de formulario
         */
        public function validate_form(array $fields): bool
        {                                         
            foreach ($fields as $key => $value) {
                // Validate uploaded files                
                if((is_array($value) && $key === 'pdf_file')) {
                    $allowed_types = ['application/pdf'];

                    if(!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
                        $this->msg = $this->getUploadError($value['error']);
                        return false; 
                    }

                    // Validate file size
                    if($value['size'] > MAX_FILE_SIZE) {
                        $this->msg = "File size must be less than 3MB.";
                        return false;
                    }

                    // Check file type
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $file_mime = $finfo->file($value['tmp_name']);
                    
                    if (!in_array($file_mime, $allowed_types)) {
                        $this->msg = "Only PDF files are accepted.";
                        return false;
                    }
                    
                    /* $this->msg = $this->getUploadError($value['error']);
                    return false; */
                }

                // Validate terms and conditions
                if($key === "terms_agreement" && !isset($value)) {
                    $this->msg = "You must accept the terms and conditions.";
                    return false;
                }

                // Validate required fields
                if (empty($value) || !isset($value)) {                                                              
                    $this->msg = "'" . ucfirst($key) . "' is a required field.";  
                    return false;                                   				
                }

                // Validate e-mail
                if($key === "email" && !$this->validate_email($value)) {
                    $this->msg = "Insert a valid e-mail.";
                    return false;
                }                
            }
                      
            return true;
        }
        
        /**
         * Show validation messages
         */
        public function get_msg(): string 
        {
            return $this->msg;
        }

        /**
         * Generate and store a CSRF token in the session
         *
         * @return string The generated CSRF token
         */
        public function csrf_token(): string
        {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $_SESSION['csrf_token'];
        }

        /**
         * Validate the CSRF token from the session and form submission
         *
         * @return bool True if the CSRF token is valid, false otherwise
         */
        public function validate_csrf_token(): bool
        {
            return isset($_SESSION['csrf_token'], $_POST['csrf_token'])
                && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
        }
        
        private function getUploadError($error_code) {
        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'File is too large (server limit).',
            UPLOAD_ERR_FORM_SIZE  => 'File is too large (form limit).',
            UPLOAD_ERR_PARTIAL    => 'File upload was incomplete.',
            UPLOAD_ERR_NO_FILE    => 'No file was selected.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server configuration error.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'File upload blocked by extension.',
        ];
        return $errors[$error_code] ?? 'Unknown upload error';
    }
    }
?>
