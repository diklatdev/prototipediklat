<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class modul_pak extends SHIPMENT_Controller{
	function __construct(){
		parent::__construct();		
		$this->auth = unserialize(base64_decode($this->session->userdata('d1kl4tkem3nd49r1-p0rt4L')));
		$this->host	= $this->config->item('base_url');
		$host = $this->host;
		
		$this->smarty->assign('host',$this->host);
		$this->smarty->assign('auth', $this->auth);
		$this->load->model("mportal");
	}
	
	function index() {
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
			$this->smarty->assign('konten', "modul-portal/pak/form_pak");
			$this->smarty->display('index-portal.html');
		}
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
	
	function getdisplay($type="", $p1="", $p2="", $p3=""){
		$modul = "front/";
		switch($type){			
			case "dupak-berhasil":
			case "dupak-gagal":
				$konten = "modul-portal/status_submit_semua";
				$this->smarty->assign('type', $type);
			break;
		}		
		$this->smarty->assign('modul',$modul);
		$this->smarty->assign('konten', $konten);
		$this->smarty->display('index-portal.html');
		
	}
	
	function savedatatodb($type=""){
		$post = array();
        foreach($_POST as $k=>$v) $post[$k] = $this->db->escape_str($this->input->post($k));
		
		//echo count($post['idxfl']);
		//exit;
		/*
		echo "<pre>";
		print_r($post);
		exit;
		//*/
		
		$savenya =  $this->savePakForm($type, $post);
		if($savenya == 1){
			if($type == "pak"){
				$this->getdisplay("dupak-berhasil");
			}
		}else{
			if($type == "pak"){
				$this->getdisplay("dupak-gagal");
			}
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