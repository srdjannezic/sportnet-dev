<?php include_once("includes/header.php"); ?>

                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
   						<?php
   						//SHOW REPORTS
						$company_counter = 1;
						echo "<form action='/companies/delete_company' method='post'>";
						echo "<div class='page-title'>
						<h2>All Companies</h2>
						</div>";
						echo '<div class="panel-group" id="accordion">';
						$company_id = null;
						foreach($companies as $company){
						if($company->company_id != $company_id){
							?>
							<?php if($company_counter > 1){ ?>
							</div>		
							</div>
							<?php } ?>
							<div class="panel panel-default">
								
							<div class="panel-heading">
							<h4 class="panel-title">
							<div class="checkbox">
								<label><input type="checkbox" name="company_id" value="<?= $company -> company_id ?>"><a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$company_counter; ?>">
							<?= $company -> name; ?></a></label>
							</div>
							</h4>
							</div>
							
							<div id="collapse<?=$company_counter; ?>" class="panel-collapse collapse">
							<?php
								$company_id = $company -> company_id;
						}
 						?>
							<div class="panel-body"></div>

							<?php
							$company_counter++;
						}
						echo "</div></div>";
						echo '<button class="btn btn-danger btn-md delete-selected" style="margin-top: 20px;" name="delete-selected"><span class="glyphicon glyphicon-remove"></span>&nbsp;Delete Selected</button>';
						echo "</form>";
							
                    	?>
                    </div>
                </div>
                             
<?php include_once("includes/footer.php"); ?>