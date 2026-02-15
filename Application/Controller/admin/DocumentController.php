<?php

declare(strict_types=1);

namespace Application\Controller\admin;

use App\Core\Controller;
use Application\model\classes\CommonTasks;
use Application\model\classes\Query;
use Application\model\classes\Validate;
use setasign\Fpdi\Tcpdf\Fpdi;

final class DocumentController extends Controller
{
    public function __construct(
        private Validate $validate,
        private Query $query,
        private string $message = "",
        private string $error_message = "",
        private CommonTasks $commonTask = new CommonTasks,
        private array $documents = [],        
    )
    {
        
    }
   
    public function upload() : void
    {        
        try {
            // Test for authorized access
            if(!$this->testAccess(['ROLE_ADMIN'])) {
                throw new \Exception("Unauthorized access!", 1);
            }

            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Manage file upload
                $fields = [
                    //'DocumentName'    =>    !empty($_POST['document_name']) ? $this->validate->test_input($_POST['document_name']) : null,
                    'YourName'        =>    !empty($_POST['uploader_name']) ? $this->validate->test_input($_POST['uploader_name']) : null,                    
                    'email'           =>    !empty($_POST['uploader_email']) ? $this->validate->test_input($_POST['uploader_email']) : null,                    
                    'pdf_file'        =>    $_FILES['pdf_file'],
                    'terms_agreement' =>    !empty($_POST['terms_agreement']) ? $_POST['terms_agreement'] : null
                ];
                            
                // Validate form
                if(!$this->validate->validate_form($fields)) {                                             
                    $this->render('admin/document/upload_view.twig', [
                        'menus'         =>  $this->showNavLinks(),                         
                        'session'       =>  $_SESSION,                        
                        'active'        =>  'administration',
                        'error_message' =>  $this->validate->get_msg(),
                        'fields'        =>  $fields
                    ]);
                }

                // Sanitize filename
                $original_name = basename($fields['pdf_file']['name']);
                $file_ext = pathinfo($original_name, PATHINFO_EXTENSION);
                $sanitized_name = preg_replace("/[^a-zA-Z0-9-_\.]/", "_", $fields['pdf_file']['name']);
                
                // Delete old file if it exists
                if (file_exists(STORAGE_DOCUMENTS_PATH . "/" . $sanitized_name)) {
                    unlink(STORAGE_DOCUMENTS_PATH . "/" . $sanitized_name);                  
                }

                // Create upload directory if it doesn't exist
                if (!file_exists(STORAGE_DOCUMENTS_PATH)) {
                    mkdir(STORAGE_DOCUMENTS_PATH, 0755, true);
                }

                // Move the uploaded file
                if (!move_uploaded_file($fields['pdf_file']['tmp_name'], STORAGE_DOCUMENTS_PATH . "/" . $sanitized_name)) {
                    throw new \Exception("Failed to move uploaded file.");
                }                                                

                // Save data in database
                $this->query->insertInto('documents', [
                    'document_name'     => $sanitized_name,
                    'uploader_name'     => $fields['YourName'],
                    'file_path'         => $this->commonTask->getWebPath(STORAGE_DOCUMENTS_PATH, $sanitized_name),
                    'uploader_email'    => $fields['email'],
                    'description'       => !empty($_POST['document_description']) ? $this->validate->test_input($_POST['document_description']) : "",                    
                ]);

                $this->message = "File uploaded successfully!";              
            }

            $this->render('admin/document/upload_view.twig', [
                'menus'     =>  $this->showNavLinks(),                         
                'session'   =>  $_SESSION,                        
                'active'    =>  'administration',
                'message'   =>  $this->message
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
    
    public function showDocuments() : void
    {        
        try {
            // Test for authorized access
            if(!$this->testAccess(['ROLE_ADMIN', 'ROLE_USER'])) {
                throw new \Exception("Unauthorized access!", 1);
            }

            // Declare variables to render
            $variables = [
                    'menus'     =>  $this->showNavLinks(),
                    'session'   =>  $_SESSION,
                    'active'    => 'administration',
                    'documents' =>  $this->query->selectAll('documents'),
            ];

            // Set message
            $this->message !== "" ? $variables['message'] = $this->message : $variables['error_message'] = $this->error_message;

            // Test if there are documents           
            if (empty($variables['documents'])) {
                $variables['error_message'] = "There aren't documents to show.";                
            }            
            
            $this->render('admin/document/index_view.twig', $variables);

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

    public function download() : void
    {
        // code... 
    }

    public function digitallySign($id = null) : void
    {                                     
        try {
            // Test for authorized access
            if(!$this->testAccess(['ROLE_ADMIN', 'ROLE_USER'])) {
                throw new \Exception("Unauthorized access!", 1);
            }            

            // Set the path to your private key and certificate
            $privateKeyPath  = 'file://' . realpath(PRIVATE_KEY_PATH);
            $certificatePath = 'file://' . realpath(CERTIFICATE_PATH);
            $certificatePass = PRIVATE_KEY_PASS;

            // Get the uploaded file
            $uploadedFile = $this->query->selectOneBy('documents', 'document_id', $id);
            $originalName = STORAGE_DOCUMENTS_PATH . "/" . $uploadedFile['document_name'];            

            // Check for Key and Certificate files
            if (!file_exists($privateKeyPath)) {
                throw new \Exception("Private key file not found.", 1);
            }

            if (!file_exists($certificatePath)) {
                throw new \Exception("Certificate file not found.", 1);
            }            
            
            // Create a new PDF document
            $pdf = new Fpdi();

            $pageCount = $pdf->setSourceFile($originalName);
    
            // Import all pages
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplIdx = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($tplIdx);
            }

            // Set document information
            $pdf->SetCreator('Ecommerce Web');
            $pdf->SetAuthor('Mario Moreno');
            $pdf->SetTitle('Signed Document');

            // Add a page
            $pdf->AddPage();

            // Set certificate info
            $pdf->setSignature(
                $certificatePath,
                $privateKeyPath,
                $certificatePass,
                '',  // empty reason
                3,
                [
                    'Name'        => 'Ecommerce Web',
                    'Location'    => 'Valencia',
                    'Reason'      => 'Document Authentication',
                    'ContactInfo' => 'cursotecnoweb@gmail.com'
                ]                               
            );

            // Signed text
            $signedText = "This document has been digitally signed to petition of the user <strong>" . ucfirst($_SESSION['user_name']) . 
                            "</strong> on " . date('Y-m-d H:i:s') . ".";

            // Add some content
            $pdf->SetFont('helvetica', '', 12);            
            $pdf->writeHTML($signedText, true, false, true, false, '');

            // Output the signed PDF
            $pdf->Output('signed_' . $uploadedFile['document_name'], 'I');

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
    
    public function delete($id = null) : void
    {                       
        try {
             // Test for authorized access
            if(!$this->testAccess(['ROLE_ADMIN'])) {
                throw new \Exception("Unauthorized access!", 1);
            }

            $document = $this->query->selectOneBy('documents', 'document_id', $id);

            if(!$document) {
                $this->error_message = "Document not found!";
                $this->showDocuments();
            }
        
            if($document) {
                $this->commonTask->deleteFileFromServer($document['file_path'] . "/" . $document['document_name']);
                $this->query->deleteRegistry('documents', 'document_id', $id);  
                $this->message = "Document deleted successfully!";
                $this->showDocuments();            
            }
           
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
