<?php
    declare(strict_types = 1);
    
    namespace model\classes;

    use Application\model\classes\interfaces\ValidateInterface;
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
                if (empty($value) || !isset($value)) {                                                              
                    $this->msg = "'" . ucfirst($key) . "' is a required field.";  
                    return false;                                   				
                }

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
    }
?>
