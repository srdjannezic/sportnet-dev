<?php 
if($mode==""){
	include_once("includes/header.php"); 
?>
 <form class="export-form" action="/reports/exportReport" method="POST"> 
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
   						<?php
						echo "<div class='page-title'>
						<h2>All Reports</h2>
						</div>";							
}
   						//SHOW REPORTS
						$reports_counter = 1;
						$country_counter = 0;


						$company_id = null;
						$country_id = null;
						
						if($mode==""){
						
						?>
						<div class="sort-reports">
						<div class="row">
						<select class="country-dropdown">
						<option value="-1">Select country</option>
						<?php
							foreach($countries as $country){
								echo "<option value='{$country->id}'>",$country->country_name,"</option>";
							}
						?>
						</select>
						<div class="btn btn-success btn-md export-all" data-toggle="modal" data-target="#exportModal" name="export"><span class="glyphicon glyphicon-export"></span>&nbsp;Export All</div>
						</div>	
						<div class="row search-box">
						<input type="text" class="search-report" placeholder="Search by company name">
						<button class="btn btn-success btn-md"><span class="glyphicon glyphicon-search"></span></button>
						</div>
						</div>
						<?php
						}
						?>
						<div class="ajax-container"><div class="panel-group row" id="accordion">
						<?php
						foreach($reports as $report){
						//echo $company_id . "<br/>";		
						//if($reports_counter == 0){ $country_id = $report->country_id; }
						//var_dump($report);
						if($report->company_id != $company_id){ ?>							
							<?php if($reports_counter > 1){ ?>
							</div>		
							</div>
							
							<?php 
							}
							if($reports_counter > 0){
							//echo $report->country_id . " " . $country_id . "<br/>";	 
							if($report->country_id != $country_id){
								
								echo "<h3>{$report->country_name}</h3>";
								$country_id = $report->country_id;

								$country_counter ++;
							}								
							} else echo "<h3>{$report->country_name}</h3>"; ?>
							<div class="panel panel-default">
								
							<div class="panel-heading">
							<h4 class="panel-title">
							<div class="checkbox">
								<label><input type="checkbox" name="company-checkbox[]" class="company-checkbox" value="<?= $report -> company_id ?>" data-report-year="<?= $report -> report_year ?>"><a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$reports_counter; ?>">
							<?= $report -> name; ?></a></label>
							</div>
							</h4>
							</div>
							
							<div id="collapse<?=$reports_counter; ?>" class="panel-collapse collapse">
							<?php
								$company_id = $report -> company_id;
						}
 						?>
							<div class="panel-body">
								<div class="left">
								<input type="checkbox" name="report-checkbox[]" class="report-checkbox" 
								value='{"company_id":"<?= $report->company_id ?>","country_name":"<?= $report->country_name ?>","report_year":"<?= $report->report_year ?>","company_name":"<?= $report->name ?>"}'> <?= $report -> report_year; ?>
								</div>
								<div class="right">
									<span class="glyphicon glyphicon-export"><a href="" class="export-selected export-single" data-toggle="modal" data-target="#exportModal">Export</a></span>
									<span class="glyphicon glyphicon-pencil"><a href="/reports/add_report/edit/<?= $report->company_id ?>/<?= $report->report_year ?>">Edit</a></span>
									<span class="glyphicon glyphicon-remove"><a href="" class="delete-report" data-company-id="<?= $report->company_id ?>" data-report-year="<?= $report->report_year ?>">Delete</a></span>
								</div>
							</div>

							<?php
							$reports_counter++;
						}
						echo "</div></div>";
if($mode==""){
                    	?>
                    </div>
                </div>
                <div class="col-lg-8">
				<div class="row">
				<div class="btn btn-success btn-md export-selected" data-toggle="modal" data-target="#exportModal"><span class="glyphicon glyphicon-export"></span>&nbsp;Export Selected</div>
                <button class="btn btn-danger btn-md delete-selected" name="delete-selected"><span class="glyphicon glyphicon-remove"></span>&nbsp;Delete Selected</button>
				</div>
				</div>
<?php 
	include_once("includes/footer.php"); 
	include_once("includes/modals.php");
}	
?>