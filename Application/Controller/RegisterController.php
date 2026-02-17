<?php
    declare(strict_types=1);

    namespace Application\Controller;

    use App\Core\Controller;
    use Application\model\User;
    use Application\Repository\UserRepository;
    use Application\model\classes\Query;
    use Application\model\classes\Validate;

    class RegisterController extends Controller 
    {
        public function __construct(
            private Validate $validate,
            private Query $query,
            private UserRepository $userRepository,
            private array $fields = [],            
        ) {
            
        }
        /** Show register view */
        public function index(): void {                            
            if($_SERVER['REQUEST_METHOD'] == 'POST') {                                       
                // Get values from register form                  
                $this->fields = [
                    'userName'        =>  $this->validate->test_input(strtolower($_POST['user_name'])),
                    'email'           =>  $this->validate->test_input(strtolower($_POST['email'])),
                    'password'        =>  $this->validate->test_input($_POST['password']),
                    'repeatPassword'  =>  $this->validate->test_input($_POST['repeat_password']),
                    'terms'           =>  isset($_POST['terms']) ? $this->validate->test_input($_POST['terms']) : "",                        
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
                            if($this->fields['password'] != $_POST['repeat_password']) {                                    
                                $data = [
                                    'menus'          =>  $this->showNavLinks(),
                                    'session'        =>  $_SESSION,
                                    'error_message'  =>  "Passwords are not equals", 
                                    'fields'         =>  $this->fields,                                    
                                    'active'         =>  'registration',
                                    'csrf_token'     =>  $this->validate
                                ];                        
                            }
                            else {
                                // Save the user
                                array_pop($this->fields); 
                                                                
                                $user = new User($this->fields);                                    

                                $this->userRepository->save($user);
    
                                $data = [
                                    'menus'       =>  $this->showNavLinks(),
                                    'session'     =>  $_SESSION,
                                    'message'     =>  "User registered successfully",
                                    'active'      =>  'registration', 
                                    'csrf_token'  =>  $this->validate
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
                'menus'       =>  $this->showNavLinks(),
                'session'     =>  $_SESSION,
                'active'      =>  'registration',
                'fields'      =>  $this->fields,
                'csrf_token'  =>  $this->validate
            ]);
        }        
    }  
?>