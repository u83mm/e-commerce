<?php
    declare(strict_types=1);

    use App\Core\Controller;
    use Application\model\Product;
    use model\classes\Query;
    use model\classes\Validate;

    class CartController extends Controller
    {
        private string $message = "";
        public function index() : void
        {                       
            try {
                // Test privileges
                if(!$this->testAccess([
                    'ROLE_USER',
                    'ROLE_ADMIN'
                ])) {
                    header('Location: /login');
                    die;
                }                                  

                if(isset($_SESSION['cart'])) {                                                       
                    $this->render('cart/index_view.twig', [
                        'menus'         =>  $this->showNavLinks(),
                        'session'       =>  $_SESSION,                        
                        'active'        =>  'catalog', 
                        'products'      =>  $_SESSION['cart']                    
                    ]);
                }
                else {
                    $this->render('cart/index_view.twig', [
                        'menus'          =>  $this->showNavLinks(),
                        'session'        =>  $_SESSION,                        
                        'active'         =>  'catalog',
                        'error_message'  =>  'Cart is empty'
                    ]);
                }                

            } catch (\Throwable $th) {
                $error_msg = [
                    'error' =>  $th->getMessage(),
                ];

                if(isset($_SESSION['role']) && $this->testAccess(['ROLE_ADMIN'])) {
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

        public function add(string $id = "") : void 
        {
            $validate = new Validate();
            $query = new Query();            

            try {
                // Test privileges
                if(!$this->testAccess([
                    'ROLE_USER',
                    'ROLE_ADMIN'
                ])) {
                    header('Location: /login');
                    die;
                }

                if($_SERVER['REQUEST_METHOD'] == "POST") {
                    $fields = [
                        'product_id' => $id,
                        'quantity'   => $validate->test_input($_POST['quantity']),
                    ];

                    $result = $query->selectOneBy('products', 'id', $fields['product_id']);              
                    $result['qty'] = $fields['quantity']; 
                    
                    $product = new Product($result);                                 

                    if($validate->validate_form($fields)) {                                              
                        $_SESSION['cart'][$product->getId()] = $product; 

                        $this->render('products/show_product_view.twig', [
                            'menus'         =>  $this->showNavLinks(),
                            'session'       =>  $_SESSION,                        
                            'active'        =>  'catalog',
                            'message'       =>  'Product added to cart',
                            'product'       =>  $product
                        ]);
                    }
                    else {
                        $this->render('products/show_product_view.twig', [
                            'menus'         =>  $this->showNavLinks(),
                            'session'       =>  $_SESSION,                        
                            'active'        =>  'catalog',
                            'error_message' =>  $validate->get_msg(),
                            'product'       =>  $product
                        ]);
                    }
                }                                

            } catch (\Throwable $th) {
                $error_msg = [
                    'error' =>  $th->getMessage(),
                ];

                if(isset($_SESSION['role']) && $this->testAccess(['ROLE_ADMIN'])) {
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

        public function remove(string $id = "") : void
        {
            // Test privileges
            if(!$this->testAccess([
                'ROLE_USER',
                'ROLE_ADMIN'
            ])) {
                header('Location: /login');
                die;
            }

            try { 
                unset($_SESSION['cart'][$id]);
                if(count($_SESSION['cart']) == 0) unset($_SESSION['cart']);                                                         
                $this->index();
            }
            catch (\Throwable $th) {
                $error_msg = [
                    'error' =>  $th->getMessage(),
                ];

                if(isset($_SESSION['role']) && $this->testAccess(['ROLE_ADMIN'])) {
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
        
        public function update(string $id = "") : void
        {
            // Test privileges
            if(!$this->testAccess([
                'ROLE_USER',
                'ROLE_ADMIN'
            ])) {
                header('Location: /login');
                die;
            }

            $validate = new Validate();            

            try {
                if($_SERVER['REQUEST_METHOD'] == "POST") {
                    $fields = [
                        'product_id' => $id,
                        'quantity'   => $validate->test_input($_POST['qty']),
                    ];

                    if($validate->validate_form($fields)) $_SESSION['cart'][$id]->setQty($fields['quantity']);                   
                }

                $this->index();

            } catch (\Throwable $th) {
                $error_msg = [
                    'error' =>  $th->getMessage(),
                ];

                if(isset($_SESSION['role']) && $this->testAccess(['ROLE_ADMIN'])) {
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