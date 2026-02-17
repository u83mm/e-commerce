<?php
    declare(strict_types=1);

    namespace Application\Controller\Admin;

    use App\Core\Controller;

    class AdminController extends Controller
    {        
        public function index(): void {           
            // Test for authorized access
            if(!$this->testAccess(['ROLE_ADMIN'])) {
                throw new \Exception("Unauthorized access!", 1);
            }
            
            $this->render('admin/index_view.twig', [
                'menus'     =>  $this->showNavLinks(),                         
                'session'   =>  $_SESSION,                        
                'active'    =>  'administration', 
            ]);
        }       
    }    
?>