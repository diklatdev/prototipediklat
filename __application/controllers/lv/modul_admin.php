<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class modul_admin extends SHIPMENT_Controller{
	function __construct(){
		parent::__construct();		
		$this->auth = unserialize(base64_decode($this->session->userdata('d1kl4tkem3nd49r1')));
		$this->host	= $this->config->item('base_url');
		$host = $this->host;
		
		$this->smarty->assign('host',$this->host);
		$this->smarty->assign('auth', $this->auth);
		$this->load->model("lv/madmin");
	}

	function getdisplay($type="", $p1="", $p2="", $p3=""){
		$modul = "front/";
		switch($type){
		////////////////////////*****levi
			case "manajemen_admin":
				$content = "modul-admin/manajemen_admin/main.html";
				$data = $this->madmin->get_data("tbl_user_admin","result_array");
				$this->smarty->assign("data", $data);				
			break;
			case "form_admin":
				$content = "modul-admin/manajemen_admin/form-add-admin.html";		
				$this->smarty->assign('idx_level_user', $this->fillcombo('idx_level_user', 'return') );
			break;
			case "manajemen_aparatur":				
				$content = "modul-admin/manajemen_aparatur/main.html";
				$data = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn');
				$this->smarty->assign("data", $data);
				$data_tk1 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk1');
				$this->smarty->assign("data_tk1", $data_tk1);
				$data_tk2 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk2');
				$this->smarty->assign("data_tk2", $data_tk2);
			break;
			case "form_aparatur":	
				$content = "modul-admin/manajemen_aparatur/form-add-aparatur.html";				
				$this->smarty->assign('idx_aparatur', $this->fillcombo('idx_aparatur', 'return') );
			break;
			case "manajemen_sertifikasi":	
				$content = "modul-admin/manajemen_sertifkat/main.html";				
				$data = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn');
				$this->smarty->assign("data", $data);
				$data_tk1 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk1');
				$this->smarty->assign("data_tk1", $data_tk1);
				$data_tk2 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk2');
				$this->smarty->assign("data_tk2", $data_tk2);
				
				$sertifikat = $this->madmin->get_data("idx_persyaratan_registrasi", "result_array");
				$this->smarty->assign("sertifikat", $sertifikat);
			break;
			case "form_sertifikat":	
				$content = "modul-admin/manajemen_sertifkat/form-add-sertifikat.html";				
				$this->smarty->assign('idx_aparatur', $this->fillcombo('idx_aparatur', 'return') );
			break;
			case "manajemen_uji_mandiri":
				$id_asn = $this->input->post('id_asn');
				if ($id_asn){					
					$content = "modul-admin/manajemen_uji_mandiri/list-uji-mandiri.html";	
					$data = $this->madmin->get_data("idx_unit_kompetensi","result_array", $id_asn);
					$this->smarty->assign("data", $data);
				}else{
					$content = "modul-admin/manajemen_uji_mandiri/main.html";				
					$this->smarty->assign('idx_aparatur', $this->fillcombo('idx_aparatur', 'return') );
					$this->smarty->assign("data", '');
				}
			break;
			case "form_uji_mandiri":	
				$content = "modul-admin/manajemen_uji_mandiri/form-add-uji.html";	
				$this->smarty->assign('idx_aparatur', $this->fillcombo('idx_aparatur', 'return') );			
			break;
			case "manajemen_instansi":
				$content = "modul-admin/manajemen_instansi/main.html";
				$data = $this->madmin->get_data("idx_instansi","result_array");
				$this->smarty->assign("data", $data);				
			break;
			case "form_instansi":	
				$content = "modul-admin/manajemen_instansi/form-add.html";	
				$this->smarty->assign('idx_provinsi_instansi_id', $this->fillcombo('idx_provinsi', 'return') );		
			break;
			case "manajemen_pangkat":
				$content = "modul-admin/manajemen_pangkat/main.html";
				$data = $this->madmin->get_data("idx_pangkat","result_array");
				$this->smarty->assign("data", $data);				
			break;
			case "form_pangkat":	
				$content = "modul-admin/manajemen_pangkat/form-add.html";	
				$this->smarty->assign('idx_provinsi_instansi_id', $this->fillcombo('idx_provinsi', 'return') );		
			break;
			case "manajemen_tuk":
				$content = "modul-admin/manajemen_tuk/main.html";
				$data = $this->madmin->get_data("idx_tuk","result_array");
				$this->smarty->assign("data", $data);				
			break;
			case "form_tuk":	
				$content = "modul-admin/manajemen_tuk/form-add.html";	
				$this->smarty->assign('idx_provinsi_instansi_id', $this->fillcombo('idx_provinsi', 'return') );		
			break;
		}
		$this->smarty->assign('type', $type);
		$this->smarty->display($content);
	}
	
	function simpansavedbx($type=""){
		$post = array();
        foreach($_POST as $k=>$v) $post[$k] = $this->db->escape_str($this->input->post($k));
		
		/*
		echo "<pre>";
		print_r($post);
		exit;
		//*/
		
		echo $this->madmin->simpansavedatabase($type, $post);
	}
	
}