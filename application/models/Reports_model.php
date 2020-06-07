<?php
Class Reports_model extends CI_Model
{
	function get_all_reports(){
		$query = $this->db->query(
		"select * from companies 
		inner join companygroups 
		on companies.company_id = companygroups.company_id 
		inner join countries
		on countries.id = companies.country_id
		group by companies.company_id,companygroups.report_year order by countries.id,companies.name,companygroups.report_year DESC"); 
		return $query->result();
	}
	
	function get_country_reports($country_id){
		$query = $this->db->query(
		"select * from companies 
		inner join companygroups 
		on companies.company_id = companygroups.company_id 
		inner join countries
		on countries.id = companies.country_id
		where companies.country_id = ?
		group by companies.company_id,companygroups.report_year order by countries.id,companies.name",array($country_id)); 
		return $query->result();
	}
	
	function get_search_reports($search,$country_id=""){
		$data = array('%'.$search.'%');
		$where = "";
		if($country_id != "" and $country_id != "-1"){ 
			$where = "and countries.id = ?";
			$data = array('%'.$search.'%',$country_id);
		}
 
		$query = $this->db->query(
		"select * from companies 
		inner join companygroups 
		on companies.company_id = companygroups.company_id 
		inner join countries
		on countries.id = companies.country_id
		where companies.name LIKE ? {$where}
		group by companies.company_id,companygroups.report_year order by countries.id,companies.name",$data); 
		return $query->result();		
	}
	
	function get_reports_for_export($company_id,$report_year="")
	{
		$query = $this->db->query(
		"SELECT a.company_id,a.report_year,a.group_id, a.company_group_id, b.type, b.title, b.header, b.na FROM `companygroups` a
		INNER JOIN `groups` b on a.group_id = b.group_id 
		WHERE a.company_id = ? and a.report_year = ? group by b.group_id order by b.custom_order",array($company_id,$report_year)); 
		return $query->result();			
	}
	
	function get_all_currencies(){
		$query = $this->db->get('currency');
		return $query;
	}
	
	function get_all_ratings(){
		$query = $this->db->get('credit_ratings');
		return $query;
	}
	
	function get_rating($rating){
		$query = $this->db->select()->where('rating_class',$rating)->get('credit_ratings');
		return $query;
	}
	
	function get_activity_codes($type){
		$query = $this->db->select()->where('type',$type)->get('activity_codes');
		return $query;
	}
	
	function get_activity_description($type,$code){
		$query = $this->db->select("description")->where('type',$type)->where('code',$code)->get('activity_codes');
		return $query->row();		
	}
	
	function get_groups_for_export($company_id,$group_id,$report_year){
		$query = $this->db->query(
		"SELECT * FROM `companygroups`
		WHERE companygroups.company_id = ? and 
		companygroups.group_id = ? and
		companygroups.report_year = ?",
		array($company_id,$group_id,$report_year)); 
		return $query;			
	}
	
	function get_items_for_export($company_group_id,$table){
		$query = $this->db->select()->where('company_group_id',$company_group_id)->get($table);
		return $query;
	}
	
	function get_item_column($item_name,$company_group_id,$table){
		$query = $this->db->select("{$item_name}")
		->where("company_group_id",$company_group_id)
		->get($table);
		return $query->row();
	}		
	
	function get_all_groups(){
		$query = $this->db->query("select * from groups order by custom_order asc");
		return $query->result();
	}
	
	function get_default_itemgroups(){
		$query = $this->db->query(
		"SELECT * FROM `groupitemdefaults` 
		INNER JOIN `itemdefaults`
		on groupitemdefaults.item_id = itemdefaults.id
		ORDER BY groupitemdefaults.order ASC");
		return $query->result();
	}
	
	function get_companygroup($company_id,$group_id,$report_year){
		$query = $this->db->select()
		->where('company_id',$company_id)
		->where('group_id',$group_id)
		->where('report_year',$report_year)
		->get('companygroups');
		return $query;
	}
	
		
	
	function get_report_companygroups($company_id,$report_year){
		$query = $this->db->select()
		->where('company_id',$company_id)
		->where('report_year',$report_year)
		->get('companygroups');
		return $query;
	}
	
	
	function get_all_companygroups($company_id){
		$query = $this->db->select()
		->where('company_id',$company_id)
		->get('companygroups');
		return $query;
	}	
	
	function get_items_for_group($group_id){
		$query = $this->db->query(
		"SELECT * FROM itemdefaults 
		INNER JOIN groupitemdefaults 
		ON itemdefaults.id = groupitemdefaults.item_id 
		WHERE groupitemdefaults.group_id = ? ORDER BY groupitemdefaults.order,groupitemdefaults.item_id ASC
		",array($group_id));
		return $query;	
	}
	
	function insert_in_table($table,$array){
		$this->db->insert($table,$array);
		return $this->db->insert_id();
	}
	
	function update_table($table,$column,$id,$array){
		$this->db->where($column,$id)
		->update($table,$array);
	}	
	
	function update_table_2where($table,$column1,$column2,$id1,$id2,$array){
		$this->db->where($column1,$id1)
		->where($column2,$id2)
		->update($table,$array);
	}		
	
	function delete_row($table,$column,$id){
		$this->db->where($column,$id)->delete($table);
	}
	
	function is_companygroup_ready($company_id,$group_id,$report_year){
		$companygroup = $this->get_companygroup($company_id, $group_id, $report_year);
		if($companygroup->num_rows() > 0){
			return true;
		}
		else{
			return false;
		}
	} 
	
	function is_company_name_null($company_id){
		$query = $this->db->select("name")
		->where('company_id',$company_id)
		->where('name IS NOT NULL')
		->get('companies');
		if($query->num_rows() > 0){
			return false;
		}
		else{
			return true;
		}
	}
	
	function is_company_name_already_exists($name){
		$query = $this->db->select("name")
		->where('name',$name)
		->get('companies');
		if($query->num_rows() > 0){
			return true;
		}
		else{
			return false;
		}		
	}
	
	function is_group_in_company($group_id,$company_id,$report_year){
		$query = $this->db->select()
		->where('company_id',$company_id)
		->where('group_id',$group_id)
		->where('report_year',$report_year)
		->get('companygroups');
		if($query->num_rows() > 0){
			return true;
		}
		else{
			return false;
		}
	}

	function is_company_group_in_table($company_group_id,$table){
		$query = $this->db->select()
		->where('company_group_id',$company_group_id)
		->get($table);
		if($query->num_rows() > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function is_column_exists($column_name,$table){
		if ($this->db->field_exists($column_name, $table))
		{
 			return true;
		}		
		else{
			return false;
		} 
	}
	
	function get_specific_group($group_id){
		$query = $this->db->select()
		->where("group_id",$group_id)
		->get("groups",1);
		return $query->row();
	}
	
	function get_specific_companygroup($company_group_id){
		$query = $this->db->select()
		->where("company_group_id",$company_group_id)
		->get("companygroups");
		return $query->row();
	}
	
	function get_country_name($country_code){
		$query = $this->db->select('country_name')->where('country_code',$country_code)->get('countries');
		return $query->row();
	}
	
	function is_company_has_report($company_id,$report_year){
		$query = $this->db->select()
		->where('company_id',$company_id)
		->where('report_year',$report_year)
		->get('companygroups');
		
		if($query->num_rows() > 0){
			return true;
		}
		else{
			return false;
		}
	}
}
?>