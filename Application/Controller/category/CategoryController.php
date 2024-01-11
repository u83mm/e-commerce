<?php
    declare(strict_types=1);    

    use App\Core\Controller;    
    use model\classes\Query;
    use model\classes\Validate;

    class CategoryController extends Controller
    {
        public function __construct(private array $categories = [], private object $dbcon = DB_CON)
        {
            
        }

        public function index() : void {
            try {                
                $query = new Query;

                $this->categories = $query->selectAll('category', $this->dbcon);                

                $this->render('categories/index_view.twig', [
                    'menus'     =>    $this->showNavLinks(),
                    'session'   =>    $_SESSION,
                    'active'    =>    'administration',
                    'categories'=>    $this->categories,                                                
                 ]);
            } catch (\Throwable $th) {
                $error_msg = [
                    'Error:' =>  $th->getMessage(),
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

        public function new() : void {
            try {
                $query = new Query;
                $validate = new Validate;

                if($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $fields = [
                        'category'  => $validate->test_input($_REQUEST['name']),
                    ];

                    if($validate->validate_form($fields)) {
                        $query->insertInto('category', $fields, $this->dbcon);

                        $this->render('categories/new_category_view.twig', [
                            'menus'     =>    $this->showNavLinks(),
                            'session'   =>    $_SESSION,
                            'active'    =>    'administration',
                            'message'   =>    "Category saved successfully",                    
                        ]);
                    }
                }

                $this->render('categories/new_category_view.twig', [
                    'menus'     =>    $this->showNavLinks(),
                    'session'   =>    $_SESSION,
                    'active'    =>    'administration',                    
                ]);

            } catch (\Throwable $th) {
                $error_msg = [
                    'Error:' =>  $th->getMessage(),
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