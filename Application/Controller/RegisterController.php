<?php

    use App\Core\Controller;
    use Application\model\User;
    use Application\Repository\UserRepository;
    use model\classes\Query;
    use model\classes\Validate;

    class RegisterController extends Controller 
    {
        /** Show register view */
        public function index(): void {
            try {
                $this->render('register/register_view.twig', [
                    'menus'     =>  $this->showNavLinks(),
                    'session'   =>  $_SESSION,
                    'active'    =>  'registration',
                ]);

            } catch (\Throwable $th) {
                $error_msg = [
                    'error' =>  $th->getMessage(),
                ];

                if(isset($_SESSION['role']) && $_SESSION['role'] === 'ROLE_ADMIN') {
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

        /** Register a new user */
        public function new(): void {
            $validate = new Validate;
            $query = new Query;             
                        
            try {                                
                if($_SERVER['REQUEST_METHOD'] == 'POST') {                     
                    // Get values from register form                  
                    $fields = [
                        'userName'          =>  $validate->test_input(strtolower($_REQUEST['user_name'])),
                        'email'             =>  $validate->test_input(strtolower($_REQUEST['email'])),
                        'password'          =>  $validate->test_input($_REQUEST['password']),
                        'repeatPassword'    =>  $validate->test_input($_REQUEST['repeat_password']),
                        'terms'             =>  isset($_REQUEST['terms']) ? $validate->test_input($_REQUEST['terms']) : "",
                    ];                     
                    
                    if($validate->validate_form($fields)) {
                        // Test if the e-mail is in use by other user
                        $result = $query->selectOneBy('users', 'email', $fields['email']);

                        if($result) {
                            $data = [
                                'error_message' =>  'The email is already in use.',
                                'fields'        =>  $fields,
                                'menus'         =>  $this->showNavLinks(),
                                'active'        =>  'registration',
                            ];
                        }
                        else {
                            // Test if passwords are equals
                            if($fields['password'] != $_REQUEST['repeat_password']) {                                    
                                $data = [
                                    'menus'             =>  $this->showNavLinks(),
                                    'error_message'     =>  "Passwords are not equals", 
                                    'fields'            =>  $fields,                                    
                                    'active'            =>  'registration',
                                ];                        
                            }
                            else {
                                // Save the user
                                array_pop($fields); 
                                                               
                                $user = new User($fields);
                                $userRepository = new UserRepository();

                                $userRepository->save($user);
    
                                $data = [
                                    'menus'         =>  $this->showNavLinks(),
                                    'message'       =>  "User registered successfully",
                                    'active'        =>  'registration', 
                                ];
                            }
                        } 
                    }
                    else {                        
                        $data = [
                            'error_message' =>  $validate->get_msg(),
                            'fields'        =>  $fields,
                            'menus'         =>  $this->showNavLinks(),
                            'active'        =>  'registration',
                        ];
                    }
                                                            
                    $this->render('register/register_view.twig', $data);                                                                                                                                                                                             
                }
                else {
                    throw new Exception("Service unavailable", 1);                    
                }                                
            } catch (\Throwable $th) {               
                $error_msg = [
                    'error' =>  $th->getMessage(),
                ];

                if(isset($_SESSION['role']) && $_SESSION['role'] === 'ROLE_ADMIN') {
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