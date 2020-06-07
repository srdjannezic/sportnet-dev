<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {
 
 private $user_id = null;
 
 function __construct()
 {
   parent::__construct();
   $this->load->model('reports_model');
   $this->load->model('companies_model');
   $this->load->library('ReportParser');

   $session = $this->session->logged_in["user_id"];
   if(empty($session)) redirect("login"); 
   else $this->user_id = $session;  
  
 }
 
 function index($country_id="",$mode="") //all_reports
 {
	if($mode == "ajax"){
		$reports = $this->reports_model->get_country_reports($country_id);
	}
	elseif($mode == "search" && isset($_POST['search'])){
		$reports = $this->reports_model->get_search_reports($_POST['search'],$country_id);
	}
	else{
		$reports = $this->reports_model->get_all_reports();
	}
	//$companies = $this->companies_model->get_all_companies();
	//var_dump($this->reportparser);	
	//var_dump($reports);

	$countries = $this->companies_model->get_all_countries();	
	$data = array('page'=>'all_reports','reports'=>$reports,'countries'=>$countries,'mode'=>$mode);	   
	$this->load->view('all_reports',$data);	
 }
 
 function exportReport(){
	
	if(isset($_POST['report-checkbox']) && isset($_POST['export-xml'])){
		if (session_status() == PHP_SESSION_NONE) { session_start(); }
		$checked_reports = $_POST['report-checkbox'];
		$reports_count = count($checked_reports);
		$counter = 0;
		$delete_counter = 0;
		$files = array();
		$delete_files = array();
		$documentXML = NULL;
		$file_name = NULL;
		$path = "assets/";
		$document_path = "assets/documents/";
		
		foreach ($checked_reports as $report) {
			$reports = json_decode($report);
			
			$company_id = $reports->company_id;
			$report_year = $reports->report_year;
			$report_export = $this->reports_model->get_reports_for_export($company_id,$report_year);
			$company_name = $reports->company_name;
 			//var_dump($report_export);			
			
			
 			$file_name = $company_name.$report_year;
			$file_name = preg_replace('/\s+/', '', $file_name);			
			$files[$counter] = $path . $file_name . ".xml";
			$delete_files[$delete_counter] = $path . $file_name . ".xml";
			
			$companygroups = $this->reports_model->get_groups_for_export($company_id,23,$report_year); //get financialstatements companygroupid
			
			foreach($companygroups->result() as $companygroup){
				$document_item = $this->reports_model->get_item_column('document_source',$companygroup->company_group_id,'items');
				if($document_item->document_source !== NULL and $document_item->document_source != ""){
					$counter ++;
					$reports_count ++;
					$files[$counter] = $document_path . $document_item->document_source;
				}
			}
			
		    $_SESSION['downloadstatus']=array("status"=>"pending","message"=>"Processing".$counter);
			$documentXML = $this->reportparser->toXML($report_export,$this->reports_model,$company_name,$report_year,$company_id);
			//print_r($documentXML);
			if($reports_count > 1){			
				$file = fopen($path.$file_name.".xml",'w');
				fwrite($file,$documentXML->asXML()); 
			}
			$counter ++;
			$delete_counter ++; 
		}
		
		$_SESSION['downloadstatus']=array("status"=>"finished","message"=>"Done");
		
		//var_dump($files);
		
		if($reports_count > 1){
			//var_dump($files);
			$this->reportparser->downloadZip($files,$delete_files);	
		}
		else{
			$this->reportparser->downloadFile($documentXML,$file_name);	
		}
	}
	elseif(isset($_POST['report-checkbox']) && isset($_POST['export-pdf'])){
		if (session_status() == PHP_SESSION_NONE) { session_start(); }
		
		$this->load->library('pdf');
		$checked_reports = $_POST['report-checkbox'];
		$reports_count = count($checked_reports);
		$counter = 0;
		$delete_counter = 0;
		$files = array();
		$delete_files = array();
		$documentXML = NULL;
		$file_name = NULL;
		$path = "assets/";
		$content = "";
		$document_path = "assets/documents/";
		
		foreach ($checked_reports as $report) {
			$reports = json_decode($report);
			
			$company_id = $reports->company_id;
			$report_year = $reports->report_year;
			$report_export = $this->reports_model->get_reports_for_export($company_id,$report_year);
			$company_name = $reports->company_name;
 			$country_name = $reports->country_name;

 			$file_name = $company_name.$report_year;
			$file_name = preg_replace('/\s+/', '', $file_name);			
			$files[$counter] = $path . $file_name . ".pdf";
			$delete_files[$delete_counter] = $path . $file_name . ".pdf";
			
			$companygroups = $this->reports_model->get_groups_for_export($company_id,23,$report_year); //get financialstatements companygroupid
			
			foreach($companygroups->result() as $companygroup){
				$document_item = $this->reports_model->get_item_column('document_source',$companygroup->company_group_id,'items');
				if($document_item->document_source !== NULL and $document_item->document_source != ""){
					$counter ++;
					$reports_count ++;
					$files[$counter] = $document_path . $document_item->document_source;
				}
			}			
			
			$_SESSION['downloadstatus']=array("status"=>"pending","message"=>"Processing".$counter);
			$content = "";
			$content .= $this->reportparser->toPDF($report_export,$this->reports_model,$company_name,$report_year,$country_name,$company_id);
			//echo $content;
			$pdf = $this->pdf->load();

			$pdf->SetFooter($_SERVER['HTTP_HOST'].'|{PAGENO}|'.date(DATE_RFC822)); // Add a footer for good measure ;)
			$pdf->WriteHTML($content); // write the HTML into the PDF
			$pdf->SetHTMLFooter('<div style="padding-top:20px;"><hr><p style="font-size:14px;">Information supplied are strictly confidential, and for commercial use only. The Client undertakes to keep the secrecy and to makethem inaccessible for a third party, holding himself accountable for anypossible damage or injury derived from their disclosure. It is moreoverforbidden the client to mention AGENCY SPORTNET DOO as the source of informationand to produce them for civil and/or criminal trials. <p></div>');
			$pdf->Output($path . $file_name . ".pdf", 'F'); // save to file because we can		
			$counter ++;
			$delete_counter ++;  
		} 
		//var_dump($files);
		//echo $content;
		$_SESSION['downloadstatus']=array("status"=>"finished","message"=>"Done");
		if($reports_count > 1){  
			//var_dump($files);
			$this->reportparser->downloadZip($files,$delete_files);	
		}
		else{		
			$this->reportparser->downloadPdf($path . $file_name . ".pdf",$file_name.".pdf");
		}
	}
	elseif(isset($_POST['report-checkbox']) && isset($_POST['delete-selected'])){
		//var_dump($_POST['report-checkbox']);
		foreach($_POST['report-checkbox'] as $report){
			$reports = json_decode($report);
			$company_id = $reports->company_id;
			$report_year = $reports->report_year;		
			
			$companygroups = $this->reports_model->get_report_companygroups($company_id,$report_year);
			foreach($companygroups->result() as $companygroup){
				$this->clear_all($companygroup->company_group_id);
			}			
		}
		
		redirect('reports');
	}
 }
 
 function add_report($mode="insert",$company_id="",$report_year=""){ //add_report
 	$groups = $this->reports_model->get_all_groups();
	$items = $this->reports_model->get_default_itemgroups();
	$companies = $this->companies_model->get_all_companies();
	$countries = $this->companies_model->get_all_countries();
	$reportparser = $this->load->library('FormBuilder');
	$currencies = $this->reports_model->get_all_currencies();
	$ratings = $this->reports_model->get_all_ratings();
	$data = array('page'=>'add_report','groups'=>$groups,'default_items'=>$items,'countries'=>$countries,'companies'=>$companies,'mode'=>$mode,"edit_company_id"=>$company_id,'edit_report_year'=>$report_year,'reports_model'=>$this->reports_model,'reportparser'=>$this->reportparser,'ratings'=>$ratings,'currencies'=>$currencies);
	$this->load->view('add_report',$data);
 }
 
 function insert_companygroup($array){
	$company_group_id = $this->reports_model->insert_in_table('companygroups',$array); //insert and generate company_group_id	
	return $company_group_id;
 }

 
 function save_report($mode){
	if(isset($_POST['group_id']) && isset($_POST['company_id']) && isset($_POST['company_group_id']) && isset($_POST['report_year']) && isset($_POST['value']) && isset($_POST['name'])){		
	
		$group_id = $_POST['group_id'];
		$company_id = $_POST['company_id'];
		$company_group_id = $_POST['company_group_id'];
		$report_year = $_POST['report_year'];
		$value = $_POST['value'];
		$column_name = $_POST['name'];
		$input_name = $column_name . $group_id;
		$is_company_has_report = $this->reports_model->is_company_has_report($company_id,$report_year);
		$table = $this->reportparser->group_exceptions($group_id);
		
		if(($input_name == "name33" and $is_company_has_report == false and $mode == "insert") or 
		$input_name != "name33" or $mode == "edit"){ //ako ne postoji isti report year za tu kompaniju than save
		if($company_id !== "null"){
			$is_company_group_in_items = $this->reports_model->is_company_group_in_table($company_group_id,$table);
			
			$is_group_in_company = $this->reports_model->is_group_in_company($group_id,$company_id,$report_year);
			if(($input_name == "name33" and !$is_company_group_in_items) or (($input_name !== "name33") and ($is_group_in_company == false or $company_group_id == "null"))){ 
				//echo $company_id . " " . $group_id . " " . $report_year; 
				$company_group_id = $this->insert_companygroup(array('company_id'=>$company_id,'group_id'=>$group_id,'report_year'=>$report_year)); //insert and generate company_group_id	 			
			}
			else{ //update data
				$this->reports_model->update_table_2where('companygroups',"company_group_id",'group_id',$company_group_id,$group_id,
				array(
					'company_id'=>$company_id,
					'group_id'=>$group_id,
					'report_year'=>$report_year)
				); //insert and generate company_group_id
			}
			
			$is_column_exists = $this->reports_model->is_column_exists($column_name,$table);
 			
			if($is_column_exists){
				$is_company_group_in_table = $this->reports_model->is_company_group_in_table($company_group_id,$table);
				if($is_company_group_in_table){ //update items		
					$this->reports_model->update_table($table,"company_group_id",$company_group_id,array($column_name=>$value));
					if (session_status() == PHP_SESSION_NONE) { session_start(); }
					if(isset($_SESSION['upload_name'])){
						$this->reports_model->update_table($table,"company_group_id",$company_group_id,array('document_source'=>$_SESSION['upload_name']));
						unset($_SESSION['upload_name']);
					}
				}
				else{ //insert items		
					$item_id = $this->reports_model->insert_in_table($table,
					array(
						"company_group_id"=>$company_group_id,				
						$column_name=>$value
					));			 
				}
				
				echo json_encode(array('company_id'=>$company_id,'company_group_id'=>$company_group_id,"error"=>"false","type"=>""));
			}
			else{
				echo json_encode(array('company_id'=>$company_id,'company_group_id'=>$company_group_id,"error"=>"true","type"=>"column-not-exists"));
			}
			
		}
		else{
			 echo json_encode(array('company_id'=>$company_id,'company_group_id'=>"null","error"=>"true","type"=>"no-company"));
		}
		}
		elseif($input_name == "name33"){
			echo json_encode(array('company_id'=>$company_id,'company_group_id'=>"null","error"=>"true","type"=>"duplicate-report"));
		}
		else{
			echo json_encode(array('company_id'=>$company_id,'company_group_id'=>"null","error"=>"true","type"=>""));
		}
	}
	else{
		if(!isset($company_id)) $company_id = NULL;
		echo json_encode(array('company_id'=>$company_id,'company_group_id'=>"null","error"=>"true","type"=>"not-populated"));
	}
 }

 function update_report_year(){
 	if(isset($_POST['report_year']) && isset($_POST['company_group_id'])){
 		$report_year = $_POST['report_year'];
		$company_group_id = $_POST['company_group_id'];
		$this->reports_model->update_table("companygroups","company_group_id",$company_group_id,array("report_year"=>$report_year));
 	}
 }
 
 function get_group($group_id,$group_counter){
	
	//create new company group
	$company_group_id = "null";
	if(isset($_POST['company_id']) && isset($_POST['report_year'])){
		$company_group_id = $this->insert_companygroup(
		array(
		'company_id'=>$_POST["company_id"],
		'group_id'=>$group_id,
		'report_year'=>$_POST["report_year"]));
	}
	
	$group = $this->reports_model->get_specific_group($group_id);
	$default_items = $this->reports_model->get_items_for_group($group_id);
	
 	$this->load->library('FormBuilder');
	$companies = $this->companies_model->get_all_companies();
	$countries = $this->companies_model->get_all_countries();
	$currencies = $this->reports_model->get_all_currencies();
	$ratings = $this->reports_model->get_all_ratings();
	$group_counter++;
	$accordion = $group_counter;
	$group = 
	'<div class="panel-group group-' . $group_id . ' group-'.$group_id.'-order-'. $accordion . '" id="accordion'.$accordion.$group_id.'">
	<input type="hidden" value="" class="company_id"/>
	
	<input type="hidden" value="'.$company_group_id.'" class="company_group_id"/>
	<div class="panel panel-default panel-' . $group_id . '">
								
	<div class="panel-heading">
	<h4 class="panel-title">
	<a data-toggle="collapse" data-parent="#accordion'.$accordion.$group_id.'" href="#collapse'.$accordion.$group_id.'">
	'.$group -> title.' #'.$accordion.'</a>
	<div style="float:right;">
        <span class="glyphicon glyphicon-plus add-group" data-group="'. $group->group_id . '" data-order="'.$group_counter.'"></span>
       	<span class="glyphicon glyphicon-minus remove-group" data-group="'. $group->group_id . '" data-order="'.$group_counter.'"></span>
	</div>
	</h4>
	</div>
	<div id="collapse'.$accordion.$group_id.'" class="panel-collapse collapse in">
	<div class="panel-body">';		
		
	foreach ($default_items->result() as $items) {
		$group .= $this -> formbuilder -> generate_form($items -> input_field, $items -> name, $items -> title, "", $group_id,$companies,$countries,$currencies,$ratings,'null',null,$group_counter);								
	}	
	
	$group .= '</div></div></div></div>';
	echo $group;
 }
 
 function delete_report(){
	if(isset($_POST['company_group_id'])){ //delete group
		$company_group_id = $_POST['company_group_id'];
		$this->clear_all($company_group_id);
		echo json_encode(array("deleted"=>true));
	}	 
 	elseif(isset($_POST['company_id']) && isset($_POST['report_year'])){ //cancel
  		$companygroups = $this->reports_model->get_report_companygroups($_POST['company_id'],$_POST['report_year']);
		foreach($companygroups->result() as $companygroup){
			$this->clear_all($companygroup->company_group_id);
		}	
		echo json_encode(array("deleted"=>true));
	}
	else{ //error
		echo json_encode(array("deleted"=>false));
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
 
 function getstatus(){
	 if (session_status() == PHP_SESSION_NONE) {
	 	session_start(); 
	 }
	echo json_encode($_SESSION['downloadstatus']);
 }
 
 function upload_file(){
	 //$sourcePath = $_FILES['file']['tmp_name'];
    //echo "company_group_id: ".$company_group_id;

	
	$uploaddir = 'assets/documents/';
	$file = $_FILES['fileToUpload'];
	//var_dump($file);
	
	$report_year = $_POST['report_year'];
	$company_id = $_POST['company_id'];
	$group_id = $_POST['group_id'];
	$file_name = preg_replace('/\s+/', '', $file['name']);
	$path = $uploaddir .basename($file_name);
	$error = false;
    if(move_uploaded_file($file['tmp_name'], $path))
    {
		if (session_status() == PHP_SESSION_NONE) { session_start(); }
		$_SESSION['upload_name'] = $file_name;			
		
		$file_parts = pathinfo($path);
		$document = "";
		$is_excel = false;
		switch($file_parts['extension']){		
			case "doc":
				$document = $this->reportparser->read_doc($path);
				break;
			case "docx":
				$document = $this->reportparser->read_docx($path);
				break;
			case "txt":
				$document = $this->reportparser->read_txt($path);
				break;
			case "xls" || "xlsx":
				$document = $this->reportparser->read_xls($path);
				$is_excel = true;
				break;
			default:
				$error = true;
				break;
		}
		
        if($is_excel){
			echo json_encode(array("document"=>$document,"is_excel"=>true));
		}
		else{
			echo json_encode(array("document"=>$document,"is_excel"=>false));
		}
		
		
		//echo $document;
		
		
		//unlink($path); //delete file
    }
    else
    {
        echo json_encode(array("document"=>"error"));
    } 
 }
 
 function load_activity_codes($type=""){
	 $codes = $this->reports_model->get_activity_codes($type);
	 $options = "";
	 foreach($codes->result() as $code){
		 $options .= '<option value="'.$code->code .'" data-id="'.$code->code_id.'">'.$code->code.'</option>';
	 } 
	 echo $options;
 }
 
  function load_activity_description($type="",$code=""){
	 $description = $this->reports_model->get_activity_description($type,$code);
	 //var_dump($description);
	 if(isset($description->description)){
		echo $description->description;
	 }
 }
 
 function load_rating_description(){
	 if(isset($_POST['rating'])){
		 $rating = $this->reports_model->get_rating($_POST['rating']);
		 echo $rating->row()->rating_description;
	 }
 }
 
}
 
?>
