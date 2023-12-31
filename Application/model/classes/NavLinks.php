<?php
    namespace model\classes;

    trait NavLinks
    {
        public function __construct(private array $menus = [])
        {
            
        }

        public function admin(): array
        {
            $this->menus = [
                "Home"				=>	"/",				
				"Registration"		=> 	"/register",
				"Administration"	=>	"/admin/admin",	
                'Catalog'           =>  "/products",			
				"Login"			    => 	"/login",
            ];

            return $this->menus;
        }

        
        public function user(): array
        {
            $this->menus = [
                "Home"				=>	"/",				
				"Registration"		=> 	"/register",
                'Catalog'           =>  "/products",				
				"Login"			    => 	"/login",
            ];

            return $this->menus;
        }
    }    
?>
