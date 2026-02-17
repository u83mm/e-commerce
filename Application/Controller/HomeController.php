<?php
    declare(strict_types=1);

    namespace Application\Controller;

    //session_start();

    use App\Core\Controller;

    class HomeController extends Controller
    {        
        public function index()
        { 
            $this->render('main_view.twig', [
                'menus'     => $this->showNavLinks(), 
                'session'   =>  $_SESSION,
                'active'    =>  'home',                
            ]);                                          
        }
    }    