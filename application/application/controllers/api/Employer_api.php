<?php

 defined('BASEPATH') OR exit('No direct script access allowed');



require APPPATH.'libraries/REST_Controller.php';



class Employer_api extends REST_Controller{



  public function __construct(){



  parent::__construct();

Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //
if ( "OPTIONS" === $_SERVER['REQUEST_METHOD'] ) {
    die();
}
// header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    //load database

    $this->load->database();

    $this->load->model(array("employer_model"));

    $this->load->model(array("common_model"));

    $this->load->library("form_validation");

    $this->load->library('Authorization_Token');

    $this->load->helper("security");

    $this->load->helper('url');



    $headers = $this->input->request_headers(); 
		$this->decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
    // print_r($this->decodedToken);die;
    $this->admin_id = $this->decodedToken['data']->admin_id ?? null;
    $this->employee_id = $this->decodedToken['data']->employee_id ?? null;
    $this->company_id = $this->decodedToken['data']->company_id ?? null;
    $this->user_type = $this->decodedToken['data']->user_type ?? null;
       if (!$this->decodedToken || $this->decodedToken['status'] != "1") {

            $err = array(

                'status'=>false,

                'message'=>'Unauthorised Token',

                'data'=>[]

            );
            echo json_encode($err);

            exit;
          }

        }

  public function company_detail_put(){

    $data = json_decode(file_get_contents("php://input"));
if(isset($data->company_id)){
  ///// update operation ////////

      if(isset($data->company_name)  && isset($data->industry) && isset($data->corporation) && isset($data->company_start_date)  && isset($data->company_size) && isset($data->vacancy_for_post) && isset($data->about))

      { 

         $error_flag = 0;

      

        if(empty($data->company_id) || empty($data->company_name) || empty($data->industry) || empty($data->corporation) || empty($data->company_start_date) || empty($data->company_size) || empty($data->vacancy_for_post) || empty($data->about))

        {

            $error_flag = 1;

        }

         if($error_flag){

            $this->response(array(

            "status" => 0,

            "message" => "All fields are required!"

          ) , REST_Controller::HTTP_NOT_FOUND);

         return;

        }

        $company_info = array(

          "company_id" => $data->company_id,

          "company_name" => $data->company_name,

          "industry" => $data->industry,

          "corporation" => $data->corporation,

          "company_start_date" => $data->company_start_date, //yy-mm-dd

          "company_size" => $data->company_size,

          "vacancy_for_post" => $data->vacancy_for_post,

          "about" => $data->about

        );

         if(isset($data->alias)){

                if(!empty($data->alias)){

                  $company_info["alias"] = $data->alias;

                }

               }

         if(isset($data->website_url)){

                if(!empty($data->website_url)){

                  $company_info["website_url"] = $data->website_url;

                }

               }

         if(isset($data->logo)){

                if(!empty($data->logo)){
                  $image_data = $data->logo;

                // Check if the image data is a base64-encoded string
                if (preg_match('/^data:image\/(\w+);base64,/', $image_data, $image_type)) {
                    $image_data = substr($image_data, strpos($image_data, ',') + 1);
                
                    $file_extension = strtolower($image_type[1]);
                
                    // Check if the image type is supported
                    if (in_array($file_extension, array('jpg', 'jpeg', 'png', 'gif'))) {
                        $image_data = base64_decode($image_data);
                    
                        $file_name_for_upload = time() . '.' . $file_extension;
                        $file_path_for_upload = './uploads/' . $file_name_for_upload;
                        file_put_contents($file_path_for_upload, $image_data);
                    
                        $logo = base_url() . 'uploads/' . $file_name_for_upload;
                        $company_info["logo"] = $logo;
                    } else {
                        // Unsupported file type
                        unset($company_info["logo"]);
                    }
                } else {
                    // Invalid base64-encoded image data
                     unset($company_info["logo"]);
                }
                }
               }
              //  print_r($company_info["logo"]);die;
               if(isset($data->franchise)){

                if(!empty($data->franchise)){

                  $company_info["franchise"] = $data->franchise;

                }

               }

        

        if($this->employer_model->addUpdateCompanyDetails($company_info)){



            $this->response(array(

              "status" => 1,

              "message" => "Employer data updated successfully"

            ), REST_Controller::HTTP_OK);

        }else{



          $this->response(array(

            "status" => 0,

            "messsage" => "Failed to update Employer data"

          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);

        }

      

      }else{



        $this->response(array(

          "status" => 0,

          "message" => "All fields are needed"

        ), REST_Controller::HTTP_NOT_FOUND);

      }
    }else{
      ///// Insert operation ////////
      if(isset($data->company_name)  && isset($data->industry) && isset($data->corporation) && isset($data->company_start_date)  && isset($data->company_size) && isset($data->vacancy_for_post) && isset($data->about))

      { 

         $error_flag = 0;

      

        if(empty($data->company_name) || empty($data->industry) || empty($data->corporation) || empty($data->company_start_date) || empty($data->company_size) || empty($data->vacancy_for_post) || empty($data->about))

        {

            $error_flag = 1;

        }

         if($error_flag){

            $this->response(array(

            "status" => 0,

            "message" => "All fields are required!"

          ) , REST_Controller::HTTP_NOT_FOUND);

         return;

        }

        $company_info = array(

          "company_name" => $data->company_name,

          "industry" => $data->industry,

          "corporation" => $data->corporation,

          "company_start_date" => $data->company_start_date, //yy-mm-dd

          "company_size" => $data->company_size,

          "vacancy_for_post" => $data->vacancy_for_post,

          "about" => $data->about

        );

         if(isset($data->alias)){

                if(!empty($data->alias)){

                  $company_info["alias"] = $data->alias;

                }

               }

         if(isset($data->website_url)){

                if(!empty($data->website_url)){

                  $company_info["website_url"] = $data->website_url;

                }

               }

         if(isset($data->logo)){

                if(!empty($data->logo)){

            
                  $image_data = $data->logo;

                // Check if the image data is a base64-encoded string
                if (preg_match('/^data:image\/(\w+);base64,/', $image_data, $image_type)) {
                    $image_data = substr($image_data, strpos($image_data, ',') + 1);
                
                    $file_extension = strtolower($image_type[1]);
                
                    // Check if the image type is supported
                    if (in_array($file_extension, array('jpg', 'jpeg', 'png', 'gif'))) {
                        $image_data = base64_decode($image_data);
                    
                        $file_name_for_upload = time() . '.' . $file_extension;
                        $file_path_for_upload = './uploads/' . $file_name_for_upload;
                        file_put_contents($file_path_for_upload, $image_data);
                    
                        $logo = base_url() . 'uploads/' . $file_name_for_upload;
                        $company_info["logo"] = $logo;
                    } else {
                        // Unsupported file type
                        unset($company_info["logo"]);
                    }
                } else {
                    // Invalid base64-encoded image data
                    unset($company_info["logo"]);
                }
                }
                
              }
              
                if(isset($data->franchise)){

                if(!empty($data->franchise)){

                  $company_info["franchise"] = $data->franchise;

                }

               }
                if(!empty($this->admin_id) && $this->user_type != "employee" && $this->user_type != "employer"){
                  $company_info["created_by_admin"] = $this->admin_id;

                }

        

        if($this->employer_model->addUpdateCompanyDetails($company_info)){



            $this->response(array(

              "status" => 1,

              "message" => "Employer data inserted successfully"

            ), REST_Controller::HTTP_OK);

        }else{



          $this->response(array(

            "status" => 0,

            "messsage" => "Failed to insert Employer data"

          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);

        }

      

      }else{



        $this->response(array(

          "status" => 0,

          "message" => "All fields are needed"

        ), REST_Controller::HTTP_NOT_FOUND);

      }
    }

   }

  public function contact_detail_put(){

      $data = json_decode(file_get_contents("php://input"));

      // print_r($data); die;

      if(isset($data->company_id) && isset($data->contact_person_name) && isset($data->email) && isset($data->contact_no) && isset($data->address) && isset($data->pin_code)  && isset($data->city) && isset($data->state) && isset($data->country))

      { 

         $error_flag = 0;

      

        if(empty($data->company_id) || empty($data->contact_person_name) || empty($data->email) || empty($data->contact_no) || empty($data->address) || empty($data->pin_code) || empty($data->city) || empty($data->state) || empty($data->country))

        {

            $error_flag = 1;

        }

         if($error_flag){

            $this->response(array(

            "status" => 0,

            "message" => "All fields are required!"

          ) , REST_Controller::HTTP_NOT_FOUND);

         return;

        }

        $contact_info = array(

          "company_id" => $data->company_id,

          "contact_person_name" => $data->contact_person_name,

          "email" => $data->email,

          "contact_no" => $data->contact_no,

          "address" => $data->address,

          "pin_code" => $data->pin_code, //yy-mm-dd

          "city" => $data->city,

          "state" => $data->state,

          "country" => $data->country

        );
        if(isset($data->contact_no_other)){
          if(!empty($data->contact_no_other)){
           $contact_info['contact_no_other'] = $data->contact_no_other;
          }
        }
        if(isset($data->designation)){
          if(!empty($data->designation)){
           $contact_info['designation'] = $data->designation;
          }
        }

        if($this->employer_model->addUpdateCompanyContact($contact_info)){



            $this->response(array(

              "status" => 1,

              "message" => "contact data updated successfully"

            ), REST_Controller::HTTP_OK);

        }else{



          $this->response(array(

            "status" => 0,

            "messsage" => "Failed to update contact data"

          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);

        }

      

      }else{



        $this->response(array(

          "status" => 0,

          "message" => "All fields are needed"

        ), REST_Controller::HTTP_NOT_FOUND);

      }

   }

  public function company_kyc_detail_put(){

      $data = json_decode(file_get_contents("php://input"));

    //   print_r($data); die;

      if(isset($data->company_id) && isset($data->pan_no)  && isset($data->name) && isset($data->pan_date) && isset($data->address)  && isset($data->pincode) && isset($data->city) && isset($data->state) && isset($data->country))

      { 

             $error_flag = 0;



            if(empty($data->company_id) || empty($data->pan_no) || empty($data->name) || empty   ($data->pan_date) || empty($data->address) || empty($data->pincode) || empty   ($data->city) || empty($data->state)  || empty($data->country)){

                $error_flag = 1;

            }

               if($error_flag){

                   $this->response(array(

                   "status" => 0,

                   "message" => "All fields are required!"

                 ) , REST_Controller::HTTP_NOT_FOUND);

               return;

               }

               else{

               $kyc_detail = array(

                 "company_id" => $data->company_id,

                 "pan_no" => $data->pan_no,

                 "name" => $data->name,

                 "pan_date" => $data->pan_date,

                 "address" => $data->address, 

                 "pincode" => $data->pincode,

                 "city" => $data->city,

                 "state" => $data->state,

                 "country" => $data->country,                

               );

               if(isset($data->gstin)){

                if(!empty($data->gstin)){

                  $kyc_detail["gstin"] = $data->gstin;

                }

               }

               if(isset($data->fax_number)){

                if(!empty($data->fax_number)){

                  $kyc_detail["fax_number"] = $data->fax_number;

                }

               }

               if(isset($data->tan_number)){

                if(!empty($data->tan_number)){

                  $kyc_detail["tan_number"] = $data->tan_number;

                }

               }
               if(isset($data->document)){

                if(!empty($data->document)){
                             
                  $kyc_document = str_replace('data:application/pdf;base64,', '', $data->document);

                  $kyc_document = str_replace(' ', '+', $kyc_document);

                  $binary_document = base64_decode($kyc_document);

                  $file_name_for_upload = time().'.pdf';

		              $document = base_url().'uploads/'.$file_name_for_upload;

		              file_put_contents('./uploads/'.$file_name_for_upload, $binary_document);

                  $kyc_detail["document"] = $document;

                }

               }
// print_r($kyc_detail);die;
             $res=$this->employer_model->addUpdate_company_kycDetails($kyc_detail);

               if($res){

                if($res == 1){
                   $this->response(array(

                     "status" => 1,

                     "message" => "Employee data updated successfully"

                   ), REST_Controller::HTTP_OK);
                  }else{
                    $this->response(array(

                     "status" => 1,

                     "message" => "Employee data inserted successfully"

                   ), REST_Controller::HTTP_OK);
                  }

               }else{

               

                 $this->response(array(

                   "status" => 0,

                   "messsage" => "Failed to update Employee data"

                 ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);

               }

         }  

      } else{



        $this->response(array(

          "status" => 0,

          "message" => "All fields are needed"

        ), REST_Controller::HTTP_NOT_FOUND);

      }

  }

   public function addJobs_put(){

      $data = json_decode(file_get_contents("php://input"));

      // print_r($data); die;

      // ------------- Update job -----------------
        if(isset($data->job_id)){

          
        $job_detail = array();
         
          if(!empty($data->job_id)){

            $job_detail["job_id"] = $data->job_id;
          }
        
          if(isset($data->company_id)){
          if(!empty($data->company_id)){
            $job_detail["company_id"] = $data->company_id;

          }
        }
          if(isset($data->job_title)){
          if(!empty($data->job_title)){
            $job_detail["job_title"] = $data->job_title;

          }
        }
          if(isset($data->experience_required)){
          if(!empty($data->experience_required)){
            $job_detail["experience_required"] = $data->experience_required;

          }
        }
          if(isset($data->salary)){
          if(!empty($data->salary)){
            $job_detail["salary"] = $data->salary;

          }
        }
          if(isset($data->location)){
          if(!empty($data->location)){
            $job_detail["location"] = $data->location;

          }
          }
          if(isset($data->industry_type)){
          if(!empty($data->industry_type)){
            $job_detail["industry_type"] = $data->industry_type;

          }
          }
          if(isset($data->apply_link)){
          if(!empty($data->apply_link)){
            $job_detail["apply_link"] = $data->apply_link;

          }
          }
          if(isset($data->job_description)){
          if(!empty($data->job_description)){
            $job_detail["job_description"] = $data->job_description;

          }
          }
          if(isset($data->your_duties)){
          if(!empty($data->your_duties)){
            $job_detail["your_duties"] = $data->your_duties;

          }
          }
          if(isset($data->requirement)){
          if(!empty($data->requirement)){
            $job_detail["requirement"] = $data->requirement;

          }
          }
          if(isset($data->department)){
          if(!empty($data->department)){
            $job_detail["department"] = $data->department;

          }
          }
          if(isset($data->job_type)){
          if(!empty($data->job_type)){
            $job_detail["job_type"] = $data->job_type;

          }
          }
          if(isset($data->education)){
          if(!empty($data->education)){
            $job_detail["education"] = $data->education;

          }
          }
          if(isset($data->language)){
          if(!empty($data->language)){
            $job_detail["language"] = $data->language;

          }
          }
          if(isset($data->keyskill)){
          if(!empty($data->keyskill)){
            $job_detail["keyskill"] = $data->keyskill;

          }
          }
          if(isset($data->employement)){
          if(!empty($data->employement)){
            $job_detail["employement"] = $data->employement;

          }
          }
          if(isset($data->job_category_id)){
          if(!empty($data->job_category_id)){
            $job_detail["job_category_id"] = $data->job_category_id;

          }
          }
                 
                // print_r($job_detail);die;

        if( $this->employer_model->addUpdate_job($job_detail)){

            $this->response(array(

              "status" => 1,

              "message" => "job data updated successfully"

            ), REST_Controller::HTTP_OK);

      
        }
        else{



          $this->response(array(

            "status" => 0,

            "messsage" => "Failed to update Employee data"

          ), REST_Controller::HTTP_OK);

        }

    }
    
      // ------------- Insert job -----------------
    else{
 
      if(isset($data->company_id) && isset($data->job_title)  && isset($data->experience_required) && isset($data->salary) && isset($data->location)  && isset($data->industry_type) && isset($data->apply_link) && isset($data->job_description) && isset($data->your_duties) && isset($data->requirement) && isset($data->department) && isset($data->job_type) && isset($data->education) && isset($data->language) && isset($data->keyskill) && isset($data->employement) && isset($data->job_category_id))

      { 

         $error_flag = 0;

      

        if(empty($data->company_id) || empty($data->job_title) || empty($data->experience_required) || empty($data->salary) || empty($data->location) || empty($data->industry_type) || empty($data->apply_link) || empty($data->job_description) || empty($data->your_duties) || empty($data->requirement) || empty($data->department) || empty($data->job_type) || empty($data->education) || empty($data->language) || empty($data->keyskill) || empty($data->employement) || empty($data->job_category_id))

        {

            $error_flag = 1;

        }

         if($error_flag){

            $this->response(array(

            "status" => 0,

            "message" => "Fields must not be empty!"

          ) , REST_Controller::HTTP_OK);

         return;

        }

        $job_detail = array(

          "company_id" => $data->company_id,

          "job_title" => $data->job_title,

          "experience_required" => $data->experience_required,

          "salary" => $data->salary,

          "location" => $data->location, 

          "industry_type" => $data->industry_type,

          "apply_link" => $data->apply_link,

          "job_description" => $data->job_description,

          "your_duties" => $data->your_duties,

          "requirement" => $data->requirement,

          "department" => $data->department,

          "job_type" => $data->job_type,

          "education" => $data->education,

          "language" => $data->language,

          "keyskill" => $data->keyskill,

          "employement" => $data->employement,

          "job_category_id" => $data->job_category_id,

        );
          if(!empty($this->admin_id) && $this->user_type != "employee" && $this->user_type != "employer"){
                  $job_detail["created_by_admin"] = $this->admin_id;

                }
             
        $response = $this->employer_model->addUpdate_job($job_detail);
        // print_r($response);die;
        if($response){
            $unique_id = $this->common_model->getLastRecord_email()['id'] ?? 1;
            $unique_id .= mt_rand(1000, 9999);
          // Sending mail and notification to company
           $company = array('from_id'=> $response->company_id,
              'type'=>'company',
              'to' => $response->email,
              'subject'=>'New job added',
              'message'=>'A new job with title-'.$response->job_title.' has been added successfully by '.$response->company_name,
              'unique_id'=>$unique_id);  
              $this->common_model->email($company);
              $this->common_model->addNotification($company);
          // Sending mail and notification to Super-Admin
           $admin = array('from_id'=> 1,
              'type'=>'Super-Admin',
              'to' =>'aashi.we2code@gmail.com',
              'subject'=>'New job added',
              'message'=>'A new job with title-'.$response->job_title.' has been added successfully by '.$response->company_name,
              'unique_id'=>$unique_id);  
              $this->common_model->email($admin);
              $this->common_model->addNotification($admin);
              // $this->common_model->sendMail($detail);

            $this->response(array(

              "status" => 1,

              "message" => "job data inserted successfully"

            ), REST_Controller::HTTP_OK);

      
        }
        else{



          $this->response(array(

            "status" => 0,

            "messsage" => "Failed to insert job data"

          ), REST_Controller::HTTP_OK);

        } 

      }else{

        $this->response(array(

          "status" => 0,

          "message" => "All fields are required !"

        ), REST_Controller::HTTP_OK);

      }
    }
   }

   public function addCategory_put(){
    
      $data = json_decode(file_get_contents("php://input"));
      // print_r($data); die;
      $category_detail = array();
       if(isset($data->category_name)){
          if(!empty($data->category_name)){
          $category_detail["category_name"]=$data->category_name;
        }
      }
       if(isset($data->category_type)){
          if(!empty($data->category_type)){
          $category_detail["category_type"]=$data->category_type;
        }
      }
        if(isset($data->parent_id) && $data->parent_id > 0){
         
          $category_detail["parent_id"]=$data->parent_id;
       
      }
        $msg = "Category added successfully";
        if(isset($data->job_category_id)){
          if(!empty($data->job_category_id)){
          $category_detail["job_category_id"]=$data->job_category_id;
          $msg = "Category updated successfully";
        }
      }
        
        if($this->employer_model->addUpdate_category($category_detail)){

            $this->response(array(
              "status" => 1,
              "message" => $msg
            ), REST_Controller::HTTP_OK);
        }else{

          $this->response(array(
            "status" => 0,
            "messsage" => "Failed !"
          ), REST_Controller::HTTP_OK);
        }
   }
   public function apply_job_post(){

     $data = json_decode(file_get_contents("php://input"));
     
     $candidate_detail = array();
    if(isset($data->job_id) && isset($data->employee_id) && isset($data->status)){
       if(!empty($data->job_id) || !empty($data->employee_id)){
     $candidate_detail["job_id"] = $data->job_id;

     $candidate_detail["employee_id"] = $data->employee_id;
                            }
    $msg = "";
    if(isset($data->apply_id)){
          if(!empty($data->apply_id)){
            $candidate_detail["apply_id"] = $data->apply_id;
            $msg = "Job switched successfully";

          }
          }
    //-------------------------------------------------------------
    // Status :
    // view job = 1
    // apply job = 0
    // connect job = 2
    // save job = 3
    //-------------------------------------------------------------
          if($data->status == 1){
            $candidate_detail["is_viewed"] = $data->status ;
            $msg = "Job viewed successfully";

          }
          if($data->status == 0){
            $candidate_detail["is_viewed"] = $data->status ;
            $msg = "Job applied successfully";

          }
          if($data->status == 2){
            $candidate_detail["is_viewed"] = $data->status ;
            $msg = "Job connected successfully";

          }
          if($data->status == 3){
            $candidate_detail["is_viewed"] = $data->status ;
            $msg = "Job saved successfully";

          }
          
          // print_r($candidate_detail);die;
          $response = $this->employer_model->applyJob($candidate_detail);
          if($response === "2"){
            $msg = "already applied on this job";
          }
          if($response === "3"){
            $msg = "already saved this job";
          }
        // print_r($response);die;
        if($response){
          if(!empty($response->company_id)){
              $unique_id = $this->common_model->getLastRecord_email()['id'] ?? 1;
              $unique_id .= mt_rand(1000, 9999);
            // Sending mail and notification to Company
             $company = array('from_id'=> $response->company_id,
              'type'=>'employee',
              'to' => $response->email ?? NULL,
              'subject'=>'new application on job',
              'message'=>'A new user applied on job with title - '.$response->job_title.'. you have posted.',
              'unique_id'=>$unique_id);
              $this->common_model->email($company);
              $this->common_model->addNotification($company);
        // Sending mail and notification to Super-Admin
           $admin = array('from_id'=> 1,
              'type'=>'Super-Admin',
              'to' =>'aashi.we2code@gmail.com',
              'subject'=>'new application on job',
              'message'=>'A new user applied on job with title - '.$response->job_title.' posted by '.$response->company_name,
              'unique_id'=>$unique_id);  
              $this->common_model->email($admin);
              $this->common_model->addNotification($admin);
          }
            $this->response(array(

              "status" => 1,

              "message" => $msg

            ), REST_Controller::HTTP_OK);
            //  ob_flush();
            //  flush();
            // if(!empty($response->email)){
            //   $this->common_model->sendMail($detail);
            // }
          }else{

          $this->response(array(

            "status" => 0,

            "messsage" => "Failed to apply job"

          ), REST_Controller::HTTP_OK);

        }
      }else{
          $this->response(array(

            "status" => 0,

            "messsage" => "all fields are required !"

          ), REST_Controller::HTTP_OK);


      }

   }
   public function getAllJobsView_post(){
    $data = json_decode(file_get_contents("php://input"));
    $details['job_id'] =$data->job_id ?? null;
    $details['employee_id'] = 0;
    if(!empty($this->employee_id) && $this->user_type == "employee"){
      $details["employee_id"] = $this->employee_id;
    }  
    if(!empty($this->company_id) && $this->user_type == "company"){
      $details["company_id"] = $this->company_id;
    }  
    $page = $data->page ?? 1;
    $limit =$data->limit ?? 999; 

    if(!empty($this->admin_id) && $this->user_type != "super-admin" && $this->user_type != "admin"){
      $details ["admin_id"] =  $this->admin_id;
      $details["admin_type"] =  $this->user_type;
    }  

    // Get search parameter
    $search = isset($data->search) ? $data->search : '';

    // Get filter parameters

    $filter = [
    "category_id" => $data->filter_category_id ?? null,
    "job_type" => $data->filter_job_swap ?? null,
    "keyskill" => $data->filter_keyskill ?? null,
    "location" => $data->filter_location ?? null,
    "company_name" => $data->company_name ?? null,
    ];
    if(isset($data->filter_by_time)){
      if($data->filter_by_time == "today"){
            $filter['start_date'] = date('Y-m-d');
            $filter['end_date'] = date('Y-m-d', strtotime('tomorrow'));

      }
      if($data->filter_by_time == "last_week"){
            $filter['start_date'] = date('Y-m-d', strtotime('last week'));
            $filter['end_date'] = date('Y-m-d', strtotime('last week +7days'));

      }
     
      if($data->filter_by_time == "last_month"){
            $filter['start_date'] = date('Y-m-01', strtotime('last month'));
            $filter['end_date'] =date('Y-m-t', strtotime('last month'));
            
          }
      if($data->filter_by_time == "current_month"){
            $filter['start_date'] = date('Y-m-01', strtotime('this month'));
            $filter['end_date'] =date('Y-m-t', strtotime('this month'));
            
          }
    }

    
    // Calculate offset for pagination
    $offset = ($page - 1) * $limit;

    // sorting 
    $sort = [
      'column_name' => $data->column_name ?? "created_at" ,
      'sort_order' => $data->sort_order ?? "DESC"
    ];
// print_r($sort);die;
    $result = $this->employer_model->viewJobs($filter, $search, $limit, $offset, $sort, $details);

    if ($result) {
              $this->response(array(
                "status" => 1,
                "message" => "successful",
                "total_rows" => $result['total_rows'],
                "data" => $result['data']
              ), REST_Controller::HTTP_OK);

          }else{



            $this->response(array(

              "status" => 0,

              "messsage" => "No data found"

            ), REST_Controller::HTTP_OK);

          }

   }

    public function allJobs_get(){

      // $employee_id = $this->get('employee_id');

      $jobs = $this->employer_model->getJob();

      if($jobs){



        $this->response(array(

          "status" => 1,

          "message" => "Jobs found",

          "data" => $jobs

        ), REST_Controller::HTTP_OK);

      }else{



        $this->response(array(

          "status" => 0,

          "message" => "No Jobs found",

        ), REST_Controller::HTTP_OK);

      }

  }
   public function getjob_post(){
      $data = json_decode(file_get_contents("php://input"));
      $job_id = $data->job_id;
      // print_r($job_id);die;
      $employee = $this->employer_model->getJob($job_id);
      if(count($employee) > 0){

        $this->response(array(
          "status" => 1,
          "message" => "Employees found",
          "data" => $employee
        ), REST_Controller::HTTP_OK);
      }else{

        $this->response(array(
          "status" => 0,
          "message" => "No Employee found",
          "data" => $employee
        ), REST_Controller::HTTP_OK);
      }
  }
//get job category
    public function allJobCategory_post(){
    $data = json_decode(file_get_contents("php://input"));
    $parent_id = 0;
    if(isset($data->parent_id) && $data->parent_id > 0){
      $parent_id = $data->parent_id;
    }

    $page = isset($data->page) ? $data->page : null;
    $limit = isset($data->limit) ? $data->limit : null; 

    $search = isset($data->search) ? $data->search : '';

    $filter = isset($data->filter_category_type) ? $data->filter_category_type: '';

    $sort = [
      'column_name' => $data->column_name ?? null,
      'sort_order' => $data->sort_order ?? null
    ];

    $offset = ($page - 1) * $limit;

    $result = $this->employer_model->getAllJobCategory($filter, $search, $limit, $offset,$sort,$parent_id);

    if($result) {
              $this->response(array(
                "status" => 1,
                "message" => "successful",
                "total_rows" => $result['total_rows'],
                "data" => $result['data']
              ), REST_Controller::HTTP_OK);

        }else{



          $this->response(array(

            "status" => 0,

            "message" => "No job category found",


          ), REST_Controller::HTTP_OK);

        }

  }

public function allEmployer_post(){
  
  $data = json_decode(file_get_contents("php://input"));
   $user_detail = array();
    // print_r($data);die;
    if(!empty($this->admin_id) && $this->user_type != "super-admin" && $this->user_type != "admin"){
    $user_detail = array("admin_id" =>  $this->admin_id,
                          "admin_type" =>  $this->user_type);
    }
                          // print_r($user_detail);die;
    // Get pagination parameters
    $page = isset($data->page) ? $data->page : 1;
    $limit = isset($data->limit) ? $data->limit : 10; 

    // Get search parameter
    $search = isset($data->search) ? $data->search : '';

    // Get filter parameters
    $filter = [
    "industry" => $data->filter_industry ?? null,
    "corporation" => $data->filter_corporation ?? null
    ];
    if(isset($data->filter_by_time)){
       if($data->filter_by_time == "today"){
            $filter['start_date'] = date('Y-m-d');
            $filter['end_date'] = date('Y-m-d', strtotime('tomorrow'));

      }
      if($data->filter_by_time == "this_week"){
            $filter['start_date'] = date('Y-m-d', strtotime('this week'));
            $filter['end_date'] = date('Y-m-d', strtotime('this week +7days'));

      }
      if($data->filter_by_time == "last_week"){
            $filter['start_date'] = date('Y-m-d', strtotime('last week'));
            $filter['end_date'] = date('Y-m-d', strtotime('last week +7days'));

      }
     
      if($data->filter_by_time == "last_month"){
            $filter['start_date'] = date('Y-m-01', strtotime('last month'));
            $filter['end_date'] =date('Y-m-t', strtotime('last month'));
            
          }
      if($data->filter_by_time == "current_month"){
            $filter['start_date'] = date('Y-m-01', strtotime('this month'));
            $filter['end_date'] =date('Y-m-t', strtotime('this month'));
            
          }
    }
    
    // Calculate offset for pagination
    $offset = ($page - 1) * $limit;

    // sorting 
    $sort = [
      'column_name' => $data->column_name ?? null ,
      'sort_order' => $data->sort_order ?? null
    ];

    $result = $this->employer_model->getAllEmployer($user_detail, $filter, $search, $limit, $offset, $sort);

    if ($result) {
              $this->response(array(
                "status" => 1,
                "message" => "successful",
                "total_rows" => $result['total_rows'],
                "data" => $result['data']
              ), REST_Controller::HTTP_OK);

      }else{



        $this->response(array(

          "status" => 0,

          "message" => "No employer found",

        ), REST_Controller::HTTP_OK);

      }

  }

    public function getEmployer_post(){

      $data = json_decode(file_get_contents("php://input"));

      // print_r($company_id);die;

      $company_detail['company_detail']= $this->employer_model->getEmployerDetail($data->company_id);

      $company_detail['kyc_detail'] = $this->employer_model->getCompany_kyc($data->company_id);

      

      if(!empty($company_detail)){



        $this->response(array(

          "status" => 1,

          "message" => "Employer found",

          "data" => $company_detail

        ), REST_Controller::HTTP_OK);

      }else{



        $this->response(array(

          "status" => 0,

          "message" => "No employer found",

          "data" => $company_detail

        ), REST_Controller::HTTP_NOT_FOUND);

      }

  }

   public function employer_delete($company_id){

      if($this->employer_model->deleteEmployer($company_id)){

        // retruns true

        $this->response(array(

          "status" => 1,

          "message" => "company has been deleted"

        ), REST_Controller::HTTP_OK);

      }else{

        // return false

        $this->response(array(

          "status" => 0,

          "message" => "Failed to delete company"

        ), REST_Controller::HTTP_NOT_FOUND);

      }

  }

   public function jobCategory_delete($job_category_id){

      if($this->employer_model->deleteJobCategory($job_category_id)){

        // retruns true

        $this->response(array(

          "status" => 1,

          "message" => "job category has been deleted"

        ), REST_Controller::HTTP_OK);

      }else{

        // return false

        $this->response(array(

          "status" => 0,

          "message" => "Failed to delete job category"

        ), REST_Controller::HTTP_NOT_FOUND);

      }

  }

public function job_delete($job_id){

      if($this->employer_model->deleteJob($job_id)){

        // retruns true

        $this->response(array(

          "status" => 1,

          "message" => "job has been deleted"

        ), REST_Controller::HTTP_OK);

      }else{

        // return false

        $this->response(array(

          "status" => 0,

          "message" => "Failed to delete job"

        ), REST_Controller::HTTP_NOT_FOUND);

      }

  }
public function addUpdateInterview_post(){

      $data = json_decode(file_get_contents("php://input"));

      // print_r($data); die;

      
      $interview_detail = array(); 
      if(isset($data->job_id) && isset($data->employee_id)  && isset($data->interview_date) && isset($data->created_by_admin))

      { 

         $error_flag = 0;

      

        if(empty($data->job_id) || empty($data->employee_id) || empty($data->interview_date) || empty($data->created_by_admin))

        {

            $error_flag = 1;

        }

         if($error_flag){

            $this->response(array(

            "status" => 0,

            "message" => "Fields must not be empty!"

          ) , REST_Controller::HTTP_OK);

         return;

        }

        $interview_detail = array(

          "job_id" => $data->job_id,

          "employee_id" => $data->employee_id,

          "interview_date" => $data->interview_date,

          "created_by_admin" => $data->created_by_admin

        );
        if(isset($data->interview_status)){
          if(!empty($data->interview_status)){
             $interview_detail['status'] = $data->interview_status;
          }
        }
        $response = $this->employer_model->addUpdateInterview($interview_detail);
        // print_r($response);die;

        if($interview_detail){
            $unique_id = $this->common_model->getLastRecord_email()['id'] ?? 1;
            $unique_id .= mt_rand(1000, 9999);
            // print_r($unique_id);die;
            // Sending mail and notification to Company
             $company = array('from_id'=> $response->employee_id,
                              'type'=>'employee',
                              'to' => $response->email ?? NULL,
                              'subject'=>'new interview scheduled',
                              'message'=>'hello, '.$response->name.' you have interview scheduled on '.$response->interview_date.'                  for job with title - '.$response->job_title.' you have applied on, scheduled with '.                $response->company_name,
                              'unique_id'=>$unique_id);
              $this->common_model->email($company);
              $this->common_model->addNotification($company);
        // Sending mail and notification to Super-Admin
           $admin = array('from_id'=> 1,
                          'type'=>'Super-Admin',
                          'to' =>'aashi.we2code@gmail.com',
                          'subject'=>'new interview scheduled',
                          'message'=>'new interview scheduled on '.$response->interview_date.' of candidate name '.$response->name.' with '.$response->company_name,
                          'unique_id'=>$unique_id);  
              $this->common_model->email($admin);
              $this->common_model->addNotification($admin);

            $this->response(array(

              "status" => 1,

              "message" => "data inserted successfully"

            ), REST_Controller::HTTP_OK);

      
        }
        else{



          $this->response(array(

            "status" => 0,

            "messsage" => "Failed to insert data"

          ), REST_Controller::HTTP_OK);

        } 

      }else{

        $this->response(array(

          "status" => 0,

          "message" => "All fields are required !"

        ), REST_Controller::HTTP_OK);

      }
    }
public function getInterview_post(){
      $data = json_decode(file_get_contents("php://input"));
      $filter = array();
      $details = array('id'=>$data->id ?? null,
                  'job_id'=>$data->job_id ?? null,
                  'employee_id'=>$data->employee_id ?? null,
                 );

     if(!empty($this->admin_id) && $this->user_type != "super-admin" && $this->user_type != "admin"){
      $details = array("admin_id" =>  $this->admin_id,
                          "admin_type" =>  $this->user_type);
    }  

      $page =  $data->page ?? 1;
      $limit = $data->limit ?? 10; 
// print_r($limit);die;
      // Get search parameter
      $search = isset($data->search) ? $data->search : '';

      // Get filter parameters
if(isset($data->filter_by_time)){
      if($data->filter_by_time == "today"){
            $filter['start_date'] = date('Y-m-d');
            $filter['end_date'] = date('Y-m-d', strtotime('tomorrow'));

      }
      if($data->filter_by_time == "last_week"){
            $filter['start_date'] = date('Y-m-d', strtotime('last week'));
            $filter['end_date'] = date('Y-m-d', strtotime('last week +7days'));

      }
      if($data->filter_by_time == "next_week"){
            $filter['start_date'] = date('Y-m-d', strtotime('next week'));
            $filter['end_date'] = date('Y-m-d', strtotime('next week +7days'));
            
          }
      if($data->filter_by_time == "last_month"){
            $filter['start_date'] = date('Y-m-01', strtotime('last month'));
            $filter['end_date'] =date('Y-m-t', strtotime('last month'));
            
          }
      if($data->filter_by_time == "current_month"){
            $filter['start_date'] = date('Y-m-01', strtotime('this month'));
            $filter['end_date'] =date('Y-m-t', strtotime('this month'));
            
          }
      if($data->filter_by_time == "next_month"){
            $filter['start_date'] = date('Y-m-01', strtotime('next month'));
            $filter['end_date'] = date('Y-m-d', strtotime('next month'));
            
          }
    }
      // Calculate offset for pagination
      $offset = ($page - 1) * $limit;

      // sorting 
      $sort = [
        'column_name' => $data->column_name ?? null ,
        'sort_order' => $data->sort_order ?? null
      ];
      
      // print_r($details);die;
      $response = $this->employer_model->getInterview($details,$filter, $search, $limit, $offset, $sort);
      if($response){

        $this->response(array(
          "status" => 1,
          "message" => " Successful ",
          "data" => $response
        ), REST_Controller::HTTP_OK);
      }else{

        $this->response(array(
          "status" => 0,
          "message" => "No data found"
        ), REST_Controller::HTTP_OK);
      }
  }
public function addUpdateLmia_put(){
    
      $data = json_decode(file_get_contents("php://input"));
      // print_r($data); die;
      $detail = array();
       if(isset($data->job_id) && isset($data->employee_id)){
          if(!empty($data->job_id) || !empty($data->employee_id)){
          $detail["job_id"]=$data->job_id;
          $detail["employee_id"]=$data->employee_id;
        }
        if(isset($data->lmia_status)){
           if(!empty($data->lmia_status)){
           $detail["lmia_status"]=$data->lmia_status;
         }
       }
        if(isset($data->completion_time)){
           if(!empty($data->completion_time)){
           $detail["expected_time_of_completion"]=$data->completion_time;
         }
       }     
        $response = $this->employer_model->addUpdateLmia($detail);
        // print_r($response);die;
        if($response){

            $this->response(array(
              "status" => 1,
              "message" => $response
            ), REST_Controller::HTTP_OK);
        }else{

          $this->response(array(
            "status" => 0,
            "messsage" => "Failed !"
          ), REST_Controller::HTTP_OK);
        }
      }else{
         $this->response(array(
            "status" => 0,
            "messsage" => "All fields required !"
          ), REST_Controller::HTTP_OK);
      }
   }
public function changePassword_put(){
        
        // print_r($this->uri->segment(2));die;
        $data = json_decode(file_get_contents("php://input"));
         if (isset($data->password) && isset($data->new_password) && isset($data->conf_password) && isset($this->company_id)){
               if(empty($data->password) || empty($data->new_password) || empty($data->conf_password) || empty( $this->company_id)){
                     $this->response(array(
                    "status" => 0,
                    "message" => "Fields must not be empty !"
                    ) , REST_Controller::HTTP_OK);
                    return;
               }
               if($data->new_password !== $data->conf_password){
                 $this->response(array(
                    "status" => 0,
                    "message" => "new password and confirm password must be same !"
                    ) , REST_Controller::HTTP_OK);
                    return;
               }
                $check_password = array('password' => md5($data->password));
                $status = $this->employer_model->checkLogin($check_password);
                if($status != false){
                // print_r($status);die;
                 $new_password = array('company_id'=>$status->company_id,
                                        'password'=>md5($data->new_password));
               if($this->employer_model->addUpdateCompanyDetails($new_password)){
                  $this->response(array(
                    "status" => 1,
                    "message" => "Password updated successfully"
                    ) , REST_Controller::HTTP_OK);
                    return;
                  }else{
                     $this->response(array(
                    "status" => 0,
                    "message" => "failed !"
                    ) , REST_Controller::HTTP_OK);
                    return;
                  }
            } else {
               $this->response(array(
                    "status" => 0,
                    "message" => "Wrong password"
                    ) , REST_Controller::HTTP_OK);
                    return;
               }
           } else {
              $this->response(array(
                    "status" => 0,
                    "message" => "all fields are required!"
                    ) , REST_Controller::HTTP_OK);
                    return;
        }

    }
    
}

 