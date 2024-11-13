<?php
    declare(strict_types=1);

    namespace Application\Controller;

    use App\Core\Controller;
    use Application\model\User;
    use Application\Repository\UserRepository;
    use model\classes\Query;
    use model\classes\Validate;

    class RegisterController extends Controller 
    {
        public function __construct(
            private array $fields = [],
        ) {
            
        }
        /** Show register view */
        public function index(): void {
            $validate = new Validate;  
            $query = new Query;                       

            try {                
                if($_SERVER['REQUEST_METHOD'] == 'POST') {                                       
                    // Get values from register form                  
                    $this->fields = [
                        'userName'          =>  $validate->test_input(strtolower($_REQUEST['user_name'])),
                        'email'             =>  $validate->test_input(strtolower($_REQUEST['email'])),
                        'password'          =>  $validate->test_input($_REQUEST['password']),
                        'repeatPassword'    =>  $validate->test_input($_REQUEST['repeat_password']),
                        'terms'             =>  isset($_REQUEST['terms']) ? $validate->test_input($_REQUEST['terms']) : "",                        
                    ];                     
                    
                    if(!$validate->validate_csrf_token()) {                         

                        $data = [
                            'error_message' =>  "Invalid CSRF token",
                            'fields'        =>  $this->fields,
                            'menus'         =>  $this->showNavLinks(),
                            'active'        =>  'registration',
                            'csrf_token'    =>  $validate
                        ];
                    }
                    else {
                        if($validate->validate_form($this->fields)) {
                            // Test if the e-mail is in use by other user
                            $result = $query->selectOneBy('users', 'email', $this->fields['email']);
    
                            if($result) {                                                              
                                $data = [
                                    'error_message' =>  'The email is already in use.',
                                    'fields'        =>  $this->fields,
                                    'menus'         =>  $this->showNavLinks(),
                                    'session'       =>  $_SESSION,
                                    'active'        =>  'registration',
                                    'csrf_token'    =>  $validate
                                ];                                
                            }
                            else {
                                // Test if passwords are equals
                                if($this->fields['password'] != $_REQUEST['repeat_password']) {                                    
                                    $data = [
                                        'menus'             =>  $this->showNavLinks(),
                                        'session'           =>  $_SESSION,
                                        'error_message'     =>  "Passwords are not equals", 
                                        'fields'            =>  $this->fields,                                    
                                        'active'            =>  'registration',
                                        'csrf_token'        =>  $validate
                                    ];                        
                                }
                                else {
                                    // Save the user
                                    array_pop($this->fields); 
                                                                   
                                    $user = new User($this->fields);
                                    $userRepository = new UserRepository();
    
                                    $userRepository->save($user);
        
                                    $data = [
                                        'menus'         =>  $this->showNavLinks(),
                                        'session'       =>  $_SESSION,
                                        'message'       =>  "User registered successfully",
                                        'active'        =>  'registration', 
                                        'csrf_token'    =>  $validate
                                    ];
                                }
                            } 
                        }
                        else {                        
                            $data = [
                                'error_message' =>  $validate->get_msg(),
                                'fields'        =>  $this->fields,
                                'menus'         =>  $this->showNavLinks(),
                                'active'        =>  'registration',
                            ];
                        }
                    }                                        
                                       
                    $this->render('register/register_view.twig', $data);                                                                                                                                                                                                              
                }
                                
                $this->render('register/register_view.twig', $data = [
                    'menus'         =>  $this->showNavLinks(),
                    'session'       =>  $_SESSION,
                    'active'        =>  'registration',
                    'fields'        =>  $this->fields,
                    'csrf_token'    =>  $validate
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
    }  
?>