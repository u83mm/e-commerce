<?php
    namespace Core;

    class App
    {
        public function __construct(
            private string $controller = "", 
            private string $method = "index",
        )
        {
            
        }
        private function splitUrl(): array|string {           
            $url = URL === '/' ? 'home' : URL;
            $url = explode('/', trim($url, "/")); 
            
            if(empty($url[0])) {
                array_shift($url);                
                return $url;
            }                                 

            return $url;
        }

        public function loadController(): void {            
            session_start();
            session_regenerate_id();      

            $url = $this->splitUrl();

            /** select controller */
            $filename = SITE_ROOT . "/../Application/Controller/" . ucfirst($url[0]) . "Controller.php"; 
            
            if(!file_exists($filename)) {
                $filename = SITE_ROOT . "/../Application/Controller/$url[0]/" . ucfirst($url[0]) . "Controller.php";                
            }
            
            /** build route to controller */
            $streep_first = SITE_ROOT . "/..";
            $controller_path = str_replace(['/var/www/public/..', '.php'], '', $filename); 
            $controller_path = str_replace('/', '\\', $controller_path);           
            
            if(file_exists($filename)) {
                //require_once($filename);                                          
                $this->controller = ucfirst($url[0]);  
                //$controller_path = '\Application\Controller\\' . strtolower($this->controller) . '\\' . ucfirst($this->controller) . 'Controller';               
                array_shift($url);                               
            }
            /* else {
                //$filename = SITE_ROOT . "/../Application/Controller/ErrorController.php";    
                //require_once($filename);
                $this->controller = "ErrorController";
                $controller_path = '\Application\Controller\\' . ucfirst($this->controller); 
            } */
            
            /** build controller */                                     
            $controller = new $controller_path();                        
            
            /** select method */
            if(count($url) > 0) {
                if(method_exists($controller, $url[0])) {
                    $this->method = $url[0];
                    array_shift($url);
                }  
            }
                                
            call_user_func_array([$controller, $this->method], $url);
        }
    }    
?>