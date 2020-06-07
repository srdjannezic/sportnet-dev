/* author: Wollson, Srdjan Nezic*/

function save_report(group_id, company_id, company_group_id, report_year, input_name, value, name, selector, mode) {
	console.log(mode);
	$.ajax({
		type : "POST",
		url : "/index.php/reports/save_report/"+mode,
		data : {
			group_id : group_id,
			company_id : company_id,
			company_group_id : company_group_id,
			report_year : report_year,
			input_name : input_name,
			value : value,
			name : name
		},
		dataType : "json",
		success : function(data) {
			console.log(data);
			if(data.company_id != "null" && data.company_id != null && data.company_id != ""){
				$('.company_id').val(data.company_id);
			}
			if(data.type == "duplicate-report"){
				alert("Same report year for this company already exists, please choose some other year and try again!");
			}
			if (data.company_group_id !== "null" || data.error == "false") {
				console.log(selector);
				selector.val(data.company_group_id);
				if (input_name == "name33") {
					$(".add_report_form").find("input").prop("disabled", false);
					$(".add_report_form").find("textarea").prop("disabled", false);
					$(".add_report_form").find("input").attr("title", "");
					$(".add_report_form").find("textarea").attr("title", "");
				}
			}
			
			/*
			 if(data.error == true){
			 console.log(data.message);
			 }*/
		},
		error : function(xhr, status, error) {
			console.log(error);
		}
	});
}

    function getstatus(){
      $.ajax({
          url: "/index.php/reports/getstatus",
          type: "POST",
          dataType: 'json',
          success: function(data) {
			 console.log(data);
            //$('#statusmessage').html(data.message);
            if(data.status=="pending")
              setTimeout('getstatus()', 1000);
            else {
              $("#statusXmlModal").modal("hide");
			  $("#statusPdfModal").modal("hide");
			}
          },
		error : function(xhr, status, error) {
			console.log(error);
		}		  
      });
    }	
	
	function load_activity_codes(type,selector){
      $.ajax({
          url: "/index.php/reports/load_activity_codes/"+type,
          type: "POST",
          dataType: 'text',
          success: function(data) {
			 console.log(type);
			 console.log(selector);
			 console.log(data);
			 selector.html(data).trigger("input");
          },
		  error : function(xhr, status, error) {
			console.log(error);
		  }		  
      });		
	}
	
	function load_activity_description(type,code,selector){
      $.ajax({
          url: "/index.php/reports/load_activity_description/"+type+"/"+code,
          type: "POST",
          dataType: 'text',
          success: function(data) {
			 console.log(data);
			 selector.html(data).trigger("input");
          },
		  error : function(xhr, status, error) {
			console.log(error);
		  }		  
      });		
	}
	
	function load_rating_description(rating,selector){
      $.ajax({
          url: "/index.php/reports/load_rating_description",
          type: "POST",
		  data: {rating:rating},
          dataType: 'text',
          success: function(data) {
			 console.log(data);
			 selector.html(data).trigger("input");
          },
		  error : function(xhr, status, error) {
			console.log(error);
		  }		  
      });		
	}

function delete_report(company_id, report_year) {
	$.ajax({
		type : "POST",
		data : {
			company_id : company_id,
			report_year : report_year
		},
		url : "/index.php/reports/delete_report",
		dataType : "json",
		success : function(data) {
			console.log(data);
		},
		error : function(xhr, status, error) {
			console.log(error);
		}
	}); 
}


$(document).ready(function() {
	console.log('test');
	var currentYear = (new Date).getFullYear();
	//POPULATE YEARS
	for ( i = new Date().getFullYear(); i > 1900; i--) {
		$('.report_year_dropdown').append($('<option />').val(i).html(i));
	}
	var report_year = $(".report_year_dropdown").val();
	if (report_year == "-1") {
		$(".add_report_form").find("input").prop("disabled", true);
		$(".add_report_form").find("textarea").prop("disabled", true);
		$(".add_report_form").find("input").attr("title", "First choose a year!");
		$(".add_report_form").find("textarea").attr("title", "First choose a year!");
	}
	//$(".panel-33").find('input').prop("disabled", false);
	//$(".add_report_form").find("save").prop("disabled", false);
	$(".add-report-btn").hide();
	var checked = $(".radio").find("input[name='v222']:checked").val();
	if(checked == 1){
		$(".false").hide();
		$(".true").show();
	}
	if(checked == 0){
		$(".true").hide();
		$(".false").show();		
	}
	
	

	$(".report_year_dropdown").change(function() {
		year = $(this).val();
		var company_name = $(".name33").val();
		var company_group_id = $(".company_group_id").val();
		if (year !== "-1") {
			$.ajax({
				type : "POST",
				url : "/index.php/reports/update_report_year",
				data : {
					report_year : year,
					company_group_id : company_group_id
				},
				dataType : "text",
				success : function(data) {
					console.log(data);
					$(".add_report_form").find(".name33").prop("disabled", false);
					$(".add_report_form").find(".name33").attr("title", "");
				},
				error : function(xhr, status, error) {
					console.log(error);
				}
			});
		} else {
			$(".add_report_form").find("input").prop("disabled", true);
			$(".add_report_form").find("input").attr("title", "First choose a year!");
		}
	});

	//SAVE CLICK
	$(".add_company_form").submit(function(event) {
		company_name = $(".company_name").val();
		country_id = $(".country_name").val();
		console.log(country_id);
		$.ajax({
			type : "POST",
			url : "/index.php/companies/add_company_to_db",
			data : {
				company_name : company_name,
				country_id : country_id
			},
			dataType : "json",
			success : function(data) {
				console.log(data);
				if(!data.error){
					$(".message").html("Successfully added company: " + company_name);
					$(".add-report-btn-container").html('<a href="/reports/add_report"><button class="btn btn-success btn-md add-report-btn">Add Report</button></a>');
				}
				else{
					alert(data.message);
				}
			},
			error : function(xhr, status, error) {
				console.log(error);
			}
		});
		event.preventDefault();
	});


	$(".save-report").click(function(event) {
		var inputs = $(this).serializeArray();
		console.log("successfully report saved!");
		$(".message").html("Successfully saved report!");
		event.preventDefault();
	});
	
	$(".type1").each(function(){ // foreach activity type disable childs
		
		var type1 = $(this).val();
		console.log(type1);
		var code1 = $(this).closest('.panel-group').find('.code1'); 
		var description1 = $(this).closest('.panel-group').find(".description1");
		console.log("TYPE START");
		console.log("TYPE: "+type1);
		console.log("CODE: " + code1);
		if(type1 == "0"){
			code1.prop("disabled", true);
			description1.prop("disabled", true);
		}
		else{
			code1.prop("disabled", false);
			description1.prop("disabled", true);
			datalist = $(this).closest('.panel-group').find('datalist'); 
			console.log(datalist);
			load_activity_codes(type1,datalist); 
			//description1.prop("disabled", false);			
		}
		console.log("TYPE END");
	});
	
	$(document).on("change",".type1",function(){
		console.log(this);
		var type1 = $(this).val();
		console.log(type1);
		var code1 = $(this).closest('.panel-body').find('.code1'); 
		var description1 = $(this).closest('.panel-body').find(".description1");
		code1.val("").trigger("input");
		description1.html("").trigger("input");
		
		console.log(code1);
		if(type1 == "0"){
			code1.prop("disabled", true);
			description1.prop("disabled", true);
		}		
		else{
			code1.prop("disabled", false);
			datalist = $(this).closest('.panel-body').find('datalist'); 
			console.log(datalist);
			load_activity_codes(type1,datalist);
			//description1.prop("disabled", false);			
		}		
	});
	
	$(document).on("input",".code1",function(){
		var code1 = $(this).val();
		var type1 = $(this).closest('.panel-group').find('.type1').val(); 
		var description1 = $(this).closest('.panel-group').find(".description1");
		
		console.log("code: "+code1);
		console.log("type: "+type1);
		console.log(description1);
		load_activity_description(type1,code1,description1);		
	});	
	
	$(document).on("change",".rating",function(){
		var rating = $(this).val();
		var selector = $(this).closest('.panel-group').find(".description");		
		load_rating_description(rating,selector);
	});

	var input_updates = function(event) {
		//if($(this).attr("name") !== "name33"){ //company name is reserved for live search
		var attr = $(this).attr('list');
		var hasList = false;
		
		if (typeof attr !== typeof undefined && attr !== false) {
			hasList = true;
		}
		
		group_id = $(this).data('group-id');
		input_name = $(this).attr("name");
		var name = $(this).data('name');
		var real_value =  $(this).val();
		/* INPUT EXCEPTIONS */
		if(name == "origin" || name == "currency" || name == "value_currency" || (name == "country" && (group_id == 2 || group_id == 3 || group_id == 4))){
			temp_value = $(this).val();
			temp_value = temp_value.split(" - ")[1];
			console.log('temp_value ' + temp_value); 
			if(temp_value != null || typeof(temp_value)!='undefined'){
				$(this).val(temp_value);
			}
		} 
		value = $(this).val();
		
		company_name = $(".name33").val();
		new_company_name = company_name.split("- (")[0].trim();
		$(".name33").val(new_company_name);
		
		
		if (document.querySelector("#browsers option[value='" + company_name + "']")) {
			company_id = document.querySelector("#browsers option[value='" + company_name + "']").dataset.id;
		} else {
			company_id = $(".company_id").val();
		}
		
		if(input_name == "name33"){$(".company_id").val(company_id);}
		
		console.log('company id '+company_id);

		selector = $(this).closest('.panel-group').find(".company_group_id");
		console.log(selector);
		company_group_id = selector.val();
		report_year = $(".report_year_dropdown").val();
		
		if(input_name == "name33") { value = new_company_name; }
		
		console.log(group_id);
		console.log(company_group_id);
		console.log(value);
		console.log(report_year);
		console.log(name);
		mode = $(".mode").val(); 
		if(name == "v2" && value == 0){
			$(".false").show();
			$(".true").hide();
		}
		
		if(name == "v2" && value == 1){
			$(".true").show();
			$(".show").hide();
		}		
		
		if(hasList){ //save for option change
			var datalist = $(this).closest('div').find('datalist');
			console.log(datalist);
			
			if(datalist.find('option').filter(function(){
				var this_value = this.value;
				if(input_name == "name33"){
					this_value = this_value.split("- (")[0].trim();
				}
				if(name == "origin" || name == "currency" || name == "value_currency" || (name == "country" && (group_id == 2 || group_id == 3 || group_id == 4))){
					this_value = this_value.split(" - ")[1]; 
				}
				return this_value === value; //ako je vrednost u inputu jednaka izabranoj      
			}).length) { //than
				//value is same
				save_report(group_id, company_id, company_group_id, report_year, input_name, value, name, selector,mode);
			}
		}
		else{ //save for each input key
			if($(this).hasClass('number') || $(this).hasClass('decimal')){ //is number
				if($.isNumeric(value)){
					save_report(group_id, company_id, company_group_id, report_year, input_name, value, name, selector,mode);
				}
				else{
					alert("input must be a number!");
				}
			}
			else{
				save_report(group_id, company_id, company_group_id, report_year, input_name, value, name, selector,mode);
			}
		}//}
		
	};

	//LIVE INPUT UPDATE
	$("body").find(".add_report_form").on("input", "input, textarea", input_updates);
	$("body").find(".add_report_form").on("change", "input[type=radio]", input_updates);
	$("body").find(".add_report_form").on("change", "select", input_updates);
	
	$('.datepicker').combodate({maxYear: currentYear,minYear: 1900});
	
	$('body').on("change",'.datepicker', function() {
		console.log($(this).children());
		day = $(this).closest('.input-group').find(".day").val();
		month = parseInt($(this).closest('.input-group').find(".month").val())+1;
		year = $(this).closest('.input-group').find(".year").val();
		value = year+"-"+month+"-"+day;
		group_id = $(this).data('group-id');
		company_name = $(".name33").val();

		company_id = $(".company_id").val();
		
		selector = $(this).closest('.panel-group').find(".company_group_id");
		console.log(selector);
		company_group_id = selector.val();
		report_year = $(".report_year_dropdown").val();
		var name = $(this).data('name');
		input_name = $(this).attr("name");
		console.log(group_id);
		console.log(company_group_id);
		console.log(value);
		console.log(report_year);
		console.log(name);
		mode = $(".mode").val();
		save_report(group_id, company_id, company_group_id, report_year, input_name, value, name, selector,mode);
	});
	
	
	/*UPLOAD DOCUMENT*/
	$('body').on("change","#fileToUpload",function(event){
		//console.log($(this).files);
		$(".src_document23").html("");
		$(".upload-form").submit(); //submit form on change

	});
	
	$('body').on("submit",".upload-form",function(event){	
		event.preventDefault();
		//inputFile = $("#fileToUpload")[0];
		//console.log(inputFile);
		if( window.FormData !== undefined ){
		var group_id = $(this).data('group-id');
		console.log(this);
		var formData = new FormData(this);
		report_year = $(".report_year_dropdown").val();
		company_id = $(".company_id").val();
		
		console.log(formData);
		var selector = $(this).find(".text_document");
		var json_selector = $(this).find(".hidden");
		
		formData.append('report_year',report_year);
		formData.append('group_id',group_id);
		formData.append('company_id',company_id);
		//formData.append(inputFile.name, inputFile.files[0]);
		//onsole.log(formData);
		
		$.ajax({
			type : "POST",
			url : "/index.php/reports/upload_file",
			data: formData,
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			dataType : "json",
			success : function(data) { 
				console.log(data.document);
				var text_document = "";
				if(data.is_excel){
					var json_document = JSON.stringify(data.document);
					$.each(data.document,function(index,row){
						row_value = row['val'];
						(row['val'] == "null" || row['val'] == null) ? row_value = "" : row_value = row['val'];
						text_document += row['tit'] + ": " + row_value + "\r\n";
					});
				}
				else{
					var text_document = data.document.toString();
				}
				if(data.document != "error"){
					selector.html(text_document);
					json_selector.html(json_document).trigger("input"); //trigger new event 
				}
			},
			error : function(xhr, status, error) {
				console.log(error);
			},
		});		
		}
		else{
			alert('not supported by your browser!');
		}
				
	});

	//EXPAND or COLLAPSE ALL
	var counter = 0;
	$(".expand-all").click(function() {
		console.log(counter);
		if (counter % 2 == 0) {
			$('#accordion .panel-collapse').collapse('show');
		} else {
			$('#accordion .panel-collapse').collapse('hide');
		}
		counter++;
	});

	//CANCEL
	$(".cancel").click(function() {
		company_id = "null";
		var company_name = $(".name33").val();

		company_id = $('.company_id').val();
		
		report_year = $(".report_year_dropdown").val();
		$('.panel').find('input').val("");
		$('.save').val("Save");
		delete_report(company_id,report_year);
	});

	//RESET GROUP
	$(".reset").click(function() {
		var group_id = $(this).data('group-id');
		var company_group_id = $(this).closest(".panel-body").find(".company_group_id").val(); 
		console.log(company_group_id);
		$('.panel-'+group_id).find('input[type=text],input[type=radio],textarea,select,.text').each(function(){
			console.log(this);
			if(!$(this).hasClass('name33')){ //don't reset identification name.
				$(this).val("").trigger('input');
    			if ($(this).is(':checked')) {
          			$(this).prop('checked', false);
 
        		}
			} 
		}); 
		
		$.ajax({
			type : "POST",
			url : "/index.php/reports/delete_report",
			data : {
				company_group_id : company_group_id
			},
			dataType : "text",
			success : function(data) {
				console.log(data);
			},
			error : function(xhr, status, error) {
				console.log(error);
			}
		});		
	});

	//DELETE REPORT
	$("body").on("click", ".delete-report", function(event) {
		var company_id = $(this).data("company-id");
		var report_year = $(this).data("report-year");

		delete_report(company_id,report_year);
		
		$(this).closest(".panel-collapse").hide();
		event.preventDefault(); 
	});

	//ADD EXISTING GROUP
	$('body').on("click", ".add-group", function() {
		group_id = $(this).data('group');
		group_counter = $(".group-" + group_id+":visible").last().find('.add-group').data('order');

		company_name = $(".name33").val();

			company_id = $(".company_id").val();
		
		report_year = $(".report_year_dropdown").val();

		console.log(group_id);
		console.log(group_counter);
		console.log(report_year);

		$.ajax({
			type : "POST",
			url : "/index.php/reports/get_group/" + group_id + "/" + group_counter,
			data : {
				company_id : company_id,
				report_year : report_year
			},
			dataType : "text",
			success : function(data) {
				console.log(data);
				$(".group-" + group_id + "-" + "order-" + group_counter).after(data);
				$('.datepicker').combodate({maxYear: currentYear,minYear: 1900});
			},
			error : function(xhr, status, error) {
				console.log(error);
			}
		});
	});
	/*REMOVE GROUP*/
	$('body').on("click", ".remove-group", function() {
		group_id = $(this).data('group');
		order = $(this).data('order');
		company_group_id = $(this).closest(".panel-group").find(".company_group_id").val();
		console.log(company_group_id);

		$.ajax({
			type : "POST",
			url : "/index.php/reports/delete_report",
			data : {
				company_group_id : company_group_id
			},
			dataType : "text",
			success : function(data) {
				console.log(data);
				$(".group-" + group_id + "-" + "order-" + order).hide();
				//hide group on front
			},
			error : function(xhr, status, error) {
				console.log(error);
			}
		});
	});

	/*COUNTRY DROPDOWN*/
	$("body").on("change", ".country-dropdown", function() {
		country_id = $(this).val();
		console.log(country_id);
		var mode = "";
		if (country_id == -1)
			mode = "all";
		else
			mode = "ajax";
		$.ajax({
			type : "GET",
			url : "/index.php/reports/index/" + country_id + "/" + mode,
			dataType : "text",
			success : function(data) {
				console.log(data);
				//$('.ajax-container').hide();
				$('.ajax-container').html(data);
			},
			error : function(xhr, status, error) {
				console.log(error);
			}
		});
	});

	$("body").on("input", ".search-report", function() {
		search = $(this).val();
		country_id = $(".country-dropdown").val();
		console.log(search);
		mode = "search";
		$.ajax({
			type : "POST",
			data : {
				search : search
			},
			url : "/index.php/reports/index/"+country_id+"/" + mode,
			dataType : "text",
			success : function(data) {
				console.log(data);
				//$('.ajax-container').hide();
				$('.ajax-container').html(data);
			},
			error : function(xhr, status, error) {
				console.log(error);
			}
		});
	});

	$("body").on("click", ".company-checkbox", function() {
		panel = $(this).closest('.panel-default');
		panel.find('input:checkbox').not(this).prop('checked', this.checked);
		//check all reports under checked company
		//$('input:checkbox').not(this)
	});

	$("body").on("click", ".export-all", function() {
		console.log('true');
		$('input:checkbox').prop('checked', true);
	});
	
	/* HIDE RADIO PARENTS */
	
	$('input:radio').each(function () {
		value = $(this).val();
		input_name = $(this).attr("name"); 
		console.log(value);		
		if(value == "0" || value == "" || value == null){
			$(".parent-"+input_name).closest(".input-group").find("select").attr("disabled","disabled");
			$(".parent-"+input_name).closest(".input-group").find("select").css('background-color',"#f0f0f0");
		}
		else{
			$(".parent-"+input_name).closest(".input-group").find("select").removeAttr("disabled");
			$(".parent-"+input_name).closest(".input-group").find("select").css('background-color',"#fff");			
		}	
	});
	$("body").on("change","#radio",function(){
		value = $(this).val();
		input_name = $(this).attr("name"); 
		
		console.log(input_name); 
		if(value == "0"){
			$(".parent-"+input_name).closest(".input-group").find("select").attr("disabled","disabled");
			$(".parent-"+input_name).closest(".input-group").find("select").css('background-color',"#f0f0f0");
		}
		else{
			$(".parent-"+input_name).closest(".input-group").find("select").removeAttr("disabled");
			$(".parent-"+input_name).closest(".input-group").find("select").css('background-color',"#fff");			
		}
	});
	
	$(".export-pdf").click(function(){
		$("#statusPdfModal").modal("show");
		$("#wait").show();
		setTimeout('getstatus()', 1000);
	});

	$(".export-xml").click(function(){
		$("#statusXmlModal").modal("show");
		$("#wait").show();
		setTimeout('getstatus()', 1000);
	});	
	
	/* check for export */
	$(document).on("click",".export-single",function(){
		$(this).closest(".panel-body").find(".report-checkbox").attr("checked","checked");
	});
	
});
