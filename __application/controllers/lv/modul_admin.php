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
				$tuk = $this->madmin->get_data("idx_tuk","result_array");
				$this->smarty->assign("tuk", $tuk);
			break;
			case "form_edit_admin":
				$content = "modul-admin/manajemen_admin/form-edit-admin.html";		
				$data = $this->madmin->get_data("tbl_user_admin","row_array", $this->input->post('id_u'));
				$this->smarty->assign("data", $data);				
				$level = $this->madmin->get_data("idx_level_user","result_array");
				$this->smarty->assign("level", $level);			
				$tuk = $this->madmin->get_data("idx_tuk","result_array");
				$this->smarty->assign("tuk", $tuk);
				
				$this->load->library('encrypt');
				$pass = $this->encrypt->decode($data['password']);
				$this->smarty->assign("pass", $pass);
			break;
			case "manajemen_aparatur":				
				$content = "modul-admin/manajemen_aparatur/main.html";
				$data = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn');
				$this->smarty->assign("data", $data);
				$data_tk1 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk1');
				$this->smarty->assign("data_tk1", $data_tk1);
				$data_tk2 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk2');
				$this->smarty->assign("data_tk2", $data_tk2);
				$data_tk3 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk3');
				$this->smarty->assign("data_tk3", $data_tk3);
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
				$data_tk3 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk3');
				$this->smarty->assign("data_tk3", $data_tk3);
				
				$sertifikat = $this->madmin->get_data("idx_persyaratan_registrasi", "result_array");
				$this->smarty->assign("sertifikat", $sertifikat);
			break;
			case "form_sertifikat":	
				$content = "modul-admin/manajemen_sertifkat/form-add-sertifikat.html";				
				$this->smarty->assign('idx_aparatur', $this->fillcombo('idx_aparatur', 'return') );
			break;
			case "manajemen_uji_mandiri":
				$id_asn = $this->input->post('id_asn');
				$id_tk1 = $this->input->post('id_tk1');					
				$id_tkn = $this->input->post('id_tkn');					
				if ($id_tk1 == 2){	
					if ($id_tkn){						
						$content = "modul-admin/manajemen_uji_mandiri/list-uji-mandiri.html";	
						$data = $this->madmin->get_data("idx_unit_kompetensi","result_array", $id_tkn);
						$this->smarty->assign("data", $data);
					}else{
						$content = "modul-admin/manajemen_uji_mandiri/add-combo.html";	
						$data_tk3 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk3',$id_asn,'topo');
						$this->smarty->assign("data_tk3", $data_tk3);
					}
				}else{
					if ($id_asn){	
						$content = "modul-admin/manajemen_uji_mandiri/list-uji-mandiri.html";	
						$data = $this->madmin->get_data("idx_unit_kompetensi","result_array", $id_asn);
						$this->smarty->assign("data", $data);
					}else{
						$content = "modul-admin/manajemen_uji_mandiri/main.html";				
						$this->smarty->assign('idx_aparatur', $this->fillcombo('idx_aparatur', 'return') );
						$this->smarty->assign("data", '');
					}
				}
			break;
			case "form_uji_mandiri":	
				$content = "modul-admin/manajemen_uji_mandiri/form-add-uji.html";	
				$this->smarty->assign('idx_aparatur', $this->fillcombo('idx_aparatur', 'return') );			
			break;
			case "edit_uji_man":	
				$id_uk = $this->input->post('id_row');
				$data = $this->madmin->get_data("idx_unit_kompetensi","row_array",'', $id_uk);
				$this->smarty->assign("data", $data);
				
				$asn = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn');
				$this->smarty->assign("asn", $asn);
				
				$asn_tk1 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk1');
				$this->smarty->assign("asn_tk1", $asn_tk1);
				
				$data_tk2 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk2');
				$this->smarty->assign("data_tk2", $data_tk2);
				
				$data_tk3 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk3');
				$this->smarty->assign("data_tk3", $data_tk3);
				$content = "modul-admin/manajemen_uji_mandiri/edit-uji.html";	
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
			case "edit_instansi":	
				$content = "modul-admin/manajemen_instansi/form-edit.html";	
				//$this->smarty->assign('idx_provinsi_instansi_id', $this->fillcombo('idx_provinsi', 'return') );		
				$prop = $this->madmin->get_data("idx_prov","result_array",'prop');
				$this->smarty->assign("prop", $prop);		
				$data = $this->madmin->get_data("idx_instansi","row_array", $this->input->post('id_row'));
				$this->smarty->assign("data", $data);						
			break;
			case "manajemen_pangkat":
				$content = "modul-admin/manajemen_pangkat/main.html";
				$data = $this->madmin->get_data("idx_pangkat","result_array");
				$this->smarty->assign("data", $data);				
			break;
			case "form_pangkat":	
				$content = "modul-admin/manajemen_pangkat/form-add.html";		
			break;
			case "edit_pangkat":	
				$content = "modul-admin/manajemen_pangkat/form-edit.html";	
				$data = $this->madmin->get_data("idx_pangkat","row_array",$this->input->post('id_row'));
				$this->smarty->assign("data", $data);		
			break;
			case "manajemen_tuk":
				$content = "modul-admin/manajemen_tuk/main.html";
				$data = $this->madmin->get_data("tbl_tuk","result_array");
				$this->smarty->assign("data", $data);				
			break;
			case "form_tuk":	
				$content = "modul-admin/manajemen_tuk/form-add.html";	
				$this->smarty->assign('idx_provinsi_instansi_id', $this->fillcombo('idx_provinsi', 'return') );		
			break;
			case "edit_tuk":	
				$content = "modul-admin/manajemen_tuk/form-edit.html";	
				$data = $this->madmin->get_data("tbl_tuk","row_array",$this->input->post('id_row'));
				$this->smarty->assign("data", $data);		
				
				$prop = $this->madmin->get_data("idx_prov","result_array",'prop');
				$this->smarty->assign("prop", $prop);
				$kab = $this->madmin->get_data("idx_prov","result_array",'kab');
				$this->smarty->assign("kab", $kab);	
			break;
			case "add_combo":	
				$content = "modul-admin/manajemen_aparatur/add-combo.html";	
				$asn = $this->input->post('id_tk1');
				$data = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", '$asn');
				$this->smarty->assign("data", $data);
				$data_tk3 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk3');
				$this->smarty->assign("data_tk3", $data_tk3);
				
				$data_tk2 = $this->madmin->get_data("idx_aparatur_sipil_negara","result_array", 'asn_tk2');
				$this->smarty->assign("data_tk2", $data_tk2);
				
				
				$this->smarty->assign("asn", $asn);
			break;
		}
		$this->smarty->assign('type', $type);
		$this->smarty->display($content);
	}
	
	function displayCombo($kode="", $content){		
		
		$this->smarty->display($content);
	}
	
	function simpansavedbx($type="", $met=""){
		$post = array();
        foreach($_POST as $k=>$v) $post[$k] = $this->db->escape_str($this->input->post($k));
		
		/*
		echo "<pre>";
		print_r($post);
		exit;
		//*/
		
		echo $this->madmin->simpansavedatabase($type, $post, $met);
	}
	
	function fillcombo($type="", $balikan="", $p1="", $p2="", $p3=""){
		$this->load->helper('db_helper');
		$this->load->model("lv/mportal");
		
		$v = $this->input->post('v');
		if($v != ""){
			$selTxt = $v;
		}else{
			$selTxt = $p1;
		}
		
		$optTemp = "";
		
		if($type == 'bulan'){
			$data = arraydate('bulan');
		}else{
			$optTemp = '<option value=""> -- Pilih -- </option>';
			$data = $this->mportal->get_data($type, 'result_array', $p1, $p2);
		}

		if($data){
			foreach($data as $k=>$v){
				if($selTxt == $v['kode']){
					$optTemp .= '<option selected value="'.$v['kode'].'">'.$v['txt'].'</option>';
				}else{ 
					$optTemp .= '<option value="'.$v['kode'].'">'.$v['txt'].'</option>';	
				}
			}
		}
		
		if($balikan == 'return'){
			return $optTemp;
		}elseif($balikan == 'echo'){
			echo $optTemp;
		}

	}
	
}