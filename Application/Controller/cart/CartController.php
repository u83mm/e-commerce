<?php
    declare(strict_types=1);

    namespace Application\Controller\cart;

    //session_start();

    use App\Core\Controller;
    use Application\model\Product;
    use Application\model\classes\Query;
    use Application\model\classes\Validate;

    class CartController extends Controller
    {        

        public function __construct(
            private Validate $validate,
            private Query $query,
            private string $message = "",            
        ) {
            
        }
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

                // Unserialize cart and get total price
                $total = 0;
                $products = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

                if(count($products) > 0) {
                    foreach($products as $item) {
                        //$total += unserialize($item)->getPrice() * unserialize($item)->getQty();
                        $total += $item->getPrice() * $item->getQty();
                        //$products_unserialized[] = unserialize($item);
                    }
                }
                
                $this->render('cart/index_view.twig', [
                    'menus'         =>  $this->showNavLinks(),
                    'session'       =>  $_SESSION,                        
                    'active'        =>  'catalog', 
                    'products'      =>  $products,
                    'total'         =>  $total,
                    'empty_cart'    =>  empty($products)
                ]);

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

        public function add($id = null) : void 
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

                if($_SERVER['REQUEST_METHOD'] == "POST") {
                    $fields = [
                        'product_id' => $id,
                        'quantity'   => $this->validate->test_input($_POST['quantity']),
                    ];

                    $result = $this->query->selectOneBy('products', 'id', $fields['product_id']);              
                    $result['qty'] = $fields['quantity']; 
                    
                    $product = new Product($result);                                 

                    if($this->validate->validate_form($fields)) { 
                        // Add product to cart                                                                    
                        $_SESSION['cart'][$product->getId()] = $product;                                                 

                        $this->render('products/show_product_view.twig', [
                            'menus'         =>  $this->showNavLinks(),
                            'session'       =>  $_SESSION,                        
                            'active'        =>  'catalog',
                            'message'       =>  'Product added to cart',
                            'product'       =>  $product,                            
                        ]);
                    }
                    else {
                        $this->render('products/show_product_view.twig', [
                            'menus'         =>  $this->showNavLinks(),
                            'session'       =>  $_SESSION,                        
                            'active'        =>  'catalog',
                            'error_message' =>  $this->validate->get_msg(),
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

        public function remove($id = null) : void
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
                header('Location: /cart/cart/index');
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
        
        public function update($id = null) : void
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
                if($_SERVER['REQUEST_METHOD'] == "POST") {
                    $fields = [
                        'product_id' => $id,
                        'quantity'   => $this->validate->test_input($_POST['qty']),
                    ];

                    if($this->validate->validate_form($fields)) $_SESSION['cart'][$id]->setQty($fields['quantity']);                   
                }

                header('Location: /cart/cart/index');

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

        public function clear() : void
        {
            // Test privileges
            if(!$this->testAccess([
                'ROLE_USER',
                'ROLE_ADMIN'
            ])) {
                header('Location: /login');
                die;
            }

            unset($_SESSION['cart']);
            header('Location: /cart/cart/index');
        }
    }    
?>