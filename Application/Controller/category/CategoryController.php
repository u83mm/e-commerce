<?php
    declare(strict_types=1);    

    use App\Core\Controller;
    use model\classes\Query;    

    class CategoryController extends Controller
    {
        public function __construct(private array $categories = [], private object $dbcon = DB_CON)
        {
            
        }

        public function index() {
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
    }    
?>