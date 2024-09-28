<?php
    declare(strict_types=1);    

    namespace Application\Controller\category;

    use App\Core\Controller;    
    use model\classes\Query;
    use model\classes\Validate;

    class CategoryController extends Controller
    {
        public function __construct(private array $categories = [])
        {
            
        }

        /** Retrieves categories from the database and renders them in a view. */
        public function index() : void {
            try {                
                $query = new Query;

                $this->categories = $query->selectAll('category');                

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

        /** Create a new category */
        public function new() : void {
            try {
                // Test for authorized access
                if(!$this->testAccess(['ROLE_ADMIN'])) {
                    throw new \Exception("Unauthorized access!", 1);
                }

                $query = new Query;
                $validate = new Validate;

                if($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $fields = [
                        'category'  => $validate->test_input($_REQUEST['name']),
                    ];

                    if($validate->validate_form($fields)) {
                        $query->insertInto('category', $fields);

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

        /** Edit category */
        public function edit(string $id = "") : void {
            try {
                $query = new Query;
                $validate = new Validate;

                $category = $query->selectOneBy('category', 'id_category', $id);  
                
                if($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $fields = [
                        'category' => $validate->test_input($_POST['name']),
                    ];                                         

                    if($validate->validate_form($fields)) {
                        $query->updateRegistry('category', $fields, 'id_category', $id);

                        $this->categories = $query->selectAll('category');

                        $this->render('categories/index_view.twig', [
                            'menus'     =>    $this->showNavLinks(),
                            'session'   =>    $_SESSION,
                            'active'    =>    'administration',
                            'categories'=>    $this->categories,
                            'message'   =>    "Category updated successfully",                    
                        ]);
                    }
                }

                $this->render('categories/edit_view.twig', [
                    'menus'     =>    $this->showNavLinks(),
                    'session'   =>    $_SESSION,
                    'active'    =>    'administration',
                    'category'  =>    $category,
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

        /** Delete a category */
        public function delete(string $id = "") : void {
            try {
                // Test for authorized access
                if(!$this->testAccess(['ROLE_ADMIN'])) {
                    throw new \Exception("Unauthorized access!", 1);
                }
                
                if(empty($id)) throw new \Exception("There are any category to delete.", 1);

                $query = new Query;                

                $query->deleteRegistry('category', 'id_category', $id);

                $this->render('categories/index_view.twig', [
                    'menus'     =>    $this->showNavLinks(),
                    'session'   =>    $_SESSION,
                    'active'    =>    'administration',
                    'message'   =>    "Category deleted successfully!",                    
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