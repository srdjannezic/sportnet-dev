<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Companies extends CI_Controller {
 
 private $user_id = null;
 
 function __construct()
 {
   parent::__construct();
   $this->load->model('reports_model');
   $this->load->model('companies_model');
   $this->user_id = $this->session->logged_in["user_id"];
   
   $session = $this->session->logged_in["user_id"];
   if(empty($session)) redirect("login");
   else $this->user_id = $session;  
 }
 
 function index() //all_reports
 {

 	$companies = $this->companies_model->get_all_companies();
	$countries = $this->companies_model->get_all_countries();
	
	$data = array('page'=>'all_companies','companies'=>$companies,'countries'=>$countries);	   
	$this->load->view('all_companies',$data);		
		
 }
 
 function add_company(){
 	$countries = $this->companies_model->get_all_countries();
 	$data = array('page'=>'add_company','countries'=>$countries);
 	$this->load->view('add_company',$data);	
 }
 
 function add_company_to_db(){
 	if(isset($_POST['company_name'])){
 		$company_name = $_POST['company_name'];
		$country_id = $_POST['country_id'];
		$is_same_company_exists = $this->companies_model->is_same_company_exists($company_name,$country_id);
		if($is_same_company_exists){
		echo json_encode(array('error'=>true,'message'=>"Company with name '{$company_name}' for selected country already exists, please choose some other name or country!"));
		}
		else{
			$date = date('Y-m-d');
			$companies_data = array("created"=>$date,"user_id"=>$this->user_id,"name"=>$company_name,'country_id'=>$country_id);
			$company_id = $this->reports_model->insert_in_table("companies",$companies_data);	
			echo json_encode(array('error'=>false,'message'=>''));
		}
 	}
 }
  
  function clear_all($company_group_id){
	$this->reports_model->delete_row("items","company_group_id",$company_group_id);
	$this->reports_model->delete_row("addressitems","company_group_id",$company_group_id);
	$this->reports_model->delete_row("commentitems","company_group_id",$company_group_id);
	$this->reports_model->delete_row("contact","company_group_id",$company_group_id);
	$this->reports_model->delete_row("financialdata","company_group_id",$company_group_id);
	$this->reports_model->delete_row("orderreferenceitems","company_group_id",$company_group_id);
	$this->reports_model->delete_row("summaryitems","company_group_id",$company_group_id);
	$this->reports_model->delete_row("companygroups","company_group_id",$company_group_id);	 
 }

 function search_companies(){
 	if(isset($_POST['value'])){
 		$companies = $this->companies_model->get_companies_like($_POST['value']);
		
		echo json_encode($companies->result_array());
 	}
 }
 
function delete_company(){
 
 	if(isset($_POST['company_id']) && isset($_POST['delete-selected'])){ //cancel
	    $company_id = $_POST['company_id'];
  		$companygroups = $this->reports_model->get_all_companygroups($_POST['company_id']);
		foreach($companygroups->result() as $companygroup){
			$this->clear_all($companygroup->company_group_id);
		}	
		$this->reports_model->delete_row("companies","company_id",$company_id);	
		redirect('companies');
	}

 }
 
}
 
?>
