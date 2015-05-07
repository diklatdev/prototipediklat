<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class modul_pak extends SHIPMENT_Controller{
	function __construct(){
		parent::__construct();		
		$this->auth = unserialize(base64_decode($this->session->userdata('d1kl4tkem3nd49r1-p0rt4L')));
		$this->autha = unserialize(base64_decode($this->session->userdata('d1kl4tkem3nd49r1')));
		$this->host	= $this->config->item('base_url');
		$host = $this->host;
		
		$this->smarty->assign('host',$this->host);
		$this->smarty->assign('auth', $this->auth);
		$this->load->model("why/mportal");
		$this->load->model("lv/mpak");
	}
	
	function index() {
		$modul = "front/";
		if(!$this->auth) {
			$this->smarty->display('login.html');
		}
	}
	
	function getdisplay($type="", $p1="", $p2="", $p3=""){
		$modul = "front/";
		switch($type){	
			case "pak_inpassing":
				$peng_pak = $this->db->query("SELECT * FROM tbl_pengajuan_pak_inpassing WHERE id_peserta = '".$this->auth['id']."';");
				$rows_num = $peng_pak->num_rows();
				if ($rows_num > 0){
					
					header("Location: " . $this->host ."hasil-pak");
				}else{
					$data_peserta = $this->mpak->get_data('tbl_data_peserta', 'row_array', $this->auth['id']);
					$masa_kerja = $this->mpak->get_data('idx_masa_kerja', 'result');
					$tingkat = $this->mpak->get_data('idx_tingkat_aparatur', 'result');
					
					$konten = "modul-portal/pak_inpassing/form_pak_inpassing";
					$this->smarty->assign('peserta', $data_peserta);
					$this->smarty->assign('masa_kerja', $masa_kerja);
					$this->smarty->assign('tingkat', $tingkat);
					
					$this->smarty->assign('type', $type);
				}
			break;
			case "angka_kredit":
				
			break;
			case "hasil_pak_inpassing_temp":
				$pak_inpassing_temp = $this->mpak->get_data('tbl_pengajuan_pak_inpassing', 'row_array', $this->auth['id'], 'temp');
				$this->smarty->assign('pak_temp', $pak_inpassing_temp);
				$konten = "modul-portal/pak_inpassing/hasil_pak_inpassing_temp";
			break;
			case "hasil_validasi_pak":
				$this->load->model("lv/madmin");
				$id_peserta = $this->auth['id'];				
				$pak_inpass = $this->db->query("SELECT * FROM tbl_pengajuan_pak_inpassing WHERE id_peserta = '$id_peserta'")->row_array();
				$id_inpass = $pak_inpass['id'];
				
				$data_diklat = $this->db->query("SELECT idx_sertifikasi_id 
					FROM tbl_data_diklat WHERE tbl_data_peserta_id = '$id_peserta'")->row_array();
				$idx_sertifikasi_id = $data_diklat['idx_sertifikasi_id'];
				
				$pak_inpassing_temp = $this->mpak->get_data('tbl_pengajuan_pak_inpassing', 'row_array', $id_peserta, 'det_pengajuan', $id_inpass);
				$data_peserta = $this->mpak->get_data('data_pribadi_peserta', 'row_array', $id_peserta);
				$masa_kerja = $this->mpak->get_data('idx_masa_kerja', 'result');
				
				$id_tingkat = $pak_inpassing_temp['id_tingkat'];
				$id_pendidikan = $pak_inpassing_temp['id_pendidikan'];
				$tot_aju = $pak_inpassing_temp['total_angka_diterima'];
				
				if ( $id_tingkat == '1'){
					if ($id_pendidikan == 7 || $id_pendidikan == 6){
						if ($tot_aju >= '25' && $tot_aju < '40'){$jabatan = 'PELAKSANA PEMULA';}
						if ($tot_aju >= '40' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
					elseif ($id_pendidikan == 5){
						if ($tot_aju >= '40' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
					elseif ($id_pendidikan == 4){
						if ($tot_aju >= '60' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
				}
				elseif ( $id_tingkat == '2'){
					if ($id_pendidikan == 3){
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PERTAMA';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
					elseif ($id_pendidikan == 2){
						if ($tot_aju >= '150' && $tot_aju < '200'){$jabatan = 'PERTAMA';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
					elseif ($id_pendidikan == 1){
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
				}
				
				$query_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $idx_sertifikasi_id);
				$n_sert = str_replace(" ", "_", $query_sertifikasi['nama_aparatur']);
				$folder_pak = $query_sertifikasi['kode_sertifikasi']."-".strtolower($n_sert);
				
				$this->smarty->assign('pak_temp', $pak_inpassing_temp);
				$this->smarty->assign('data', $data_peserta);;
				$this->smarty->assign('folder_pak', $folder_pak);
				$this->smarty->assign('masa_kerja', $masa_kerja);
				$this->smarty->assign('jabatan', $jabatan);
				
				$konten = "modul-portal/pak_inpassing/hasil_verifikasi_pak";
			break;
			case "dupak-gagal":
				$konten = "modul-portal/pak_inpassing/hasil_pak_inpassing";
				$this->smarty->assign('type', $type);
			break;
		}		
		$this->smarty->assign('modul',$modul);
		$this->smarty->assign('konten', $konten);
		$this->smarty->display('index-portal.html');
		
	}
	
	function getdisplayadmin($type="", $p1="", $p2="", $p3=""){
		switch($type){
			case "pak_temp_admin":
				$pak_inpassing_temp = $this->mpak->get_data('tbl_pengajuan_pak_inpassing', 'result_array', '','admin');
				$this->smarty->assign('pak_temp', $pak_inpassing_temp);
				$konten = "modul-admin/pak_inpassing/main.html";
			break;
			case "pak_result_admin":
				$pak_inpassing = $this->mpak->get_data('tbl_pengajuan_pak_inpassing', 'result_array', '','result');
				$this->smarty->assign('pak_temp', $pak_inpassing);
				$konten = "modul-admin/pak_inpassing/result.html";
			break;
			case "pak_pengajuan_det":
				$this->load->model("lv/madmin");
				$id_peserta = $this->input->post('id_peserta');
				$id_angdit = $this->input->post('id_angdit');
				
				$data_diklat = $this->db->query("SELECT idx_sertifikasi_id 
					FROM tbl_data_diklat WHERE tbl_data_peserta_id = '$id_peserta'")->row_array();
				$idx_sertifikasi_id = $data_diklat['idx_sertifikasi_id'];
				
				$pak_inpassing_temp = $this->mpak->get_data('tbl_pengajuan_pak_inpassing', 'row_array', $id_peserta, 'det_pengajuan', $id_angdit);
				$data_peserta = $this->mpak->get_data('data_pribadi_peserta', 'row_array', $id_peserta);
				$masa_kerja = $this->mpak->get_data('idx_masa_kerja', 'result');
				
				$id_tingkat = $pak_inpassing_temp['id_tingkat'];
				$id_pendidikan = $pak_inpassing_temp['id_pendidikan'];
				$tot_aju = $pak_inpassing_temp['total_angka_diajukan'];
				
				if ( $id_tingkat == '1'){
					if ($id_pendidikan == 7 || $id_pendidikan == 6){
						if ($tot_aju >= '25' && $tot_aju < '40'){$jabatan = 'PELAKSANA PEMULA';}
						if ($tot_aju >= '40' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
					elseif ($id_pendidikan == 5){
						if ($tot_aju >= '40' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
					elseif ($id_pendidikan == 4){
						if ($tot_aju >= '60' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
				}
				elseif ( $id_tingkat == '2'){
					if ($id_pendidikan == 3){
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PERTAMA';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
					elseif ($id_pendidikan == 2){
						if ($tot_aju >= '150' && $tot_aju < '200'){$jabatan = 'PERTAMA';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
					elseif ($id_pendidikan == 1){
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
				}
				
				$query_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $idx_sertifikasi_id);
				$n_sert = str_replace(" ", "_", $query_sertifikasi['nama_aparatur']);
				$folder_pak = $query_sertifikasi['kode_sertifikasi']."-".strtolower($n_sert);
				
				$this->smarty->assign('pak_temp', $pak_inpassing_temp);
				$this->smarty->assign('data', $data_peserta);;
				$this->smarty->assign('folder_pak', $folder_pak);
				$this->smarty->assign('masa_kerja', $masa_kerja);
				$this->smarty->assign('jabatan', $jabatan);
				
				$konten = "modul-admin/pak_inpassing/konten_pak_temp.html";
			break;
			case "validasi_pak":
				$this->load->model("lv/madmin");
				$id_peserta = $this->input->post('id_peserta');
				$id_angdit = $this->input->post('id_angdit');
				
				$data_diklat = $this->db->query("SELECT idx_sertifikasi_id 
					FROM tbl_data_diklat WHERE tbl_data_peserta_id = '$id_peserta'")->row_array();
				$idx_sertifikasi_id = $data_diklat['idx_sertifikasi_id'];
				
				$pak_inpassing_temp = $this->mpak->get_data('tbl_pengajuan_pak_inpassing', 'row_array', $id_peserta, 'det_pengajuan', $id_angdit);
				$data_peserta = $this->mpak->get_data('data_pribadi_peserta', 'row_array', $id_peserta);
				$masa_kerja = $this->mpak->get_data('idx_masa_kerja', 'result');
				
				$id_tingkat = $pak_inpassing_temp['id_tingkat'];
				$id_pendidikan = $pak_inpassing_temp['id_pendidikan'];
				$tot_aju = $pak_inpassing_temp['total_angka_diterima'];
				
				if ( $id_tingkat == '1'){
					if ($id_pendidikan == 7 || $id_pendidikan == 6){
						if ($tot_aju >= '25' && $tot_aju < '40'){$jabatan = 'PELAKSANA PEMULA';}
						if ($tot_aju >= '40' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
					elseif ($id_pendidikan == 5){
						if ($tot_aju >= '40' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
					elseif ($id_pendidikan == 4){
						if ($tot_aju >= '60' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
				}
				elseif ( $id_tingkat == '2'){
					if ($id_pendidikan == 3){
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PERTAMA';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
					elseif ($id_pendidikan == 2){
						if ($tot_aju >= '150' && $tot_aju < '200'){$jabatan = 'PERTAMA';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
					elseif ($id_pendidikan == 1){
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
				}
				
				$query_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $idx_sertifikasi_id);
				$n_sert = str_replace(" ", "_", $query_sertifikasi['nama_aparatur']);
				$folder_pak = $query_sertifikasi['kode_sertifikasi']."-".strtolower($n_sert);
				
				$this->smarty->assign('pak_temp', $pak_inpassing_temp);
				$this->smarty->assign('data', $data_peserta);;
				$this->smarty->assign('folder_pak', $folder_pak);
				$this->smarty->assign('masa_kerja', $masa_kerja);
				$this->smarty->assign('jabatan', $jabatan);
				
				$konten = "modul-admin/pak_inpassing/formulir_verifikasi_pak.html";
			break;
		}
		$this->smarty->assign('type', $type);
		$this->smarty->display($konten);
	
	}
	
	function combo_angdit(){
			$id_tingkat = $this->input->post('tingkat');
			$id_golongan = $this->input->post('golongan');
			$id_pendidikan = $this->input->post('pendidikan');
			$id_masa_kerja = $this->input->post('masa');
			$angka_kredit = $this->mpak->get_data('idx_angka_kredit_inpassing', 
					'row', $id_tingkat, $id_golongan, $id_pendidikan, $id_masa_kerja);
			if ($angka_kredit){
				$html = "<span class='form-control' style='border:none !important;'>".$angka_kredit->angka_kredit."</span>					
						<input type='hidden' id='angdit' name='angdit' value='".$angka_kredit->angka_kredit."'>						
						<input type='hidden' id='id_kredit' name='id_kredit' value='".$angka_kredit->id."'>";			
				$this->smarty->display('string:'.$html);
			}else{
				$html = "<span class='form-control' style='border:none !important;'>0</span>";		
				$this->smarty->display('string:'.$html);
			}
	}
	
	function view_pak(){
			$level1 = $this->db->query("SELECT * FROM idx_kegiatan_pak WHERE level = 1")->result();
			$this->smarty->assign('level1', $level1);
			$level2 = $this->db->query("SELECT * FROM idx_kegiatan_pak WHERE level = 2")->result();
			$this->smarty->assign('level2', $level2);
			$level3 = $this->db->query("SELECT * FROM idx_kegiatan_pak WHERE level = 3")->result();
			$this->smarty->assign('level3', $level3);
			$level4 = $this->db->query("SELECT * FROM idx_kegiatan_pak WHERE level = 4")->result();
			$this->smarty->assign('level4', $level4);
			
			$cekKeg = "SELECT id_kegiatan as id FROM idx_kegiatan_pak WHERE level = 4 ";
			$cekKeg = $this->db->query($cekKeg);
			
			$cekSudah = "SELECT p.id, p.idx_angka_kredit, file_pendukung, A.angka_kredit FROM tbl_pengajuan_pak p
				LEFT JOIN idx_angka_kredit A ON A.id = p.idx_angka_kredit
				WHERE idx_peserta = '".$this->auth['id']."'";
			$cekSudah = $this->db->query($cekSudah)->result();
			
			$angka_kredit = "SELECT A.id, A.idx_kegiatan_pak, K.nama_unsur_kegiatan, A.satuan_hasil,j.nama_jenjang, A.idx_jenjang,
				(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM A.angka_kredit) AS char)))) AS angka_kredit
				FROM idx_kegiatan_pak K
				INNER JOIN idx_angka_kredit A ON K.id = A.idx_kegiatan_pak
				LEFT JOIN idx_jenjang j ON j.id = A.idx_jenjang;";
			$angka_kredit = $this->db->query($angka_kredit)->result();
			foreach($cekKeg->result() as $row){
                $data[] = $row->id;
            }
			
			$this->smarty->assign("cek_sudah", $cekSudah);
			$this->smarty->assign("cek_keg", $data);
			$this->smarty->assign("angka_kredit", $angka_kredit);
			
			$this->smarty->assign('modul',$modul);
			$this->smarty->assign('konten', "modul-portal/pak/form_pak");
			$this->smarty->display('index-portal.html');
	}
	
	function hasil_pak(){
		$modul = "front/";
		if(!$this->auth) {
			$this->smarty->display('login.html');
		}else {
			$level1 = $this->db->query("SELECT * FROM idx_kegiatan_pak WHERE level = 1")->result();
			$this->smarty->assign('level1', $level1);
			$level2 = $this->db->query("SELECT * FROM idx_kegiatan_pak WHERE level = 2")->result();
			$this->smarty->assign('level2', $level2);
			$level3 = $this->db->query("SELECT * FROM idx_kegiatan_pak WHERE level = 3")->result();
			$this->smarty->assign('level3', $level3);
			$level4 = $this->db->query("SELECT * FROM idx_kegiatan_pak WHERE level = 4")->result();
			$this->smarty->assign('level4', $level4);
			
			$cekKeg = "SELECT id_kegiatan as id FROM idx_kegiatan_pak WHERE level = 4 ";
			$cekKeg = $this->db->query($cekKeg);
			
			$cekSudah = "SELECT p.id, p.idx_angka_kredit, file_pendukung, A.angka_kredit FROM tbl_pengajuan_pak p
				LEFT JOIN idx_angka_kredit A ON A.id = p.idx_angka_kredit
				WHERE idx_peserta = '".$this->auth['id']."'";
			$cekSudah = $this->db->query($cekSudah)->result();
			
			$angka_kredit = "SELECT A.id, A.idx_kegiatan_pak, K.nama_unsur_kegiatan, A.satuan_hasil,j.nama_jenjang, A.idx_jenjang,
				(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM A.angka_kredit) AS char)))) AS angka_kredit
				FROM idx_kegiatan_pak K
				INNER JOIN idx_angka_kredit A ON K.id = A.idx_kegiatan_pak
				LEFT JOIN idx_jenjang j ON j.id = A.idx_jenjang;";
			$angka_kredit = $this->db->query($angka_kredit)->result();
			foreach($cekKeg->result() as $row){
                $data[] = $row->id;
            }
			
			$this->smarty->assign("cek_sudah", $cekSudah);
			$this->smarty->assign("cek_keg", $data);
			$this->smarty->assign("angka_kredit", $angka_kredit);
			
			$this->smarty->assign('modul',$modul);
			$this->smarty->assign('konten', "modul-portal/pak/hasil_pak");
			$this->smarty->display('index-portal.html');
		}
	}
	
	function savedatatodb($type=""){
			$post = array();
			foreach($_POST as $k=>$v) $post[$k] = $this->db->escape_str($this->input->post($k));
			
			if ($type == 'pak'){
				$savenya =  $this->savePakInpassing($type, $post);
			}elseif($type == 'approve_pak'){
				$savenya =  $this->approvePakInpassing($type, $post);		
			}elseif($type == 'peripikasi_pak'){
				$savenya =  $this->verifikasiPakInpassing($type, $post);		
			}
			if($savenya == 1){
				if($type == "pak"){
					//$this->getdisplay("hasil_pak_inpassing_temp");
					header("Location: " . $this->host ."hasil-pak-temp");	
				}
				/*if($type == "approve_pak"){
					//$this->getdisplayadmin("pak_temp_admin", 'pak');
					header("Location: " . $this->host ."pak-temp");	
				}*/
			}else{
				if($type == "pak"){
					$this->getdisplay("dupak-gagal");
				}
			}
		
		
	}
	
	function verifikasiPakInpassing($type, $post){	
		if($this->autha){
			$usid = $_POST['usaid'];			
			$idpak = $_POST['idpak'];
			$no_sk = "";
			
			$masa_nilai = $_POST['masa_nilai'];
			$masa_nilai = date("Y-m-d", strtotime($masa_nilai));
			
			$tmt = $_POST['tmt'];
			$tmt = date("Y-m-d", strtotime($tmt));
			
			$no_sk_tbl = $this->db->query("SELECT nomor_sk_keputusan FROM tbl_pengajuan_pak_inpassing 
				WHERE id = '$idpak'")->row_array();
			
			$max_nomor_sk = "SELECT MAX(SUBSTR(nomor_sk_keputusan,1,6)) as nomor_sk FROM `tbl_pengajuan_pak_inpassing`;";
			$max_nosk = $this->db->query($max_nomor_sk)->row_array();
			$num_nosk = $this->db->query($max_nomor_sk)->num_rows();
			
			if ($num_nosk <= 0){
				$no_sk = '000001';
			}else{
				$no_sk = $max_nosk['nomor_sk']+1;
			}
			
			$no_leng = strlen($no_sk);
			//echo $no_leng;
			$nol_bef = '';
			if ($no_leng == 1){$nol_bef = '00000';}
			if ($no_leng == 2){$nol_bef = '0000';}
			if ($no_leng == 3){$nol_bef = '000';}
			if ($no_leng == 4){$nol_bef = '00';}
			if ($no_leng == 5){$nol_bef = '0';}
			if ($no_leng >= 6){$nol_bef = '';}
			
			$no_sk_exis = $no_sk_tbl['nomor_sk_keputusan'];
			if ($no_sk_exis == ''){
				$no_sk_keputusan = $nol_bef.$no_sk.'/'.$_POST['inisial_daerah'].'/'.date('Y');
			}else{
				$no_sk_keputusan = $no_sk_tbl['nomor_sk_keputusan'];
			}
			
			$arayupdate = array(
							"keputusan" => $_POST['keputusan'],
							"instansi" => $_POST['instansi'],
							"masa_penilaian" => $masa_nilai,
							"tmt" => $tmt,
							"status" => 2,
							"nomor_sk_keputusan"=>$no_sk_keputusan,
							"pejabat_berwenang" => $_POST['pejabat'],
							"ditetapkan_di" => $_POST['ditetapkan_di']
						);
						
					$this->db->where('id', $idpak);
					$this->db->update("tbl_pengajuan_pak_inpassing", $arayupdate);
					
				
				if($this->db->trans_status() == false){
					$this->db->trans_rollback();
					return "Data not saved";
				} else{
					echo $this->db->trans_commit();
				}
		}else{
			header("Location: " . $this->host);		
		}
		
	}
	
	function savePakInpassing($type, $post){
		$this->load->model('madmin');
		if($this->auth){			
			$target_path = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_pak/";
			if(!is_dir($target_path)) {
				mkdir($target_path, 0777);
			}
			
			$query_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $this->auth['idx_sertifikasi_id']);
			$n_sert = str_replace(" ", "_", $query_sertifikasi['nama_aparatur']);
			$folder_pak = $query_sertifikasi['kode_sertifikasi']."-".strtolower($n_sert);
			$target_path2 = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_pak/".$folder_pak."/";
			if(!is_dir($target_path2)) {
				mkdir($target_path2, 0777);
			}
			
			if($_FILES['sk_masa']['name'] != ''){	
			
				$ext_p = explode('.',$_FILES['sk_masa']['name']);
				$exttemp_p = sizeof($ext_p) - 1;
				$extension_p = $ext_p[$exttemp_p];
				$ext = end((explode(".", $_FILES['sk_masa']['name'])));
						
				$file_p	= $_FILES['sk_masa']['name'];
				$tmp_p = $_FILES['sk_masa']['tmp_name'];
				$filename_a	= "file_SK_Jabatan.".$ext;
				//echo "FILE NAME : ".$target_path2.$filename_a	;
				
				$uploadfile_p = $target_path2.$filename_a;	
				move_uploaded_file($tmp_p, $uploadfile_p);
				if (!chmod($uploadfile_p, 0775)) {
					 echo "Gagal mengupload file persyaratan";
					 exit;
				}
			}else{
				$filename_a = null;
			}
			
			/*** ID Pendidikan for 80% n 20%
			6 & 7 SLTA, D1 eq 25
			5 DII eq 40
			4 DIII eq 60
			3 DIV & S1 eq 100
			2 S2 eq 150
			1 S3 eq 200
			*/
			
			$edu = $_POST['pendidikan'];
			$angdit = $_POST['angdit'];
			$v_pendidikan = "";
			$v_utama = "";
			$v_penunjang = "";
			
			if ($edu == '6' || $edu == '7'){ $v_pendidikan = 25; }
			else if ($edu == '5'){ $v_pendidikan = 40; }
			else if ($edu == '4'){ $v_pendidikan = 60; }
			else if ($edu == '3'){ $v_pendidikan = 100; }
			else if ($edu == '2'){ $v_pendidikan = 150; }
			else if ($edu == '1'){ $v_pendidikan = 200; }
			
			$angdit = $angdit - $v_pendidikan;
			$v_utama = 0.8*$angdit;
			$v_penunjang = 0.2*$angdit;
			
			$arayinserting = array(
						"id_peserta" => $_POST['usaid'],
						"id_tingkat" => $_POST['tingkat'],
						"id_golongan" => $_POST['golongan'],
						"id_pendidikan" => $_POST['pendidikan'],
						"id_masa_kerja" => $_POST['masa'],
						"id_angka_kredit_diajukan" => $_POST['id_kredit'],
						"total_angka_diajukan" => $_POST['angdit'],
						"angka_unsur_pendidikan" => $v_pendidikan,
						"angka_unsur_utama" => $v_utama,
						"angka_unsur_penunjang" => $v_penunjang,
						"file_sk_masa_jabatan"=>$filename_a,
						"status" => 0,
						"tgl_pengajuan"=>date('Y-m-d H:i:s')
					);
			$this->db->insert("tbl_pengajuan_pak_inpassing", $arayinserting);		
		
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			return "Data not saved";
		} else{
			return $this->db->trans_commit();
		}
			
		}else{
			header("Location: " . $this->host);		
		}
	
	}
	
	function approvePakInpassing($type, $post){
		if($this->autha){		
			$masa = $_POST['masa'];		
			$usid = $_POST['usaid'];			
			$idpak = $_POST['idpak'];	
			
			$pak_temp = $this->db->query("SELECT id_tingkat, id_golongan, id_pendidikan, id_masa_kerja 
				FROM `tbl_pengajuan_pak_inpassing` WHERE id = '$idpak';")->row_array();
			$edu = $pak_temp['id_pendidikan'];
			
			
			if ($masa != ''){
				$angka_kredit = $this->mpak->get_data('idx_angka_kredit_inpassing', 
						'row_array', $pak_temp['id_tingkat'],$pak_temp['id_golongan'],$pak_temp['id_pendidikan'],$masa);	
				$angdit = $angka_kredit['angka_kredit'];
			
			}else{				
				$angka_kredit = $this->mpak->get_data('idx_angka_kredit_inpassing', 
						'row_array', $pak_temp['id_tingkat'],$pak_temp['id_golongan'],$pak_temp['id_pendidikan'],$pak_temp['id_masa_kerja']);	
				$angdit = $angka_kredit['angka_kredit'];
			}
			
				$v_pendidikan = "";
				$v_utama = "";
				$v_penunjang = "";
				
				if ($edu == '6' || $edu == '7'){ $v_pendidikan = 25; }
				else if ($edu == '5'){ $v_pendidikan = 40; }
				else if ($edu == '4'){ $v_pendidikan = 60; }
				else if ($edu == '3'){ $v_pendidikan = 100; }
				else if ($edu == '2'){ $v_pendidikan = 150; }
				else if ($edu == '1'){ $v_pendidikan = 200; }
				
				$angdit = $angdit - $v_pendidikan;
				$v_utama = 0.8*$angdit;
				$v_penunjang = 0.2*$angdit;
				
				$arayupdate = array(
						"id_tingkat" => $pak_temp['id_tingkat'],
						"id_golongan" => $pak_temp['id_golongan'],
						"id_pendidikan" => $pak_temp['id_pendidikan'],
						"id_masa_kerja" => $pak_temp['id_masa_kerja'],
						"id_angka_kredit_diterima" => $angka_kredit['id'],
						"total_angka_diterima" => $angka_kredit['angka_kredit'],
						"angka_unsur_pendidikan" => $v_pendidikan,
						"angka_unsur_utama" => $v_utama,
						"angka_unsur_penunjang" => $v_penunjang,
						"status" => 1,
						"tgl_approval"=>date('Y-m-d H:i:s'),
						"catatan"=>$_POST['note']
					);
					
				$this->db->where('id', $idpak);
				$this->db->update("tbl_pengajuan_pak_inpassing", $arayupdate);
				
			
			if($this->db->trans_status() == false){
				$this->db->trans_rollback();
				return "Data not saved";
			} else{
				echo $this->db->trans_commit();
			}
			
		}else{
			header("Location: " . $this->host);		
		}
	}
	
	function gen_sertifikat($p1="", $p2="", $p3=""){
		if ($this->autha){
			$this->load->library('mlpdf');
			
			$data = $this->mpak->get_data('data_pribadi_peserta', 'row_array', $p1);
			
			$data_pak = $this->db->query("SELECT id FROM tbl_pengajuan_pak_inpassing WHERE id_peserta = '".$p1."' AND status = 1")->row_array();
			
			$data_peserta = $this->mpak->get_data('tbl_detail_peserta_cetak', 'row_array', $this->auth['id'], $p2);
			$data_sertifikasi = $this->db->get_where('idx_aparatur_sipil_negara', array('id'=>$p2))->row_array();
			$pak_inpassing_temp = $this->mpak->get_data('tbl_pengajuan_pak_inpassing', 'row_array', $p1, 'det_pengajuan', $p2);
			
				$id_tingkat = $pak_inpassing_temp['id_tingkat'];
				$id_pendidikan = $pak_inpassing_temp['id_pendidikan'];
				$tot_aju = $pak_inpassing_temp['total_angka_diterima'];
				
				if ( $id_tingkat == '1'){
					if ($id_pendidikan == 7 || $id_pendidikan == 6){
						if ($tot_aju >= '25' && $tot_aju < '40'){$jabatan = 'PELAKSANA PEMULA';}
						if ($tot_aju >= '40' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
					elseif ($id_pendidikan == 5){
						if ($tot_aju >= '40' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
					elseif ($id_pendidikan == 4){
						if ($tot_aju >= '60' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
				}
				elseif ( $id_tingkat == '2'){
					if ($id_pendidikan == 3){
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PERTAMA';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
					elseif ($id_pendidikan == 2){
						if ($tot_aju >= '150' && $tot_aju < '200'){$jabatan = 'PERTAMA';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
					elseif ($id_pendidikan == 1){
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
				}
					
			$this->smarty->assign('data_sertifikasi', $data_sertifikasi);	
			$this->smarty->assign('pak_val', $pak_inpassing_temp);		
			$this->smarty->assign('data', $data);			
			$this->smarty->assign('date', date('d-m-Y'));
			$this->smarty->assign('jab', $jabatan);
			
			//$htmlheader = $this->smarty->fetch('modul-admin/pak_inpassing/sertifikat_header.html');
			$htmlcontent = $this->smarty->fetch('modul-admin/pak_inpassing/sk_pak_pdf.html');
			
			$pdf = $this->mlpdf->load();
			$spdf = new mPDF('', 'A4', 0, '', 12.7, 12.7, 5, 20, 5, 2, 'P');
			$spdf->ignore_invalid_utf8 = true;
			// bukan sulap bukan sihir sim salabim jadi apa prok prok prok
			$spdf->allow_charset_conversion = true;     // which is already true by default
			$spdf->charset_in = 'iso-8859-2';  // set content encoding to iso
			$spdf->SetDisplayMode('fullpage');		
			//$spdf->SetHTMLHeader($htmlheader);
			$spdf->SetHTMLFooter('
				<div style="font-family:arial; font-size:8px; text-align:center; font-weight:bold;">
					Sistem Informasi Sertifikasi & Penilaian Kementerian Dalam Negeri
				</div>
			');				
			$spdf->SetProtection(array('print'));				
			$spdf->WriteHTML($htmlcontent); // write the HTML into the PDF
			//$spdf->Output('repositories/Dokumen_LS/LS_PDF/'.$filename.'.pdf', 'F'); // save to file because we can
			$spdf->Output('__repository/temp_sertifikat/'.$filename.'.pdf', 'I'); // view file
		}else{
			header("Location: " . $this->host);	
		}
	}
	
	function gen_keputusan($p1="", $p2="", $p3=""){
		if ($this->autha){
			$this->load->library('mlpdf');
			
			$data = $this->mpak->get_data('data_pribadi_peserta', 'row_array', $p1);
			
			$data_pak = $this->db->query("SELECT id FROM tbl_pengajuan_pak_inpassing WHERE id = '".$p2."' AND status = 1")->row_array();
			
			$data_peserta = $this->mpak->get_data('tbl_detail_peserta_cetak', 'row_array', $p1);
			$data_sertifikasi = $this->db->get_where('idx_aparatur_sipil_negara', array('id'=>$p2))->row_array();
			$pak_inpassing_temp = $this->mpak->get_data('tbl_pengajuan_pak_inpassing', 'row_array', $p1, 'det_pengajuan', $p2);
			
				$id_tingkat = $pak_inpassing_temp['id_tingkat'];
				$id_pendidikan = $pak_inpassing_temp['id_pendidikan'];
				$tot_aju = $pak_inpassing_temp['total_angka_diterima'];
				
				if ( $id_tingkat == '1'){
					if ($id_pendidikan == 7 || $id_pendidikan == 6){
						if ($tot_aju >= '25' && $tot_aju < '40'){$jabatan = 'PELAKSANA PEMULA';}
						if ($tot_aju >= '40' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
					elseif ($id_pendidikan == 5){
						if ($tot_aju >= '40' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
					elseif ($id_pendidikan == 4){
						if ($tot_aju >= '60' && $tot_aju < '100'){$jabatan = 'PELAKSANA';}
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PELAKSANA LANJUTAN';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'PENYELIA';}
					}
				}
				elseif ( $id_tingkat == '2'){
					if ($id_pendidikan == 3){
						if ($tot_aju >= '100' && $tot_aju < '200'){$jabatan = 'PERTAMA';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
					elseif ($id_pendidikan == 2){
						if ($tot_aju >= '150' && $tot_aju < '200'){$jabatan = 'PERTAMA';}
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
					elseif ($id_pendidikan == 1){
						if ($tot_aju >= '200' && $tot_aju < '400'){$jabatan = 'MUDA';}
						if ($tot_aju >= '400' && $tot_aju < '800'){$jabatan = 'MADYA';}
					}
				}
			
			$number = $pak_inpassing_temp['total_angka_diterima'].'.';
			$number = $this->mpak->convertNumber($number);
			$this->smarty->assign('number',$number);
					
			$this->smarty->assign('data_sertifikasi', $data_sertifikasi);	
			$this->smarty->assign('pak_val', $pak_inpassing_temp);		
			$this->smarty->assign('data', $data);		
			$this->smarty->assign('date', date('d-m-Y'));	
			$this->smarty->assign('jab', $jabatan);	
			
			//$htmlheader = $this->smarty->fetch('modul-admin/pak_inpassing/sertifikat_header.html');
			$htmlcontent = $this->smarty->fetch('modul-admin/pak_inpassing/sk_keputusan_pdf.html');
			
			$pdf = $this->mlpdf->load();
			$spdf = new mPDF('', 'A4', 0, '', 12.7, 12.7, 5, 20, 5, 2, 'P');
			$spdf->ignore_invalid_utf8 = true;
			// bukan sulap bukan sihir sim salabim jadi apa prok prok prok
			$spdf->allow_charset_conversion = true;     // which is already true by default
			$spdf->charset_in = 'iso-8859-2';  // set content encoding to iso
			$spdf->SetDisplayMode('fullpage');		
			//$spdf->SetHTMLHeader($htmlheader);
			$spdf->SetHTMLFooter('
				<div style="font-family:arial; font-size:8px; text-align:center; font-weight:bold;">
					Sistem Informasi Sertifikasi & Penilaian Kementerian Dalam Negeri
				</div>
			');				
			$spdf->SetProtection(array('print'));				
			$spdf->WriteHTML($htmlcontent); // write the HTML into the PDF
			//$spdf->Output('repositories/Dokumen_LS/LS_PDF/'.$filename.'.pdf', 'F'); // save to file because we can
			$spdf->Output('__repository/temp_sertifikat/'.$filename.'.pdf', 'I'); // view file
		}else{
			header("Location: " . $this->host);	
		}
	}
	
	function savePakForm($type, $post){
		$this->load->model('madmin');
		if($this->auth){
			//echo $this->auth['no_registrasi']."id :".$this->auth['idx_sertifikasi_id'];
			$target_path = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_pak/";
			if(!is_dir($target_path)) {
				mkdir($target_path, 0777);
			}
			
			$query_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $this->auth['idx_sertifikasi_id']);
			$n_sert = str_replace(" ", "_", $query_sertifikasi['nama_aparatur']);
						$folder_pak = $query_sertifikasi['kode_sertifikasi']."-".strtolower($n_sert);
						$target_path2 = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_pak/".$folder_pak."/";
						mkdir($target_path2, 0777);
			
			
			$countol = count($post['idxkomp'])-1;
			//echo $countol;
			for($i = 0; $i <= $countol; $i++){
				$id_kmp = $post['idxkomp'][$i];				
				
				if(isset($_POST['st_kmp_'.$i]) && $_POST['st_kmp_'.$i] == $i) 
				{
					//if($_FILES['fl_pdk']['name'][$i] != ''){			
						$ext_p = explode('.',$_FILES['fl_pdk_'.$i]['name']);
						$exttemp_p = sizeof($ext_p) - 1;
						$extension_p = $ext_p[$exttemp_p];
						$ext = end((explode(".", $_FILES['fl_pdk_'.$i]['name'])));
						
						//echo "Ext : ".$ext;
						//echo " Extention : ".$ext_p;
						
						$file_p	= $_FILES['fl_pdk_'.$i]['name'];
						$tmp_p = $_FILES['fl_pdk_'.$i]['tmp_name'];
						$filename_a	= "file_kompetensi_".$i."(".$post['idxkomp'][$i].").".$ext;
						//echo "FILE NAME : ".$target_path2.$filename_a	;
												
						$uploadfile_p = $target_path2.$filename_a;	
						move_uploaded_file($tmp_p, $uploadfile_p);
						if (!chmod($uploadfile_p, 0775)) {
							 echo "Gagal mengupload file persyaratan";
							 exit;
						}
						
					/*}else{
						$filename_a = null;
					}*/
					$arayinserting = array(
						"idx_angka_kredit" => $post['st_kmp_'.$i],
						"idx_peserta" => $this->auth['id'],
						"status_pak" => 0,
						"tgl_pengajuan_pak"=>date('Y-m-d H:i:s'),
						"file_pendukung"=>$filename_a
					);
					$this->db->insert("tbl_pengajuan_pak", $arayinserting);
				}
				
			}
		}else{
			header("Location: " . $this->host);		
		}
		
	}
}