<?php
Class Users_model extends CI_Model
{
 function login($username, $password)
 {
   $this -> db -> select('user_id, user_name, password');
   $this -> db -> from('users');
   $this -> db -> where('user_name', $username);
   $this -> db -> where('password', MD5($password));
   $this -> db -> limit(1);
 
   $query = $this -> db -> get();
 
   if($query -> num_rows() == 1)
   {
     return $query->result();
   }
   else
   {
     return false;
   }
 }
 
 function get_all_users(){
 	return $this->db->get('users');
 }
 
 function insert_user($array){
 	$this->db->insert('users',$array);
 }

 function update_user($array,$user_id){
 	$this->db->where('user_id',$user_id)->update('users',$array);
 }
 
 function delete_user($user_id){
 	$this->db->where('user_id',$user_id)->delete('users');
 } 
  
 function is_user_exists($user_name){
 	$query = $this->db->select()->where('user_name',$user_name)->get('users');
	if($query->num_rows() > 0){
		return true;
	}
	else return false;
 } 
 
 function get_user($user_id){
 	$query = $this->db->select()->where('user_id',$user_id)->get('users',1);
	return $query->row();
 }  
}
?>