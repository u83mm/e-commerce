<?php
    declare(strict_types=1);
    
    namespace Application\Controller;

    use App\Core\Controller;

    class ErrorController extends Controller
    {        

        public function index()
        {  
            $this->render('error_view.twig', [
                'menus'             => $this->showNavLinks(),
                'exception_message' => "Page NOT found",
                'session'           => $_SESSION,
            ]);                   
        }
    }
?>
