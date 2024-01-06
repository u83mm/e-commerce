<?php
    declare(strict_types=1);

    use App\Core\Controller;
    use model\classes\CommonTasks;
    use model\classes\Query;
    use model\classes\Validate;

    class ProductsController extends Controller
    {
        public function __construct(
            private array $products = [],
            private $categories = [],
            private object $dbcon = DB_CON
        ) 
        {

        }

        /** Show products index */
        public function index() {
            try {
                $query = new Query;

                // We obtain all products from DB
                $this->products = $query->selectAll('products', $this->dbcon);

                if (!$this->products) {
                    $this->render('products/index_view.twig', [
                        'menus'         =>  $this->showNavLinks(), 
                        'error_message' =>  "There aren't products to show.",
                        'session'       =>  $_SESSION,                        
                        'active'        =>  'catalog',                
                    ]);
                }
                
                $this->render('products/index_view.twig', [
                    'menus'     =>  $this->showNavLinks(),
                    'session'   =>  $_SESSION,
                    'active'    => 'catalog',
                    'products'  =>  $this->products,
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

        /** Create a new product */
        public function new() : void {
            $validate = new Validate;

            try {
                // Test for authorized access
                if(!$this->testAccess(['ROLE_ADMIN'])) {
                    throw new Exception("Unauthorized access!", 1);
                }

                // Build objects
                $commonTask = new CommonTasks;
                $query = new Query;

                // Get all categories
                $this->categories = $query->selectAll('category', $this->dbcon);                

                // If form is send and is valid
                if($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // Get values from form
                    $fields = [
                        'name'          =>!empty($_POST['name'])        ? $validate->test_input($_POST['name']) : null,
                        'description'   =>!empty($_POST['description']) ? $validate->test_input($_POST['description']) : null,
                        'price'         =>!empty($_POST['price'])       ? $validate->test_input($_POST['price']) : "",
                        'id_category'   =>!empty($_POST['category'])    ? $validate->test_input($_POST['category']): "",
                    ];                    
                    
                    // Save data in DB
                    if($validate->validate_form($fields)) { 
                        /** Picture's data */                        
                        $type = trim($_FILES['image']['type']);                                                                                                                      

                        // Posible errors on upload files
                        $php_errors = array(	
                            1 => 'Tamaño máximo de archivo in php.ini excedido',
                            2 => 'Tamaño máximo de archivo en formulario HTML excedido',
                            3 => 'Sólo una parte del archivo fué subido',
                            4 => 'No se seleccionó ningún archivo para subir.')
                        ;

                        // Manage any error on upload
                        if($_FILES['image']['error'] != 0) {
                            throw new \Exception("Error Processing Request " . $php_errors[$_FILES['image']['error']]);                                                                                
                        }

                        if(!is_uploaded_file($_FILES['image']['tmp_name'])) {
                            throw new Exception(
                                "Something went wrong!. Uploaded request: file named '{$_FILES['image']['tmp_name']}'", 
                                1
                            );
                        }

                        if(!getimagesize($_FILES['image']['tmp_name'])) {
                            throw new Exception(
                                "The file yo're trying to upload isn't a valid file. {$_FILES['image']['name']}" . 
                                "must be (*.gif, *.jpg, *.jpeg o *.png).", 
                                1);                            
                        }
                        
                        // New name for the file to upload
                        $now = time();
                        $upload_filename = STORAGE_IMAGES_PATH . '/' . $now . '-' . $_FILES['image']['name'];

                        if(file_exists($upload_filename)) $now++;

                        if(strncmp($type, "image/", 6) == 0) {
                            if(!move_uploaded_file($_FILES['image']['tmp_name'], $upload_filename)){
                                throw new Exception(
                                    "Error Processing Request to save the file. Maybe you've a problem with permissions folder " . 
                                    "in $upload_filename",
                                    1
                                );                                
                            }                                                        

                            // Create file type (jpeg, jpg, png or gif)                            
                            $original = $commonTask->createImageFromSource($upload_filename, $type);

                            // Resize the image    
                            $w = 400; // width value for the new image
                            $h = 500; // heigh value for the new image
                            $final_image = $commonTask->resizeImage($original, $w, $h);

                            // replace the image on the server
                            ImagePNG($final_image, $upload_filename, 9);
                            ImageDestroy($original);
                            ImageDestroy($final_image); 
                        }
                        else {
                            throw new Exception("The file format must be (jpeg, jpg, gif or png).");                    	
                        }

                        // Insert data in DB                        
                        $fields['image'] = $commonTask->getWebPath($upload_filename);

                        $query->insertInto('products', $fields, $this->dbcon);

                        $this->render('products/new_product_view.twig', [
                            'menus'     =>    $this->showNavLinks(),
                            'session'   =>    $_SESSION,
                            'active'    =>    'administration',
                            'categories'    =>  $this->categories,                            
                            'message'   =>    "Product saved successfully!",
                        ]);                        
                    }
                    else {
                        $this->render('products/new_product_view.twig', [
                            'menus'         =>  $this->showNavLinks(),
                            'session'       =>  $_SESSION,
                            'active'        => 'administration',
                            'fields'        =>  $fields,
                            'categories'    =>  $this->categories,
                            'error_message' => $validate->get_msg(),
                        ]);
                    }
                }
                                                
                $this->render('products/new_product_view.twig', [
                    'menus'         =>  $this->showNavLinks(),
                    'session'       =>  $_SESSION,
                    'active'        =>  'administration',
                    'categories'    =>  $this->categories,
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
                    'session'           =>  $_SESSION,                    
                    'exception_message' => $error_msg,                
                ]);
            }
        }

        /** Show product */
        public function show(string $id = "") : void {  
            try {
                if(empty($id)) throw new Exception("There are any product to show.", 1);

                // Build objects
                $query = new Query;

                $product = $query->selectOneByIdInnerjoinOnfield('products', 'category', 'id_category', 'id', $id, $this->dbcon);                
                
                $this->render('products/show_product_view.twig', [
                    'menus'     =>  $this->showNavLinks(),
                    'session'   =>  $_SESSION,
                    'active'    =>  'catalog', 
                    'product'   =>  $product,                   
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