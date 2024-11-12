<?php
    declare(strict_types = 1);

    namespace Application\Controller;

    use App\Core\Controller;
    use model\classes\Query;
    use model\classes\Validate;

    class LoginController extends Controller
    {    
        
        public function __construct(
            private string $message = "",
            private array $limited_access_data = [],
			private int $remaining_time = 0,
            private array $fields = [],
        ){
            
        } 

        public function index(): void
        { 
            $validate = new Validate;
            $query_object = new Query;                     

            try {
                if(isset($_SESSION['id_user'])) {
                    header('Location: /');
                    die();
                }

                if($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Test for restrictions
					$this->limited_access_data = $query_object->selectOneBy("limit_access", "ip", $_SERVER['REMOTE_ADDR']) ? 
                        $query_object->selectOneBy("limit_access", "ip", $_SERVER['REMOTE_ADDR']) : [];						

					// If the IP is restricted, return the remaining time
					if(count($this->limited_access_data) > 0) {						
						if($this->limited_access_data['failed_tries'] >= 3) $this->remaining_time = $this->limited_access_data['restriction_time'] - time();
					}

                    // Get values from login form
                    $this->fields = [
                        'email'     =>  $validate->test_input(strtolower($_REQUEST['email'])),
                        'password'  =>  $validate->test_input($_REQUEST['password']),
                    ];

                    if(!$validate->validate_csrf_token()) {
                        $this->message = "Invalid CSRF token";																					
                    }
                    else {
                        if($this->remaining_time <= 0) {                        
                            // Validate form                    
                            if($validate->validate_form($this->fields)) {
                                if(!isset($_SESSION['id_user'])) {                                
                                    // Test user to do login                           
                                    $result = $query_object->selectLoginUser('users', 'roles', 'id_role', $this->fields['email']);                                                       
                                                                
                                    if($result) {                                
                                        if(password_verify($this->fields['password'], $result['password'])) {												
                                            $_SESSION['id_user']    = $result['id'];						
                                            $_SESSION['user_name']  = $result['user_name'];
                                            $_SESSION['role']       = $result['role'];
                                            
                                            // Delete the restriction time
                                            if(isset($this->limited_access_data['id'])) $query_object->deleteRegistry("limit_access", 'id', $this->limited_access_data['id']);										
                                                                                                            
                                            $this->render('main_view.twig', [
                                                'menus'     =>  $this->showNavLinks(),                                     
                                                'session'   =>  $_SESSION, 
                                                'active'    =>  'home',                  
                                            ]);						
                                        }
                                        else {
                                            $this->message = "Bad credentials";
                                        }                                                                
                                    }
                                    else {                                    
                                        $this->message = "Bad credentials";
                                    }                                  
                                    
                                    // Search if there is a restriction time
                                    if(isset($this->limited_access_data['id'])) {																																										
                                        // Update the restriction time										
                                        $this->limited_access_data['failed_tries'] += 1;
                                        $this->limited_access_data['restriction_time'] = time() + (5 * 60 );											
                                        $query_object->updateRow("limit_access", $this->limited_access_data, $this->limited_access_data['id']);
                                    }
                                    else {
                                        $this->limited_access_data['failed_tries'] = 1;
    
                                        // Insert into table limit_access
                                        $data = [
                                            'ip' => $_SERVER['REMOTE_ADDR'],
                                            'restriction_time' => time() + (5 * 60),
                                            'failed_tries' => $this->limited_access_data['failed_tries'],
                                            'created_at' => date('Y-m-d H:i:s')
                                        ];                                   
    
                                        if($validate->validate_form($data)) {											
                                            $query_object->insertInto("limit_access", $data);
                                        }
                                    }                                                                        
                                }
                                else {
                                    $this->render('main_view.twig', [
                                        'menus'     =>  $this->showNavLinks(),                                     
                                        'session'   =>  $_SESSION,                  
                                    ]);	
                                }
                            }
                            else {   
                                $this->limited_access_data['failed_tries'] = 0;
                                
                                $this->message = $validate->get_msg();                                    
                            }
                        }
                        else {
                            $this->limited_access_data['failed_tries'] = 0;
    
                            // Display message with remaining time (formatted)
                            $minutes = floor($this->remaining_time / 60);
                            $seconds = $this->remaining_time % 60;
                            
                            $this->message = "Access restricted. Please try again in " . $minutes . " minutes and " . $seconds . " seconds.";							                                
                        } 
                    }                                       
                }   
                
                $this->fields['csrf_token'] = $validate->csrf_token();

                $this->render('login/login_view.twig', [
                    'menus'             =>  $this->showNavLinks(),
                    'error_message'     =>  $this->message,
                    'fields'            =>  $this->fields,
                    'active'            =>  'login',                                                                              
                ]);

            } catch (\Throwable $th) {
                $error_msg = [
                    'error' =>  $th->getMessage(),
                ];

                if($this->testAccess(['ROLE_ADMIN'])) {
                    $error_msg = [
                        "Message:"  =>  $th->getMessage(),
                        "Path:"     =>  $th->getFile(),
                        "Line:"     =>  $th->getLine(),
                    ];
                }

                $this->render('error_view.twig', [
                    'menus'             => $this->showNavLinks(),
                    'exception_message' => $error_msg,                
                ]);
            }                                             
        }        
    }    
?>