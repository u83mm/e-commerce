<?php
    declare(strict_types=1);

    namespace Application\Controller;

    use App\Core\Controller;
    use Application\model\User;
    use Application\Repository\UserRepository;
    use App\model\classes\Query;
    use App\model\classes\Validate;

    class RegisterController extends Controller 
    {
        public function __construct(
            private array $fields = [],
            private Validate $validate = new Validate,
            private Query $query = new Query
        ) {
            
        }
        /** Show register view */
        public function index(): void {                            
            try {                
                if($_SERVER['REQUEST_METHOD'] == 'POST') {                                       
                    // Get values from register form                  
                    $this->fields = [
                        'userName'          =>  $this->validate->test_input(strtolower($_REQUEST['user_name'])),
                        'email'             =>  $this->validate->test_input(strtolower($_REQUEST['email'])),
                        'password'          =>  $this->validate->test_input($_REQUEST['password']),
                        'repeatPassword'    =>  $this->validate->test_input($_REQUEST['repeat_password']),
                        'terms'             =>  isset($_REQUEST['terms']) ? $this->validate->test_input($_REQUEST['terms']) : "",                        
                    ];                     
                    
                    if(!$this->validate->validate_csrf_token()) {                         

                        $data = [
                            'error_message' =>  "Invalid CSRF token",
                            'fields'        =>  $this->fields,
                            'menus'         =>  $this->showNavLinks(),
                            'active'        =>  'registration',
                            'csrf_token'    =>  $this->validate
                        ];
                    }
                    else {
                        if($this->validate->validate_form($this->fields)) {
                            // Test if the e-mail is in use by other user
                            $result = $this->query->selectOneBy('users', 'email', $this->fields['email']);
    
                            if($result) {                                                              
                                $data = [
                                    'error_message' =>  'The email is already in use.',
                                    'fields'        =>  $this->fields,
                                    'menus'         =>  $this->showNavLinks(),
                                    'session'       =>  $_SESSION,
                                    'active'        =>  'registration',
                                    'csrf_token'    =>  $this->validate
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
                                        'csrf_token'        =>  $this->validate
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
                                        'csrf_token'    =>  $this->validate
                                    ];
                                }
                            } 
                        }
                        else {                        
                            $data = [
                                'error_message' =>  $this->validate->get_msg(),
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
                    'csrf_token'    =>  $this->validate
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