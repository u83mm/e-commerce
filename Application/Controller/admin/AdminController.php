<?php
    declare(strict_types=1);

    use App\Core\Controller;

    class AdminController extends Controller
    {
        public function __construct(private object $dbcon = DB_CON) {
            
        }

        public function index(): void {           
            try {
                $this->render('admin/index_view.twig', [
                    'menus'     =>  $this->showNavLinks(),                         
                    'session'   =>  $_SESSION,                        
                    'active'    =>  'administration', 
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