<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
 
 private $user_id = null;
 
 function __construct()
 {
   parent::__construct();
   $this->load->model('users_model');

   $session = $this->session->logged_in["user_id"];
   if(empty($session)) redirect("login"); 
   else $this->user_id = $session;  
  
 }
 
 function index() //all_reports
 {
 	$users = $this->users_model->get_all_users();
	$data = array('users'=>$users);	   
	$this->load->view('all_users',$data);	
 }
 
 function add_user(){
 	$this->load->view('add_user',array('message'=>"",'mode'=>'insert'));
 }
 
 function add_new_user(){
 	$data = array();
 	if(isset($_POST['add_user']) && isset($_POST['user_name']) && isset($_POST['password'])){
 		$user_name = $_POST['user_name'];
		$is_user_exists = $this->users_model->is_user_exists($user_name);
		if(!$is_user_exists){
			$password = MD5($_POST['password']);
			$email = (isset($_POST['email'])) ? $_POST['email'] : NULL;
			$this->users_model->insert_user(array('user_name'=>$user_name,'password'=>$password,'email'=>$email));
			$data['message'] = "Successfully inserted!";
		}
		else{
			$data['message'] = "User with same username already exists, please choose some other username!";
		}
 	}
	else{
		$data['message'] = "Please populate all required fields!";
	}
	$data['mode'] = 'insert';
	$this->load->view('add_user',$data);
 }
 
 function edit_user($user_id){
 	$user = $this->users_model->get_user($user_id);
	$this->load->view('add_user',array('message'=>"",'mode'=>'edit','user_id'=>$user_id,'user_name'=>$user->user_name,'password'=>$user->password,'email'=>$user->email));
 }
 
 function edit_this_user($user_id){
  	$data = array();
 	if(isset($_POST['add_user']) && isset($_POST['user_name']) && isset($_POST['password'])){
 			$user_name = $_POST['user_name'];
			$password = MD5($_POST['password']);
			$email = (isset($_POST['email'])) ? $_POST['email'] : NULL;
			$this->users_model->update_user(array('user_name'=>$user_name,'password'=>$password,'email'=>$email),$user_id);
			$data['password'] = $password;
			$data['user_name'] = $user_name;
			$data['email'] = $email;
			$data['message'] = "Successfully updated!";
 	}
	else{
		$data['message'] = "";
	}
	$data['mode'] = 'edit';
	$data['user_id'] = $user_id;
 	$this->load->view('add_user',$data);
 }
 
 function delete_user($user_id){
 	$this->users_model->delete_user($user_id);
	$this->index();
 }
}
 
?>
