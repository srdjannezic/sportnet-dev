<?php
include APPPATH.'/third_party/PHPExcel/Classes/PHPExcel/IOFactory.php';;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ALL);
ini_set('display_errors', 1);
class ReportParser{
	function toXML($reports,$reports_model,$company_name,$report_year,$company_id){
		$documentXML = new SimpleXMLElement("<document></document>");
		$documentXML->addAttribute('type', 'sportnet-xml');
		$groupParentXML = $documentXML->addChild('group');
		$groupParentXML->addAttribute('type', 'cr:Company Data');
		
		//var_dump($reports_model->get_items_for_group(1)->result());
		foreach($reports as $report){
			//var_dump($report); 
			$groupXML = $groupParentXML->addChild('group');
			$groupXML->addAttribute('type', $report->type);

			/*
			$groupXML->addAttribute('title',$report->title);
				
			if($report->header !== NULL){
				$groupXML->addAttribute('header',$report->header.".Row");
			}
				
			if($report->na !== NULL){
				$groupXML->addAttribute('na',$report->na);
			}
			*/
	
			$groups = $reports_model->get_groups_for_export($report->company_id,$report->group_id,$report->report_year);
			
			
			//var_dump($groups);
			foreach ($groups->result() as $group) {
				$groupRowXML = $groupXML->addChild('group');
				$company_group_id = $group->company_group_id;
				$groupRowXML->addAttribute('type',$report->type.".Row");
				
				$default_items = $reports_model->get_items_for_group($group->group_id);
				
				//$items = $reports_model->get_items_for_export($company_group_id,$table);
				$item_alias = "";
				foreach($default_items->result() as $item){ 
					//var_dump($report->group_id);
					if($item->alias == "address") {
						$table = "addressitems";
					}
					else $table = $this->group_exceptions($report->group_id);
					//echo $item->name . " {$table}";
					//var_dump($table . " " . $item->name . " " . $company_group_id);
					$item_column = $reports_model->get_item_column($item->name,$company_group_id,$table);
					if($item_column === NULL) $item_column = $reports_model->get_item_column($item->name,$company_group_id,"items");
					//var_dump($company_group_id);
	
					
					if(isset($item_column->{$item->name})){
						$item_value = $item_column->{$item->name};
					}
					else{
						$item_value = "error"; //we have error
					}
					
					if($item_value == "0000-00-00"){
						$item_value = NULL;
					}
					
					//var_dump($item_column);
					//var_dump($company_group_id . " " . $group->group_id . " " . $item_value);
					
					if(trim($item_value) == "1") $item_value = "true" ;
					elseif(trim($item_value) == "0") $item_value = "false";					
					if($item_value !== NULL and $item_value !== "error" and trim($item_value) !== "" and !empty(trim($item_value))) {
						if($item->input_field == "date"){
							$item_value = date("Y-m-d",strtotime($item_value));
						}
					
						if($item->input_field == "datetime"){
							$item_value = date("Y-m-d H:i:s",strtotime($item_value));
						}	
						
						$special_group = false;
						if(($item->alias == "address" and !($group->group_id == 2 or $group->group_id == 3 or $group->group_id == 4))){
							$special_group = true;
						}
						//var_dump($expression)
						if($group->group_id == 1 and $item->alias == "activity"){
							$special_group = true;
						} 
						
						if($special_group){ //add subgroup for address/activity
							//var_dump($item->id ." ". $item->title . " " . $item->alias . " " . $group->group_id); 
							//var_dump($item->alias . $report->title) ;
							$item_type = $item->type;
							if($item->alias != $item_alias){
	 							$subGroupXML = $groupRowXML->addChild('group');
								$subGroupXML->addAttribute('type',$item->alias);
								//$subGroupXML->addAttribute('title',$item->title);
								$item_alias = $item->alias;	 		
							}
							//if($group->group_id == 33)
							
							$subGroupItemXML = $subGroupXML->addChild('item',htmlspecialchars($item_value));
							$subGroupItemXML->addAttribute('type',strtolower($item->title));
							//$subGroupItemXML->addAttribute('title',$item->title);
						}				
						else{
							
							$item_type = $item->type;
							if($item_type === NULL) $item_type = $item->name;
							if($item->name == "src_document"){ //financial statement
							$json_value = json_decode($item_value,true);
								foreach($json_value as $line){ //read each line and add tag line
									$itemGroupXML = $groupRowXML->addChild('group');
									$itemGroupXML->addAttribute('type',"line");
									if($line['tit'] !== null and $line['tit'] !== "null" and trim($line['tit']) !== ""){
										$itemXML = $itemGroupXML->addChild('item',htmlspecialchars($line['tit']));
										$itemXML->addAttribute('type',"tit");
									}
									if($line['val'] !== null and $line['val'] !== "null" and trim($line['val']) !== ""){
										$itemXML = $itemGroupXML->addChild('item',htmlspecialchars($line['val']));
										$itemXML->addAttribute('type',"val");									
									}
								} 
							}
							else{
								$itemXML = $groupRowXML->addChild('item',htmlspecialchars($item_value));
								$itemXML->addAttribute('type',$item_type);
								//$itemXML->addAttribute('title',$item->title);
								//$itemXML->addAttribute('str',$item->str);	
								$item_alias = "";
							}
						}
					}
				}
			}
		}
		return $documentXML;
	}
	
	function is_empty_group($default_items){
		
	}
	
	function toPDF($reports,$reports_model,$company_name,$report_year,$country_name,$company_id){
		$content = "<div style='width:1000px'>";
		$content .= "<table style=''>";
		$content .= "<thead>";
		$content .= "<th style='width:700px;'>" . "<th>";
		$content .= "<th style='width:300px;'>" . "</th>";
		$content .= "</thead>";
		$content .= "<tbody>"; 
		$content .= "<tr>";
		$content .= "<td style='text-align:left;width:380px;'>" . "Logo" . "<td>";
		$content .= "<td style='text-align:left;font-size:14px;'>" . "AGENCY SPORTNET DOO<br/>International Business Information Services<br/>11010 Beograd, St. Vojvode Djurovica 2 b<br/>Tel ++381-63-259971<br/>E-mail: nkosic@eunet.rs  " . "</td>";		
		$content .= "</tr>"; 
		$content .= "</tbody>";
		$content .= "</table>";
		$content .= "<hr/>";
		$content .= "<h1 style='text-align:center;text-decoration:underline;'>{$company_name} - {$report_year}</h1>";
		
		$content .= $this->create_pdf_summary($reports,$reports_model,$report_year,$company_id); //CREATE SUMMMARY HEAD
		$content .= '<pagebreak />'; 
		foreach($reports as $report){

			$item_alias = ""; 
			
			$content .= "<tr>" . "<h3 style='font-weight:bold;margin:0;background-color:#efdbc4;width:250px;'>" . $report->title . "</h3></tr>";
			//var_dump($report->group_id);
			$companygroups = $reports_model->get_groups_for_export($report->company_id,$report->group_id,$report->report_year);
			$default_items = $reports_model->get_items_for_group($report->group_id);	

			$group_counter = 0;	
			$tel_counter = 0;
			//var_dump($default_items);
			foreach ($companygroups->result() as $group) { //for each companygroup					
				$company_group_id = $group->company_group_id;
				$padding = '';
				//if(!$this->is_contact($report->group_id)){
					$padding = 'padding-bottom:20px';
									
				$content .= "<table style='{$padding};overflow:wrap;'>"; 	
				$content .= "<thead><th><h3 style='font-weight:bold;margin:0;background-color:#efdbc4;width:250px;'></th><th></th></thead>"; 	 
				$content .= "<tbody>"; 				 				
				foreach($default_items->result() as $item){
					$item_title = $item->title;
					$item_type = $item->type;
					if($item_title === NULL) $item_title = $item->name;		
					if($item_type === NULL) $item_type = $item->name;			
				
					if($item->alias == "address") {
						$table = "addressitems";
					}
					else $table = $this->group_exceptions($report->group_id);
					
					$item_column = $reports_model->get_item_column($item->name,$company_group_id,$table);
					if($item_column === NULL) $item_column = $reports_model->get_item_column($item->name,$company_group_id,"items");
						
					if(isset($item_column->{$item->name})){
						$item_value = $item_column->{$item->name};
					}
					else{
						$item_value = NULL; //we have error
					}					
					
					if($item_value == "0000-00-00"){
						$item_value = NULL;
					}
					if($item->name == "gender"){
						if(trim($item_value) == "1") $item_value = "male" ;
						elseif(trim($item_value) == "0") $item_value = "female";							
					}
					else{
						if(trim($item_value) == "1") $item_value = "yes" ;
						elseif(trim($item_value) == "0") $item_value = "no";		
					}
					
					if($item_value !== NULL and $item_value !== "") { 	
						if($item->name == "origin" or ($this->is_address($report->group_id) and $item->name == 'country')){
							if(isset($reports_model->get_country_name($item_value)->country_name)){
								$item_value = $item_value . " (" . $reports_model->get_country_name($item_value)->country_name . ")"; 
							}
						}
					if($this->is_contact($report->group_id) and $item->name == 'contact'){
						$item_title = $item_value;
						$value = $reports_model->get_item_column('value',$company_group_id,$table)->value;
						$prefix = $reports_model->get_item_column('prefix',$company_group_id,$table)->prefix;
						
						if(trim($item_title) == "TEL" or trim($item_title) == "MOB"){
							$item_value = $prefix . $value;
							$tel_counter ++;
						}
						else{
							$tel_counter = 0;
							$item_value = $value;
						}
					}
					$is_exception = $this->is_exception($report->group_id, $item->name,$item_value);
						
					if($item->name == "a_src"){ //FINANCIAL STATEMENTS SOURCE EXCEPTION
						$document_source = $reports_model->get_item_column('document_source',$company_group_id,$table)->document_source;
						if($document_source !== NULL){
							$item_title = "Document attached";
							$item_value = $document_source;
						}
						else{
							$is_exception = true; 
						}
					}

					if(!$is_exception){
						$content .= "<tr>"; 
						if($report->group_id == 12){ //FINAL COMMENTS
							$content .= '<td colspan="2">';						
							$content .= '<p style="word-wrap:break-word;width:600px;">' . $item_value . "</p>";
							$content .= "</td>";							
						}
						else{
							//var_dump($item->name);
							if($item->name == "src_document"){
								$json_value = json_decode($item_value,true);
								//var_dump($json_value);
								foreach($json_value as $line){
									$item_title = $line['tit'];
									$item_value = $line['val'];
									$content .= "<tr>";
									$has_title = false;
									$has_value = false;
									
									if($line['tit'] !== null and $line['tit'] !== "null" and trim($line['tit']) !== ""){
										$has_title = true;
									}
									if($line['val'] !== null and $line['val'] !== "null" and trim($line['val']) !== ""){
										$has_value = true;
									}
									
									if($has_title){
										$has_title = true;
										$content .= "<td style='background-color:#f0f0f0;width:350px;float:left;font-size:12px;'>";  
										$content .=  ucfirst($item_title); 									
										$content .= "</td>";  
									}
									if($has_value){
										$has_value = true;
										$content .= '<td>';						
										$content .= '<p style="word-wrap:break-word;width:500px;font-size:12px;">' . $item_value . "</p>";
										$content .= "</td>";
									}
									$content .= "</tr>";
								}
							}
							else{
								$back_color = "#f0f0f0";
								if($report->group_id == 23){ //financial statements
									$back_color = "#fff2e5";
								}
								$content .= "<td style='background-color:".$back_color.";width:250px;float:left;'>";  
								$content .=  ucfirst($item_title); 
								$content .= "</td>";  
								$content .= '<td style="background-color:'.$back_color.';">';						
								$content .= '<p style="word-wrap:break-word;width:600px;">' . $item_value . "</p>";
								$content .= "</td>";
									
							}
						}
						$content .= "</tr>";						
					}						
					} 
					$group_counter ++;	 					
				}
				$content .= "</tbody>";
				$content .= "</table>";
			}

		}
		$content .= "</div>";
		//echo $content;
		return $content;
	}
	
	function is_exception($group_id,$item_name,$item_value=""){
		if($group_id == 13 and $item_name == "value"){ //contacts
			return true;
		}
		elseif($group_id == 13 and $item_name == "prefix"){
			return true;
		}
		elseif($item_name == "is_cancelled"){
			return true;
		}
		/*elseif($item_name == "src_document"){
			return true;
		}*/		
		else{
			return false;
		}
	}
	
	function is_contact($group_id){
		if($group_id == 13){
			return true;
		}
		else{
			return false;
		}
	}
	
	function is_address($group_id){
		if($group_id == 2 or $group_id == 3 or $group_id == 4){
			return true;
		}
		else{
			return false;
		}
	}
	
	function create_pdf_summary($reports,$reports_model,$report_year,$company_id){
		$identifications = $reports_model->get_groups_for_export($company_id,33,$report_year);
		$activities = $reports_model->get_groups_for_export($company_id,1,$report_year);
		$addresses = $reports_model->get_groups_for_export($company_id,2,$report_year);
		$legal_addresses = $reports_model->get_groups_for_export($company_id,3,$report_year);
		$other_addresses = $reports_model->get_groups_for_export($company_id,4,$report_year);
		$contacts = $reports_model->get_groups_for_export($company_id,13,$report_year); 
		$contacts = $reports_model->get_groups_for_export($company_id,13,$report_year); 
		$registration_data = $reports_model->get_groups_for_export($company_id,39,$report_year); 
		$other_registrations = $reports_model->get_groups_for_export($company_id,40,$report_year);
		
		$legal_form = $reports_model->get_groups_for_export($company_id,27,$report_year); 
		$foundations = $reports_model->get_groups_for_export($company_id,25,$report_year); 
		$employees = $reports_model->get_groups_for_export($company_id,17,$report_year); 
		$financial_data = $reports_model->get_groups_for_export($company_id,22,$report_year); 
		$credit_opinion = $reports_model->get_groups_for_export($company_id,14,$report_year); 
		
		$content = "<div>";
		$content .= "<tr><h3 style='font-weight:bold;background-color:#efdbc4;padding:5px;margin:0;'>Reported Subject</h3></tr>";		
		$content .= "<table style='width:100%;'>";
		$content .= "<thead><th style='width:30%;'></th><th style='width:70%'></th></thead>";
		$content .= "<tbody>";
		foreach($identifications->result() as $identity){
			//var_dump($identity);
			$items = $reports_model->get_items_for_export($identity->company_group_id,'items');
			
			if($items->num_rows() > 0){
				$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
				$content .=  "<td style='background-color:#f0f0f0;'>Full Name</td>";
				$content .=  "<td style='background-color:#fff;'>" . $items->row()->fullname . "</td>";
				$content .= "</tr>";
			
				$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>"; 
				$content .=  "<td style='background-color:#f0f0f0;'>Name in national language</td>";
				$content .=  "<td style='background-color:#fff;'>" . $items->row()->origname . "</td>";
				$content .= "</tr>";	
			}
		}
		
		if($activities->num_rows() > 0){			
			$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
			$content .=  "<td style='background-color:#f0f0f0;'>Activities, SIC</td>";
			$content .=  "<td style='background-color:#fff;'>";				
			
			foreach ($activities->result() as $activity) {
				$items = $reports_model->get_items_for_export($activity->company_group_id,'items');		
				if($items->row()->type == "SIC"){
					$content .= $items->row()->code . ": " . $items->row()->description . "<br/>";
				}
			}
			
			$content .= "</td></tr>";

			$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>"; 
			$content .=  "<td style='background-color:#f0f0f0;'>Activities, NACE_2</td>";
			$content .=  "<td style='background-color:#fff;'>";	
			
			foreach ($activities->result() as $activity) {
				$items = $reports_model->get_items_for_export($activity->company_group_id,'items');		
				if($items->row()->type == "NACE"){
					$content .= $items->row()->code . ": " . $items->row()->description . "<br/>";
				}
			}	
			
			$content .= "</td></tr>";						
		}

		$counter = 0;
		foreach($addresses->result() as $address){
			//var_dump($identity);
			$items = $reports_model->get_items_for_export($address->company_group_id,'addressitems');
			if($items->num_rows() > 0){
				if($counter == 0){
					$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
					$content .=  "<td style='background-color:#f0f0f0;'>Office Address</td>";
					$content .=  "<td style='background-color:#fff;'>";			
				}
				$content .= $items->row()->address . "<br/>";
				
				if($counter == $addresses->num_rows()){
					$content .= "</td></tr>";
				}
			}
			$counter ++;
		}		
		
		$counter = 0;
		foreach($legal_addresses->result() as $address){
			//var_dump($identity);
			$items = $reports_model->get_items_for_export($address->company_group_id,'addressitems');
			if($items->num_rows() > 0){
				if($counter == 0){
					$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
					$content .=  "<td style='background-color:#f0f0f0;'>Legal Address</td>";
					$content .=  "<td style='background-color:#fff;'>";			
				}
				$content .= $items->row()->address . "<br/>";
				
				if($counter == $legal_addresses->num_rows()){
					$content .= "</td></tr>";
				}
			}
			$counter ++;
		}		
		
		$counter = 0;
		foreach($other_addresses->result() as $address){
			//var_dump($identity);
			$items = $reports_model->get_items_for_export($address->company_group_id,'addressitems');
			if($items->num_rows() > 0){
				if($counter == 0){
					$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
					$content .=  "<td style='background-color:#f0f0f0;'>Other Address</td>";
					$content .=  "<td style='background-color:#fff;'>";			
				}
				$content .= $items->row()->address . "<br/>";
				
				if($counter == $other_addresses->num_rows()){
					$content .= "</td></tr>";
				}
			}
			$counter ++;
		}						
		
		if($contacts->num_rows() > 0){			
			$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
			$content .=  "<td style='background-color:#f0f0f0;'>Contact</td>";
			$content .=  "<td style='background-color:#fff;'>";				
			
			$counter = 0;
			foreach ($contacts->result() as $contact) { 
				$items = $reports_model->get_items_for_export($contact->company_group_id,'contact');		
				if($items->row()->contact == "TEL" or $items->row()->contact == "MOB"){
					if($counter == 0) $content .= "Phone: ";
					$content .= $items->row()->prefix . $items->row()->value . ", ";
					$counter ++;
				}
			}
			
			$counter = 0;
			foreach ($contacts->result() as $contact) {
				$items = $reports_model->get_items_for_export($contact->company_group_id,'contact');		
				if($items->row()->contact == "FAX"){
					if($counter == 0) $content .= "<br/>Fax: ";
					$content .= $items->row()->value . ", ";
					$counter ++;
				}
			}
			
			$counter = 0;
			foreach ($contacts->result() as $contact) {
				$items = $reports_model->get_items_for_export($contact->company_group_id,'contact');		
				if($items->row()->contact == "EML"){
					if($counter == 0) $content .= "<br/>E-mail: ";
					$content .= $items->row()->value . ", ";
					$counter ++;
				}
			}
			
			$counter = 0;
			foreach ($contacts->result() as $contact) {
				$items = $reports_model->get_items_for_export($contact->company_group_id,'contact');		
				if($items->row()->contact == "WWW"){	
					if($counter == 0) $content .= "<br/>WWW: ";
					$content .= $items->row()->value . " "; 
					$counter ++;
				}
			}									
			
			$content .= "</td></tr>";					
		}


		$counter = 0;
		foreach($registration_data->result() as $registration){
			//var_dump($identity);
			$items = $reports_model->get_items_for_export($registration->company_group_id,'items');
			if($items->num_rows() > 0){
				if($counter == 0){
					$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
					$content .=  "<td style='background-color:#f0f0f0;'>".$items->row()->regno_type."</td>";
					$content .=  "<td style='background-color:#fff;'>";			
				}
				$content .= $items->row()->regno . "<br/>";
				
				if($counter == $registration_data->num_rows()){
					$content .= "</td></tr>";
				}
			}
			$counter ++;
		}
		
		$counter = 0;
		foreach($other_registrations->result() as $registration){
			//var_dump($identity);
			$items = $reports_model->get_items_for_export($registration->company_group_id,'items');
			if($items->num_rows() > 0){
				if($counter == 0){
					$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
					$content .=  "<td style='background-color:#f0f0f0;'>"."Other: " . $items->row()->regno_type."</td>";
					$content .=  "<td style='background-color:#fff;'>";			
				}
				$content .= $items->row()->regno . "<br/>";
				
				if($counter == $other_registrations->num_rows()){
					$content .= "</td></tr>";
				}
			}
			$counter ++;
		}							
							
		
		$content .= "</tbody>";
		$content .= "</table>";
		
		
		/*BEGIN SUMMARY*/
		
		$content .= "<tr><h2 style='font-weight:bold;background-color:#efdbc4;padding:5px;margin:0;text-align:center;'>S U M M A R Y</h2></tr>";		
		$content .= "<table style='width:100%;border:10px solid #efdbc4'>";
		$content .= "<thead><th style='width:30%;'></th><th style='width:70%'></th></thead>";
		$content .= "<tbody>";
			
		$counter = 0;
		foreach($legal_form->result() as $legal){
			//var_dump($identity);
			$items = $reports_model->get_items_for_export($legal->company_group_id,'items');
			if($items->num_rows() > 0){
				if($counter == 0){
					$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
					$content .=  "<td style='background-color:#f0f0f0;'>Legal Form</td>";
					$content .=  "<td style='background-color:#fff;'>";			
				}
				$content .= $items->row()->form . "<br/>"; 
				
				if($counter == $legal_form->num_rows()){
					$content .= "</td></tr>";
				}
			}
			$counter ++;
		}	

		$counter = 0;
		foreach($foundations->result() as $foundation){
			//var_dump($identity);
			$items = $reports_model->get_items_for_export($foundation->company_group_id,'items');
			if($items->num_rows() > 0){
				if($counter == 0){
					$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
					$content .=  "<td style='background-color:#f0f0f0;'>Incorporation</td>";
					$content .=  "<td style='background-color:#fff;'>";			
				}
				$content .= date('Y',strtotime($items->row()->date)) . "<br/>";
				
				if($counter == $foundations->num_rows()){
					$content .= "</td></tr>";
				}
			}
			$counter ++;
		}	
		
		$counter = 0;
		foreach($employees->result() as $employee){
			//var_dump($identity);
			$items = $reports_model->get_items_for_export($employee->company_group_id,'items');
			if($items->num_rows() > 0){
				if($counter == 0){
					$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
					$content .=  "<td style='background-color:#f0f0f0;'>Staff</td>";
					$content .=  "<td style='background-color:#fff;'>";			
				}
				$content .= $items->row()->number . "<br/>";
				
				if($counter == $employees->num_rows()){
					$content .= "</td></tr>";
				}
			}
			$counter ++;
		}	
		
		$counter = 0;
		foreach($financial_data->result() as $finance){
			//var_dump($identity);
			$items = $reports_model->get_items_for_export($finance->company_group_id,'financialdata');
			if($items->num_rows() > 0){
				if($counter == 0){
					$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
					$content .=  "<td style='background-color:#f0f0f0;'>Sales</td>";
					$content .=  "<td style='background-color:#fff;'>";			
				}
				$content .= $items->row()->net_sales . " " . $items->row()->currency . "<br/>";
				
				if($counter == $financial_data->num_rows()){
					$content .= "</td></tr>";
				}
			}
			$counter ++; 
		}							
		
		$counter = 0;
		if($credit_opinion->num_rows() > 0){ 
			$content .= "<tr><td colspan='2'><h3 style='font-weight:bold;background-color:#efdbc4;padding:5px;margin:0;text-align:left;'>Credit Opinion</h3></td></tr>";
		
			$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
			$content .=  "<td style='background-color:#f0f0f0;'>Credit Rating</td>";
			$content .=  "<td style='background-color:#fff;'>";	
			foreach($credit_opinion->result() as $credit){
				$items = $reports_model->get_items_for_export($credit->company_group_id,'items'); 
				if($items->num_rows() > 0){
					$content .= $items->row()->rating . " " . $items->row()->description . "<br/>";		
				}
			}
			$content .= "</td></tr>";
			
			$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
			$content .=  "<td style='background-color:#f0f0f0;'>Credit Limit</td>";
			$content .=  "<td style='background-color:#fff;'>";	
			foreach($credit_opinion->result() as $credit){
				$items = $reports_model->get_items_for_export($credit->company_group_id,'items'); 
				if($items->num_rows() > 0){
					$content .= $items->row()->amount . " " . $items->row()->currency . "<br/>";
					$content .= $items->row()->comments . "<br/>";	
				}
			}
			$content .= "</td></tr>";			
					
			$content .= "<tr style='border-bottom:1px solid #000;padding:10px;'>";
			$content .=  "<td style='background-color:#f0f0f0;'>Range</td>";
			$content .=  "<td style='background-color:#fff;'>";	
			
			foreach($credit_opinion->result() as $credit){
				$items = $reports_model->get_items_for_export($credit->company_group_id,'items'); 
				if($items->num_rows() > 0){
					$content .= $items->row()->conclusion . "<br/>";	
				}
			}
			$content .= "</td></tr>";
		}			
				
		$content .= "</tbody>";
		$content .= "</table>";	
		$content .= "</div>";				
		return $content;
	}
	
	function downloadFile($documentXML,$file_name){
		header('Content-type: text/xml');
		header('Content-Disposition: attachment; filename="'.$file_name.'.xml"');
		echo $documentXML->asXML();		
	}
	
	function downloadPdf($file,$name){
		header("Content-Type: application/octet-stream");

		header("Content-Disposition: attachment; filename=" . urlencode($name));   
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Description: File Transfer");            
		header("Content-Length: " . filesize($file));
		flush(); // this doesn't really matter.
		$fp = fopen($file, "r");
		while (!feof($fp))
		{
			echo fread($fp, 65536);
			flush(); // this is essential for large downloads
		} 
		fclose($fp); 		
	}
	
	function downloadZip($files,$delete_files){
		$zipname = 'reports'.time().'.zip';
		$zip = new ZipArchive;
		$zip->open($zipname, ZipArchive::CREATE);
		foreach ($files as $file) {
			//var_dump($file);
  			$zip->addFile($file);
		}
		//var_dump($zip);
		$zip->close(); 
        foreach($delete_files as $file){
			
			unlink($file); //delete file
			
		}
        header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: ".filesize($zipname));
        header("Content-Disposition: attachment; filename=\"".basename($zipname)."\"");
        readfile($zipname);
		unlink($zipname);		
	}
	
 function group_exceptions($group_id){
 	$table = "items";
 	switch ($group_id) {
		 case 2:
		 case 3:
		 case 4:
			 $table = "addressitems";
			 break;
		 case 12:
		 	 $table = "commentitems";
			 break;
		 case 13:
			 $table = "contact";
			 break;
		 case 22:
			 $table = "financialdata";
			 break;
		 case 46:
			 $table = "orderreferenceitems";
			 break;
		 default:
			 $table = "items";
			 break;
	 }
	return $table;
 }	
 
 function read_doc($filename) {
    $fileHandle = fopen($filename, "r");
    $line = @fread($fileHandle, filesize($filename));   
    $lines = explode(chr(0x0D),$line);
    $outtext = "";
    foreach($lines as $thisline)
      {
        $pos = strpos($thisline, chr(0x00));
        if (($pos !== FALSE)||(strlen($thisline)==0))
          {
          } else {
            $outtext .= $thisline." ";
          }
      }
     $outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$outtext);
    return $outtext;
 } 
 
	function read_docx($filename){

        $striped_content = '';
        $content = '';

        $zip = zip_open($filename);

        if (!$zip || is_numeric($zip)) return false;

        while ($zip_entry = zip_read($zip)) {

            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

            if (zip_entry_name($zip_entry) != "word/document.xml") continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }// end while

        zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);
			
        return $striped_content;
    } 
	
	function read_txt($filename){
		$content = "";
		$fh = fopen($filename,'r');
		while ($line = fgets($fh)) {
			$content .= $line;
		}
		fclose($fh);		
		return $content;
	}
	
	function read_csv($path){
		$file = fopen($path,"r");
		var_dump($file);
	}
	
	function read_xls($path){
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$objReader->setReadDataOnly(true);

		$objPHPExcel = $objReader->load($path);
		$objWorksheet = $objPHPExcel->getActiveSheet();

		$highestRow = $objWorksheet->getHighestRow();
		$highestColumn = $objWorksheet->getHighestColumn();
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		
		//var_dump($highestColumnIndex);
		$rows = array();

		for ($row = 1; $row <= $highestRow; $row++) {  
			$rows[$row]['tit'] = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
			$rows[$row]['val'] = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
		}
		return $rows;
	}
}
?>