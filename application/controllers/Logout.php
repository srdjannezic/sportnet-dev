<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {
 
 function __construct()
 {
   parent::__construct();
 }
 
 function index()
 {
	$this->session->unset_userdata('logged_in');
	redirect('login','refresh');
 }
 
}
 
?>
