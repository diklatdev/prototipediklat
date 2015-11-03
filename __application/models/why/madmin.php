<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class madmin extends SHIPMENT_Model{
	function __construct(){
		parent::__construct();
		$this->auth = unserialize(base64_decode($this->session->userdata('d1kl4tkem3nd49r1')));
	}
	
	function get_data($type="", $balikan="", $p1="", $p2="", $p3="", $p4="", $p5="", $p6=""){
		$where = " WHERE 1=1 ";
		$join = "";
		
		switch($type){
			// User Manajemen & ACL
			case "data_login_admin":
				$sql = "
					SELECT A.*
					FROM tbl_user_admin A
					WHERE A.username = '".$p1."' AND A.aktif = '1'
				";
			break;
			case "get_module":
				$sql = "
					SELECT A.nama_module, B.id as function_id
					FROM idx_menu_module A
					LEFT JOIN idx_menu_submodule B ON A.id = B.idx_module_id
					WHERE B.nama_submodule = 'Main Menu'
				";
			break;
			case "get_submodule":
				$sql = "
					SELECT nama_submodule, id as function_id
					FROM idx_menu_submodule
					WHERE nama_submodule <> 'Main Menu'
				";
			break;
			// End User Manajemen & ACL
            
            case "tanggal_penetapan_hasil":
                $sql = "
                    SELECT DATE_ADD(tanggal_wawancara,INTERVAL 1 DAY) AS tanggal_penetapan 
                    FROM tbl_jadwal_wawancara
                    WHERE id = '".$p1."'
                ";
            break;
			
			case "monitoring_jml_peserta":
				$sql = "
					SELECT count(A.id) as jml_peserta, B.nama_aparatur
					FROM tbl_data_diklat A
					LEFT JOIN idx_aparatur_sipil_negara B ON A.idx_sertifikasi_id = B.id
					WHERE A.`status` = '1'
					GROUP BY A.idx_sertifikasi_id
				";
			break;
			
			//TRANSAKSI PESERTA
			case "tbl_data_peserta_diklat":
				if($this->auth['level_admin'] == 2){
					$where = " AND E.idx_asesor_id = '".$this->auth['id']."' ";
				}else{
					$where = "";
				}
				
				$no_registrasi = $this->input->post('nre');				
				if($no_registrasi){
					$where .= "
						AND A.no_registrasi like '%".$no_registrasi."%'
					";
				}				
				
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, E.idx_sertifikasi_id,
						E.kdreg_diklat, D.nama_tuk as tuknya, F.real_name as nama_asesor
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
					LEFT JOIN idx_tuk D ON E.idx_tuk_id = D.id
					LEFT JOIN (SELECT * FROM tbl_user_admin WHERE level_admin = '2') AS F ON F.id = E.idx_asesor_id
					WHERE B.step_registrasi = '2' $where
				";
			break;
			case "tbl_peserta_asesmen":
				if($this->auth['level_admin'] == 2){
					$where = " AND E.idx_asesor_id = '".$this->auth['id']."' ";
				}else{
					$where = "";
				}
				
				$no_registrasi = $this->input->post('nre');				
				if($no_registrasi){
					$where .= "
						AND A.no_registrasi like '%".$no_registrasi."%'
					";
				}	
				
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur,
						DATE_FORMAT( D.tgl_ujian,  '%d-%m-%Y' ) AS tanggal_ujian, E.idx_sertifikasi_id, 
						E.kdreg_diklat, B.step_asesmen_mandiri, F.nama_tuk as tuknya, G.real_name as nama_asesor
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_asessmen_mandiri_header WHERE status_data=1) D ON A.id = D.tbl_data_peserta_id
					LEFT JOIN idx_tuk F ON E.idx_tuk_id = F.id
					LEFT JOIN (SELECT * FROM tbl_user_admin WHERE level_admin = '2') AS G ON G.id = E.idx_asesor_id
					WHERE B.step_asesmen_mandiri IN ('2', '3') $where
				";
			break;			
			case "tbl_peserta_pembayaran":
				if($this->auth['level_admin'] == 5){
					$where = " AND D.idx_bendahara_id = '".$this->auth['id']."' ";
				}else{
					$where = "";
				}
				
				$no_registrasi = $this->input->post('nre');				
				if($no_registrasi){
					$where .= "
						AND A.no_registrasi like '%".$no_registrasi."%'
					";
				}	
				
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur,
						DATE_FORMAT( D.tanggal_pembayaran,  '%d-%m-%Y' ) AS tanggal_pembayaran, 
						DATE_FORMAT( D.tgl_konfirm,  '%d-%m-%Y' ) AS tanggal_konfirm, D.file_bukti_pembayaran,
						E.idx_sertifikasi_id, D.metode_pembayaran, D.kode_pembayaran, D.kode_voucher,
						E.kdreg_diklat
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_pembayaran_header WHERE status_data=1) D ON A.id = D.tbl_data_peserta_id
					WHERE B.step_pembayaran = '2' $where
				";
			break;
			case "tbl_peserta_ujitulis_sekarang":
				if($this->auth['level_admin'] == 7){
					$where = " AND E.idx_tuk_id = '".$this->auth['idx_tuk_id']."' ";
				}else{
					$where = "";
				}
				
				$no_registrasi = $this->input->post('nre');				
				if($no_registrasi){
					$where .= "
						AND A.no_registrasi like '%".$no_registrasi."%'
					";
				}	
				
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, E.idx_sertifikasi_id,
						E.kdreg_diklat, G.real_name as nama_asesor
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_daftar_test WHERE status_data=1) F ON A.id = F.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_user_admin WHERE level_admin = '2') AS G ON G.id = E.idx_asesor_id
					WHERE B.step_uji_test = '4' $where
				";
			break;			
			case "tbl_peserta_ujitulis":
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, 
						DATE_FORMAT( D.tgl_test,  '%d-%m-%Y' ) AS tanggal_ujian, D.jumlah_salah, D.jumlah_benar,
						E.idx_sertifikasi_id, F.nama_tuk as tuknya
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_ujitest_header WHERE status_data=1) D ON A.id = D.tbl_data_peserta_id
					LEFT JOIN idx_tuk F ON E.idx_tuk_id = F.id
					WHERE B.step_uji_test = '2'
				";
			break;
			case "tbl_peserta_simulasi":
				if($this->auth['level_admin'] == 2){
					$where = " AND E.idx_asesor_id = '".$this->auth['id']."' ";
				}else{
					$where = "";
				}
				
				$no_registrasi = $this->input->post('nre');				
				if($no_registrasi){
					$where .= "
						AND A.no_registrasi like '%".$no_registrasi."%'
					";
				}	
				
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, E.idx_sertifikasi_id,
						E.kdreg_diklat, F.nama_tuk as tuknya, G.real_name as nama_asesor
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_uji_simulasi_header WHERE status_data=1) D ON A.id = D.tbl_data_peserta_id
					LEFT JOIN idx_tuk F ON E.idx_tuk_id = F.id
					LEFT JOIN (SELECT * FROM tbl_user_admin WHERE level_admin = '2') AS G ON G.id = E.idx_asesor_id
					WHERE B.step_uji_simulasi = '2' $where
				";
			break;
			case "tbl_peserta_wawancara":
				if($this->auth['level_admin'] == 2){
					$where = " AND E.idx_asesor_id = '".$this->auth['id']."' ";
				}else{
					$where = "";
				}
				
				$no_registrasi = $this->input->post('nre');				
				if($no_registrasi){
					$where .= "
						AND A.no_registrasi like '%".$no_registrasi."%'
					";
				}	
			
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, E.idx_sertifikasi_id,
						E.kdreg_diklat, F.nama_tuk as tuknya, G.real_name as nama_asesor
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_wawancara_header WHERE status_data=1) D ON A.id = D.tbl_data_peserta_id
					LEFT JOIN idx_tuk F ON E.idx_tuk_id = F.id
					LEFT JOIN (SELECT * FROM tbl_user_admin WHERE level_admin = '2') AS G ON G.id = E.idx_asesor_id
					WHERE B.step_wawancara = '2' $where
				";
			break;
			case "tbl_peserta_hasil":
			case "tbl_peserta_cetak_sertifikat":
				$no_registrasi = $this->input->post('nre');
				
				if($type == 'tbl_peserta_hasil'){
					$where = " WHERE B.step_hasil = '2' ";
				}elseif($type == 'tbl_peserta_cetak_sertifikat'){
					$where = " WHERE B.step_hasil = '1' AND D.siap_cetak = 'Y' ";
				}
				
				if($no_registrasi){
					$where_join = " ";
					$where_join2 = " ";
					$where .= "
						AND A.no_registrasi like '%".$no_registrasi."%'
					";
				}else{
					$where_join = " WHERE status=1 ";
					$where_join2 = " WHERE status_data=1 ";
				}
				
				
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, B.idx_sertifikasi_id,
						D.status_penilaian as lulus_tidak, B.kdreg_diklat, G.real_name as nama_asesor
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta $where_join ) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON B.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_hasil_akhir $where_join2 ) D ON E.tbl_data_peserta_id = D.tbl_data_peserta_id AND E.idx_sertifikasi_id = D.idx_sertifikasi_id
					LEFT JOIN (SELECT * FROM tbl_user_admin WHERE level_admin = '2') AS G ON G.id = E.idx_asesor_id
					$where
				";
			break;
			case "tbl_peserta_tidak_lulus":
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, B.idx_sertifikasi_id, 
							D.status_penilaian as lulus_tidak, B.kdreg_diklat
					FROM tbl_data_peserta A 
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status = 1 ) B ON A.id = B.tbl_data_peserta_id 
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status = 1) E ON A.id = E.tbl_data_peserta_id 
					LEFT JOIN idx_aparatur_sipil_negara C ON B.idx_sertifikasi_id = C.id 
					LEFT JOIN (SELECT * FROM tbl_hasil_akhir WHERE status_data = 1  ) D ON E.tbl_data_peserta_id = D.tbl_data_peserta_id AND E.idx_sertifikasi_id = D.idx_sertifikasi_id AND E.kdreg_diklat = D.kdreg_diklat
					WHERE B.step_hasil = '1' AND status_penilaian = 'TL'
				";
			break;
			//END
			
			//REGISTRASI Peserta
			case "tbl_data_peserta":
			case "tbl_data_peserta_detail":
				if($type == "tbl_data_peserta"){
					$select = " A.id, A.no_registrasi, A.nama_lengkap, A.nip, A.status ";
				}elseif($type == "tbl_data_peserta_detail"){
					$select = " 
						A.*, H.nama_pendidikan, I.nama_programstudi, C.name as nama_provinsi, D.name as nama_kabupaten, 
						E.nama_instansi, F.nama_pangkat, G.nama_aparatur, B.jabatan, B.alamat_instansi, B.idx_sertifikasi_id, B.file_pak, B.kdreg_diklat,  
						J.nama_kementerian, K.nama_formasi, B.idx_lokasi_id, L.nama_tuk, M.real_name as nama_asesor
					";
					$where .= " AND A.id = '".$p1."' ";
					$join .= "
						LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status='1') B ON A.id = B.tbl_data_peserta_id
						LEFT JOIN (SELECT idprov, name FROM idx_area WHERE level='1') AS C ON B.idx_provinsi_instansi_id = C.idprov
						LEFT JOIN idx_area D ON B.idx_kabupaten_instansi_id = D.id
						LEFT JOIN idx_instansi E ON B.idx_instansi_id = E.id
						LEFT JOIN idx_pangkat F ON B.idx_pangkat_id = F.id
						LEFT JOIN idx_aparatur_sipil_negara G ON B.idx_sertifikasi_id = G.id 
						LEFT JOIN idx_pendidikan H ON A.idx_pendidikan_id = H.id 
						LEFT JOIN idx_programstudi I ON A.idx_programstudi_id = I.id 
						LEFT JOIN idx_kementerian J ON J.id = B.idx_kementerian_id
						LEFT JOIN idx_formasi K ON K.id = B.idx_formasi_id
						LEFT JOIN idx_tuk L ON L.id = B.idx_tuk_id
						LEFT JOIN (SELECT * FROM tbl_user_admin WHERE level_admin = '2') AS M ON M.id = B.idx_asesor_id
					";
				}
				
				$sql = "
					SELECT $select 
					FROM tbl_data_peserta A 
					$join
					$where
				";
				//echo $sql;exit;
			break;
			case "tbl_persyaratan":
				$sql = "
					SELECT A.*, B.nama_persyaratan
					FROM tbl_persyaratan_sertifikasi A
					LEFT JOIN idx_persyaratan_registrasi B ON A.idx_persyaratan_id = B.id
					WHERE A.tbl_data_peserta_id = '".$p1."' 
					AND idx_sertifikasi_id = '".$p2."' 
					AND kdreg_diklat = '".$p3."'
				";
			break;			
			//END Registrasi
			
			//Asesmen Mandiri
			case "tbl_asesmen_header":
				$sql = "
					SELECT status, nama_asesor,
						DATE_FORMAT( tgl_ujian,  '%d-%m-%Y' ) AS tanggal_ujian,
						DATE_FORMAT( tgl_verifikasi,  '%d-%m-%Y' ) AS tanggal_verifikasi
					FROM tbl_asessmen_mandiri_header
					WHERE tbl_data_peserta_id = '".$p1."'
					AND idx_sertifikasi_id = '".$p2."' 
					AND kdreg_diklat = '".$p3."' 
					AND status_data = '1'
				";
			break;
			case "tbl_test_assemen":
				$sql = "
					SELECT A.*, B.kode_unit, B.judul_unit
					FROM tbl_asessmen_mandiri A
					LEFT JOIN idx_unit_kompetensi B ON A.idx_unit_kompetensi_id = B.id
					WHERE A.tbl_data_peserta_id = '".$p1."'
					AND idx_sertifikasi_id = '".$p2."' 
					AND kdreg_diklat = '".$p3."'
				";
			break;
			//End Asesmen
			
			//Uji Tertulis Online
			case "tbl_uji_header":
				$sql = "
					SELECT jumlah_salah, jumlah_benar, total_skor, status, nama_asesor, 
						DATE_FORMAT( tgl_test,  '%d-%m-%Y' ) AS tanggal_ujian,
						DATE_FORMAT( tgl_verifikasi,  '%d-%m-%Y' ) AS tanggal_verifikasi
					FROM tbl_ujitest_header 
					WHERE tbl_data_peserta_id = '".$p1."'  
					AND idx_sertifikasi_id = '".$p2."' 
					AND kdreg_diklat = '".$p3."'
				";
			break;
			case "tbl_test_ujionline":
				$tbl_data_peserta_id = $this->input->post('idxss');
				$idx_sertifikasi_id = $this->input->post('idxsert');
				$kdreg_diklat = $this->input->post('kdr');
				$lmt = $this->input->post('lmt');
				
				if(isset($tbl_data_peserta_id)){
					$p1 = $tbl_data_peserta_id;
				}
				if(isset($idx_sertifikasi_id)){
					$p2 = $idx_sertifikasi_id;
				}
				if(isset($kdreg_diklat)){
					$p3 = $kdreg_diklat;
				}
				if(isset($lmt)){
					$perpage = 10;
					$page = (($this->input->post('page')) ? $this->input->post('page') : 0 );
					$start = ($page * $perpage);
					$limit = $perpage;
					
					$limits = " LIMIT $start, $limit ";
				}else{
					$limits = "";
				}
				
				$sql = "
					SELECT A.status, B.soal, C.jawaban
					FROM tbl_ujitest A
					LEFT JOIN idx_bank_soal B ON A.idx_bank_soal_id = B.id
					LEFT JOIN idx_bank_jawaban C ON A.idx_bank_jawaban_id = C.id 
					WHERE A.tbl_data_peserta_id = '".$p1."' 
					AND idx_sertifikasi_id = '".$p2."'
					AND kdreg_diklat = '".$p3."' 
					$limits
				";
			break;
			case "idx_bank_soal":
				$sql = "
					SELECT *
					FROM idx_bank_soal
					WHERE idx_aparatur_negara = '".$p1."'
				";
			break;			
			case "idx_bank_soal_simulasi":
				$sql = "
					SELECT *
					FROM idx_bank_soal_simulasi
					WHERE idx_sertifikasi_id = '".$p1."'
				";
			break;			
			//End Uji Tertulis Online
			
			//Uji Simulasi
			case "tbl_uji_simulasi_header":
				$sql = "
					SELECT *, DATE_FORMAT( tanggal_verifikasi,  '%d-%m-%Y' ) AS tgl_verifikasi
					FROM tbl_uji_simulasi_header
					WHERE tbl_data_peserta_id = '".$p1."' 
					AND idx_sertifikasi_id = '".$p2."' 
					AND kdreg_diklat = '".$p3."'
				";
			break;
			case "tbl_uji_simulasi":
				$sql = "
					SELECT A.*, B.soal
					FROM tbl_uji_simulasi A
					LEFT JOIN idx_bank_soal_simulasi B ON A.idx_bank_soal_simulasi_id = B.id
					WHERE A.tbl_data_peserta_id = '".$p1."' 
					AND A.idx_sertifikasi_id = '".$p2."' 
					AND A.kdreg_diklat = '".$p3."'
					ORDER BY A.idx_bank_soal_simulasi_id ASC
				";
			break;
			//End Uji Simulasi
			
			//Uji Wawancara
			case "tbl_wawancara_header":
				$sql = "
					SELECT nilai, status, nama_asesor, memo, komplain,
						DATE_FORMAT( tgl_verifikasi,  '%d-%m-%Y' ) AS tanggal_verifikasi
					FROM tbl_wawancara_header 
					WHERE tbl_data_peserta_id = '".$p1."'
					AND idx_sertifikasi_id = '".$p2."'
				";
			break;
			//End Uji Wawancara
			
			//Cetak Sertifikat
			case "tbl_detail_peserta_cetak":
				$sql = "
					SELECT 	A.*, H.nama_pendidikan, I.nama_programstudi, C.name as nama_provinsi, D.name as nama_kabupaten, 
						E.nama_instansi, F.nama_pangkat, G.nama_aparatur, B.jabatan, B.alamat_instansi,
						B.idx_lokasi_id, B.idx_flag_kab_kota 
					FROM tbl_data_peserta A
					LEFT JOIN (
						SELECT * 
						FROM tbl_data_diklat 
						WHERE tbl_data_peserta_id = '".$p1."'
						AND idx_sertifikasi_id = '".$p2."' 
					) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT idprov, name FROM idx_area WHERE level='1') AS C ON B.idx_provinsi_instansi_id = C.idprov
					LEFT JOIN idx_area D ON B.idx_kabupaten_instansi_id = D.id
					LEFT JOIN idx_instansi E ON B.idx_instansi_id = E.id
					LEFT JOIN idx_pangkat F ON B.idx_pangkat_id = F.id
					LEFT JOIN idx_aparatur_sipil_negara G ON B.idx_sertifikasi_id = G.id 
					LEFT JOIN idx_pendidikan H ON A.idx_pendidikan_id = H.id 
					LEFT JOIN idx_programstudi I ON A.idx_programstudi_id = I.id 
					WHERE A.id = '".$p1."'
				";
			break;
			//End Cetak
			
			
			//Record Diklat
			case "tbl_diklat_terakhir":
				$sql = "
					SELECT B.*, C.name as nama_provinsi, D.name as nama_kabupaten, 
						E.nama_instansi, F.nama_pangkat, G.nama_aparatur
					FROM tbl_data_diklat B
					LEFT JOIN (SELECT idprov, name FROM idx_area WHERE level='1') AS C ON B.idx_provinsi_instansi_id = C.idprov
					LEFT JOIN idx_area D ON B.idx_kabupaten_instansi_id = D.id
					LEFT JOIN idx_instansi E ON B.idx_instansi_id = E.id
					LEFT JOIN idx_pangkat F ON B.idx_pangkat_id = F.id
					LEFT JOIN idx_aparatur_sipil_negara G ON B.idx_sertifikasi_id = G.id
					WHERE B.tbl_data_peserta_id = '".$p1."' 
					ORDER BY id DESC LIMIT 0,1
				";
			break;
			case "tbl_record_diklat":
				$sql = "
					SELECT B.*, C.nama_aparatur
					FROM tbl_data_diklat B
					LEFT JOIN idx_aparatur_sipil_negara C ON B.idx_sertifikasi_id = C.id
					WHERE B.tbl_data_peserta_id = '".$p1."' 
					ORDER BY id ASC
				";
			break;
			//End Record
			
			//File Folder
			case "folder_sertifikasi":
				$sql = "
					SELECT nama_aparatur, kode_sertifikasi
					FROM idx_aparatur_sipil_negara
					WHERE id = '".$p1."'
				";
			break;
			//End File Folder
			
			//Jadwal Ujian
			case "tbl_penjadwalan":
				if($p1){
					$whereform = " AND A.id = '".$p1."' ";
				}else{
					$whereform = "";
				}
				$sql = "
					SELECT A.*, DATE_FORMAT( A.tanggal_wawancara,  '%d-%m-%Y' ) AS tgl_wawancara,
						B.nama_tuk, C.nama_aparatur
					FROM tbl_jadwal_wawancara A
					LEFT JOIN idx_tuk B ON A.idx_tuk_id = B.id
					LEFT JOIN idx_aparatur_sipil_negara C ON A.idx_sertifikasi_id = C.id
					WHERE A.status = 'A' $whereform
				";
			break;
			//End Jadwal Ujian
			
			//Master Data				
			case "idx_voucher":
				$sql = "
					SELECT id, kode_voucher, DATE_FORMAT( tgl_terbit,  '%d-%m-%Y' ) AS tanggal_terbit,
						status_data
					FROM idx_voucher
					WHERE 1=1 AND status_data = '1'
				";
			break;
			case "tbl_petunjukdokumen":
				if($p1 == 'limit'){
					$limit = " ORDER BY RAND() LIMIT 5 ";
				}else{
					$limit = "";
				}
				
				$sql = "
					SELECT *,  DATE_FORMAT( tanggal_terbit,  '%d-%m-%Y' ) AS tgl_terbit
					FROM tbl_petunjukdokumen
					$where
					$limit
				";
			break;
			case "tbl_petunjukdokumen_detail":
				$sql = "
					SELECT *
					FROM tbl_petunjukdokumen 
					WHERE id = '".$p1."'
				";
			break;
			case "tbl_berita":
				if($p1 == 'limit'){
					$limit = " ORDER BY RAND() LIMIT 5 ";
				}else{
					$limit = "";
				}
				
				$sql = "
					SELECT *, DATE_FORMAT( tgl_terbit,  '%d-%m-%Y' ) AS tanggal_terbit
					FROM tbl_berita
					$where
					$limit
				";
			break;
			case "tbl_berita_detail":
				$sql = "
					SELECT *, DATE_FORMAT( tgl_terbit,  '%d-%m-%Y' ) AS tanggal_terbit
					FROM tbl_berita
					WHERE id = '".$p1."'
				";
			break;
			case "tbl_faq":
				$sql = "
					SELECT *
					FROM tbl_faq
				";
			break;
			case "tbl_faq_detail":
				$sql = "
					SELECT *
					FROM tbl_faq
					WHERE id = '".$p1."'
				";
			break;
			
			case "tbl_jadwal_to_asesor":
				$sql = "
					SELECT B.real_name
					FROM tbl_jadwal_to_asesor A
					LEFT JOIN tbl_user_admin B ON B.id = A.tbl_user_admin_id
					WHERE A.tbl_jadwal_wawancara_id = '".$p1."'
				";
			break;
			//End Master Data						
		}
		
		if($balikan == "result_array"){
			return $this->db->query($sql)->result_array();
		}elseif($balikan == "row_array"){
			return $this->db->query($sql)->row_array();
		}
		
	}
	
	function get_data_grid($type="", $p1="", $p2=""){
		$this->load->library('lib');
		$where = "";
		
		//Filter Jadwal
		$filter_jadwal = $this->input->post('idxjd');
		$nama_peserta = $this->input->post('nmpsr');
		if($filter_jadwal){
			$where .= "
				AND F.tbl_jadwal_wawancara_id = '".$filter_jadwal."' 
			";
			$order = "ORDER BY A.nama_lengkap ASC ";
		}else{
			$order = "ORDER BY F.tbl_jadwal_wawancara_id DESC ";
		}
		
		if($nama_peserta){
			$where .= "
				AND A.nama_lengkap LIKE '%".$nama_peserta."%' 
			";
		}
		//End Filter Jadwal
		
		if($this->auth['level_admin'] == 2){
			if($type == 'cetak_sertifikat'){
				$where .= " AND E.idx_asesor_id = '".$this->auth['id']."' ";
			}else{
				$where .= " AND B.idx_asesor_id = '".$this->auth['id']."' ";
			}
		}
					
		switch($type){
			case 'data_peserta':				
				$sql = "
					SELECT A.nama_lengkap, A.no_registrasi, D.real_name as nama_asesor,
						C.step_registrasi, C.step_asesmen_mandiri, C.step_uji_test, C.step_uji_simulasi, C.step_wawancara, 
						B.kdreg_diklat, B.idx_sertifikasi_id, B.tbl_data_peserta_id, DATE_FORMAT( G.tanggal_wawancara,  '%d-%m-%Y' ) AS tgl_wawancara,
						E.nama_aparatur, H.nama_tuk
					FROM tbl_data_peserta A
					LEFT JOIN ( SELECT * FROM tbl_data_diklat WHERE `status` = '1') AS B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN ( SELECT * FROM tbl_step_peserta WHERE `status` = '1') AS C ON B.tbl_data_peserta_id = C.tbl_data_peserta_id AND B.idx_sertifikasi_id = B.idx_sertifikasi_id
					LEFT JOIN ( SELECT * FROM tbl_user_admin WHERE level_admin = '2' ) AS D ON D.id = B.idx_asesor_id
					LEFT JOIN idx_aparatur_sipil_negara E ON B.idx_sertifikasi_id = E.id
					LEFT JOIN ( SELECT * FROM tbl_daftar_test WHERE  status_data = '1' ) AS F ON B.tbl_data_peserta_id = F.tbl_data_peserta_id AND B.idx_sertifikasi_id = F.idx_sertifikasi_id
					LEFT JOIN tbl_jadwal_wawancara G ON F.tbl_jadwal_wawancara_id = G.id
					LEFT JOIN idx_tuk H ON B.idx_tuk_id = H.id
					WHERE C.step_hasil = '0' $where 
					$order
				";
			break;
			case 'administrasi_peserta' :
				$sql = "
					SELECT A.id as idnya_data_peserta, A.nama_lengkap, B.tbl_data_peserta_id, B.idx_sertifikasi_id, B.kdreg_diklat,
						B.is_hadir, C.step_uji_test, C.step_uji_simulasi, A.no_handphone, A.email
					FROM tbl_data_peserta A
					LEFT JOIN ( SELECT * FROM tbl_data_diklat WHERE `status` = '1') AS B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN ( SELECT * FROM tbl_step_peserta WHERE `status` = '1') AS C ON B.tbl_data_peserta_id = C.tbl_data_peserta_id AND B.idx_sertifikasi_id = B.idx_sertifikasi_id
					LEFT JOIN ( SELECT * FROM tbl_daftar_test WHERE  status_data = '1' ) AS F ON B.tbl_data_peserta_id = F.tbl_data_peserta_id AND B.idx_sertifikasi_id = F.idx_sertifikasi_id
					LEFT JOIN tbl_jadwal_wawancara G ON F.tbl_jadwal_wawancara_id = G.id
					LEFT JOIN idx_tuk H ON B.idx_tuk_id = H.id
					WHERE 1=1 $where 
					$order
				";
			break;
			case "hasil_akhir":
				$sql = "
					SELECT A.nama_lengkap, A.no_registrasi, A.nip, D.real_name as nama_asesor,
						B.kdreg_diklat, B.idx_sertifikasi_id, B.tbl_data_peserta_id,
						E.nama_aparatur
					FROM tbl_data_peserta A
					LEFT JOIN ( SELECT * FROM tbl_data_diklat WHERE `status` = '1') AS B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN ( SELECT * FROM tbl_step_peserta WHERE `status` = '1') AS C ON B.tbl_data_peserta_id = C.tbl_data_peserta_id AND B.idx_sertifikasi_id = B.idx_sertifikasi_id
					LEFT JOIN ( SELECT * FROM tbl_user_admin WHERE level_admin = '2' ) AS D ON D.id = B.idx_asesor_id
					LEFT JOIN idx_aparatur_sipil_negara E ON B.idx_sertifikasi_id = E.id
					LEFT JOIN ( SELECT * FROM tbl_daftar_test WHERE  status_data = '1' ) AS F ON B.tbl_data_peserta_id = F.tbl_data_peserta_id AND B.idx_sertifikasi_id = F.idx_sertifikasi_id
					LEFT JOIN tbl_jadwal_wawancara G ON F.tbl_jadwal_wawancara_id = G.id
					LEFT JOIN idx_tuk H ON B.idx_tuk_id = H.id
					WHERE (C.step_hasil = '2')
					$where 
					$order
				";
			break;
			case "cetak_sertifikat":			
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, B.idx_sertifikasi_id,
						D.status_penilaian as lulus_tidak, B.kdreg_diklat, G.real_name as nama_asesor, E.is_cetak_sertifikat
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON B.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_hasil_akhir) D ON E.tbl_data_peserta_id = D.tbl_data_peserta_id AND E.idx_sertifikasi_id = D.idx_sertifikasi_id
					LEFT JOIN (SELECT * FROM tbl_user_admin WHERE level_admin = '2') AS G ON G.id = E.idx_asesor_id
					LEFT JOIN ( SELECT * FROM tbl_daftar_test ) AS F ON E.tbl_data_peserta_id = F.tbl_data_peserta_id AND E.idx_sertifikasi_id = F.idx_sertifikasi_id
					LEFT JOIN tbl_jadwal_wawancara H ON F.tbl_jadwal_wawancara_id = H.id
					WHERE B.step_hasil = '1' AND D.siap_cetak = 'Y' 
					$where
				";
			break;
			case "penjadwalan":
				$sql = "
					SELECT A.*, DATE_FORMAT( A.tanggal_wawancara,  '%d-%m-%Y' ) AS tgl_sertifikasi,
						B.nama_tuk, C.nama_aparatur
					FROM tbl_jadwal_wawancara A
					LEFT JOIN idx_tuk B ON A.idx_tuk_id = B.id
					LEFT JOIN idx_aparatur_sipil_negara C ON A.idx_sertifikasi_id = C.id
				";
			break;
		}
		
		//echo $sql;exit;
		return $this->lib->jsondata($sql, $type);
	}
	
	function simpansavedatabase($type="", $post="", $p1="", $p2="", $p3=""){
		$this->load->library('lib');
		$this->db->trans_begin();
		
		switch($type){
			case "ver_reg":				
				$this->db->update("tbl_step_peserta", array("step_registrasi"=>1, "step_asesmen_mandiri"=>3), array('tbl_data_peserta_id'=>$post['idus'], 'idx_sertifikasi_id'=>$post['idxus']) );
			break;
			case "ver_dok_peryaratan":
				$array_updater = array(
					'flag' => "V",
					'status_penilaian' => $post['kp'],
					'memo' => $post['mm'],
				);
				$this->db->update('tbl_persyaratan_sertifikasi', $array_updater, array('id'=>$post['idws']) );
			break;
			case "ver_ass":
				$countol = count($post['idxkomp'])-1;
				for($i = 0; $i <= $countol; $i++){
					$id_kmp = $post['idxkomp'][$i];
					$this->db->update("tbl_asessmen_mandiri", array("status_ver"=>$post['rek_'.$id_kmp], "memo"=>$post['memo_'.$id_kmp]), array('id'=>$id_kmp));
				}
				
				if($post['hsl_as'] == "L"){					
					$status = "L";
					$this->db->update("tbl_step_peserta", array( "step_asesmen_mandiri"=>1, "step_uji_test"=>4, "step_uji_simulasi"=>4 ), array('tbl_data_peserta_id'=>$post['usid'], 'idx_sertifikasi_id'=>$post['sertid'], "kdreg_diklat"=>$post['kdr']) );
				}elseif($post['hsl_as'] == "TL"){
					$status = "TL";
				}
				
				
				$array_asesmen_header = array(
					'status'=>$status, 
					'nama_asesor'=>$this->auth['real_name'], 
					'tgl_verifikasi'=>date('Y-m-d H:i:s')
				);
				$this->db->update("tbl_asessmen_mandiri_header", $array_asesmen_header, array('tbl_data_peserta_id'=>$post['usid'], 'idx_sertifikasi_id'=>$post['sertid'], "kdreg_diklat"=>$post['kdr']) );
			break;
			case "ver_konfpembayaran":
				/* Blok Program Untuk Verifikasi Pembayaran Peserta Oleh Admin
				$array_pembayaran_header = array(
					"status" => $post['hsl_konf'],
					'nama_asesor'=>$this->auth['real_name'],
					'tgl_verifikasi'=>date('Y-m-d H:i:s'),
				);
				
				if($post['hsl_konf'] == "L"){
					$this->db->update("tbl_step_peserta", array("step_pembayaran"=>1,"step_penjadwalan"=>2), array('tbl_data_peserta_id'=>$post['usiid'], 'idx_sertifikasi_id'=>$post['sertaidd'], "kdreg_diklat"=>$post['kdr']) );
				}elseif($post['hsl_uj'] == "TL"){
					//action kalo gak lulus
					//$this->db->update("tbl_step_peserta", array("status"=>0), array('tbl_data_peserta_id'=>$post['usiid'], 'idx_sertifikasi_id'=>$post['sertaidd']) );
				}
				$this->db->update("tbl_pembayaran_header", $array_pembayaran_header, array('tbl_data_peserta_id'=>$post['usiid'], 'idx_sertifikasi_id'=>$post['sertaidd'], "kdreg_diklat"=>$post['kdr']) );
				*/
			break;	
			case "ver_ikt_ujol":
				if($post['typ'] == 'tpa'){
					$this->db->update("tbl_step_peserta", array("step_uji_test"=>3), array('tbl_data_peserta_id'=>$post['usiid'], 'idx_sertifikasi_id'=>$post['sertaidd'], "kdreg_diklat"=>$post['kdr']) );
				}elseif($post['typ'] == 'sim'){
					$this->db->update("tbl_step_peserta", array("step_uji_simulasi"=>3), array('tbl_data_peserta_id'=>$post['usiid'], 'idx_sertifikasi_id'=>$post['sertaidd'], "kdreg_diklat"=>$post['kdr']) );
				}
			break;
			case "ver_ujol":
				/* Blok Program Ujian Online Old
				$array_ujitest_header = array(
					'total_skor'=>$post['nilai_uj'],
					'status'=>$post['hsl_uj'],
					'nama_asesor'=>$this->auth['username'],
					'tgl_verifikasi'=>date('Y-m-d H:i:s')
				);
				
				$array_wawancara_header = array(
					'tbl_data_peserta_id' => $post['usid'],
					'idx_sertifikasi_id' => $post['sertaidd'],
					'tgl_wawancara' => date('Y-m-d'),
					'status_data' => '1',
				);
				
				if($post['hsl_uj'] == "L"){
					$this->db->update("tbl_step_peserta", array("step_uji_test"=>1,"step_wawancara"=>2), array('tbl_data_peserta_id'=>$post['usid'], 'idx_sertifikasi_id'=>$post['sertaidd']) );
				}elseif($post['hsl_uj'] == "TL"){
					//action kalo gak lulus
					$this->db->update("tbl_step_peserta", array("status"=>0), array('tbl_data_peserta_id'=>$post['usid'], 'idx_sertifikasi_id'=>$post['sertaidd']) );
				}
				$this->db->update("tbl_ujitest_header", $array_ujitest_header, array('tbl_data_peserta_id'=>$post['usid'], 'idx_sertifikasi_id'=>$post['sertaidd']) );
				$this->db->insert("tbl_wawancara_header", $array_wawancara_header, array('tbl_data_peserta_id'=>$post['usid'], 'idx_sertifikasi_id'=>$post['sertaidd']) );
				*/
			break;
			case "savesimulasi":
				$array_header = array(
					"total_skor" => $post['nl_ujsm'],
					"status_penilaian" => $post['hsl_ujsm'],
					"nama_asesor" => $this->auth['real_name'],
					"tanggal_verifikasi" => date('Y-m-d H:i:s'),
				);
				$array_wawancara_header = array(
					'tbl_data_peserta_id' => $post['usid'],
					'idx_sertifikasi_id' => $post['sertid'],
					'tgl_wawancara' => date('Y-m-d'),
					'status_data' => '1',
					"kdreg_diklat" => $post['kdr'],
				);
				
				/*
				if($post['hsl_ujsm'] == "L"){
					$this->db->update("tbl_step_peserta", array("step_uji_simulasi"=>1,"step_wawancara"=>2), array('tbl_data_peserta_id'=>$post['usid'], 'idx_sertifikasi_id'=>$post['sertid']) );
				}elseif($post['hsl_ujsm'] == "TL"){
					$this->db->update("tbl_step_peserta", array("status"=>0), array('tbl_data_peserta_id'=>$post['usid'], 'idx_sertifikasi_id'=>$post['sertid']) );
				}
				*/
				
				$this->db->update("tbl_step_peserta", array("step_uji_simulasi"=>1,"step_wawancara"=>2), array('tbl_data_peserta_id'=>$post['usid'], 'idx_sertifikasi_id'=>$post['sertid'], "kdreg_diklat" => $post['kdr']) );
				$this->db->update("tbl_uji_simulasi_header", $array_header, array('tbl_data_peserta_id'=>$post['usid'], 'idx_sertifikasi_id'=>$post['sertid'], "kdreg_diklat" => $post['kdr']));
				$this->db->insert("tbl_wawancara_header", $array_wawancara_header);
			break;
			case "savewawancara":
				$array_update = array(
					"nilai" => $post['nilai_wa'],
					"status" => $post['hsl_wa'],
					"memo" => $post['ed_memo'],
					"nama_asesor" => $this->auth['real_name'],
					"tgl_verifikasi" => date('Y-m-d H:i:s'),
					'tgl_wawancara' => date('Y-m-d'),
					"kdreg_diklat" => $post['kdr'],
				);
				
				/*
				if($post['hsl_wa'] == "L"){
					$this->db->update("tbl_step_peserta", array("step_wawancara"=>1,"step_hasil"=>2), array('tbl_data_peserta_id'=>$post['forbiddenlove'], 'idx_sertifikasi_id'=>$post['forbiddenshit']) );
				}elseif($post['hsl_uj'] == "TL"){
					//action kalo gak lulus
					$this->db->update("tbl_step_peserta", array("status"=>0), array('tbl_data_peserta_id'=>$post['forbiddenlove'], 'idx_sertifikasi_id'=>$post['forbiddenshit']) );
				}
				*/
				
				$this->db->update("tbl_step_peserta", array("step_wawancara"=>1,"step_hasil"=>2), array('tbl_data_peserta_id'=>$post['forbiddenlove'], 'idx_sertifikasi_id'=>$post['forbiddenshit'], 'kdreg_diklat'=>$post['kdr']) );
				$this->db->update('tbl_wawancara_header', $array_update, array('tbl_data_peserta_id'=>$post['forbiddenlove'], 'idx_sertifikasi_id'=>$post['forbiddenshit'], 'kdreg_diklat'=>$post['kdr']) );
			break;
			case "savehasil":
				if($post['hsl_hs'] == "L"){
					$sts_remedial = 'N';
					$sdh_remedial = null;
					$siap_cetak = 'Y';
				}elseif($post['hsl_hs'] == "TL"){
					$sts_remedial = 'Y';
					$sdh_remedial = 'N';
					$siap_cetak = 'N';
				}
				
				$array_insert = array(
					"tbl_data_peserta_id" => $post['ibdff'],
					"idx_sertifikasi_id" => $post['idxsrt'],
					"status_penilaian" => $post['hsl_hs'],
					"nama_asesor" => $this->auth['real_name'],
					"tgl_verifikasi" => date('Y-m-d H:i:s'),
					"status_data" => '1',
					"kdreg_diklat" => $post['kdr'],
					"sts_remedial" => $sts_remedial,
					"sdh_remedial" => $sdh_remedial,
					"siap_cetak" => $siap_cetak
				);
				
				$this->db->update("tbl_step_peserta", array("step_hasil"=>1), array('tbl_data_peserta_id'=>$post['ibdff'], 'idx_sertifikasi_id'=>$post['idxsrt'], 'kdreg_diklat'=>$post['kdr']) );
				$this->db->insert('tbl_hasil_akhir', $array_insert);
			break;
			case "saveremedial":				
				$array_remedial = array(
					'status_penilaian' => $post['hsl_hs'],
					'memo_remedial' => $post['memo_rem'],
					'sdh_remedial' => 'Y',
					"siap_cetak" => "Y"
				);
				
				$this->db->update("tbl_hasil_akhir", $array_remedial, array('tbl_data_peserta_id'=>$post['ibdff'], 'idx_sertifikasi_id'=>$post['idxsrt'], 'kdreg_diklat'=>$post['kdr']) );
			break;
			
			case "savejadwal":
				if(isset($post['sbxx_ap_tk4'])){
					$kode_sertifikasi = $post['sbxx_ap_tk4'];
				}else{
					if(isset($post['sbxx_ap_tk3'])){
						$kode_sertifikasi = $post['sbxx_ap_tk3'];
					}else{
						$kode_sertifikasi = $post['sbxx_ap_tk2'];
					}
				}
				
				if($post['broedst'] == 'add'){
					$cekdata = $this->db->get_where('tbl_jadwal_wawancara', array("idx_tuk_id" => $post['edtuk'],"tanggal_wawancara" => $post['tggw'], "idx_sertifikasi_id"=>$kode_sertifikasi, "status"=>'A') )->row_array();
					if($cekdata){
						return 2;
						exit;
					}
					
					$htmlcek = "";
					$cekdata_syarat_registrasi = $this->db->get_where('idx_persyaratan_registrasi', array("idx_asn_id" => $kode_sertifikasi) )->result_array();
					if( !$cekdata_syarat_registrasi ){
						$htmlcek .= "
							Data Persyaratan Sertifikasi Tidak Ada, <br/>
						";
					}
					
					$cekdata_unit_kompetensi = $this->db->get_where('idx_unit_kompetensi', array("idx_aparatur_id" => $kode_sertifikasi) )->result_array();
					if( !$cekdata_unit_kompetensi ){
						$htmlcek .= "
							Data Unit Kompetensi Asesmen Mandiri Tidak Ada, <br/>
						";
					}
					
					$cekdata_soal_cat = $this->db->get_where('idx_bank_soal', array("idx_aparatur_negara" => $kode_sertifikasi) )->result_array();
					if( !$cekdata_soal_cat ){
						$htmlcek .= "
							Data Soal Uji CAT Tidak Ada, <br/>
						";
					}
					
					$cekdata_soal_simulasi = $this->db->get_where('idx_bank_soal_simulasi', array("idx_sertifikasi_id" => $kode_sertifikasi) )->result_array();
					if( !$cekdata_soal_simulasi ){
						$htmlcek .= "
							Data Soal Uji Simulasi Tidak Ada, <br/>
						";
					}
					
					if($htmlcek != ""){
						return $htmlcek;
						exit;
					}
					
				}
				
				$kode_gen = $this->lib->randomString(10);
				
				$array_save = array(
					"idx_tuk_id" => $post['edtuk'],
					"idx_sertifikasi_id" => $kode_sertifikasi,
					"tanggal_wawancara" => $post['tggw'],
					"jam" => $post['jmg'],
					"kuota" => $post['ktpp'],
					"label_pejabat1" => $post['label1'],
					"pejabat1" => $post['pejabat1'],
					"label_pejabat2" => $post['label2'],
					"pejabat2" => $post['pejabat2'],
					"label_pejabat3" => $post['label3'],
					"pejabat3" => $post['pejabat3'],
				);
				
				if($post['broedst'] == 'add'){
					$array_save['status'] = 'A';
					$array_save['kode_gen'] = strtoupper($kode_gen);
					$this->db->insert('tbl_jadwal_wawancara', $array_save);
					$dt_jadwal = $this->db->get_where('tbl_jadwal_wawancara', array('kode_gen'=>$kode_gen) )->row_array();
					$id_jadwal = $dt_jadwal['id'];
				}elseif($post['broedst'] == 'edit'){
					$id = $post['broedid'];
					$array_save['status'] = $post['sts'];
					$this->db->update('tbl_jadwal_wawancara', $array_save, array('id'=>$id) );
					$id_jadwal = $id;
				}
				
				if( isset($post['as_list']) ){
					$count = count($post['as_list'])-1;
					$array_batch = array();
					for($i = 0; $i <= $count; $i++){
						$cek_data = $this->db->get_where('tbl_jadwal_to_asesor', array('tbl_jadwal_wawancara_id'=>$id_jadwal, 'tbl_user_admin_id'=>$post['as_list'][$i]) )->row_array();
						if(!$cek_data){
							$array_insert = array(
								'tbl_jadwal_wawancara_id' => $id_jadwal,
								'tbl_user_admin_id' => $post['as_list'][$i],
							);
							array_push($array_batch, $array_insert);
						}
					}
					
					if($array_batch){
						$this->db->insert_batch('tbl_jadwal_to_asesor', $array_batch);
					}
					
				}
				
				
			break;	
			case "deletejadwal":
				$id = $post['idx_jd'];
				$cek_data_diklat = $this->db->get_where('tbl_data_diklat', array('tbl_jadwal_wawancara_id'=>$id) )->result_array();
				if($cek_data_diklat){
					return 2;
					exit;
				}
				$this->db->delete('tbl_jadwal_wawancara', array('id'=>$id) );
				$this->db->delete('tbl_jadwal_to_asesor', array('tbl_jadwal_wawancara_id'=>$id) );
			break;	
			case "savevoucher":
				$jml = ($post['jml']-1);
				for($i = 0; $i <= $jml; $i++){
					$kode_voucher = $this->lib->randomString(6);
					$kode_voucher = "VC-".$kode_voucher;
					$cek_data = $this->db->get_where('idx_voucher', array('kode_voucher'=>$kode_voucher, 'status_data'=>1) );
					if(!$cek_data){
						$kode_beneran = $kode_voucher;
					}else{
						$kode_beneran = $this->lib->randomString(6);
						$kode_beneran = "VC-".$kode_beneran;
					}
					$array_insert = array(
						'kode_voucher'=>$kode_beneran,
						'tgl_terbit'=>date('Y-m-d'),
						'status_data'=>1,
					);
					
					$this->db->insert('idx_voucher', $array_insert);
				}
			break;
			case "savepetunjukdokumen":
				$kode_acak = ($post['editstatus'] == 'add' ? $this->lib->randomString(5) : $post['kdac']);
				if(!empty($_FILES['edFile_ptjk']['name'])){
					$filepe_path = "./__repository/dokumenpetunjuk/";
					$file_pe = "petunjukdokumen_".$kode_acak;
					$filename_pe =  $this->lib->uploadnong($filepe_path, 'edFile_ptjk', $file_pe);

					$post_bnr['file_petunjuk'] = $filename_pe;
				}
				$id = $post['ixdx'];
				
				$post_bnr['nama_sertifikasi'] = $post['nm_ser'];
				$post_bnr['jenis_dokumen'] = $post['edjnka'];
				$post_bnr['tanggal_terbit'] = $post['tggl_ptn'];
				$post_bnr['kd_acak'] = $kode_acak;
				
				if($post['editstatus'] == 'add'){
					$this->db->insert('tbl_petunjukdokumen', $post_bnr);
				}elseif($post['editstatus'] == 'edit'){
					$this->db->update('tbl_petunjukdokumen', $post_bnr, array('id'=>$id));
				}
			break;
			case "saveberita":
				$kode_acak = ($post['editstatus'] == 'add' ? $this->lib->randomString(5) : $post['kdac']);
				if(!empty($_FILES['edFile_gb']['name'])){
					$filepe_path = "./__repository/gambarberita/";
					$file_pe = "filegambar_".$kode_acak;
					$filename_pe =  $this->lib->uploadnong($filepe_path, 'edFile_gb', $file_pe);

					$post_bnr['file_gambar'] = $filename_pe;
				}
				$id = $post['ixdx'];
				
				$post_bnr['judul_berita'] = $post['jd_ed'];
				$post_bnr['kd_acak'] = $kode_acak;
				$post_bnr['isi'] = $post['isbrt_ed'];
				
				if($post['editstatus'] == 'add'){
					$post_bnr['tgl_terbit'] = date('Y-m-d');
					$post_bnr['diposting_oleh'] = $this->auth['username'];
					$this->db->insert('tbl_berita', $post_bnr);
				}elseif($post['editstatus'] == 'edit'){
					$this->db->update('tbl_berita', $post_bnr, array('id'=>$id));
				}
			break;
			case "savefaq":
				$id = $post['ixdx'];
				
				$post_bnr['pertanyaan'] = $post['prtny_ed'];
				$post_bnr['jawaban'] = $post['jwb_ed'];
				
				if($post['editstatus'] == 'add'){
					$this->db->insert('tbl_faq', $post_bnr);
				}elseif($post['editstatus'] == 'edit'){
					$this->db->update('tbl_faq', $post_bnr, array('id'=>$id));
				}
			break;
			case "savebanksoal":
				if($post['editstatus'] != 'delete'){
					$acak_kode = $this->lib->randomString(8);
					$array_pertanyaan = array(
						'idx_aparatur_negara' => $post['idx_sert'],
						'soal' => $post['ed_soal'],
						'status' => $post['cbSts'],
						'kode_unik_soal' => $acak_kode,
					);
				}
				
				if($post['editstatus'] == 'add'){
					$insert_soal = $this->db->insert('idx_bank_soal', $array_pertanyaan);
					if($insert_soal){
						$get_id = $this->db->get_where('idx_bank_soal', array('kode_unik_soal'=>$acak_kode))->row_array();
						$jml_pilihan = count($post['ed_jawaban'])-1;
						$array_batch = array();
						for($i=0; $i <= $jml_pilihan; $i++){
							$array_jawaban = array(
								'idx_bank_soal_id'=> $get_id['id'],
								'jawaban'=> $post['ed_jawaban'][$i],
								'stat_flag_bnr_slh'=>  $post['flagbener'][$i],
							);
							array_push($array_batch, $array_jawaban);
						}
						$this->db->insert_batch('idx_bank_jawaban', $array_batch);
					}
				}elseif($post['editstatus'] == 'edit'){
					$update_soal = $this->db->update('idx_bank_soal', $array_pertanyaan, array('id'=>$post['idx_sola']) );
					$jml_pilihan = count($post['ed_jawaban'])-1;
					$array_batch = array();
					for($i=0; $i <= $jml_pilihan; $i++){
						$array_jawaban = array(
							'id' => $post['ixd_jb'][$i],
							'jawaban'=> $post['ed_jawaban'][$i],
							'stat_flag_bnr_slh'=>  $post['flagbener'][$i],
						);
						array_push($array_batch, $array_jawaban);
					}
					$this->db->update_batch('idx_bank_jawaban', $array_batch, 'id');
				}elseif($post['editstatus'] == 'delete'){
					$id_soal = $this->input->post('usiid');
					$this->db->delete('idx_bank_soal', array('id'=>$id_soal) );
					$this->db->delete('idx_bank_jawaban', array('idx_bank_soal_id'=>$id_soal) );
				}
			break;
			case "savebanksoal_simulasi":
				if($post['editstatus'] != 'delete'){
					$array_pertanyaan = array(
						'idx_sertifikasi_id' => $post['idx_sert'],
						'soal' => $post['ed_soal'],
						'status' => $post['cbSts'],
					);
				}
				
				if($post['editstatus'] == 'add'){
					$this->db->insert('idx_bank_soal_simulasi', $array_pertanyaan);
				}elseif($post['editstatus'] == 'edit'){
					$this->db->update('idx_bank_soal_simulasi', $array_pertanyaan, array('id'=>$post['idx_sola']) );
				}elseif($post['editstatus'] == 'delete'){
					$id_soal = $this->input->post('usiid');
					$this->db->delete('idx_bank_soal_simulasi', array('id'=>$id_soal) );
				}
			break;
			case "absensi":
				$tbl_data_peserta_id = $post['idsw'];
				$idx_sertifikasi_id = $post['idxw'];
				$parameter = $post['prm'];
				
				if($parameter == 'hdr'){
					$absensi = 1;
				}elseif($parameter == 'tdkhdr'){
					$absensi = 2;
				}
				
				$this->db->update('tbl_data_diklat', array('is_hadir'=>$absensi), array('tbl_data_peserta_id'=>$tbl_data_peserta_id, 'idx_sertifikasi_id'=>$idx_sertifikasi_id) );
			break;
		}
		
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			return "Data not saved";
		} else{
			return $this->db->trans_commit();
		}
	
	}
	
	function is_access($function_id="", $level_id=""){
		$get_policy = $this->db->get_where('idx_menu_policy', array('idx_submodule_id'=>$function_id, 'idx_level_user_id'=>$level_id))->row_array();
		$ret = false;
		
		if($get_policy){
			if($get_policy['is_access'] == 1){
				$ret = true;
			}else{
				$ret = false;
			}
		}
		
		return $ret;
	}
	
	
}