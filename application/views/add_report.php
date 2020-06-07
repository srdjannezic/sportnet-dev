<?php
include_once ("includes/header.php");
 ?>
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
   						<?php

						//SHOW GROUPS
						$CI = &get_instance();
						$CI -> load -> library('FormBuilder');

						//var_dump($default_items);
						$groups_counter = 1;
							?>
							<div class='page-title'>
							<?php if($mode == "edit"){ ?>
							<h2>Edit Report</h2>
							<?php } else { ?>
							<h2>Add Report</h2>
							<?php } ?>
							</div>
							
							
							<div class='collapse-container '>
							
							<button class='btn btn-primary expand-all'>Expand / Collapse All</button></div>
							<div id="report_required">
							<div class="form-group">
   							<label for="report_year_dropdown">Report Year: </label><br/>
							<select class='report_year_dropdown'>
								<?php if($mode == "edit") { ?>
									<option value="<?= $edit_report_year ?>"><?= $edit_report_year ?></option>
								<?php } else{?>
								<option value="-1">Select a year</option>
								<?php } ?>
							</select>
							</div>	
							
   							</div>
							
							<div class="add_report_form">		
							<div class="panel-group" id="accordion">
							<input type="hidden" value="<?=$mode?>" class="mode"/>
							<?php if($mode == "insert") { ?>
							<input type="hidden" value="null" class="company_id"/>
							<?php }
							foreach($groups as $group){
							$expanded = "";
							if($groups_counter == 1) $expanded = "in"; 
							if($group->title != ""){
								?>
							<div class="panel panel-default panel-<?= $group -> group_id ?>">
								
							<div class="panel-heading">
							<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$groups_counter; ?>">
							<?= $group -> title ?></a>
							</h4>
							</div>
							
							<div id="collapse<?=$groups_counter; ?>" class="panel-collapse collapse <?= $expanded ?>">
							<div class="panel-body">
								
<!-- SUBGROUP -->
							<?php

							$company_group_counter = 0;
							//var_dump($mode);
							//var_dump($reports_model->get_companygroup($edit_company_id, $group->group_id, $edit_report_year)->result());
							if($mode == "edit"){
								$get_companygroup_query = $reports_model->get_companygroup($edit_company_id, $group->group_id, $edit_report_year);
								//var_dump($get_companygroup_query->result());
								$get_companygroup = $get_companygroup_query->result();
								if($get_companygroup_query->num_rows() == 0) $get_companygroup = array(1); //hack loop
								
								//var_dump($get_companygroup_query->result());
							}
							else{
								$get_companygroup = array(1); //hack loop
							}
							
							//var_dump($get_companygroup->result());
							$order = 0;
							foreach($get_companygroup as $companygroup){ 
								$company_group_counter ++;

								$group_order = "group-" . $group -> group_id . "-order-{$company_group_counter}";
								if ($groups_counter == 1) {
									$group_order = "";
								}							
							?>
							
							<div class="panel-group group-<?= $group -> group_id ?> <?= $group_order ?>" id="accordion<?=$groups_counter.$company_group_counter?>">
 							<?php if($mode == "edit") { ?>
							<input type="hidden" value="<?= $edit_company_id ?>" class="company_id"/>
							<?php if($get_companygroup_query->num_rows() > 0){ ?>
								<input type="hidden" value="<?= $companygroup->company_group_id ?>" class="company_group_id"/>
							<?php } else { ?>
								<input type="hidden" value="null" class="company_group_id"/>
							<?php } ?>
							<?php } else{ ?>
							<input type="hidden" value="" class="company_id"/>
							<input type="hidden" value="null" class="company_group_id"/>
							<?php } ?>
							<div class="panel panel-default panel-<?= $group -> group_id ?>">
								
							<div class="panel-heading">
							<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion<?=$groups_counter.$company_group_counter?>" href="#collapse<?=$groups_counter.$company_group_counter?>b">
							<?= $group -> title ?> #<?= $company_group_counter ?></a>
							<?php if($groups_counter > 1){ ?>
							<div class="manage-group" style="float:right;">
          						<span class="glyphicon glyphicon-plus add-group" data-group="<?= $group -> group_id ?>" data-order="<?=$company_group_counter?>"></span>
								<?php if($mode == "edit" && $company_group_counter > 1) : ?>
								<span class="glyphicon glyphicon-minus remove-group" data-group="<?= $group -> group_id ?>" data-order="<?=$company_group_counter?>"></span>
								<?php endif; ?>
							</div>
							<?php } ?>
							</h4>
							</div>
							
							<div id="collapse<?=$groups_counter.$company_group_counter?>b" class="panel-collapse collapse in">
							<div class="panel-body">								
								<?php
								
								foreach ($default_items as $items) {
									//var_dump($items->group_id . " " . $group->group_id);
									if ($items -> group_id == $group -> group_id) {
										//echo $items->input_field;
										$item_value = "";
										if($mode == "edit"){
											//var_dump($get_companygroup->row());
											if($get_companygroup_query->num_rows() > 0){
												//$companygroup = $get_companygroup->row();
												$company_group_id = $companygroup->company_group_id;
												if($items->alias == "address") {
													$table = "items";
												}
												if($group->group_id == 2 or $group->group_id == 3 or $group->group_id == 4){
													$table = "addressitems";
												}
												else $table = $reportparser->group_exceptions($group->group_id);
												//var_dump($table . " " . $company_group_id);
												$item = $reports_model->get_item_column($items->name,$company_group_id,$table);
												if(isset($item->{$items->name})){ 
													$item_value = $item->{$items->name};
												}
												 
											}
										}
										//$is_column_exists = $reports_model->is_column_exists($items->name, 'items');
										//var_dump($is_column_exists);
										//var_dump($item->{$items->name});
										echo $CI -> formbuilder -> generate_form($items -> input_field, $items -> name, $items -> title, $item_value, $group -> group_id,$companies,$countries,$currencies,$ratings,"null", $items->v2,$company_group_counter);
									}
								}
								?>
							</div>
							</div>
							</div>
							</div>
							
							<?php } ?>
							<button class="btn btn-danger btn-md reset" data-group-id="<?= $group -> group_id; ?>">Reset</button>
							</div>
							</div>
							
							</div>
							
							<?php
							$groups_counter++;
							}
							}
							?>
							<div class="buttons-right">
							<p class="message"></p> 
							<?php if($mode != "edit") { ?>
							<button class="btn btn-danger btn-md cancel">Cancel</button>
							<?php } ?>
							<input type="submit" class="btn btn-success btn-md save-report" value="Save" />
							</div>
							</div>
							</div>
                    </div>
                </div>
<?php
					include_once ("includes/footer.php");
 ?>