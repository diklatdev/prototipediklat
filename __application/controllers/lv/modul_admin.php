<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class modul_admin extends SHIPMENT_Controller{
	function __construct(){
		parent::__construct();		
		$this->auth = unserialize(base64_decode($this->session->userdata('d1kl4tkem3nd49r1')));
		$this->host	= $this->config->item('base_url');
		$host = $this->host;
		
		$this->smarty->assign('host',$this->host);
		$this->smarty->assign('auth', $this->auth);
	}        
      

	function getdisplay($type="", $p1="", $p2="", $p3=""){
		$this->load->model("lv/madmin");
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
				$sert = $this->madmin->get_data("idx_sertifikat","result_array");
				$this->smarty->assign("sert", $sert);
			break;
			case "form_edit_admin":
				$content = "modul-admin/manajemen_admin/form-edit-admin.html";		
				$data = $this->madmin->get_data("tbl_user_admin","row_array", $this->input->post('id_u'));
				$this->smarty->assign("data", $data);				
				$level = $this->madmin->get_data("idx_level_user","result_array");
				$this->smarty->assign("level", $level);			
				$tuk = $this->madmin->get_data("idx_tuk","result_array");
				$this->smarty->assign("tuk", $tuk);
				
				$sert = $this->madmin->get_data("idx_sertifikat","result_array");
				$this->smarty->assign("sert", $sert);
				
				$this->load->library('encrypt');
				$pass = $this->encrypt->decode($data['password']);
				$this->smarty->assign("pass", $pass);
			break;
			case "manajemen_peserta":
				$content = "modul-admin/manajemen_peserta/main-peserta.html";
				$data = $this->madmin->get_data("tbl_data_peserta","result_array");
				$this->smarty->assign("data", $data);
			break;
			case "change_peserta":
				$id_u = $this->input->post('id_u');
				$content = "modul-admin/manajemen_peserta/form-edit-pass.html";
				$data = $this->madmin->get_data("tbl_data_peserta_detail","row_array", $id_u);
				
				$this->load->library('encrypt');
				$pass = $this->encrypt->decode($data['password']);
				$this->smarty->assign("pass", $pass);
				$this->smarty->assign("data", $data);
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
			case "tile_absen":
				$tuk = 'SELURUH Lokasi TUK Yang Sedang Berlangsung Sertifikasi';
				$id_admin = $this->auth['id'];
				$level_admin = $this->auth['level_admin'];
				
				if ($p1 == 'jadwal'){					
					$id_tuk = $this->input->post('id_tuk');
					$tuk = $this->input->post('tuk');					
					
					$data = $this->madmin->get_data("laporan","result_array",$id_tuk,$id_admin, $level_admin);
					$this->smarty->assign('id_user', $level_admin);
					$this->smarty->assign('data', $data);
					$this->smarty->assign('p1', 'thing');
					$this->smarty->assign('tuk', $tuk);
					$content = "modul-admin/laporan/absensi_tuk.html";
				}else{
					$jadwal = $this->madmin->get_data("jadwal_tuk","result_array");
					$this->smarty->assign('jadwal', $jadwal);
					$data = $this->madmin->get_data("laporan","result_array");
					$this->smarty->assign('data', $data);
					$this->smarty->assign('p1', 'nothing');
					$this->smarty->assign('tuk', $tuk);
					$content = "modul-admin/laporan/absensi.html";
				}
			break;
            case "progress_sertifikasi":
				$tuk = 'SELURUH Lokasi TUK Yang Sedang Berlangsung Sertifikasi';
				$id_admin = $this->auth['id'];
				$level_admin = $this->auth['level_admin'];
                if ($p1 == 'progress'){					
					$id_jadwal = $this->input->post('id_jadwal');
					$tuk = $this->input->post('tuk');					
					
					$data = $this->madmin->get_data("progress","result_array",$id_jadwal,$id_admin, $level_admin);
					$this->smarty->assign('id_user', $level_admin);
					$this->smarty->assign('data', $data);
					$this->smarty->assign('p1', 'thing');
					$this->smarty->assign('tuk', $tuk);
					$content = "modul-admin/laporan/progress_tuk.html";
				}else{
					$jadwal = $this->madmin->get_data("jadwal_tuk","result_array");
					$this->smarty->assign('jadwal', $jadwal);
					$data = $this->madmin->get_data("progress","result_array");
					$this->smarty->assign('data', $data);
					$this->smarty->assign('p1', 'nothing');
					$this->smarty->assign('tuk', $tuk);
                                        $content = "modul-admin/laporan/progress.html";
				}
                        break;
                        case "manajemen_pejabat":
				$content = "modul-admin/manajemen_pejabat/pejabat.html";
				$data = $this->madmin->get_data("tbl_master_pejabat","result_array");
				$this->smarty->assign("data", $data);				
			break;
			case "form_pejabat":
				$content = "modul-admin/manajemen_pejabat/form-add.html";
			break;
			case "form_edit_pejabat":
				$content = "modul-admin/manajemen_pejabat/form-edit.html";		
				$data = $this->madmin->get_data("tbl_master_pejabat","row_array", $this->input->post('id_row'));
				$this->smarty->assign("data", $data);	
			break;
                        case "hasil_akhir":
				$tuk = 'SELURUH Lokasi TUK Yang Sedang Berlangsung Sertifikasi';
				$id_admin = $this->auth['id'];
				$level_admin = $this->auth['level_admin'];
                                if ($p1 == 'data'){					
					$id_jadwal = $this->input->post('id_jadwal');
					$tuk = $this->input->post('tuk');					
					
					$data = $this->madmin->get_data("hasil_akhir","result_array",$id_jadwal,$id_admin, $level_admin);
					$this->smarty->assign('id_user', $level_admin);
					$this->smarty->assign('data', $data);
					$this->smarty->assign('p1', 'thing');
					$this->smarty->assign('tuk', $tuk);
					$content = "modul-admin/laporan/hasil_akhir_data.html";
				}else{
					$jadwal = $this->madmin->get_data("jadwal_tuk","result_array");
					$this->smarty->assign('jadwal', $jadwal);
					$data = $this->madmin->get_data("hasil_akhir","result_array");
					$this->smarty->assign('data', $data);
					$this->smarty->assign('p1', 'nothing');
					$this->smarty->assign('tuk', $tuk);
                                        $content = "modul-admin/laporan/hasil_akhir.html";
				}
                        break;
                        case "biodata":
				$tuk = 'SELURUH Lokasi TUK ';
				$id_admin = $this->auth['id'];
				$level_admin = $this->auth['level_admin'];
                                if ($p1 == 'peserta'){					
					$id_jadwal = $this->input->post('id_jadwal');
					$tuk = $this->input->post('tuk');					
					
					$data = $this->madmin->get_data("biodata_peserta","result_array",$id_jadwal,$id_admin, $level_admin);
					$this->smarty->assign('id_user', $level_admin);
					$this->smarty->assign('data', $data);
					$this->smarty->assign('p1', 'thing');
					$this->smarty->assign('tuk', $tuk);
					$content = "modul-admin/laporan/tabel_biodata.html";
				}else{
					$jadwal = $this->madmin->get_data("jadwal_tuk","result_array");
					$this->smarty->assign('jadwal', $jadwal);
					//$data = $this->madmin->get_data("peserta","result_array");
					//$this->smarty->assign('data', $data);
					$this->smarty->assign('p1', 'nothing');
					$this->smarty->assign('tuk', $tuk);
                                        $content = "modul-admin/laporan/biodata.html";
				}
                        break;                        
                        case "datagridview":
                            $content = "modul-admin/manajemen_admin/grid.html";
                            if($p1 == 'data_user'){
                                $this->smarty->assign('breadcumb', "Manajemen User");
                                $this->smarty->assign('tinggi', "40px");
                            }
                            if($p1 == 'master_tuk'){
                                $this->smarty->assign('breadcumb', "Manajemen TUK");
                                $this->smarty->assign('tinggi', "40px");
                            }
                            $this->smarty->assign('tipe', $p1);
                        break;
                    
		}
		$this->smarty->assign('type', $type);
		$this->smarty->display($content);
	}
	
	function getdatagrid($type){
		echo $this->madmin->get_data_grid($type);
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
        
       	
	function absensi_pdf($p1="", $p2="", $p3=""){
		$this->load->model('lv/madmin');
		$id_tuk = $p1;
		$id_sertifikasi = $p2;
		$id_jadwal = $p3;
		if ($this->auth){
			$this->load->library('mlpdf');			
			$tuk_prof = "SELECT t.nama_tuk as tuk, a.nama_aparatur as sertifikasi, 
				DATE_FORMAT(w.tanggal_wawancara,'%d-%m-%Y') as tanggal_wawancara
				FROM `tbl_jadwal_wawancara` w
				LEFT JOIN idx_tuk t ON t.id = w.idx_tuk_id
				LEFT JOIN idx_aparatur_sipil_negara a ON a.id = w.idx_sertifikasi_id
				WHERE w.id = '$id_jadwal';";
			$prof_tuk = $this->db->query($tuk_prof)->row_array();
			$this->smarty->assign('prof_tuk',$prof_tuk);
			
			$where_admin = '';
			if ($this->auth['level_admin'] != '99'){$where_admin = " AND idx_asesor_id = '".$this->auth['id']."' ";}
			
			$sql = "SELECT p.nama_lengkap, p.nip, d.jabatan, i.nama_instansi,  aa.`name` as provinsi,  a.`name` as kabupaten
				FROM tbl_data_peserta p 
				LEFT JOIN tbl_data_diklat d ON d.tbl_data_peserta_id = p.id
				LEFT JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = p.id
				LEFT JOIN tbl_jadwal_wawancara W ON W.id = T.tbl_jadwal_wawancara_id
				LEFT JOIN idx_instansi i ON i.id = d.idx_instansi_id
				LEFT JOIN idx_area a ON a.id = d.idx_kabupaten_instansi_id AND a.idkec = ''
				LEFT JOIN idx_area aa ON aa.idprov = d.idx_provinsi_instansi_id AND aa.idkot = ''
				WHERE W.id = '$id_jadwal'
                                $where_admin
				ORDER BY p.nama_lengkap;";
			$data = $this->db->query($sql)->result_array();
			$this->smarty->assign('data',$data);
			
			//$htmlheader = $this->smarty->fetch('modul-admin/pak_inpassing/sertifikat_header.html');
			$htmlcontent = $this->smarty->fetch('modul-admin/laporan/absensi_pdf.html');
                        $filename = 'Absensi_'.$prof_tuk['tuk'].'_'.$prof_tuk['sertifikasi'].'_'.$prof_tuk['tanggal_wawancara'];
			$this->show_pdf($htmlcontent,$filename);
			
		}else{
			header("Location: " . $this->host);	
		}
	}
        
        function hasil_akhir_pdf($p1 = ''){
            $this->load->model('lv/madmin');
            $id_jadwal = $p1;
            if ($this->auth){
                    $this->load->library('mlpdf');
                    
                    $pejabat = "SELECT * FROM tbl_master_pejabat WHERE kode_pangkat = 2";
                    $pejabat = $this->db->query($pejabat)->row_array();
                    $this->smarty->assign('pejabat',$pejabat);
                    
                    $tuk_prof = "SELECT t.nama_tuk as tuk, a.nama_aparatur as sertifikasi, 
                            DATE_FORMAT(w.tanggal_wawancara,'%d-%m-%Y') as tanggal_wawancara
                            FROM `tbl_jadwal_wawancara` w
                            LEFT JOIN idx_tuk t ON t.id = w.idx_tuk_id
                            LEFT JOIN idx_aparatur_sipil_negara a ON a.id = w.idx_sertifikasi_id
                            WHERE w.id = '$id_jadwal';";
                    $prof_tuk = $this->db->query($tuk_prof)->row_array();
                    $this->smarty->assign('prof_tuk',$prof_tuk);

                    $where_admin = '';
                    if ($this->auth['level_admin'] != '99'){$where_admin = " AND idx_asesor_id = '".$this->auth['id']."' ";}

                    $sql = "SELECT p.nama_lengkap, p.nip, d.jabatan, i.nama_instansi,  aa.`name` as provinsi,  a.`name` as kabupaten
                            ,p.tempat_lahir, DATE_FORMAT(p.tanggal_lahir,'%d-%m-%Y') as tanggal_lahir
                            FROM tbl_data_peserta p 
                            LEFT JOIN tbl_data_diklat d ON d.tbl_data_peserta_id = p.id
                            LEFT JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = p.id
                            LEFT JOIN tbl_jadwal_wawancara W ON W.id = T.tbl_jadwal_wawancara_id
                            LEFT JOIN idx_instansi i ON i.id = d.idx_instansi_id
                            LEFT JOIN idx_area a ON a.id = d.idx_kabupaten_instansi_id AND a.idkec = ''
                            LEFT JOIN idx_area aa ON aa.idprov = d.idx_provinsi_instansi_id AND aa.idkot = ''
                            LEFT JOIN tbl_step_peserta S ON S.tbl_data_peserta_id = d.tbl_data_peserta_id
                            WHERE W.id = '$id_jadwal'
                            $where_admin AND S.step_hasil = '1'
                            ORDER BY p.nama_lengkap;";
                    $data = $this->db->query($sql)->result_array();
                    $this->smarty->assign('data',$data);

                    //$htmlheader = $this->smarty->fetch('modul-admin/pak_inpassing/sertifikat_header.html');
                    $htmlcontent = $this->smarty->fetch('modul-admin/laporan/hasil_akhir_pdf.html');
                    $filename = 'Absensi_'.$prof_tuk['tuk'].'_'.$prof_tuk['sertifikasi'].'_'.$prof_tuk['tanggal_wawancara'];
                    $this->show_pdf($htmlcontent,$filename);

            }else{
                    header("Location: " . $this->host);	
            }
            
        }
        
        function biodata_pdf($p1 = ''){
            $this->load->model('why/madmin');
            $userid = $p1;
            $userQry = $this->db->query("SELECT idx_sertifikasi_id, kdreg_diklat FROM tbl_data_diklat WHERE tbl_data_peserta_id = $userid")->row_array();
            $idxsertifikasi_id = $userQry['idx_sertifikasi_id'];
            $kdreg_diklat = $userQry['kdreg_diklat'];
            
            if ($this->auth){
                    $this->load->library('mlpdf');
                    
                    $data = $this->madmin->get_data("tbl_data_peserta_detail", "row_array", $userid);
                    $data_file_persyaratan = $this->madmin->get_data("tbl_persyaratan", "result_array", $userid, $idxsertifikasi_id, $kdreg_diklat);
				
                    $this->smarty->assign("data", $data);
                    $this->smarty->assign("data_file_persyaratan", $data_file_persyaratan);
                    
                    $htmlcontent = $this->smarty->fetch('modul-admin/laporan/biodata_pdf.html');
                    $filename = '$userid';
                    $this->show_pdf($htmlcontent,$filename);

            }else{
                    header("Location: " . $this->host);	
            }
            
        }
        
        function show_pdf($htmlcontent, $filename){
            
            $pdf = $this->mlpdf->load();
            $spdf = new mPDF('', 'A4', 0, '', 12.7, 12.7, 5, 20, 5, 2, 'P');
            $spdf->ignore_invalid_utf8 = true;
            $spdf->useOnlyCoreFonts = true;
            $spdf->SetProtection(array('print'));
            // bukan sulap bukan sihir sim salabim jadi apa prok prok prok
            $spdf->allow_charset_conversion = true;     // which is already true by default
            $spdf->charset_in = 'iso-8859-2';  // set content encoding to iso
            $spdf->SetDisplayMode('fullpage');		
            //$spdf->SetHTMLHeader($htmlheader);
            /*$spdf->SetHTMLFooter('
                    <div style="font-family:arial; font-size:8px; text-align:center; font-weight:bold;">
                            Sistem Informasi Sertifikasi & Penilaian Kementerian Dalam Negeri
                    </div>
            ');
            */
            $spdf->WriteHTML($htmlcontent); // write the HTML into the PDF
            //$spdf->Output('repositories/Dokumen_LS/LS_PDF/'.$filename.'.pdf', 'F'); // save to file because we can
            $spdf->Output('__repository/temp_sertifikat/'.$filename.'.pdf', 'I'); // view file
            
        }
	
}