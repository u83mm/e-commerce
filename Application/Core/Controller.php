<?php    

    namespace App\Core;
    
    use model\classes\AccessControl;
    use model\classes\NavLinks; 	

    class Controller    
    {
    	use NavLinks;
    	use AccessControl;

        public function __construct(protected object $dbcon = DB_CON)
        {
            
        }
    	
        /** Properties to use twig */
        private object $loader;
        private object $twig;        

        /** Show html templates */
        public function view(string $name, array $data = []) 
        {
            if(!empty($data)) extract($data);
            $filename = SITE_ROOT . "/../Application/view/" . $name . "_view.php";                      
            
            if(file_exists($filename)) {                
                require_once($filename);                
            }
            else {
                $filename = SITE_ROOT . "/../Application/view/database_error.php";    
                require_once($filename);
            }    
        }

        /** Show twig templates */
        public function render(
            string $template,
            array $parameters,                   
        )
        {   
            $this->loader = new \Twig\Loader\FilesystemLoader(SITE_ROOT . '/../Application/view');
            $this->twig = new \Twig\Environment($this->loader); 
                    
            foreach ($parameters as $key => $value) {
                $parameters[$key] = $value;
            }
            
            echo $this->twig->render($template, $parameters);
            die;
        }
        
        /** Shows nav menus */
        public function showNavLinks(): array {                     
            if(isset($_SESSION['role'])) {                
               match ($_SESSION['role']) {
                'ROLE_ADMIN'    => $nav_links = $this->admin(),
                'ROLE_USER'     => $nav_links = $this->user(),
               };

               array_pop($nav_links);
               $nav_links['Logout'] = '/logout';
            }
            else {
                $nav_links = $this->user();
            }

            return $nav_links;
        }
    }    
?>