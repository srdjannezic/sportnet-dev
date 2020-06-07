 <!-- Modal -->
  <div class="modal fade" id="exportModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Export selected reports.</h4>
        </div>
        <div class="modal-body">
		<center style="padding:20px;">
          <button class="btn btn-success btn-md export-selected export-xml" name="export-xml" data-toggle="modal" data-target="#exportModal"><span class="glyphicon glyphicon-export"></span>&nbsp;Export as XML</button>
		  <button class="btn btn-success btn-md export-selected export-pdf" name="export-pdf" data-toggle="modal" data-target="#exportModal"><span class="glyphicon glyphicon-export"></span>&nbsp;Export as PDF</button>
        </center>
		</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  </form>
  
   <!-- Modal -->
  <div class="modal fade" id="statusPdfModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">

        <div class="modal-body">
		     <center style="padding:20px;">
          Converting to PDF, please wait... 
          <img src="<?=base_url();?>/assets/loading.gif" width="50" id="wait" />
         </center>
		    </div>
        
      </div>
      
    </div>
  </div>
  
  
     <!-- Modal -->
  <div class="modal fade" id="statusXmlModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">

        <div class="modal-body">
		     <center style="padding:20px;">
          Converting to XML, please wait... 
          <img src="<?=base_url();?>/assets/loading.gif" width="50" id="wait" />
         </center>
		    </div>
        
      </div>
      
    </div>
  </div>