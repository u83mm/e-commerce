<?php   
    declare(strict_types=1);

    namespace App\model\classes;

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
				"Administration"	=>	"/admin/admin/index",	
                'Catalog'           =>  "/products/products/index",			
				"Login"			    => 	"/login",
            ];

            return $this->menus;
        }

        
        public function user(): array
        {
            $this->menus = [
                "Home"				=>	"/",				
				"Registration"		=> 	"/register",
                'Catalog'           =>  "/products/products/index",				
				"Login"			    => 	"/login",
            ];

            return $this->menus;
        }
    }    
?>
