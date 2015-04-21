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
		//$username = $this->input->post("username");
		//$pass = $this->input->post("password");
		//$pass = $this->encrypt->encode($pass);
		$username = "admin";
		$pass = "admin";
		
		
		if (!$username OR !$pass){echo "Password/Username Tidak Boleh Kosong";}
		
		/*$sql = "SELECT * FROM admin WHERE nama = '".$username."';";
				
		$rs  = $this->db->query($sql)->row();
		$pass = "demo";
		if ($rs){
			$row['password'] = $rs->password;
			$passInput = md5($pass);
			$passdb = $row['password'];
			
			if($passdb != $passInput){
				echo "<script>alert('Password Yang Anda Masukkan Salah!'); </script>";
			}
			else{
                $row['username'] 	=  $rs->nama;
				$row['level_user'] 		=  $rs->level_user;
				*/
                $row['username'] 	=  $username;
				$row['level_user'] 	=  $pass;
				
                $this->session->set_userdata('d1kl4tkem3nd49r1', base64_encode(serialize($row)));
				//header("Location: " . $this->host ."dashboard");
				
				redirect('/admin-ctrlpnl','refresh');
			/*}
        }else {
			echo "<script>alert('Password atau Username Yang Anda Masukkan Salah!');</script>";
		}*/
		
	}
	function logoutadm(){
        $this->session->unset_userdata("d1kl4tkem3nd49r1");
        header("Location: " . $this->host . "admin-ctrlpnl");
    }
	
	function login_sbm(){
		$this->load->library('encrypt');
		
		$user = $this->db->escape_str($this->input->post('ed_usr'));
		//$pass = $this->db->escape_str($this->input->post('ed_psd'));
		$pass = $this->input->post('ed_psd');
		
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