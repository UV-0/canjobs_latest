<?php

Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure

Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure

Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //

defined('BASEPATH') OR exit('No direct script access allowed');



require APPPATH.'libraries/REST_Controller.php';



class Employer_registration extends REST_Controller{



  public function __construct(){



    parent::__construct();

    //load database

    $this->load->database();

    $this->load->model(array("employer_model"));

    $this->load->model(array("common_model"));

    $this->load->library(array("form_validation"));

    $this->load->library('Authorization_Token');

    $this->load->helper("security");

    $this->load->helper('url');

  }


  public function signup_post(){  

    $email = $this->security->xss_clean($this->input->post("email"));

    $contact_no = $this->security->xss_clean($this->input->post("contact_no"));

    $password = $this->security->xss_clean($this->input->post("password"));  
    $otp =$this->input->post("otp");  

    // form validation for inputs

    $this->form_validation->set_rules("email", "Email", "required|valid_email");

    $this->form_validation->set_rules("contact_no", "contact_no", "required");

    $this->form_validation->set_rules("password", "password", "required");

    $this->form_validation->set_rules("otp", "otp", "required");



    // checking form submittion have any error or not

    if($this->form_validation->run() === FALSE){



      // we have some errors

      $this->response(array(

        "status" => 0,

        "message" => "All fields are required!"

      ) , REST_Controller::HTTP_NOT_FOUND);

      return;

  }   

     $existing_email = $email;

     $existing_employer = $this->employer_model->get_employer_by_email($existing_email);

        if($existing_employer){

        $this->response(array(

        "status" => 0,

        "message" => "Email already exists"

        ), REST_Controller::HTTP_OK);

        return;

        }    

      if(!empty($email) && !empty($contact_no) && !empty($password) && !empty($otp)){
        
        $validate_otp = $this->common_model->validate_otp($email,$otp);
        if(!$validate_otp){
        $this->response(array(
        "status" => 0,
        "message" => " incorrect otp "
        ), REST_Controller::HTTP_OK);
        return;
        }

        // all values are available

        $employer = array(

         

          "email" => $email,

          "contact_no" => $contact_no,

          "password" => md5($password),

        

        );
        
        $response = $this->employer_model->insert_employer($employer);
        // print_r($response);die;
        if($response){
            $unique_id = $this->common_model->getLastRecord_email()['id'] ?? 1;
            $unique_id .= mt_rand(1000, 9999);
            $detail = array('from_id'=>$response->company_id,
              'type'=>'employer',
              'email' => $response->email,
              'subject'=>'Welcome',
              'message'=>'Hey '.$response->email.' welcome onboard explore our pool of talent.',
              'unique_id'=>$unique_id);  
              $this->common_model->addNotification($detail);
              $this->common_model->email($detail);

          $this->response(array(

            "status" => 1,

            "message" => "Employer has been registered"

          ), REST_Controller::HTTP_OK);

        }else{
          $this->response(array(

            "status" => 0,

            "message" => "Failed to create employer"

          ), REST_Controller::HTTP_OK);

          return;

        }

      }else{

        // we have some empty field

        $this->response(array(

          "status" => 0,

          "message" => "Field must not be empty !"

        ), REST_Controller::HTTP_OK);
        return;

      }



  }

  public function login_post()

    {

        $email = $this->security->xss_clean($this->input->post("email"));

        $password = $this->security->xss_clean($this->input->post("password"));



        $this->form_validation->set_rules('email', 'Email', 'required');

        $this->form_validation->set_rules('password', 'Pasword', 'required');

        if ($this->form_validation->run()) {

            $credentials = array('email' => $email, 'password' => md5($password)); //md5($password)

            $loginStatus = $this->employer_model->checkLogin($credentials);

            //  print_r($loginStatus);die;

            if ($loginStatus != false) {

                $userId = array('company_id' => $loginStatus->company_id,
                                'user_type' => 'company');

                $bearerToken = $this->authorization_token->generateToken($userId);

                $this->response(array(

                "status" => 1,

                "message" => "Successfully Logged In",

                "company_id"=>$loginStatus->company_id,
                
                "company_name"=>$loginStatus->company_name,

                "token" => $bearerToken,

                 ), REST_Controller::HTTP_OK);
               
                 return;
               
            } else {
                 $this->response(array(

                  "status" => 0,

                  "message" => "Invalid Credentials !"

                ), REST_Controller::HTTP_OK);
                 return;
            }

        } else {
                $this->response(array(

                  "status" => 0,

                  "message" => "Email-id and password required !"

                ), REST_Controller::HTTP_OK);
                 return; 
        }

    }
  public function forgetPassword_post(){
          $data = json_decode(file_get_contents("php://input"));
          // print_r($data->forget_email);die;
           if (isset($data->forget_email)){
               if(empty($data->forget_email)){
                     $this->response(array(
                    "status" => 0,
                    "message" => "Fields must not be empty !"
                    ) , REST_Controller::HTTP_OK);
                    return;
               }
              $email = array('email' => $data->forget_email);
              $loginStatus = $this->employer_model->checkLogin($email);
            //  print_r($loginStatus);die;
              if ($loginStatus != false) {
               $detail = array('company_id'=>$loginStatus->company_id,
                                'token'=> md5($loginStatus->email));
               if($this->employer_model->addUpdateCompanyDetails($detail)){
                            $unique_id = $this->common_model->getLastRecord_email()['id'] ?? 1;
                            $unique_id .= mt_rand(1000, 9999);
                            $detail['email']=$loginStatus->email;
                            $detail['subject']='Reset password';
                            $detail['message']='Hello, '.$loginStatus->contact_person_name.' click on the link to reset your password http://localhost:3000/resetpassword/company:'.$detail['token'];
                            $detail['unique_id']=$unique_id;
                            $this->common_model->email($detail);
                            $this->response(array(
                            "status" => 1,
                            "message" => "Sent you a mail"
                            ) , REST_Controller::HTTP_OK);
                            return;
                       }
         
            } else {
              $this->response(array(
                    "status" => 0,
                    "message" => "No user found"
                    ) , REST_Controller::HTTP_OK);
                    return;
            }
           } else {
             $this->response(array(
                    "status" => 0,
                    "message" => "Email id required!"
                    ) , REST_Controller::HTTP_OK);
                    return;
        }
    }
  public function resetPassword_put(){
        
        // print_r($this->uri->segment(2));die;
        $data = json_decode(file_get_contents("php://input"));
         if (isset($data->password) && isset($data->conf_password) && isset($data->token)){
               if(empty($data->password) || empty($data->conf_password) || empty($data->token)){
                     $this->response(array(
                    "status" => 0,
                    "message" => "Fields must not be empty !"
                    ) , REST_Controller::HTTP_OK);
                    return;
               }
               if($data->password !== $data->conf_password){
                 $this->response(array(
                    "status" => 0,
                    "message" => "password and confirm password must be same !"
                    ) , REST_Controller::HTTP_OK);
                    return;
               }
                $reset_details = array('token' => $data->token,'password' => md5($data->password));
              if($this->employer_model->resetPassword($reset_details)){
                  $this->response(array(
                    "status" => 1,
                    "message" => "Password updated successfully"
                    ) , REST_Controller::HTTP_OK);
                    return;
            } else {
               $this->response(array(
                    "status" => 0,
                    "message" => "failed to update password"
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