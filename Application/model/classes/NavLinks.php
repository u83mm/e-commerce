<?php    
    declare(strict_types=1);

    namespace Application\model\classes;

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
                "Show documents"    =>  "/admin/document/showDocuments",
                'Catalog'           =>  "/products/products/index",				
				"Login"			    => 	"/login",
            ];

            return $this->menus;
        }

        public function visitor(): array
        {
            $this->menus = [
                "Home"				=>	"/",								
                'Catalog'           =>  "/products/products/index",				
				"Login"			    => 	"/login",
            ];

            return $this->menus;
        }
        
    }    
?>
