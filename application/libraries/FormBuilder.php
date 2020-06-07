<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FormBuilder{
	function generate_form($type,$name,$title="",$value="",$group_id = "",$companies,$countries,$currencies,$ratings,$company_group_id="null",$v2="null",$group_order=0){
		$form = "";
		if($title == ""){
			$title = $name;
		}
		$required = "";
		$input_name = $name.$group_id;
		
		$name33 = false;
		if($input_name == "name33"){
			$required = "*";
			$name33 = true;
		}
		switch($type){
			case 'text':
			case 'number':
			case 'decimal':
				if($name33 == true or $name == "origin" or $name == "currency" or $name == "value_currency" or $name == "country" or $name == "rating"){ //datalists
				if($name33 == true){ 
					$form = 
					"<div class='form-group {$v2}' >
					    <label for='text'>".$title.$required."</label>
						<input list='browsers' placeholder='start typing' class='form-control name33 {$type}' data-name='".$name."' value='".$value."' data-group-id='".$group_id."' data-company-group-id='".$company_group_id."' name='".$input_name."'>
						<datalist id='browsers'><select>";
						foreach($companies as $company){
							$form .= "<option value='".$company->name." - (".$company->country_name.")' data-id='".$company->company_id."'>".$company->country_name."</option>";
						}
					$form.="</select></datalist>
					</div>";
				}
				elseif($name == "origin" or ($name == "country" and ($group_id == 2 or $group_id == 3 or $group_id == 4))){
						$form = 
					"<div class='form-group {$v2}'>
					    <label for='text'>".$title.$required."</label>
						<input list='origin-list-".$group_order."' placeholder='start typing' class='form-control {$type}' data-name='".$name."' value='".$value."' data-group-id='".$group_id."' 
						data-company-group-id='".$company_group_id."' name='".$input_name."'>
						<datalist id='origin-list-".$group_order."'>";
						foreach($countries as $country){
							$form .= "<option value='".$country->country_name . " - " . $country->country_code . "' data-id='".$country->id."' 
							data-country-code='". $country->country_code."'>".$country->country_code."</option>";
						}
					$form.="</datalist>
					</div>";
				}
				elseif($name == "currency" or $name == "value_currency"){
						$form = 
					"<div class='form-group {$v2}'>
					    <label for='text'>".$title.$required."</label>
						<input list='currency-list-".$group_order."' placeholder='start typing' class='form-control {$type}' data-name='".$name."' value='".$value."' data-group-id='".$group_id."' 
						data-company-group-id='".$company_group_id."' name='".$input_name."'>
						<datalist id='currency-list-".$group_order."'>";
						foreach($currencies->result() as $currency){
							//var_dump($currency);
							$form .= "<option value='".$currency->currency_name . " - " . $currency->currency_code . "' data-id='".$currency->currency_id."' 
							data-currency-code='". $currency->currency_code."'>".$currency->currency_code."</option>";
						}
					$form.="</datalist>
					</div>";					
				}
				elseif($name == "rating"){ 
					$form = '<div class="form-group '.$v2.'" >
  					<label for="text">'.$title.$required.'</label>
  					<select class="form-control rating '.$input_name.' dropdown" data-name="'.$name.'" data-company-group-id="'.$company_group_id.'" data-group-id="'.$group_id.'" name="'.$input_name.'" id="text">';
					
  					$form .= "<option value='".$value."'>".$value."</option>";
					
					foreach($ratings->result() as $rating){
						if($rating->rating_class != $value){
						$form .= "<option value='".$rating->rating_class."' data-id='".$rating->rating_id."'>".$rating->rating_class."</option>";
						}
					}
  					$form .= '</select>
					</div>';					
				}				
				else{
						$form = 
					"<div class='form-group {$v2}'>
					    <label for='text'>".$title.$required."</label>
						<input list='countries-list-".$group_order."' placeholder='start typing' class='form-control {$type}' data-name='".$name."' value='".$value."' data-group-id='".$group_id."' 
						data-company-group-id='".$company_group_id."' name='".$input_name."'>
						<datalist id='countries-list-".$group_order."'>";
						foreach($countries as $country){
							$form .= "<option value='".$country->country_name."' data-id='".$country->id."'>".$country->country_name."</option>";
						}
					$form.="</datalist>
					</div>";
				}
				}
				elseif($input_name == "type1"){ //ACTIVITIES TYPE
					$form = '<div class="form-group '.$v2.'" >
  					<label for="text">'.$title.$required.'</label>
  					<select class="form-control '.$input_name.' dropdown" data-name="'.$name.'" data-company-group-id="'.$company_group_id.'" data-group-id="'.$group_id.'" name="'.$input_name.'" id="text">';
  					if($value == "NACE"){
						$form .= "<option>NACE</option><option>SIC</option><option value='0'>choose option</option>";
					}
					elseif($value == "SIC"){
						$form .= "<option value='SIC'>SIC</option><option value='NACE'>NACE</option><option value='0'>choose option</option>";
					}
					else{
						$form .= "<option value='0'>choose option</option><option value='NACE'>NACE</option><option value='SIC'>SIC</option>";
					}
  					$form .= '</select>
					</div>';					
				}
				elseif($name == "contact"){ //CONTACT 
					$form = '<div class="form-group '.$v2.'" >
  					<label for="text">'.$title.$required.'</label>
  					<select class="form-control '.$input_name.' dropdown" data-name="'.$name.'" data-company-group-id="'.$company_group_id.'" data-group-id="'.$group_id.'" name="'.$input_name.'" id="text">';
  					if($value == "TEL"){
						$form .= "<option value='TEL'>TEL</option><option value='MOB'>MOB</option><option value='FAX'>FAX</option><option value='WWW'>WWW</option><option value='EML'>EML</option><option value='0'>choose option</option>";
					}
					elseif($value == "FAX"){
						$form .= "<option value='FAX'>FAX</option><option value='TEL'>TEL</option><option value='MOB'>MOB</option><option value='WWW'>WWW</option><option value='EML'>EML</option><option value='0'>choose option</option>";
					}
					elseif($value == "WWW"){
						$form .= "<option value='WWW'>WWW</option><option value='TEL'>TEL</option><option value='MOB'>MOB</option><option value='FAX'>FAX</option><option value='EML'>EML</option><option value='0'>choose option</option>";
					}		
					elseif($value == "EML"){
						$form .= "<option value='EML'>EML</option><option value='TEL'>TEL</option><option value='MOB'>MOB</option><option value='FAX'>FAX</option><option value='WWW'>WWW</option><option value='0'>choose option</option>";
					}
					elseif($value == "MOB"){
						$form .= "<option value='MOB'>MOB</option><option value='TEL'>TEL</option><option value='MOB'>MOB</option><option value='FAX'>FAX</option><option value='WWW'>WWW</option><option value='EML'>EML</option><option value='0'>choose option</option>";	
					}					
					else{
						$form .= "<option value='0'>choose option</option><option value='TEL'>TEL</option><option value='MOB'>MOB</option><option value='FAX'>FAX</option><option value='WWW'>WWW</option><option value='EML'>EML</option>";
					}
  					$form .= '</select>
					</div>';					
				}				
				elseif($input_name == "code1"){
					$form = "<div class='form-group {$v2}'> 
					    <label for='text'>".$title.$required."</label>
						<input list='code-list-".$group_order."' placeholder='start typing' class='form-control {$input_name}' data-name='".$name."' value='".$value."' data-group-id='".$group_id."' 
						data-company-group-id='".$company_group_id."' name='".$input_name."'>
						<datalist id='code-list-".$group_order."'></datalist></div>";					
				}
				elseif($input_name == "type9" or $input_name == "type10"){ //SHARE CAPITAL OR CAPITAL HISTORY TYPE  
					$form = '<div class="form-group '.$v2.'" >
  					<label for="text">'.$title.$required.'</label>
  					<select class="form-control '.$input_name.' dropdown" data-name="'.$name.'" data-company-group-id="'.$company_group_id.'" data-group-id="'.$group_id.'" name="'.$input_name.'" id="text">';
  					if($value == "registered"){
						$form .= "<option>registered</option><option>paid</option><option>called</option><option value='0'>choose option</option>";
					}
					elseif($value == "paid"){
						$form .= "<option>paid</option><option>registered</option><option>called</option><option value='0'>choose option</option>";
					}
					elseif($value == "called"){
						$form .= "<option>called</option><option>paid</option><option>registered</option><option value='0'>choose option</option>";
					}					
					else{
						$form .= "<option value='0'>choose option</option><option>registered</option><option>paid</option><option>called</option>";
					}
  					$form .= '</select>
					</div>';					
				}
				elseif($input_name == "type16"){ /* DEBTS TYPE */
					$form = '<div class="form-group '.$v2.'" >
  					<label for="text">'.$title.$required.'</label>
  					<select class="form-control '.$input_name.' dropdown" data-name="'.$name.'" data-company-group-id="'.$company_group_id.'" data-group-id="'.$group_id.'" name="'.$input_name.'" id="text">';
  					if($value == "Debt collection"){
						$form .= "<option>Debt collection</option><option>Tax arrears</option><option>Local databases</option><option value='0'>choose option</option>";
					}
					elseif($value == "Tax arrears"){
						$form .= "<option>Tax arrears</option><option>Debt collection</option><option>Local databases</option><option value='0'>choose option</option>";
					}
					elseif($value == "Local databases"){
						$form .= "<option>Local databases</option><option>Debt collection</option><option>Tax arrears</option><option value='0'>choose option</option>";
					}					
					else{
						$form .= "<option value='0'>choose option</option><option>Debt collection</option><option>Tax arrears</option><option>Local databases</option>";
					}
  					$form .= '</select>
					</div>';					
				}
				elseif($input_name == "payment_status16"){ //DEBTS PAYMENT STATUS
					$form = '<div class="form-group '.$v2.'" >
  					<label for="text">'.$title.$required.'</label>
  					<select class="form-control '.$input_name.' dropdown" data-name="'.$name.'" data-company-group-id="'.$company_group_id.'" data-group-id="'.$group_id.'" name="'.$input_name.'" id="text">';
  					if($value == "none"){
						$form .= "<option>none</option><option>partial</option><option>complete</option><option>dismissed</option><option value='0'>choose option</option>";
					}
					elseif($value == "partial"){
						$form .= "<option>partial</option><option>none</option><option>complete</option><option>dismissed</option><option value='0'>choose option</option>";
					}
					elseif($value == "complete"){
						$form .= "<option>complete</option><option>partial</option><option>none</option><option>dismissed</option><option value='0'>choose option</option>";
					}	
					elseif($value == "dismissed"){
						$form .= "<option>dismissed</option><option>partial</option><option>complete</option><option>none</option><option value='0'>choose option</option>";
					}						
					else{
						$form .= "<option value='0'>choose option</option><option>none</option><option>partial</option><option>complete</option><option>dismissed</option>";
					}
  					$form .= '</select>
					</div>';					
				}				
				elseif($input_name == "type43" or $input_name == "type15" or $input_name == "type36"){ //SHAREHOLDER OR LITIGATION TYPE
					$form = '<div class="form-group '.$v2.'" >
  					<label for="text">'.$title.$required.'</label>
  					<select class="form-control '.$input_name.' dropdown" data-name="'.$name.'" data-company-group-id="'.$company_group_id.'" data-group-id="'.$group_id.'" name="'.$input_name.'" id="text">';
  					if($value == "Company"){
						$form .= "<option>Company</option><option>Person</option><option value='0'>choose option</option>";
					}
					elseif($value == "Person"){
						$form .= "<option>Person</option><option>Company</option><option value='0'>choose option</option>";
					}
					else{
						$form .= "<option value='0'>choose option</option><option>Company</option><option>Person</option>";
					}
  					$form .= '</select>
					</div>';	
				}
				else{
				$form = 
				'<div class="form-group '.$v2.'" >
  					<label for="text">'.$title.$required.'</label>
  					<input type="text" class="form-control '.$input_name.' '.$type.'" data-name="'.$name.'" data-company-group-id="'.$company_group_id.'" data-group-id="'.$group_id.'" name="'.$input_name.'" value="'.$value.'" id="text" />
				</div>';						
				}
				break;		
			case 'textarea':
				$form = 
				'<div class="form-group '.$v2.'">
  					<label for="textarea">'.$title.'</label>
  					<textarea class="form-control '.$input_name. " " . $name . '" rows="5" data-name="'.$name.'" data-company-group-id="'.$company_group_id.'" data-group-id="'.$group_id.'" name="'.$input_name.'"  class="'.$input_name.'" id="textarea">'.$value.'</textarea>
				</div>';
				break;
			case 'upload':
				$json_value = json_decode($value,true);
				if($json_value){
					$textarea_value = $this->json_to_text($json_value);
				}
				else{
					$textarea_value = $value;
				}
				$form = 
				'<div class="form-group '.$v2.'">
					<form action="" class="upload-form" method="post" data-group-id="'.$group_id.'" >
  					<label for="textarea">'.$title.'</label>
  					<textarea disabled class="form-control '.$input_name.' text_document" rows="15" data-name="'.$name.'" data-company-group-id="'.$company_group_id.'" data-group-id="'.$group_id.'" name="'.$input_name.'"  class="'.$input_name.'" id="textarea">'.$textarea_value.'</textarea>
					
  					<textarea class="form-control'.$input_name.' hidden" rows="15" data-name="'.$name.'" data-company-group-id="'.$company_group_id.'" data-group-id="'.$group_id.'" name="'.$input_name.'"  class="'.$input_name.'" id="textarea">'.$value.'</textarea>					
					
					<input type="file" name="fileToUpload" id="fileToUpload" accept=".xls,.xlsx">
					</form>
				</div>';
				break;			
			case 'radio':
				$checked_yes = "";
				$checked_no = "";
				if($value == "1") $checked_yes = "checked='checked'";
				elseif($value == "0") $checked_no = "checked='checked'";
				$radio_value_true = "Yes";
				$radio_value_false = "No";
				$checker = "sss";
				if(trim($name) == "gender"){
					$radio_value_true = "Male";
					$radio_value_false = "Female";
				}
				elseif(trim($name) == "is_cancelled" and ($value === NULL or $value == "")){
					$checked_no = "checked='checked'";
				}
				else{
					$checker = "xxx";
					$radio_value_true = "Yes";
					$radio_value_false = "No";				
				}
				$form = 
				'<div class="'.$checker.' form-group '.$v2.'"> <label>'.$title.'</label>
				<div class="radio" data-checked="'.$v2.'">
  					<label class="radio-inline">
  					<input type="radio" data-company-group-id="'.$company_group_id.'" '.$checked_yes.' data-name="'.$name.'" data-group-id="'.$group_id.'" name="'.$input_name.'" value="1" id="radio" />'.$radio_value_true.
  					'</label>
  					<label class="radio-inline">
  					<input type="radio" data-company-group-id="'.$company_group_id.'" '.$checked_no .' data-name="'.$name.'" data-group-id="'.$group_id.'" name="'.$input_name.'" value="0" id="radio" />'.$radio_value_false.
  					'</label>
				</div></div>';
				break;
			case 'date':
			case 'datetime':
				$parent_class = "";
				if(trim($name) == "cancelled"){
					$parent_class = "parent-is_cancelled".$group_id;
				}	
				$form = 
		   '<div class="form-group '.$v2.'">
                <label>'.$title.'</label>		    
                <div class="input-group">
                    <input type="text" data-company-group-id="'.$company_group_id.'" value="'.$value.'" data-name="'.$name.'" data-group-id="'.$group_id.'" class="form-control datepicker '.$parent_class.'  '.$input_name.'"  data-format="YYYY-MM-DD" data-template="D MMM YYYY" name="'.$input_name.'" />
                </div>
                
            </div>';	 
			break;
		}
		
		return $form;
	}
	
	function json_to_text($json){
		$text = "";
		foreach($json as $row){
			$text .= $row['tit'] . ": " . $row['val'] . "\r\n";
		}
		return $text;
	}
}
?>