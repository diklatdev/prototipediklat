<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class login extends SHIPMENT_Controller{
	function __construct(){
		parent::__construct();		
		$host = $this->host;
		$this->smarty->assign('host',$this->host);
		$this->load->model(array("why/mportal", "why/madmin"));
	}
	
	function loginadm(){		
		$this->load->library('encrypt');
		
		$user = $this->db->escape_str($this->input->post('username'));
		$pass = $this->db->escape_str($this->input->post('password'));
		//$pass = $this->input->post('password');
		
		/*			
		//echo $this->encrypt->encode($pass);
		//exit;
		*/
		
		$data = $this->madmin->get_data("data_login_admin", "row_array", trim($user)); 		
		if($data && $pass == $this->encrypt->decode($data["password"])){
			$this->session->set_userdata('d1kl4tkem3nd49r1', base64_encode(serialize($data)));	
			header("Location: " . $this->host . "admin-ctrlpnl");
		}else{
			header("Location: " . $this->host . "admin-ctrlpnl");
		}
		
	}
	function logoutadm(){
        $this->session->unset_userdata("d1kl4tkem3nd49r1");
        header("Location: " . $this->host . "admin-ctrlpnl");
    }
	
	function login_sbm(){
		$this->load->library('encrypt');
		
		$user = $this->db->escape_str($this->input->post('ed_usr'));
		$pass = $this->db->escape_str($this->input->post('ed_psd'));
		//$pass = $this->input->post('ed_psd');
		
		//echo $user.' -> '.$pass;exit;
		//echo $this->encrypt->encode($pass);
		//exit;
				
		$data = $this->mportal->get_data("data_login", "row_array", trim($user)); 
		//echo $this->encrypt->decode($data["password"]);
		//exit;
		if($data && $pass == $this->encrypt->decode($data["password"])){
			$this->session->set_userdata('d1kl4tkem3nd49r1-p0rt4L', base64_encode(serialize($data)));	
			header("Location: " . $this->host);
		}else{
			header("Location: " . $this->host);
		}

	}
	
	function logogut(){
		$this->session->unset_userdata('d1kl4tkem3nd49r1-p0rt4L', 'limit');
		$this->session->sess_destroy();
		header("Location: " . $this->host);
	}


}