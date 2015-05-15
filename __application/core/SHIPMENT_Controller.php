<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SHIPMENT_Controller extends CI_Controller{
	
	function SHIPMENT_Controller () {
		parent::__construct();
		
		$this->load->database();
		$this->cuti = $this->config->item('base_url');
		$this->USER = unserialize(base64_decode($this->session->userdata('shipment_session')));		
		$this->host	= $this->config->item('base_url');
		
		$this->smarty->assign('USER', $this->USER);
		$this->smarty->assign('host', $this->cuti);
		
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("If-Modified-Since: Mon, 22 Jan 2008 00:00:00 GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Cache-Control: private");
		header("Pragma: no-cache");
		
		//if (!$this->USER){header("Location : " .$this->host);}
	}
}