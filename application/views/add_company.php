<?php include_once("includes/header.php"); ?>
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
					<?php
						echo "<div class='page-title'>
						<h2>Add new Company</h2>
						</div>";
					?>
   						<form class="add_company_form" action="" >
   							<div class="form-group">
   								<label for="country_name">Country Name: </label><br/>
   								<select class="country_name">
   									<?php
   										foreach ($countries as $country) {
											echo "<option value='".$country->id."'>",$country->country_name,"</option>";	   
										}
   									?>
   								</select>
   							</div>
   							<div class="form-group">
   								<label for="company_name">Company Name: </label><br/>
   								<input type="text" name="company_name" class="company_name" />
   							</div>
   							<input type="submit" name="add_company" class="btn btn-success btn-md add-company-btn" value="Save" />
   						</form>
   						<p class="message"></p>
                 		<div class="add-report-btn-container">
                 			
                 		</div>               													
                    </div>
                </div>

<?php include_once("includes/footer.php"); ?>