        <div class="right-account">  	
        	<span><?php echo $this->session->logged_in["user_name"]  ?></span> 
    		<a href="/logout">| Logout</a>    	
        </div>
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
				<li class="sidebar-brand">
                    <a href="#">
                        SportNet CMS System
                    
                    </a>
                </li>	
                <li>
                    <a data-toggle="collapse" data-target="#companies">Companies</a>
                    <ul class="nav nav-list collapse in" id="companies">
                    <li><a href="/companies/add_company">Add New</a></li>
                    <li><a href="/companies">All Companies</a></li>                                        
               	 	</ul>      
                </li>                   
                			
                <li>
                    <a data-toggle="collapse" data-target="#reports">Reports</a>
                    <ul class="nav nav-list collapse in" id="reports">
                    <li><a href="/reports/add_report">Add New</a></li>                       	
                    <li><a href="/reports">All Reports</a></li>                 
               	 	</ul>      
                </li>        
                
                <li>
                    <a data-toggle="collapse" data-target="#users">Users</a>
                    <ul class="nav nav-list collapse in" id="users">
                    <li><a href="/users/add_user">Add New</a></li>                       	
                    <li><a href="/users">All Users</a></li>                 
               	 	</ul>     
                </li>
				
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->