<?php
    declare(strict_types=1);

    namespace Application\Controller\products;

    use App\Core\Controller;
    use Application\model\classes\CommonTasks;
    use Application\model\classes\Entity\Product;
    use Application\model\classes\Query;
    use Application\model\classes\Validate;

    class ProductsController extends Controller
    {
        public function __construct(
            private Validate $validate,
            private Query $query,
            private CommonTasks $commonTask,
            private array $products = [],
            private array $categories = [],                                    
            private string $message = "",            
        ) 
        {

        }

        /** Show products index */
        public function index() {
            // We obtain all products from DB
            $this->products = $this->query->selectAll('products');

            // Variables to use in the view
            $twig_variables = [
                'menus'     =>  $this->showNavLinks(),
                'session'   =>  $_SESSION,
                'active'    => 'catalog',
                'products'  =>  $this->products,
            ];

            if (!$this->products) $twig_variables['error_message'] = "There aren't products to show.";
            
            $this->render('products/index_view.twig', $twig_variables);            
        }

        /** Create a new product */
        public function new() : void {            
            // Test for authorized access
            if(!$this->testAccess(['ROLE_ADMIN'])) {
                throw new \Exception("Unauthorized access!", 1);
            }
                            
            // Get all categories
            $this->categories = $this->query->selectAll('category');
            
            $twig_variables = [
                'menus'         =>  $this->showNavLinks(),
                'session'       =>  $_SESSION,
                'active'        =>  'administration',
                'categories'    =>  $this->categories,
            ];

            // If form is send and is valid
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Get values from form
                $fields = [
                    'name'        => !empty($_POST['name'])        ? $this->validate->test_input($_POST['name']) : null,
                    'description' => !empty($_POST['description']) ? $this->validate->test_input($_POST['description']) : null,
                    'price'       => !empty($_POST['price'])       ? (float) $this->validate->test_input($_POST['price']) : "",
                    'id_category' => !empty($_POST['category'])    ? (int) $this->validate->test_input($_POST['category']): null,
                ];                    
                
                // Save data in DB
                if($this->validate->validate_form($fields)) { 
                    /** Picture's data */                        
                    $type = trim($_FILES['image']['type']);                                                                                                                      

                    // Posible errors on upload files
                    $php_errors = array(	
                        1 => 'Tamaño máximo de archivo in php.ini excedido',
                        2 => 'Tamaño máximo de archivo en formulario HTML excedido',
                        3 => 'Sólo una parte del archivo fué subido',
                        4 => 'No se seleccionó ningún archivo para subir.'
                    );

                    // Manage any error on upload
                    if($_FILES['image']['error'] != 0) {
                        throw new \Exception("Error Processing Request " . $php_errors[$_FILES['image']['error']]);                                                                                
                    }

                    if(!is_uploaded_file($_FILES['image']['tmp_name'])) {
                        throw new \Exception(
                            "Something went wrong!. Uploaded request: file named '{$_FILES['image']['tmp_name']}'", 
                            1
                        );
                    }

                    if(!getimagesize($_FILES['image']['tmp_name'])) {
                        throw new \Exception(
                            "The file yo're trying to upload isn't a valid file. {$_FILES['image']['name']}" . 
                            "must be (*.gif, *.jpg, *.jpeg o *.png).", 
                            1
                        );                            
                    }
                    
                    // New name for the file to upload
                    $now = time();
                    $upload_filename = STORAGE_IMAGES_PATH . '/' . $now . '-' . $_FILES['image']['name'];

                    if(file_exists($upload_filename)) $now++;

                    if(strncmp($type, "image/", 6) == 0) {
                        if(!move_uploaded_file($_FILES['image']['tmp_name'], $upload_filename)){
                            throw new \Exception(
                                "Error Processing Request to save the file. Maybe you've a problem with permissions folder " . 
                                "in $upload_filename",
                                1
                            );                                
                        }                                                        

                        // Create file type (jpeg, jpg, png or gif)                            
                        $original = $this->commonTask->createImageFromSource($upload_filename, $type);

                        // Resize the image    
                        $w = 400; // width value for the new image
                        $h = 500; // heigh value for the new image
                        $final_image = $this->commonTask->resizeImage($original, $w, $h);

                        // replace the image on the server
                        ImagePNG($final_image, $upload_filename, 9);
                        ImageDestroy($original);
                        ImageDestroy($final_image); 
                    }
                    else {
                        throw new \Exception("The file format must be (jpeg, jpg, gif or png).");                    	
                    }

                    // Insert data in DB                        
                    $fields['image'] = $this->commonTask->getWebPath($upload_filename);

                    $product = new Product(...$fields);
                    $this->query->insertInto('products', $product);

                    $twig_variables['message'] = "Product saved successfully!";                                                               
                }
                else {
                    $twig_variables['fields']        = $fields;
                    $twig_variables['error_message'] = $this->validate->get_msg();                                     
                }
            }
                                            
            $this->render('products/new_product_view.twig', $twig_variables);
        }

        /** Show product */
        public function show($id = null) : void {            
            if(empty($id)) throw new \Exception("There are any product to show.", 1);                

            $product = $this->query->selectOneByIdInnerjoinOnfield('products', 'category', 'id_category', 'id', $id);                                                
            
            $this->render('products/show_product_view.twig', [
                'menus'     =>  $this->showNavLinks(),
                'session'   =>  $_SESSION,
                'active'    =>  'catalog', 
                'product'   =>  $product,
                'message'   =>  $this->message,                   
            ]);            
        }

        /** Edit product */
        public function edit($id = null) : void {            
            // Test for authorized access
            if(!$this->testAccess(['ROLE_ADMIN'])) {
                throw new \Exception("Unauthorized access!", 1);
            }

            if(empty($id)) throw new \Exception("There are any product to edit.", 1);    

            $product = $this->query->selectOneByIdInnerjoinOnfield('products', 'category', 'id_category', 'id', $id);
            
            // Get all categories
            $this->categories = $this->query->selectAll('category');
            
            // Update product                
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $fields = [
                    'name'        => !empty($_POST['name'])        ? $this->validate->test_input($_POST['name']) : null,
                    'description' => !empty($_POST['description']) ? $this->validate->test_input($_POST['description']) : null,                        
                    'id_category' => !empty($_POST['category'])    ? intval($this->validate->test_input($_POST['category'])) : null, 
                    'price'       => !empty($_POST['price'])       ? floatval($this->validate->test_input($_POST['price'])) : null,                      
                ];

                if($this->validate->validate_form($fields)) {
                    // If there is an image to update
                    if(!empty($_FILES['image']['name'])) {
                        /** Picture's data */                        
                        $type = trim($_FILES['image']['type']);                                                                                                                      

                        // Posible errors on upload files
                        $php_errors = array(	
                            1 => 'Tamaño máximo de archivo in php.ini excedido',
                            2 => 'Tamaño máximo de archivo en formulario HTML excedido',
                            3 => 'Sólo una parte del archivo fué subido',
                            4 => "You don't have selected any file to upload."
                        );

                        // Manage any error on upload
                        if($_FILES['image']['error'] != 0) {
                            throw new \Exception("Error Processing Request: " . $php_errors[$_FILES['image']['error']]);                                                                                
                        }

                        if(!is_uploaded_file($_FILES['image']['tmp_name'])) {
                            throw new \Exception(
                                "Something went wrong!. Uploaded request: file named '{$_FILES['image']['tmp_name']}'", 
                                1
                            );
                        }

                        if(!getimagesize($_FILES['image']['tmp_name'])) {
                            throw new \Exception(
                                "The file yo're trying to upload isn't a valid file. {$_FILES['image']['name']}" . 
                                "must be (*.gif, *.jpg, *.jpeg o *.png).", 
                                1
                            );                            
                        }
                        
                        // New name for the file to upload
                        $now = time();
                        $upload_filename = STORAGE_IMAGES_PATH . '/' . $now . '-' . $_FILES['image']['name'];

                        if(file_exists($upload_filename)) $now++;

                        if(strncmp($type, "image/", 6) == 0) {
                            if(!move_uploaded_file($_FILES['image']['tmp_name'], $upload_filename)){
                                throw new \Exception(
                                    "Error Processing Request to save the file. Maybe you've a problem with permissions folder " . 
                                    "in $upload_filename",
                                    1
                                );                                
                            }                                                        

                            // Create file type (jpeg, jpg, png or gif)                            
                            $original = $this->commonTask->createImageFromSource($upload_filename, $type);

                            // Resize the image    
                            $w = 400; // width value for the new image
                            $h = 500; // heigh value for the new image
                            $final_image = $this->commonTask->resizeImage($original, $w, $h);

                            // replace the image on the server
                            ImagePNG($final_image, $upload_filename, 9);
                            ImageDestroy($original);
                            ImageDestroy($final_image); 
                        }
                        else {
                            throw new \Exception("The file format must be (jpeg, jpg, gif or png).");                    	
                        }

                        // Delete old picture and add the new one.
                        $this->commonTask->deleteFileFromServer($product['image']); 

                        $fields = [
                            'name'        => !empty($_POST['name'])         ? $this->validate->test_input($_POST['name']) : null,
                            'description' => !empty($_POST['description'])  ? $this->validate->test_input($_POST['description']) : null,                        
                            'id_category' => !empty($_POST['category'])     ? intval($this->validate->test_input($_POST['category'])) : null,
                            'image'       => $this->commonTask->getWebPath($upload_filename),
                            'price'       => !empty($_POST['price'])        ? floatval($this->validate->test_input($_POST['price'])) : null,                      
                        ];                                                  
                    }

                    $this->query->updateRegistry('products', $fields, 'id', $id);
                    $this->message = "Product updated successfully!";
                    $this->show(); 
                }                                       
            }
            
            $this->render('products/edit_product_view.twig', [
                'menus'      =>  $this->showNavLinks(),
                'session'    =>  $_SESSION,
                'active'     =>  'administration', 
                'product'    =>  $product, 
                'categories' =>  $this->categories,                  
            ]);
        }

        /** Delete product */
        public function delete($id = null) : void {            
            // Test for authorized access
            if(!$this->testAccess(['ROLE_ADMIN'])) {
                throw new \Exception("Unauthorized access!", 1);
            }
            
            if(empty($id)) throw new \Exception("There are any product to edit.", 1);                

            // Get product to delete           
            $product = $this->query->findOneBy('products', 'id', $id, Product::class);

            $this->query->deleteRegistry('products', 'id', $id);
            $this->commonTask->deleteFileFromServer($product->image);

            $this->render('products/index_view.twig', [
                'menus'    =>  $this->showNavLinks(),
                'session'  =>  $_SESSION,
                'active'   =>  'home',                                                
                'message'  =>  "Product deleted successfully!",
            ]);
        }
    }    
?>