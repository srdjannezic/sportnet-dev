<?php
Class Companies_model extends CI_Model
{
	function get_all_companies(){
		$query = $this->db->query("select * from companies inner join countries on companies.country_id = countries.id order by companies.name");
		return $query->result();
	}
	
	function get_all_countries(){
		$query = $this->db->query("select * from countries");
		return $query->result();
	}
	
	function get_companies_like($value){
		$query = $this->db->select()
		->like("name",$value)
		->get("companies");
		return $query;
	}
	
	function is_same_company_exists($company_name,$country_id){
		$query = $this->db->select()
		->where('name',$company_name)
		->where('country_id',$country_id)
		->get('companies');
		if($query->num_rows() > 0){
			return true;
		}
		else{
			return false;
		}
	}
}
?>