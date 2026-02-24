<?php
    declare(strict_types=1);

    namespace Application\Controller\Admin;

    use App\Core\Controller;

    class AdminController extends Controller
    {        
        public function index(): void {                                   
            $this->render('admin/index_view.twig', [
                'menus'     =>  $this->showNavLinks(),                         
                'session'   =>  $_SESSION,                        
                'active'    =>  'administration', 
            ]);
        }       
    }    
?>