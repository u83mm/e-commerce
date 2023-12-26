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
            $url = $this->splitUrl();

            /** select controller */
            $filename = SITE_ROOT . "/../Application/Controller/" . ucfirst($url[0]) . "Controller.php";                                           
            
            if(file_exists($filename)) {
                require_once($filename);                
                $this->controller = ucfirst($url[0]) . "Controller";                
                //unset($url[0]);                               
            }
            else {
                $filename = SITE_ROOT . "/../Application/Controller/ErrorController.php";    
                require_once($filename);
                $this->controller = "ErrorController";
            }                         
            
            $controller = new $this->controller;                        
            
            /** select method */
            if(count($url) > 1) {
                if(method_exists($controller, $url[1])) {
                    $this->method = $url[1];
                }
                //unset($url[1]);
            }                         

            call_user_func_array([$controller, $this->method], $url);
        }
    }    
?>
