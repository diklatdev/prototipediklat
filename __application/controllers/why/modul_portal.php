<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class modul_portal extends SHIPMENT_Controller{
	function __construct(){
		parent::__construct();		
		$this->auth = unserialize(base64_decode($this->session->userdata('d1kl4tkem3nd49r1-p0rt4L')));
		$this->host	= $this->config->item('base_url');
		$host = $this->host;
		
		$this->smarty->assign('host',$this->host);
		$this->smarty->assign('auth', $this->auth);
		$this->load->model("why/mportal");
	}
	
	function index(){
		$this->load->model('why/madmin');
		$modul = "front/";
		if($this->auth){
			$data_status_peserta = $this->mportal->get_data("status_peserta", "row_array");
			$data_peserta_detail = $this->madmin->get_data("tbl_data_peserta_detail", "row_array", $this->auth['id']);			
			
			if(!$this->auth['idx_sertifikasi_id']){
				$data_diklat_terakhir = $this->madmin->get_data("tbl_diklat_terakhir", "row_array", $this->auth['id']);
				$data_record_diklat = $this->madmin->get_data("tbl_record_diklat", "result_array", $this->auth['id']);
								
				$this->smarty->assign('data_diklat_terakhir',$data_diklat_terakhir);
				$this->smarty->assign('data_record_diklat',$data_record_diklat);
				
			}else{
				$query_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $this->auth['idx_sertifikasi_id']);
				$n_sert = str_replace(" ", "_", $query_sertifikasi['nama_aparatur']);
				$folder_sertifikasi = $query_sertifikasi['kode_sertifikasi']."-".strtolower($n_sert);
				$data_file_persyaratan = $this->madmin->get_data("tbl_persyaratan", "result_array", $this->auth['id'], $this->auth['idx_sertifikasi_id'], $this->auth['kdreg_diklat']);
				
				$data_jadwal = $this->mportal->get_data("dashboard_jadwal", "row_array", $this->auth['id'], $this->auth['idx_sertifikasi_id'], $this->auth['kdreg_diklat']);				
				$now = time(); // or your date as well
				if($data_jadwal){
					$your_date = strtotime($data_jadwal['tanggal_wawancara']);
					$datediff = $now - $your_date;
					$countdownwaktu =  floor($datediff/(60*60*24));
					
					$this->smarty->assign('tglujian',$data_jadwal['tgl_beneran']);
					$this->smarty->assign('countdownwaktu',abs($countdownwaktu));
				}   

				
				$this->smarty->assign('data_file_persyaratan',$data_file_persyaratan);
				$this->smarty->assign('folder_sertifikasi',$folder_sertifikasi);
			}
			
			
			$this->smarty->assign('sts_psr',$data_status_peserta);
			$this->smarty->assign('datapeserta',$data_peserta_detail);
			$this->smarty->assign('idx_pendidikan_id', $this->fillcombo('idx_pendidikan', 'return', $data_peserta_detail['idx_pendidikan_id'] ) );
			$this->smarty->assign('idx_programstudi_id', $this->fillcombo('idx_programstudi', 'return', $data_peserta_detail['idx_programstudi_id'] ) );
			$this->smarty->assign('tahun_lulus', $this->fillcombo('tahun_lahir', 'return', $data_peserta_detail['tahun_lulus'] ) );
			
			$this->smarty->assign('konten', "dashboard-portal/container-user");
		}else{
			$this->load->library('lib');
			$data_petunjukdokumen = $this->madmin->get_data('tbl_petunjukdokumen', 'result_array', 'limit');
			$data_berita = $this->madmin->get_data('tbl_berita', 'result_array', 'limit');
			$data_jadwal = $this->madmin->get_data('tbl_penjadwalan', 'result_array');
			
			foreach($data_berita as $k => $v){
				$data_berita[$k]['judul_berita'] = $this->lib->cutstring($v['judul_berita'], 40);
			}
			
			$this->smarty->assign('petunjuk_dokumen',$data_petunjukdokumen);
			$this->smarty->assign('berita',$data_berita);
			$this->smarty->assign('jadwal',$data_jadwal);
			$this->smarty->assign('konten', "dashboard-portal/container");
		}
		
		$this->smarty->assign('modul',$modul);
		$this->smarty->display('index-portal.html');
	}
	
	function getdisplay($type="", $p1="", $p2="", $p3=""){
		/*
		if(!$this->auth['idx_sertifikasi_id']){
			$this->session->unset_userdata('d1kl4tkem3nd49r1-p0rt4L', 'limit');
			$this->session->sess_destroy();
			header("Location: " . $this->host);
		}
		*/
		$modul = "front/";
		switch($type){
			case "login-portal":
				$konten = "modul-portal/registrasi/form-login";
			break;
			
			case "registrasi":
				$konten = "modul-portal/registrasi/form-registrasi";
				$this->smarty->assign('tgl_lahir', $this->fillcombo('tanggal', 'return') );
				$this->smarty->assign('bulan_lahir', $this->fillcombo('bulan', 'return') );
				$this->smarty->assign('tahun_lahir', $this->fillcombo('tahun_lahir', 'return') );
				$this->smarty->assign('jenis_kelamin', $this->fillcombo('jenis_kelamin', 'return') );
				$this->smarty->assign('kebangsaan', $this->fillcombo('kebangsaan', 'return') );
				
				$this->smarty->assign('idx_pendidikan_id', $this->fillcombo('idx_pendidikan', 'return') );
				$this->smarty->assign('idx_programstudi_id', $this->fillcombo('idx_programstudi', 'return') );
				$this->smarty->assign('idx_provinsi_instansi_id', $this->fillcombo('idx_provinsi', 'return') );
				$this->smarty->assign('idx_kementerian', $this->fillcombo('idx_kementerian', 'return') );
				$this->smarty->assign('idx_formasi', $this->fillcombo('idx_formasi', 'return') );
				$this->smarty->assign('idx_lokasi', $this->fillcombo('lokasi', 'return') );
				$this->smarty->assign('idx_instansi', $this->fillcombo('idx_instansi', 'return') );
				$this->smarty->assign('idx_pangkat_id', $this->fillcombo('idx_pangkat', 'return') );
				$this->smarty->assign('idx_aparatur', $this->fillcombo('idx_aparatur', 'return') );
				$this->smarty->assign('idx_tuk', $this->fillcombo('jadwal_ujian_tuk', 'return') );
				$this->smarty->assign('idx_lingkup_instansi', $this->fillcombo('lingkup_instansi', 'return') );
				$this->smarty->assign('editstatus', "b_r" );
			break;
			case "registrasi_diktlatbaru":
				$this->load->model('why/madmin');
				$konten = "modul-portal/registrasi/form-registrasi-baru";
				$datadiklat_terakhir = $this->madmin->get_data('tbl_diklat_terakhir', 'row_array', $this->auth['id']);
				
				$this->smarty->assign('idx_pendidikan_id', $this->fillcombo('idx_pendidikan', 'return', $this->auth['idx_pendidikan_id']) );
				$this->smarty->assign('idx_programstudi_id', $this->fillcombo('idx_programstudi', 'return', $this->auth['idx_programstudi_id']) );
				
				$this->smarty->assign('idx_provinsi_instansi_id', $this->fillcombo('idx_provinsi', 'return', $datadiklat_terakhir['idx_provinsi_instansi_id']) );
				$this->smarty->assign('idx_kabupaten_instansi_id', $this->fillcombo('ka', 'return', $datadiklat_terakhir['idx_kabupaten_instansi_id'], $datadiklat_terakhir['idx_provinsi_instansi_id']) );
				$this->smarty->assign('idx_kementerian', $this->fillcombo('idx_kementerian', 'return', $datadiklat_terakhir['idx_kementerian_id']) );
				$this->smarty->assign('idx_formasi', $this->fillcombo('idx_formasi', 'return', $datadiklat_terakhir['idx_formasi_id']) );
				$this->smarty->assign('idx_lokasi', $this->fillcombo('lokasi', 'return', $datadiklat_terakhir['idx_lokasi_id']) );
				$this->smarty->assign('idx_instansi', $this->fillcombo('idx_instansi', 'return', $datadiklat_terakhir['idx_instansi_id']) );
				$this->smarty->assign('idx_pangkat_id', $this->fillcombo('idx_pangkat', 'return', $datadiklat_terakhir['idx_pangkat_id']) );
				$this->smarty->assign('idx_tuk', $this->fillcombo('jadwal_ujian_tuk', 'return') );
				//$this->smarty->assign('idx_aparatur', $this->fillcombo('idx_aparatur', 'return') );
				$this->smarty->assign('idx_lingkup_instansi', $this->fillcombo('lingkup_instansi', 'return', $datadiklat_terakhir['idx_flag_kab_kota']) );
				
				$tgl_tmt = explode('-', $datadiklat_terakhir['tgl_tmt_pangkat']);
				$this->smarty->assign('tgl_lahir', $this->fillcombo('tanggal', 'return', $tgl_tmt[2] ) );
				$this->smarty->assign('bulan_lahir', $this->fillcombo('bulan', 'return', $tgl_tmt[1]) );
				$this->smarty->assign('tahun_lahir', $this->fillcombo('tahun_lahir', 'return', $tgl_tmt[0]) );
								
				$this->smarty->assign('alamat_instansi', $datadiklat_terakhir['alamat_instansi']);
				$this->smarty->assign('jabatan', $datadiklat_terakhir['jabatan']);
			break;
			case "registrasi_diktlatngulang":
				$this->load->model('why/madmin');
				$konten = "modul-portal/registrasi/form-registrasi-ngulang";
				$datadiklat_terakhir = $this->madmin->get_data('tbl_diklat_terakhir', 'row_array', $this->auth['id']);
				$data_persyaratan = $this->db->get_where("idx_persyaratan_registrasi", array('idx_asn_id'=>$datadiklat_terakhir['idx_sertifikasi_id']))->result_array();
				
				/*
				echo "<pre>";
				print_r($datadiklat_terakhir);exit;
				//*/
				
				$this->smarty->assign('idx_pendidikan_id', $this->fillcombo('idx_pendidikan', 'return', $this->auth['idx_pendidikan_id']) );
				$this->smarty->assign('idx_programstudi_id', $this->fillcombo('idx_programstudi', 'return', $this->auth['idx_programstudi_id']) );
				
				$this->smarty->assign('idx_provinsi_instansi_id', $this->fillcombo('idx_provinsi', 'return', $datadiklat_terakhir['idx_provinsi_instansi_id']) );
				$this->smarty->assign('idx_kabupaten_instansi_id', $this->fillcombo('ka', 'return', $datadiklat_terakhir['idx_kabupaten_instansi_id'], $datadiklat_terakhir['idx_provinsi_instansi_id']) );
				$this->smarty->assign('idx_kementerian', $this->fillcombo('idx_kementerian', 'return', $datadiklat_terakhir['idx_kementerian_id']) );
				$this->smarty->assign('idx_formasi', $this->fillcombo('idx_formasi', 'return', $datadiklat_terakhir['idx_formasi_id']) );
				$this->smarty->assign('idx_lokasi', $this->fillcombo('lokasi', 'return', $datadiklat_terakhir['idx_lokasi_id']) );
				$this->smarty->assign('idx_instansi', $this->fillcombo('idx_instansi', 'return', $datadiklat_terakhir['idx_instansi_id']) );
				$this->smarty->assign('idx_pangkat_id', $this->fillcombo('idx_pangkat', 'return', $datadiklat_terakhir['idx_pangkat_id']) );
				$this->smarty->assign('idx_tuk', $this->fillcombo('jadwal_ujian_tuk', 'return') );
				$this->smarty->assign('idx_lingkup_instansi', $this->fillcombo('lingkup_instansi', 'return', $datadiklat_terakhir['idx_flag_kab_kota']) );
				
				$tgl_tmt = explode('-', $datadiklat_terakhir['tgl_tmt_pangkat']);
				$this->smarty->assign('tgl_lahir', $this->fillcombo('tanggal', 'return', $tgl_tmt[2] ) );
				$this->smarty->assign('bulan_lahir', $this->fillcombo('bulan', 'return', $tgl_tmt[1]) );
				$this->smarty->assign('tahun_lahir', $this->fillcombo('tahun_lahir', 'return', $tgl_tmt[0]) );
				
				$this->smarty->assign('alamat_instansi', $datadiklat_terakhir['alamat_instansi']);
				$this->smarty->assign('jabatan', $datadiklat_terakhir['jabatan']);
				$this->smarty->assign('sertifikasi', $datadiklat_terakhir['nama_aparatur']);
				$this->smarty->assign('id_sertifikasi', $datadiklat_terakhir['idx_sertifikasi_id']);
				$this->smarty->assign('jml_coba', $datadiklat_terakhir['jml_coba']);
				
				$this->smarty->assign('data_persyaratan', $data_persyaratan);
			break;
			
			case "assesmen":
				//echo $this->auth['no_registrasi'];exit;
				if(!$this->auth['idx_sertifikasi_id']){
					$this->getdisplay('tidak_ada_diklat');
					exit;
				}
				
				$data_status_asesmen = $this->mportal->get_data("status_peserta", "row_array");
				if($data_status_asesmen['step_asesmen_mandiri'] == '0'){
					$this->getdisplay('belum_boleh_asesmen');
					exit;
				}
				
				if($data_status_asesmen['step_asesmen_mandiri'] == '3'){
					$konten = "modul-portal/asesmen_mandiri/form-assesmen";
					$data_unit_kompetensi = $this->mportal->get_data("data_unit_kompetensi", "result_array");
					$this->smarty->assign("unit_komp", $data_unit_kompetensi);
				}elseif($data_status_asesmen['step_asesmen_mandiri'] == '2' || $data_status_asesmen['step_asesmen_mandiri'] == '1'){
					$this->load->model("why/madmin");
					$konten = "modul-portal/asesmen_mandiri/form-assesmen-hsl";
					$data_asesmen_mandiri = $this->madmin->get_data("tbl_test_assemen", "result_array", $this->auth['id'], $this->auth['idx_sertifikasi_id'], $this->auth['kdreg_diklat'] );
					$query_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $this->auth['idx_sertifikasi_id']);
					$n_sert = str_replace(" ", "_", $query_sertifikasi['nama_aparatur']);
					$folder_sertifikasi = $query_sertifikasi['kode_sertifikasi']."-".strtolower($n_sert);
					
					$this->smarty->assign("data", $data_asesmen_mandiri);
					$this->smarty->assign("folder_sertifikasi", $folder_sertifikasi);
				}
				$this->smarty->assign("sts_as", $data_status_asesmen);
			break;
			
			case "pembayaran":
				if(!$this->auth['idx_sertifikasi_id']){
					$this->getdisplay('tidak_ada_diklat');
					exit;
				}
				
				$this->load->model('why/madmin');
				$data_status_pembayaran = $this->mportal->get_data("status_peserta", "row_array");
				if($data_status_pembayaran['step_pembayaran'] == '1'){
					$this->getdisplay('sudah_pembayaran');
					exit;
				}elseif($data_status_pembayaran['step_pembayaran'] == '2'){
					$this->getdisplay('tunggu_verifikasi_pembayaran');
					exit;
				}elseif($data_status_pembayaran['step_pembayaran'] == '0'){
					$this->getdisplay('belum_boleh_pembayaran');
					exit;					
				}
				
				$konten = "modul-portal/pembayaran/form_pembayaran";
				$sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $this->auth['idx_sertifikasi_id']);
				$kd_unik = $this->mportal->get_data('tbl_pembayaran', 'row_array');
				$harga_sertifikasi = 900000;
				$total_transfer = $harga_sertifikasi;
				
				$this->smarty->assign('sertifikasi', $sertifikasi['nama_aparatur']  );
				$this->smarty->assign('total_transfer', number_format($total_transfer,0,",",".") );
				$this->smarty->assign('tgl_lahir', $this->fillcombo('tanggal', 'return') );
				$this->smarty->assign('bulan_lahir', $this->fillcombo('bulan', 'return') );
				$this->smarty->assign('tahun_lahir', $this->fillcombo('tahun_lahir', 'return') );				
				$this->smarty->assign('kode_pembayaran', $kd_unik['kode_pembayaran'] );				
				$this->smarty->assign("sts_pemb", $data_status_pembayaran);
			break;			
			
			case "penjadwalan":
				if(!$this->auth['idx_sertifikasi_id']){
					$this->getdisplay('tidak_ada_diklat');
					exit;
				}
				
				$data_status_penjadwalan = $this->mportal->get_data("status_peserta", "row_array");
				if($data_status_penjadwalan['step_penjadwalan'] == '0'){
					$this->getdisplay('belum_boleh_penjadwalan');
					exit;
				}
				
				$cek_sdh_daftar = $this->db->get_where('tbl_daftar_test', array('tbl_data_peserta_id'=>$this->auth['id'], 'idx_sertifikasi_id'=>$this->auth['idx_sertifikasi_id']) )->row_array();
				if($cek_sdh_daftar){
					$this->getdisplay('warning-sudah-daftar-test');
					exit;
				}
				
				$konten = "modul-portal/penjadwalan/form-penjadwalan";
				$data_penjadwalan = $this->mportal->get_data("tbl_penjadwalan_peserta", "result_array");
				$this->smarty->assign("penjadwalan", $data_penjadwalan);
				$this->smarty->assign("sts_penj", $data_status_penjadwalan);
			break;			
			
			case "uji_online":
				if(!$this->auth['idx_sertifikasi_id']){
					$this->getdisplay('tidak_ada_diklat');
					exit;
				}
				
				$data_status_ujitest = $this->mportal->get_data("status_peserta", "row_array");
				if($data_status_ujitest['step_uji_test'] == '1'){
					$this->getdisplay('sudah_uji_online');
					exit;
				}elseif($data_status_ujitest['step_uji_test'] == '2'){
					$this->getdisplay('tunggu_verifikasi_ujionline');
					exit;
				}elseif($data_status_ujitest['step_uji_test'] == '0' || $data_status_ujitest['step_uji_test'] == '4'){
					$this->getdisplay('belum_boleh_ujianonline');
					exit;					
				}
				
				$cekdataujian = $this->mportal->get_data('cekdataujian', 'result_array');
				if($cekdataujian){
					$datasoal = $this->mportal->get_data('soal_sudah', '', $cekdataujian);
					$array_soalsudah = array();
					foreach($cekdataujian as $k => $v){
						array_push($array_soalsudah, $v['idx_bank_soal_id']);
					}
					$datasoal_belum = $this->mportal->get_data('soal_belum', '', $array_soalsudah);
					
					$datawaktu = $this->db->get_where('tbl_ujitest_waktu', array('tbl_data_peserta_id'=>$this->auth['id'], 'idx_sertifikasi_id'=>$this->auth['idx_sertifikasi_id']) )->row_array();
					$konten = "modul-portal/uji_online/form_ujionline_sudah";
					$this->smarty->assign("datasoal", $datasoal);
					$this->smarty->assign("datasoal_belum", $datasoal_belum);
					$this->smarty->assign("jmlsoal", count($datasoal));
					$this->smarty->assign("datawaktu", $datawaktu);
				}else{
					$konten = "modul-portal/uji_online/form_ujionline";
				}
				
				$this->smarty->assign("sts_ut", $data_status_ujitest);
			break;
			
			case "test_simulasi" :
				if(!$this->auth['idx_sertifikasi_id']){
					$this->getdisplay('tidak_ada_diklat');
					exit;
				}
				$data_status_ujitest = $this->mportal->get_data("status_peserta", "row_array");
				if($data_status_ujitest['step_uji_simulasi'] == '1'){
					$this->getdisplay('sudah_uji_simulasi');
					exit;
				}elseif($data_status_ujitest['step_uji_simulasi'] == '2'){
					$this->getdisplay('tunggu_verifikasi_simulasi');
					exit;
				}elseif($data_status_ujitest['step_uji_simulasi'] == '0' || $data_status_ujitest['step_uji_simulasi'] == '4'){
					$this->getdisplay('belum_boleh_simulasi');
					exit;					
				}
				
				$cekdataujian_simulasi = $this->mportal->get_data('cekdataujian_simulasi', 'result_array');
				if($cekdataujian_simulasi){
					$konten = "modul-portal/uji_simulasi/form_uji_simulasi_sudah";
					$array_soalsudah = array();
					foreach($cekdataujian_simulasi as $k => $v){
						array_push($array_soalsudah, $v['idx_bank_soal_simulasi_id']);
					}
					
					$datasoal_belum = $this->mportal->get_data('soal_simulasi_belum', '', $array_soalsudah);
					$datawaktu = $this->db->get_where('tbl_uji_simulasi_waktu', array('tbl_data_peserta_id'=>$this->auth['id'], 'idx_sertifikasi_id'=>$this->auth['idx_sertifikasi_id']) )->row_array();
					
					
					$this->smarty->assign("soal_sudah", $cekdataujian_simulasi);
					$this->smarty->assign("soal_belum", $datasoal_belum);
					$this->smarty->assign("datawaktu", $datawaktu);
					$this->smarty->assign("jml", count($cekdataujian_simulasi));
				}else{
					$data_soal = $this->mportal->get_data("idx_bank_soal_simulasi", "result_array");
					$konten = "modul-portal/uji_simulasi/form_uji_simulasi";
					
					$this->smarty->assign("data_soal", $data_soal);
				}
			break;
						
			case "hasil":
				if(!$this->auth['idx_sertifikasi_id']){
					$this->getdisplay('tidak_ada_diklat');
					exit;
				}
				
				$data_status_terakhir = $this->mportal->get_data("status_peserta", "row_array");
				if($data_status_terakhir['step_hasil'] == '0'){
					$this->getdisplay('belum_boleh_hasil');
					exit;
				//}elseif($data_status_terakhir['step_hasil'] == '2'){
				//	$this->getdisplay('tunggu_verifikasi_hasil');
					//exit;
				}
				
				$this->load->model('why/madmin');
				$konten = "modul-portal/hasil_akhir/form_hasilakhir";
				
				$data_asesmen_header = $this->madmin->get_data("tbl_asesmen_header", "row_array", $this->auth['id'], $this->auth['idx_sertifikasi_id'], $this->auth['kdreg_diklat'] );
				$data_ujian_header = $this->madmin->get_data("tbl_uji_header", "row_array", $this->auth['id'], $this->auth['idx_sertifikasi_id'], $this->auth['kdreg_diklat']);
				$data_simulasi_header = $this->madmin->get_data("tbl_uji_simulasi_header", "row_array", $this->auth['id'], $this->auth['idx_sertifikasi_id'], $this->auth['kdreg_diklat']);
				$data_wawancara_header = $this->madmin->get_data("tbl_wawancara_header", "row_array", $this->auth['id'], $this->auth['idx_sertifikasi_id'], $this->auth['kdreg_diklat']);
				
				if($data_status_terakhir['step_hasil'] == '1'){
					$datahasil = $this->db->get_where('tbl_hasil_akhir', array('tbl_data_peserta_id'=>$this->auth['id'], 'idx_sertifikasi_id'=>$this->auth['idx_sertifikasi_id'], 'kdreg_diklat'=>$this->auth['kdreg_diklat']) )->row_array('status_penilaian');
					$this->smarty->assign("datahasil", $datahasil);
				}
				
				$this->smarty->assign("data_asesmen_header", $data_asesmen_header);
				$this->smarty->assign("data_ujian_header", $data_ujian_header);
				$this->smarty->assign("data_simulasi_header", $data_simulasi_header);
				$this->smarty->assign("data_wawancara_header", $data_wawancara_header);
				$this->smarty->assign("sts_akh", $data_status_terakhir);
			break;
			
			case "kinerja_peserta":
				$konten = "modul-portal/kinerja_peserta/form-kinerja";
			break;
			
			// Display Warning
			case "registrasi-berhasil":
			case "registrasi-gagal":
			case "asesmen-berhasil":
			case "asesmen-gagal":
			case "pembayaran-berhasil":
			case "pembayaran-gagal":
			case "ujitest-berhasil":
			case "ujitest-gagal":
			case "test-simulasi-berhasil":
			case "test-simulasi-gagal":
			case "submit-soal-sisa-gagal":
				$konten = "modul-portal/status_submit_semua";
				$this->smarty->assign('type', $type);
			break;
			case "warning-sudah-daftar-test":
				$konten = "modul-portal/warning";
				$cekdata = $this->db->get_where('tbl_daftar_test', array('tbl_data_peserta_id'=>$this->auth['id'], 'idx_sertifikasi_id'=>$this->auth['idx_sertifikasi_id'], 'status_data'=>'1') )->row_array();
				$data_jadwal = $this->mportal->get_data("tbl_penjadwalan_peserta", "row_array", $cekdata['tbl_jadwal_wawancara_id']);
				$this->smarty->assign('data_jadwal', $data_jadwal);
				$this->smarty->assign('type', $type);
			break;
			case "sudah_assesmen":
			case "tunggu_verifikasi_asesmen":
			case "belum_boleh_asesmen":
			case "sudah_uji_online":
			case "tunggu_verifikasi_ujionline":
			case "belum_boleh_ujianonline":
			case "sudah_uji_simulasi":
			case "tunggu_verifikasi_simulasi":
			case "belum_boleh_simulasi":
			case "sudah_pembayaran":
			case "tunggu_verifikasi_pembayaran":
			case "belum_boleh_pembayaran":
			case "belum_boleh_penjadwalan":
			case "tunggu_verifikasi_hasil":
			case "belum_boleh_hasil":
			case "tidak_ada_diklat":
				$konten = "modul-portal/warning";
				$this->smarty->assign('type', $type);
			break;			
			// End Display Warning
			
			case "additional":
				switch($p1){
					case "checking_data":
						$id_aparatur = $this->input->post("id_asn_child_tk1");
						$cek_data = $this->db->get_where('idx_aparatur_sipil_negara', array('id_asn_child_tk1'=>$id_aparatur, 'level'=>'3'))->result_array();
						if($cek_data){
							//Levi
							$tuk = $this->input->post("tuk");
							$this->smarty->assign('combo_child_tk2', $this->fillcombo('idx_asn_child_tk2', 'return', "", $id_aparatur));
							if ($tuk){
								$this->smarty->assign('type', "tuk_list");
							}else{
								$this->smarty->assign('type', "form_asn_tk2");
							}
							$this->smarty->display('modul-portal/registrasi/additional-form.html');
						}else{
							echo 0;
						}
					break;
					case "checking_data_2":
						$id_aparatur = $this->input->post('id_asn_child_tk2');
						$cek_data = $this->db->get_where('idx_aparatur_sipil_negara', array('id_asn_child_tk2'=>$id_aparatur, 'level'=>'4'))->result_array();
						if($cek_data){
							$this->smarty->assign('combo_child_tk3', $this->fillcombo('idx_asn_child_tk3', 'return', "", $id_aparatur));
							$this->smarty->assign('type', "form_asn_tk3");
							$this->smarty->display('modul-portal/registrasi/additional-form.html');
						}else{
							echo 0;
						}
					break;
					case "checking_data_3":
						$id_aparatur = $this->input->post('id_asn_child_tk3');
						$cek_data = $this->db->get_where('idx_aparatur_sipil_negara', array('id_asn_child_tk3'=>$id_aparatur, 'level'=>'5'))->result_array();
						if($cek_data){
							$this->smarty->assign('combo_child_tk4', $this->fillcombo('idx_asn_child_tk4', 'return', "", $id_aparatur));
							$this->smarty->assign('type', "form_asn_tk4");
							$this->smarty->display('modul-portal/registrasi/additional-form.html');
						}else{
							echo 0;
						}
					break;
					case "registrasi_file_persyaratan":
						$id_aparatur_asn = $this->input->post("id_asn");
						$data_persyaratan = $this->db->get_where("idx_persyaratan_registrasi", array('idx_asn_id'=>$id_aparatur_asn))->result_array();
						$this->smarty->assign('type', "file_persyaratan_registrasi");
						$this->smarty->assign('idx_sertifikasi_id', $id_aparatur_asn);
						$this->smarty->assign('data_persyaratan', $data_persyaratan);
						$this->smarty->display('modul-portal/registrasi/additional-form.html');
					break;
					case "checking_data_login":
						$this->load->library('encrypt');
						$usernm = $this->db->escape_str($this->input->post('usr'));
						$pswd = $this->db->escape_str($this->input->post('pwd'));
						
						$cekdata = $this->db->get_where("tbl_data_peserta", array('username'=>$usernm))->row_array();
						//echo 1;exit;
						if($cekdata){
							if($pswd == $this->encrypt->decode($cekdata["password"]) ){
								if($cekdata['status'] == "A" || $cekdata['status'] == "BV") echo 1;
								elseif($cekdata['status'] == "I") echo 0;
							}else{
								echo -1;
							}
						}else{
							echo -2;
						}
					break;
					case "load_soal":
						/*
						$start = $this->input->post('st');
						$end = $this->input->post('ed');
						*/
						
						$data_soal = $this->mportal->get_data("data_soal", "result_array");
						$this->smarty->assign('data_soal', $data_soal);
						$this->smarty->assign('penomoran', 0);
						$page = $this->smarty->fetch("modul-portal/uji_online/load_soal.html");
						
						$array_encode = array(
							//"st" => $start,
							//"ed" => $end,
							"pg"	=> $page,
						);
						echo json_encode($array_encode);
					break;
					case "registrasi_wawancara":
						$iddaftar = $this->input->post('iddf');
						$data_jadwal = $this->mportal->get_data("tbl_penjadwalan_peserta", "row_array", $iddaftar);
						$this->smarty->assign('data_jadwal', $data_jadwal);
						$this->smarty->assign('iddf', $iddaftar);
						$this->smarty->display("modul-portal/penjadwalan/daftar-penjadwalan.html");
					break;
					case "checking_tuk":
						$id_tuk = $this->input->post('xtu_id');
						$cekdata = $this->mportal->get_data('tbl_penjadwalan_peserta', 'row_array', "", $id_tuk);
						
						if(empty($id_tuk)){
							echo -2;
							exit;
						}
						
						if(!isset($cekdata['kuota'])){
							echo 0;
							exit;
						}
						
						$tgl_sekarang = strtotime(date('Y-m-d'));
						$tgl_ujian = strtotime($cekdata['tanggal_wawancara']);
						if ($tgl_ujian > $tgl_sekarang) {
							
						}else{
							//echo -3;
							//exit;
						}
						
						if($cekdata['kuota'] != 0){
							$get_min_pak = $this->db->get_where('idx_aparatur_sipil_negara', array('id'=>$cekdata['idx_sertifikasi_id']) )->row_array();
							
							$balikin = array(
								"tanggal" => $cekdata['tgl_wawancara'],
								"kuota" => $cekdata['kuota'],
								"sertifikasi" => $cekdata['nama_aparatur'],
								"idx_sertifikasi_id" => $cekdata['idx_sertifikasi_id'],
								"min_nilai_pak" => $get_min_pak['minimum_pak'],
								"is_pak" => $get_min_pak['flag_is_pak'],
							);
							echo json_encode($balikin);
						}else{
							echo 0;
						}
					
					break;
					case "checking_datareg":
						$nip = $this->input->post('npi');
						$nip = str_replace(" ", "", $nip);
						$nip = preg_replace('/[^a-zA-Z0-9_ \-()\/%-&]/s', '', $nip);
						$cekdata = $this->db->get_where('tbl_data_peserta', array('username'=>$nip) )->row_array();
						if($cekdata){
							echo 0;
						}else{
							echo 1;
						}
					break;
					case "checking_kuota":
						$id_tuk = $this->input->post('xtu_id');
						$cekdata = $this->mportal->get_data('checking_kuota', 'row_array', $id_tuk);
						if(isset($cekdata['kuota'])){
							echo 1;
						}else{
							echo 0;
						}
					break;
					
				}
				exit;
			break;
			
			case "kontaks":
				$datatuk = $this->db->get_where('idx_tuk')->result_array();
				$this->smarty->assign('datatuk', $datatuk);
				$konten = "dashboard-portal/kontak";
			break;
			case "faqqs":
				$this->load->model('why/madmin');
				$data = $this->madmin->get_data('tbl_faq', 'result_array');
				$konten = "dashboard-portal/faq";
				$this->smarty->assign('data', $data);
			break;
			case "konten_berita":
				$this->load->model('why/madmin');
				$this->load->library('lib');
				
				$data = $this->madmin->get_data('tbl_berita', 'result_array');
				foreach($data as $k => $v){
					$data[$k]['isi'] = $this->lib->cutstring($v['isi'], 500);
				}
				
				$konten = "dashboard-portal/berita";
				$this->smarty->assign('data', $data);
			break;
			case "konten_berita_detail":
				$this->load->model('why/madmin');
				$data = $this->madmin->get_data('tbl_berita_detail', 'row_array', $p1);
				$konten = "dashboard-portal/berita-detail";
				$this->smarty->assign('data', $data);
			break;
			case "konten_petunjuk_dokumen":
				$this->load->model('why/madmin');
				$data = $this->madmin->get_data('tbl_petunjukdokumen', 'result_array');
				$konten = "dashboard-portal/petunjuk-dokumen";
				$this->smarty->assign('data', $data);
			break;
			case "download_petunjuk_dokumen":
				$this->load->helper('download');
				$data = file_get_contents("./__repository/dokumenpetunjuk/".$p1); // Read the file's contents
				$name = 'file_petunjuk_sertifikasi.pdf';
				force_download($name, $data);
			break;
		}
		
		$this->smarty->assign('modul',$modul);
		$this->smarty->assign('konten', $konten);
		$this->smarty->display('index-portal.html');
	}
		
	function generate_kartu_ujian(){
		$this->load->library('ciqrcode');
		$this->load->library('mlpdf');
		
		$data_tuk = $this->mportal->get_data('tuk_peserta', 'row_array');
		$data_sertifikasi = $this->db->get_where('idx_aparatur_sipil_negara', array('id'=>$this->auth['idx_sertifikasi_id']))->row_array();
		$filefoto = $this->db->get_where('tbl_data_peserta', array('no_registrasi'=>$this->auth['no_registrasi']) )->row_array();
		$this->smarty->assign('data_sertifikasi', $data_sertifikasi);
		$this->smarty->assign('data_tuk', $data_tuk['nama_tuk']);
		$this->smarty->assign('tgl_wawancara', $data_tuk['tgl_wawancara']);
		$this->smarty->assign('jam', $data_tuk['jam']);
		
		//$barcode_isi = $this->auth['no_registrasi'];
		//$nama_file_barcode = $this->auth['nip']."-".$this->auth['idx_tujuan_assesmen_id'];
		//$filename = $nama_file_barcode;
		
		//generatebarcode($barcode_isi, '40', 'horizontal', 'code128', $nama_file_barcode);
		//$this->smarty->assign('barcode_file', $nama_file_barcode);

		$params['data'] = $this->auth['no_registrasi'].'-'.$data_sertifikasi['nama_aparatur'];
		$params['level'] = 'H';
		$params['size'] = 2;
		$params['savename'] = './__repository/temp_qrcode/kartu_ujian/'.$this->auth['no_registrasi'].'-'.$this->auth['idx_sertifikasi_id'].'.png';
		$this->ciqrcode->generate($params);
		$qrcode_isi = $this->auth['no_registrasi'].'-'.$this->auth['idx_sertifikasi_id'].'.png';
		$this->smarty->assign('qrcode_isi', $qrcode_isi);
		$this->smarty->assign('foto', $filefoto['foto_profil']);
		
		$htmlcontent = $this->smarty->fetch('modul-portal/sertifikat/kartu_ujian_pdf.html');
		
		$pdf = $this->mlpdf->load();
		$spdf = new mPDF('', 'A6-L', 0, '', 3, 3, 3, 3, 0, 0);
		$spdf->ignore_invalid_utf8 = true;
		// bukan sulap bukan sihir sim salabim jadi apa prok prok prok
		$spdf->allow_charset_conversion = true;     // which is already true by default
		$spdf->charset_in = 'iso-8859-2';  // set content encoding to iso
		$spdf->SetDisplayMode('fullpage');		
		//$spdf->SetHTMLHeader($htmlheader);
		
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
	
	function simpansavedbs($type=""){
		$post = array();
        foreach($_POST as $k=>$v) $post[$k] = ( $type == 'save_komplain' || $type == 'registrasi' || $type == 'savesimulasisatuan' || $type == 'registrasi_ngulang' ? $this->input->post($k) : $this->db->escape_str($this->input->post($k)) );
		
		if(!isset($post)){
			header("Location: " . $this->host);
			exit;
		}
		
		if($type == "registrasi"){
			//block check data double
			$username = str_replace(" ", "", $post['ed_nonip']);
			$nama = trim($post['ed_namalengkap']);
			
			$cek_data1 = $this->db->get_where('tbl_data_peserta', array('username'=>$username) )->row_array();
			if($cek_data1){
				echo -1;
				exit;
			}
			
			$cek_data2 = $this->db->get_where('tbl_data_peserta', array('nama_lengkap'=>$nama) )->row_array();
			if($cek_data2){
				echo -1;
				exit;
			}
			
			//block check kuota penjadwalan
			$cek_data_kuota = $this->db->get_where('tbl_jadwal_wawancara', array('id'=>$post['tku_dxi']) )->row_array();
			if($cek_data_kuota['kuota'] == 0){
				echo -2;
				exit;
			}
			
			//block check tanggal jadwal ujian
			$tgl_sekarang = strtotime(date('Y-m-d'));
			$tgl_ujian = strtotime($cek_data_kuota['tanggal_wawancara']);
			if ($tgl_ujian > $tgl_sekarang) {
				
			}else{
				echo -3;
				exit;
			}
			
			if(!isset($post['tku_dxi'])){
				echo -4;
				exit;
			}
			
		}elseif($type == "registrasi_ngulang"){
			//block check kuota penjadwalan
			$cek_data_kuota = $this->db->get_where('tbl_jadwal_wawancara', array('id'=>$post['tku_dxi']) )->row_array();
			if($cek_data_kuota['kuota'] == 0){
				echo -2;
				exit;
			}
			
			//block check tanggal jadwal ujian
			$tgl_sekarang = strtotime(date('Y-m-d'));
			$tgl_ujian = strtotime($cek_data_kuota['tanggal_wawancara']);
			if ($tgl_ujian > $tgl_sekarang) {
				
			}else{
				echo -3;
				exit;
			}
			
			if(!isset($post['tku_dxi'])){
				echo -4;
				exit;
			}
		}elseif($type == "registrasi_baru"){
			$cek_data_kuota = $this->db->get_where('tbl_jadwal_wawancara', array('id'=>$post['tku_dxi']) )->row_array();
			if($cek_data_kuota['kuota'] == 0){
				echo -2;
				exit;
			}
			
			//block check tanggal jadwal ujian
			$tgl_sekarang = strtotime(date('Y-m-d'));
			$tgl_ujian = strtotime($cek_data_kuota['tanggal_wawancara']);
			if ($tgl_ujian > $tgl_sekarang) {
				
			}else{
				echo -3;
				exit;
			}
			
			if(!isset($post['tku_dxi'])){
				echo -4;
				exit;
			}
			
			//$jadwal = $this->db->get_where('tbl_jadwal_wawancara', array('id'=>$post['tku_dxi']) )->row_array();
			$cek = $this->db->get_where('tbl_data_diklat', array('tbl_data_peserta_id'=>$post['idusrx'], 'idx_sertifikasi_id'=>$post['sertis_id'] ) )->result_array();
			if($cek){
				echo -5;
				exit;
			}
		}elseif($type == "asesmen"){
			if($this->auth){
				//Checking Data Double
				$array = array(
					"tbl_data_peserta_id" => $this->auth['id'],
					"idx_sertifikasi_id" => $this->auth['idx_sertifikasi_id'],
					"status_data"=>'1',
					"kdreg_diklat" => $this->auth['kdreg_diklat']
				);
				$cek_data = $this->db->get_where('tbl_asessmen_mandiri_header', $array )->row_array();
				if($cek_data){
					echo 0; //header("Location: " . $this->host ."assesmen-mandiri");
					exit;
				}
			}else{
				header("Location: " . $this->host);
				exit;
			}
		}elseif($type == "saveujian"){
			if($this->auth){
				$array = array(
					"tbl_data_peserta_id" => $this->auth['id'],
					"idx_sertifikasi_id" => $this->auth['idx_sertifikasi_id'],
					"status_data"=>'1',
					"kdreg_diklat" => $this->auth['kdreg_diklat']
				);
				$cek_data = $this->db->get_where('tbl_ujitest_header', $array )->row_array();
				if($cek_data){
					header("Location: " . $this->host ."uji-online-mandiri");
					exit;
				}
			}else{
				header("Location: " . $this->host);
				exit;
			}
		}
				
		//echo count($post['idxfl']);
		//exit;
		
		/*
		echo "<pre>";
		print_r($post);
		exit;
		//*/
		
		$savenya =  $this->mportal->simpansavedatabase($type, $post);
		if($savenya == 1){
			if($type == "registrasi"){
				echo $savenya; //$this->getdisplay("registrasi-berhasil");
			}elseif($type == "asesmen"){
				//$this->getdisplay("asesmen-berhasil");
				echo $savenya;
			}elseif($type == "saveujian"){
				$this->getdisplay("ujitest-berhasil");
			}elseif($type == "savepembayaran"){
				$this->getdisplay("pembayaran-berhasil");
			}elseif($type == "savedaftarwawancara"){
				echo $savenya;
			}elseif($type == "registrasi_baru"){
				//$this->session->unset_userdata('d1kl4tkem3nd49r1-p0rt4L', 'limit');
				//$this->session->sess_destroy();
				//header("Location: " . $this->host ."login-peserta");
				echo $savenya;
			}elseif($type == "registrasi_ngulang"){
				$this->session->unset_userdata('d1kl4tkem3nd49r1-p0rt4L', 'limit');
				$this->session->sess_destroy();
				header("Location: " . $this->host ."login-peserta");
			}else{
				echo $savenya;
			}
		}else{
			if($type == "registrasi"){
				//$this->getdisplay("registrasi-gagal");
				echo $savenya;
			}elseif($type == "asesmen"){
				//$this->getdisplay("asesmen-gagal");
				echo $savenya;
			}elseif($type == "saveujian"){
				$this->getdisplay("ujitest-gagal");
			}elseif($type == "savepembayaran"){
				$this->getdisplay("pembayaran-gagal");
			}elseif($type == "savedaftarwawancara"){
				echo $savenya;				
			}elseif($type == "registrasi_baru"){
				$this->getdisplay("registrasi-gagal");
			}else{
				echo $savenya;
			}
		}
	}
		
	function fillcombo($type="", $balikan="", $p1="", $p2="", $p3=""){
		$this->load->helper('db_helper');
		$v = $this->input->post('v');
		if($v != ""){
			$selTxt = $v;
		}else{
			$selTxt = $p1;
		}
		
		$optTemp = "";
		
		if($type == 'bulan'){
			$data = arraydate('bulan');
		}elseif($type == 'tahun_lahir'){
			$data = arraydate('tahun_lahir');
		}elseif($type == 'tahun'){
			$data = arraydate('tahun');
		}elseif($type == 'tanggal'){
			$data = arraydate('tanggal');
		}elseif($type == 'jenis_kelamin'){
			$optTemp = '<option value=""> -- Pilih -- </option>';
			$data = array(
				'0' => array('kode'=>'L','txt'=>'Laki-Laki'),
				'1' => array('kode'=>'P','txt'=>'Perempuan'),
			);
		}elseif($type == 'kebangsaan'){
			$optTemp = '<option value=""> -- Pilih -- </option>';
			$data = array(
				'0' => array('kode'=>'WNI','txt'=>'Warga Negara Indonesia'),
				'1' => array('kode'=>'WNA','txt'=>'Warga Negara Asing'),
			);
		}elseif($type == 'lokasi'){
			$optTemp = '<option value=""> -- Pilih -- </option>';
			$data = array(
				'0' => array('kode'=>'P','txt'=>'Pusat'),
				'1' => array('kode'=>'D','txt'=>'Daerah'),
			);
		}elseif($type == 'lingkup_instansi'){
			$optTemp = '<option value=""> -- Pilih -- </option>';
			$data = array(
				'0' => array('kode'=>'1','txt'=>'Kota'),
				'1' => array('kode'=>'2','txt'=>'Kabupaten'),
			);
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

	function tester1(){
		//$this->load->library('encrypt');
		//echo $this->encrypt->encode("12345");
		//echo $this->mportal->kirimemail("email_registrasi", "triwahyunugroho11@gmail.com", "usernya", "passnya");
		
		$this->load->library('lib');
		echo $this->lib->kirimemail("email_registrasi", "triwahyunugroho11@gmail.com", "usernya", "passnya");
		
		//$this->load->library('user_agent');
		//echo $this->agent->browser();
		
		//echo $this->mportal->get_data("data_soal");
		//$angka = sprintf('%07d', 89);
		//echo $angka;
		//$folder_sertifikasi = "10-P2UPD";
		//$target_path = "./repository/dokumen_peserta/".$folder_sertifikasi."/";
		//mkdir($target_path, 0777);
						
	}
	
	function tester2(){
		$this->smarty->assign('username', 'test');
		$this->smarty->assign('password', 'tust');
		$this->smarty->display('modul-portal/template_email.html');
		//$hoster = unserialize(base64_decode($this->session->userdata('d1kl4tkem3nd49r1-p0rt4L')));
		//echo $this->session->userdata('d1kl4tkem3nd49r1-p0rt4L');
	}
	
	function printdaftar(){
		$this->load->library('mlpdf');
		$pdf = $this->mlpdf->load();
		$spdf = new mPDF('', 'A4', 0, '', 12.7, 12.7, 20, 20, 10, 2, 'P');
		$spdf->ignore_invalid_utf8 = true;
		$spdf->allow_charset_conversion = true;     // which is already true by default
		$spdf->charset_in = 'iso-8859-2';  // set content encoding to iso
		$spdf->SetDisplayMode('fullpage');
		
		$data_ujian_kompetensi = "
			SELECT A.nama_lengkap, G.real_name as nama_asesor
			FROM tbl_data_peserta A
			LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
			LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
			LEFT JOIN (SELECT * FROM tbl_user_admin WHERE level_admin = '2') AS G ON G.id = E.idx_asesor_id
			WHERE B.step_uji_test = '4' AND E.idx_sertifikasi_id = '13'
			ORDER BY E.idx_asesor_id ASC
		";
		$query_ujian = $this->db->query($data_ujian_kompetensi)->result_array();
		
		$data_uji_asesmen = "
			SELECT A.nama_lengkap, G.real_name as nama_asesor
			FROM tbl_data_peserta A
			LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
			LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
			LEFT JOIN (SELECT * FROM tbl_user_admin WHERE level_admin = '2') AS G ON G.id = E.idx_asesor_id
			WHERE B.step_asesmen_mandiri = '3' AND E.idx_sertifikasi_id = '13'
			ORDER BY E.idx_asesor_id ASC
		";
		$query_asesmen = $this->db->query($data_uji_asesmen)->result_array();
		
		$this->smarty->assign('ujitest', $query_ujian);
		$this->smarty->assign('ujiasesmen', $query_asesmen);
		
		$htmlcontent = $this->smarty->fetch('modul-portal/rekap_peserta.html');
		
		$spdf->SetProtection(array('print'));				
		$spdf->WriteHTML($htmlcontent); // write the HTML into the PDF
		//$spdf->Output('repositories/Dokumen_LS/LS_PDF/'.$filename.'.pdf', 'F'); // save to file because we can
		$spdf->Output('repository/temp_sertifikat/rekap_peserta.pdf', 'I'); // view file
		
		
	}

}