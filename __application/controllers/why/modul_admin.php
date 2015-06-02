<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class modul_admin extends SHIPMENT_Controller{
	function __construct(){
		parent::__construct();		
		$this->auth = unserialize(base64_decode($this->session->userdata('d1kl4tkem3nd49r1')));
		$this->host	= $this->config->item('base_url');
		$host = $this->host;
		
		$this->smarty->assign('host',$this->host);
		$this->smarty->assign('auth', $this->auth);
		$this->load->model("why/madmin");
	}
	
	function index() {
		if($this->auth) {
			$module = $this->madmin->get_data('get_module', 'result_array');
			$submodule = $this->madmin->get_data('get_submodule', 'result_array');
			
			foreach($module as $k=>$v){
				$menu = strtolower($v['nama_module']);
				$menu = str_replace(" ","", $menu);
				$menu = str_replace("/","", $menu);
				
				$access = $this->madmin->is_access($v['function_id'], $this->auth['level_admin']);
				$this->smarty->assign($menu, $access);
			}
			
			foreach($submodule as $y=>$t){
				$menu = strtolower($t['nama_submodule']);
				$menu = str_replace(" ","", $menu);
				$menu = str_replace("/","", $menu);
				
				$access = $this->madmin->is_access($t['function_id'], $this->auth['level_admin']);
				$this->smarty->assign($menu, $access);
			}
			
			if($this->auth['level_admin'] == 99){
				$monitoring_jml_peserta = $this->madmin->get_data('monitoring_jml_peserta', 'result_array');
				$peserta_registrasi = $this->madmin->get_data('tbl_data_peserta_diklat', 'result_array');
				$peserta_assesmen = $this->madmin->get_data('tbl_peserta_asesmen', 'result_array');
				$peserta_ujitest = $this->madmin->get_data('tbl_peserta_ujitulis', 'result_array');
				$peserta_ujisimulasi = $this->madmin->get_data('tbl_peserta_simulasi', 'result_array');
				$peserta_wawancara = $this->madmin->get_data('tbl_peserta_wawancara', 'result_array');
				
				//print_r($peserta_registrasi);exit;
				
				$this->smarty->assign('konten', "dashboard-admin/main-page");		
				$this->smarty->assign('jmlpeserta', $monitoring_jml_peserta);		
				$this->smarty->assign('peserta_registrasi', $peserta_registrasi);		
				$this->smarty->assign('peserta_assesmen', $peserta_assesmen);		
				$this->smarty->assign('peserta_ujitest', $peserta_ujitest);		
				$this->smarty->assign('peserta_ujisimulasi', $peserta_ujisimulasi);		
				$this->smarty->assign('peserta_wawancara', $peserta_wawancara);		
			}elseif($this->auth['level_admin'] == 2){
				$this->smarty->assign('konten', "dashboard-admin/main-page-asesor");
			}elseif($this->auth['level_admin'] == 7){
				$this->smarty->assign('konten', "dashboard-admin/main-page-asesor");
			}
			
			
			$this->smarty->display('index-admin.html');
		}else {
			$this->smarty->display('login.html');
		}
	}
	
	function getdisplay($type="", $p1="", $p2="", $p3=""){
		switch($type){
			case "manajemen_peserta":
				$content = "modul-admin/manajemen_peserta/main-reg.html";
				$data = $this->madmin->get_data("tbl_data_peserta_diklat","result_array");
				$this->smarty->assign("data", $data);
			break;
			case "detail_peserta":
				$userid = $this->input->post("id_u");
				$idxsertifikasi_id = $this->input->post("idx_s");
				$kdreg_diklat = $this->input->post("kdr");
				
				$content = "modul-admin/manajemen_peserta/form-det-peserta.html";
				$data = $this->madmin->get_data("tbl_data_peserta_detail", "row_array", $userid);
				$data_file_persyaratan = $this->madmin->get_data("tbl_persyaratan", "result_array", $userid, $idxsertifikasi_id, $kdreg_diklat);
				$folder_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $idxsertifikasi_id);
				$folder = $folder_sertifikasi['kode_sertifikasi']."-".strtolower( str_replace(" ", "_", $folder_sertifikasi['nama_aparatur']) );
				
				$this->smarty->assign("data", $data);
				$this->smarty->assign("data_file_persyaratan", $data_file_persyaratan);
				$this->smarty->assign("folder_sertifikasi", $folder);
				$this->smarty->assign("kdreg_diklat", $kdreg_diklat);
			break;
			case "asesmen_mandiri":
				$content = "modul-admin/asesmen_mandiri/main-asesmen.html";
				$data = $this->madmin->get_data("tbl_peserta_asesmen","result_array");
				$this->smarty->assign("data", $data);
			break;
			case "detail_asesmen":
				$userid = $this->input->post("id_uny");
				$namalengkap = $this->input->post("nm_l");
				$namaaparatur = $this->input->post("ap_n");
				$noreg = $this->input->post("rg");
				$id_sertifikasi = $this->input->post("id_sert");
				$tanggal_ujian = $this->input->post("tu");
				$koderegdiklat = $this->input->post("kdr");
				
				$content = "modul-admin/asesmen_mandiri/form-det-assesmen.html";
				$data_kompetensi_user = $this->madmin->get_data("tbl_test_assemen", "result_array", $userid, $id_sertifikasi, $koderegdiklat);
				
				$query_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $id_sertifikasi);
				$n_sert = str_replace(" ", "_", $query_sertifikasi['nama_aparatur']);
				$folder_sertifikasi = $query_sertifikasi['kode_sertifikasi']."-".strtolower($n_sert);
				
				$kompeten = 0;
				$tidak_kompeten = 0;
				$jml_kompetensi = count($data_kompetensi_user);
				foreach($data_kompetensi_user as $k=>$v){
					if($v['penilaian'] == 'K'){
						$kompeten++;
					}else{
						$tidak_kompeten++;
					}
				}
				$persentase_rekomendasi = ($kompeten/$jml_kompetensi) * 100;
				
				if($persentase_rekomendasi > 60){
					$rekomendasi = "<font color='green'>MEMENUHI PERSYARATAN SERTIFIKASI</font>";
				}else{
					$rekomendasi = "<font color='red'>TIDAK MEMENUHI PERSYARATAN SERTIFIKASI</font>";
				}
				
				$this->smarty->assign("userid", $userid);
				$this->smarty->assign("nama_lengkap", $namalengkap);
				$this->smarty->assign("sertifikasi", $namaaparatur);
				$this->smarty->assign("no_reg", $noreg);
				$this->smarty->assign("folder_sertifikasi", $folder_sertifikasi);
				$this->smarty->assign("id_sertifikasi", $id_sertifikasi);
				$this->smarty->assign("tanggal_ujian", $tanggal_ujian);
				$this->smarty->assign("koderegdiklat", $koderegdiklat);
				$this->smarty->assign("kompeten", $kompeten);
				$this->smarty->assign("tidak_kompeten", $tidak_kompeten);
				$this->smarty->assign("persentase_rekomendasi", number_format($persentase_rekomendasi,1));
				$this->smarty->assign("rekomendasi", $rekomendasi);
				$this->smarty->assign("data", $data_kompetensi_user);
			break;
			case "pembayaran_online":
				$content = "modul-admin/pembayaran_online/main-pembayaran.html";
				$data = $this->madmin->get_data("tbl_peserta_pembayaran","result_array");
				$this->smarty->assign("data", $data);
			break;		
			case "detail_pembayaran":
				$userid = $this->input->post("id_uny");
				$noreg = $this->input->post("nr");
				$namalengkap = $this->input->post("nm_l");
				$nip = $this->input->post("np");
				$namaaparatur = $this->input->post("ap_n");
				$tgl_pembayaran = $this->input->post("tgp");
				$tgl_konfirm = $this->input->post("tgk");
				$filenya = $this->input->post("flpm");
				$id_sertifikasi = $this->input->post("idxsert");
				$metode_pembayaran = $this->input->post("mtdpm");
				$kode_pembayaran = $this->input->post("kdppm");
				$kode_voucher = $this->input->post("kdvpm");
				$kdreg_diklat = $this->input->post("kdrpm");
				
				$query_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $id_sertifikasi);
				$n_sert = str_replace(" ", "_", $query_sertifikasi['nama_aparatur']);
				$folder_sertifikasi = $query_sertifikasi['kode_sertifikasi']."-".strtolower($n_sert);
				
				$content = "modul-admin/pembayaran_online/form-det-pembayaran.html";
				$this->smarty->assign("userid", $userid);
				$this->smarty->assign("noreg", $noreg);
				$this->smarty->assign("namalengkap", $namalengkap);
				$this->smarty->assign("nip", $nip);
				$this->smarty->assign("sertifikasi", $namaaparatur);
				$this->smarty->assign("tgl_pembayaran", $tgl_pembayaran);
				$this->smarty->assign("tgl_konfirm", $tgl_konfirm);
				$this->smarty->assign("filenya", $filenya);
				$this->smarty->assign("metode_pembayaran", $metode_pembayaran);
				$this->smarty->assign("kode_pembayaran", $kode_pembayaran);
				$this->smarty->assign("kode_voucher", $kode_voucher);
				$this->smarty->assign("id_sertifikasi", $id_sertifikasi);
				$this->smarty->assign("kdreg_diklat", $kdreg_diklat);
				$this->smarty->assign("folder_sertifikasi", $folder_sertifikasi);
			break;			
			case "ujitulis_online":
				$content = "modul-admin/ujitulis_online/main-ujitulis.html";
				$data = $this->madmin->get_data("tbl_peserta_ujitulis","result_array");
				$data_peserta_ujitest = $this->madmin->get_data("tbl_peserta_ujitulis_sekarang","result_array");
				$this->smarty->assign("data", $data);
				$this->smarty->assign("data_peserta_ujitest", $data_peserta_ujitest);
			break;			
			case "detail_ujitulis":
				$userid = $this->input->post("id_uny");
				$noreg = $this->input->post("nr");
				$namalengkap = $this->input->post("nm_l");
				$nip = $this->input->post("np");
				$namaaparatur = $this->input->post("ap_n");
				$idx_sertifikasi_id = $this->input->post("ap_dx");
				$tgl_ujian = $this->input->post("tgu");
				$jawabansalah = $this->input->post("js");
				$jawabanbenar = $this->input->post("jb");
				
				$total_soal = ($jawabansalah + $jawabanbenar);
				$persentase_rekomendasi = ($jawabanbenar/$total_soal) * 100;
				
				if($persentase_rekomendasi > 60){
					$rekomendasi = "<font color='green'>MEMENUHI PERSYARATAN SERTIFIKASI</font>";
				}else{
					$rekomendasi = "<font color='red'>TIDAK MEMENUHI PERSYARATAN SERTIFIKASI</font>";
				}
				
				$content = "modul-admin/ujitulis_online/form-det-ujitulis.html";
				$data_ujionline_user = $this->madmin->get_data("tbl_test_ujionline", "result_array", $userid, $idx_sertifikasi_id);
				$this->smarty->assign("userid", $userid);
				$this->smarty->assign("noreg", $noreg);
				$this->smarty->assign("namalengkap", $namalengkap);
				$this->smarty->assign("nip", $nip);
				$this->smarty->assign("sertifikasi", $namaaparatur);
				$this->smarty->assign("idx_sertifikasi_id", $idx_sertifikasi_id);
				$this->smarty->assign("tgl_ujian", $tgl_ujian);
				$this->smarty->assign("jawabansalah", $jawabansalah);
				$this->smarty->assign("jawabanbenar", $jawabanbenar);
				$this->smarty->assign("data", $data_ujionline_user);
				$this->smarty->assign("persentase_rekomendasi", $persentase_rekomendasi);
				$this->smarty->assign("rekomendasi", $rekomendasi);
				$this->smarty->assign("total_soal", $total_soal);
			break;
			case "penjadwalan":
				$content = "modul-admin/penjadwalan/main-penjadwalan.html";
				$data = $this->madmin->get_data("tbl_penjadwalan","result_array");
				$this->smarty->assign("data", $data);
			break;
			case "form_penjadwalan":
				$content = "modul-admin/penjadwalan/form-penjadwalan.html";
				$this->smarty->assign('cmbtuk', $this->fillcombo('idx_tuk', 'return') );
				$this->smarty->assign("editstatus", $p1);
			break;
			case "simulasi":
				$content = "modul-admin/uji_simulasi/main-simulasi.html";
				$data = $this->madmin->get_data("tbl_peserta_simulasi","result_array");
				$this->smarty->assign('data', $data);
			break;
			case "detail_simulasi":
				$userid = $this->input->post("cdn");
				$noreg = $this->input->post("nreg");
				$namalengkap = $this->input->post("nmlng");
				$idx_sertifikasi_id = $this->input->post("dxisert");
				$namaaparatur = $this->input->post("nmapart");
				$kdreg_diklat = $this->input->post("kdr");
				
				$content = "modul-admin/uji_simulasi/form-ujisimulasi.html";
				$this->smarty->assign("userid", $userid);
				$this->smarty->assign("noreg", $noreg);
				$this->smarty->assign("namalengkap", $namalengkap);
				$this->smarty->assign("idx_sertifikasi_id", $idx_sertifikasi_id);
				$this->smarty->assign("namaaparatur", $namaaparatur);
				$this->smarty->assign("kdreg_diklat", $kdreg_diklat);
			break;
			case "wawancara":
				$content = "modul-admin/wawancara/main-wawancara.html";
				$data = $this->madmin->get_data("tbl_peserta_wawancara","result_array");
				$this->smarty->assign("data", $data);
			break;
			case "detail_wawancara":
				$id_peserta = $this->input->post('cdn');
				$idx_sertifikasi_id = $this->input->post('xdiser');
				$kdreg_diklat = $this->input->post('kdr');
				$content = "modul-admin/wawancara/form-det-wawancara.html";
				
				$datapeserta = $this->madmin->get_data('tbl_data_peserta_detail', "row_array", $id_peserta);
				$data_file_persyaratan = $this->madmin->get_data("tbl_persyaratan", "result_array", $id_peserta, $idx_sertifikasi_id, $kdreg_diklat);
				
				$data_asesmen_header = $this->madmin->get_data("tbl_asesmen_header", "row_array", $id_peserta, $idx_sertifikasi_id, $kdreg_diklat);
				$data_asesmen = $this->madmin->get_data("tbl_test_assemen", "result_array", $id_peserta, $idx_sertifikasi_id, $kdreg_diklat);
				
				$data_ujian_header = $this->madmin->get_data("tbl_uji_header", "row_array", $id_peserta, $idx_sertifikasi_id, $kdreg_diklat);
				
				$data_ujian_simulasi = $this->madmin->get_data("tbl_uji_simulasi_header", "row_array", $id_peserta, $idx_sertifikasi_id, $kdreg_diklat);
				
				$folder_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $idx_sertifikasi_id);
				$folder = $folder_sertifikasi['kode_sertifikasi']."-".strtolower( str_replace(" ", "_", $folder_sertifikasi['nama_aparatur']) );				
				
				$this->smarty->assign("id_peserta", $id_peserta);
				$this->smarty->assign("idx_sertifikasi_id", $idx_sertifikasi_id);
				$this->smarty->assign("kdreg_diklat", $kdreg_diklat);
				$this->smarty->assign("datapeserta", $datapeserta);
				$this->smarty->assign("data_file_persyaratan", $data_file_persyaratan);
				$this->smarty->assign("data_asesmen_header", $data_asesmen_header);
				$this->smarty->assign("data_asesmen", $data_asesmen);
				$this->smarty->assign("data_ujian_header", $data_ujian_header);
				$this->smarty->assign("data_ujian_simulasi", $data_ujian_simulasi);
				$this->smarty->assign("folder_sertifikasi", $folder);
				
				//$data_ujian = $this->madmin->get_data("tbl_test_ujionline", "result_array", $id_peserta, $idx_sertifikasi_id, $kdreg_diklat);
				//$this->smarty->assign("data_ujian", $data_ujian);
			break;
			case "hasil_akhir":
				$content = "modul-admin/hasil_akhir/main-hasil.html";
				$data = $this->madmin->get_data("tbl_peserta_hasil","result_array");
				$this->smarty->assign("data", $data);
			break;
			case "detail_hasil":
			case "detail_remedial":
				$id_peserta = $this->input->post('cdn');
				$idx_sertifikasi_id = $this->input->post('dxisert');
				$no_reg = $this->input->post('nreg');
				$nama_lengkap = $this->input->post('nmlng');
				$nip = $this->input->post('ipin');
				$kdreg_diklat = $this->input->post('kdr');
				
				$content = "modul-admin/hasil_akhir/form-det-hasil.html";
				$datapeserta = $this->madmin->get_data('tbl_data_peserta_detail', "row_array", $id_peserta);				
				$data_asesmen_header = $this->madmin->get_data("tbl_asesmen_header", "row_array", $id_peserta, $idx_sertifikasi_id, $kdreg_diklat);
				$data_ujian_header = $this->madmin->get_data("tbl_uji_header", "row_array", $id_peserta, $idx_sertifikasi_id, $kdreg_diklat);
				$data_ujian_simulasi = $this->madmin->get_data("tbl_uji_simulasi_header", "row_array", $id_peserta, $idx_sertifikasi_id, $kdreg_diklat);
				$data_wawancara_header = $this->madmin->get_data("tbl_wawancara_header", "row_array", $id_peserta, $idx_sertifikasi_id, $kdreg_diklat);
				
				$this->smarty->assign("id_peserta", $id_peserta);
				$this->smarty->assign("idx_sertifikasi_id", $idx_sertifikasi_id);
				$this->smarty->assign("no_reg", $no_reg);
				$this->smarty->assign("nama_lengkap", $nama_lengkap);
				$this->smarty->assign("nip", $nip);
				$this->smarty->assign("kdreg_diklat", $kdreg_diklat);
				
				$this->smarty->assign("datapeserta", $datapeserta);
				$this->smarty->assign("data_asesmen_header", $data_asesmen_header);
				$this->smarty->assign("data_ujian_header", $data_ujian_header);
				$this->smarty->assign("data_ujian_simulasi", $data_ujian_simulasi);
				$this->smarty->assign("data_wawancara_header", $data_wawancara_header);
				$this->smarty->assign("type", $type);
			break;
			case "print_sertifikat":
				$content = "modul-admin/cetak_sertifikat/main-cetak.html";
				$data = $this->madmin->get_data("tbl_peserta_cetak_sertifikat","result_array");
				$this->smarty->assign("data", $data);
			break;
			case "peserta_tidak_lulus":
				$content = "modul-admin/hasil_akhir/main-hasil.html";
				$data = $this->madmin->get_data("tbl_peserta_tidak_lulus","result_array");
				$this->smarty->assign("data", $data);
				$this->smarty->assign("type", $type);
			break;
			
			case "voucher":
				$content = "modul-admin/manajemen_voucher/main-voucher.html";
				$data = $this->madmin->get_data("idx_voucher","result_array");
				$this->smarty->assign("data", $data);
			break;
			case "form_voucher":
				$content = "modul-admin/manajemen_voucher/form-voucher.html";
			break;
			case "form_kirim_voucher":
				$id_voucher = $this->input->post('idvcf');
				$content = "modul-admin/manajemen_voucher/form-kirim-voucher.html";
				$this->smarty->assign('idvcf', $id_voucher);
			break;			
			
			case "petunjuk_dokumen":
				$content = "modul-admin/konten_petunjukdokumen/main-petunjuk.html";
				$data = $this->madmin->get_data("tbl_petunjukdokumen","result_array");
				$this->smarty->assign("data", $data);
			break;
			case "form_petunjuk_dokumen":
				$editstatus = $this->input->post('editstatus');
				if($editstatus == 'edit'){
					$id = $this->input->post('isdx');
					$data_edit = $this->madmin->get_data('tbl_petunjukdokumen_detail', 'row_array', $id);
					$this->smarty->assign("data", $data_edit);
				}
				$content = "modul-admin/konten_petunjukdokumen/form-petunjuk.html";
				$this->smarty->assign("editstatus", $editstatus);
			break;
			case "berita":
				$content = "modul-admin/konten_berita/main-berita.html";
				$data = $this->madmin->get_data("tbl_berita", "result_array");
				$this->smarty->assign("data", $data);
			break;
			case "form_berita":
				$editstatus = $this->input->post('editstatus');
				if($editstatus == 'edit'){
					$id = $this->input->post('isdx');
					$data_edit = $this->madmin->get_data('tbl_berita_detail', 'row_array', $id);
					$this->smarty->assign("data", $data_edit);
				}
				$content = "modul-admin/konten_berita/form-berita.html";
				$this->smarty->assign("editstatus", $editstatus);
			break;
			case "faq":
				$content = "modul-admin/konten_faq/main-faq.html";
				$data = $this->madmin->get_data("tbl_faq", "result_array");
				$this->smarty->assign("data", $data);
			break;
			case "form_faq":
				$editstatus = $this->input->post('editstatus');
				if($editstatus == 'edit'){
					$id = $this->input->post('isdx');
					$data_edit = $this->madmin->get_data('tbl_faq_detail', 'row_array', $id);
					$this->smarty->assign("data", $data_edit);
				}
				$content = "modul-admin/konten_faq/form-faq.html";
				$this->smarty->assign("editstatus", $editstatus);
			break;

		}
		$this->smarty->assign('type', $type);
		$this->smarty->display($content);
	}
	
	function getdatasearch($type="", $p1=""){
		$data = $this->madmin->get_data($type, 'result_array');
		$this->smarty->assign('type', $type);
		$this->smarty->assign('data', $data);
		$this->smarty->display('modul-admin/tabel-cari.html');
	}
	
	function gettabelpaging($type=""){
		$data = $this->madmin->get_data($type, "result_array");
		$perpage = 10;
		$page = (($this->input->post('page')) ? $this->input->post('page') : 0 );
		
		$this->smarty->assign('type', $type);
		$this->smarty->assign('data', $data);
		$this->smarty->assign('idx', (($page * $perpage)+1) );
		$this->smarty->display('modul-admin/tabel-paging.html');
	}
	
	function simpansavedbx($type=""){
		$post = array();
        //foreach($_POST as $k=>$v) $post[$k] = $this->db->escape_str($this->input->post($k));
        foreach($_POST as $k=>$v) $post[$k] = $this->input->post($k);
		
		/*
		echo "<pre>";
		print_r($post);
		exit;
		//*/
		
		echo $this->madmin->simpansavedatabase($type, $post);
	}
	
	function gen_sertifikat($p1="", $p2="", $p3="", $p4=""){
		
		$cek_data = $this->db->get_where('tbl_log_cetak_sertifikat', array('tbl_data_peserta_id'=>$p1, 'idx_sertifikasi_id'=>$p2))->row_array();
		if(!$cek_data){			
			$this->db->update('tbl_step_peserta', array('status'=>0), array('tbl_data_peserta_id'=>$p1, 'idx_sertifikasi_id'=>$p2, 'kdreg_diklat'=>$p4) );
			$this->db->update('tbl_data_diklat', array('status'=>0, 'penilaian'=>$p3, 'tanggal_hasil'=>date('Y-m-d') ), array('tbl_data_peserta_id'=>$p1, 'idx_sertifikasi_id'=>$p2, 'kdreg_diklat'=>$p4) );
			$this->db->update('tbl_asessmen_mandiri_header', array('status_data'=>0), array('tbl_data_peserta_id'=>$p1, 'idx_sertifikasi_id'=>$p2, 'kdreg_diklat'=>$p4) );
			$this->db->update('tbl_pembayaran_header', array('status_data'=>0), array('tbl_data_peserta_id'=>$p1, 'idx_sertifikasi_id'=>$p2, 'kdreg_diklat'=>$p4) );
			$this->db->update('tbl_daftar_test', array('status_data'=>0), array('tbl_data_peserta_id'=>$p1, 'idx_sertifikasi_id'=>$p2, 'kdreg_diklat'=>$p4) );
			$this->db->update('tbl_ujitest_header', array('status_data'=>0), array('tbl_data_peserta_id'=>$p1, 'idx_sertifikasi_id'=>$p2, 'kdreg_diklat'=>$p4) );
			$this->db->update('tbl_wawancara_header', array('status_data'=>0), array('tbl_data_peserta_id'=>$p1, 'idx_sertifikasi_id'=>$p2, 'kdreg_diklat'=>$p4) );
			$this->db->update('tbl_hasil_akhir', array('status_data'=>0), array('tbl_data_peserta_id'=>$p1, 'idx_sertifikasi_id'=>$p2, 'kdreg_diklat'=>$p4) );
		}
		
		$array_log = array(
			"tbl_data_peserta_id" => $p1,
			"idx_sertifikasi_id" => $p2,
			"tanggal_cetak" => date('Y-m-d'),
			"nama_petugas" => $this->auth['real_name'],
			"kdreg_diklat" => $p4,
		);
		$this->db->insert('tbl_log_cetak_sertifikat', $array_log);
		

		//$this->load->helper('barcode_helper');
		$this->load->library('mlpdf');
		$this->load->library('ciqrcode');
		
		$data_peserta = $this->madmin->get_data('tbl_detail_peserta_cetak', 'row_array', $p1, $p2);
		$data_sertifikasi = $this->db->get_where('idx_aparatur_sipil_negara', array('id'=>$p2))->row_array();
		$data_unit_kompetensi = $this->db->get_where('idx_unit_kompetensi', array('idx_aparatur_id'=>$p2) )->result_array();
		$tanggal_penetapan = $this->db->get_where('tbl_hasil_akhir', array('tbl_data_peserta_id'=>$p1, 'idx_sertifikasi_id'=>$p2, 'kdreg_diklat'=>$p4) )->row_array();
		
		$this->smarty->assign('data_sertifikasi', $data_sertifikasi);
		$this->smarty->assign('data_unit_kompetensi', $data_unit_kompetensi);
		$this->smarty->assign('tanggal_penetapan', $tanggal_penetapan['tgl_verifikasi']);
		
		//$barcode_isi = $this->auth['no_registrasi'];
		//$nama_file_barcode = $this->auth['nip']."-".$this->auth['idx_tujuan_assesmen_id'];
		//$filename = $nama_file_barcode;
		
		$params['data'] = $data_peserta['no_registrasi'].'-'.$data_sertifikasi['nama_aparatur'];
		$params['level'] = 'H';
		$params['size'] = 2;
		$params['savename'] = './__repository/temp_qrcode/sertifikat/'.$data_peserta['no_registrasi'].'-'.$p2.'.png';
		$this->ciqrcode->generate($params);
		
		
		//generatebarcode($barcode_isi, '40', 'horizontal', 'code128', $nama_file_barcode);
		$qrcode_isi = $data_peserta['no_registrasi'].'-'.$p2.'.png';
		$this->smarty->assign('qrcode_isi', $qrcode_isi);	
		$this->smarty->assign('data_peserta', $data_peserta);
		$this->smarty->assign('lulus_tidak', $p3);
		
		$htmlheader = $this->smarty->fetch('modul-admin/cetak_sertifikat/sertifikat_header.html');
		$htmlcontent = $this->smarty->fetch('modul-admin/cetak_sertifikat/sertifikat_pdf.html');
		
		//echo $htmlcontent;exit;
		
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
				&nbsp;
			</div>
		');				
		$spdf->SetProtection(array('print'));				
		$spdf->WriteHTML($htmlcontent); // write the HTML into the PDF
		//$spdf->Output('repositories/Dokumen_LS/LS_PDF/'.$filename.'.pdf', 'F'); // save to file because we can
		$spdf->Output('repository/temp_sertifikat/'.$filename.'.pdf', 'I'); // view file
	}
	
	function kirimvoucher(){
		$this->load->library('lib');
		$id = $this->input->post('idxv');
		$email = $this->input->post('email');
		$data = $this->db->get_where('idx_voucher', array('id'=>$id) )->row_array();
		
		$kirimemail = $this->lib->kirimemail('email_voucher', $email, $data['kode_voucher'], $data['tgl_terbit']);
		if($kirimemail == 1){
			$array_update = array(
				'status_data' => 0,
				'dikirim_ke' => $email,
				'tgl_kirim' => date('Y-m-d'),
				'nama_petugas' => $this->auth['real_name'],
			);
			echo $this->db->update('idx_voucher', $array_update, array('id'=>$id));
		}else{
			echo $kirimemail;
		}
		
	}
	
	function generate_voucher(){
		$data = $this->madmin->get_data('idx_voucher', 'result_array', 'cetak');
		
		$this->smarty->assign('data', $data);
		$htmlcontent = $this->smarty->fetch('modul-admin/manajemen_voucher/voucher_pdf.html');
		
		$this->load->library('mlpdf');	
		$pdf = $this->mlpdf->load();
		$spdf = new mPDF('', 'A6-L', 0, '', 3, 3, 3, 3, 0, 0);
		$spdf->ignore_invalid_utf8 = true;
		$spdf->allow_charset_conversion = true;     // which is already true by default
		$spdf->charset_in = 'iso-8859-2';  // set content encoding to iso
		$spdf->SetDisplayMode('fullpage');		
		
		$spdf->SetHTMLFooter('
			<div style="font-family:arial; font-size:8px; text-align:center; font-weight:bold;">
				Sistem Informasi Penilaian Kompetensi & Sertifikasi Kementerian Dalam Negeri
			</div>
		');				
		
		$spdf->SetProtection(array('print'));				
		$spdf->WriteHTML($htmlcontent); // write the HTML into the PDF
		//$spdf->Output('repositories/Dokumen_LS/LS_PDF/'.$filename.'.pdf', 'F'); // save to file because we can
		$spdf->Output('repository/temp_sertifikat/'.$filename.'.pdf', 'I'); // view file
	}


	function fillcombo($type="", $balikan="", $p1="", $p2="", $p3=""){
		$this->load->helper('db_helper');
		$this->load->model("why/mportal");
		
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
	
	function imel(){
		$html = "
			<table width='100%'>
				<tr>
					<td style='background-color:#124162;font-size:18px;color:#fff;'>
						Lembaga Sertifikasi Profesi Pemerintahan Daerah - Kementerian Dalam Negeri
					</td>
				</tr>
				<tr>
					<td style='background-color:#ECECEC;font-size:16px;color:#fff;'>
						Voucher APBN Sertifikasi
					</td>
				</tr>
				<tr>
					<td style='background-color:#ECECEC;font-size:16px;color:#fff;'>
						Kode Voucher : Peler <br/>
						Tanggal Terbit : Kuda <br/>
					</td>
				</tr>
				<tr>
					<td align='center' style='background-color:#124162;font-size:12px;color:#fff;'>
						Sistem Informasi Penilaian Kompetensi & Sertifikasi Pemerintahan Dalam Negeri
					</td>
				</tr>
			</table>
		";
		$subject = "Distribusi Voucher APBN Sertifikasi Profesi Pemerintahan Daerah - Kementerian Dalam Negeri";
		
		$config = Array(
              'protocol' => 'smtp',
              'smtp_host' => 'students.paramadina.ac.id',
              'smtp_port' => 25,
              'smtp_user' => 'orangbaik@students.paramadina.ac.id', // change it to yours
              'smtp_pass' => 'S@l4mb3l@k4ng', // change it to yours
              'mailtype' => 'html',
              'charset' => 'iso-8859-1',
              'wordwrap' => TRUE
        );   
		
		$this->load->library('email', $config);
		
		$this->email->from("orangbaik@students.paramadina.ac.id");
		$this->email->to("triwahyunugroho11@gmai.com");
		$this->email->subject($subject);
		$this->email->message($html);
		$this->email->set_newline("\r\n");
		if($this->email->send())
			//echo "<h3> SUKSES EMAIL ke $email </h3>";
			echo 1;
		else
			//echo $this->email->print_debugger();
			echo $this->email->print_debugger();		
	}
	
	
}