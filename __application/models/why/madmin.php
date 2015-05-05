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
			case "tbl_data_peserta":
			case "tbl_data_peserta_detail":
				if($type == "tbl_data_peserta"){
					$select = " A.id, A.no_registrasi, A.nama_lengkap, A.nip, A.status ";
				}elseif($type == "tbl_data_peserta_detail"){
					$select = " 
						A.*, H.nama_pendidikan, I.nama_programstudi, C.name as nama_provinsi, D.name as nama_kabupaten, 
						E.nama_instansi, F.nama_pangkat, G.nama_aparatur, B.jabatan, B.alamat_instansi, B.idx_sertifikasi_id, B.file_pak, B.kdreg_diklat 
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
			case "tbl_diklat_terakhir":
				$sql = "
					SELECT B.*, C.name as nama_provinsi, D.name as nama_kabupaten, 
						E.nama_instansi, F.nama_pangkat
					FROM tbl_data_diklat B
					LEFT JOIN (SELECT idprov, name FROM idx_area WHERE level='1') AS C ON B.idx_provinsi_instansi_id = C.idprov
					LEFT JOIN idx_area D ON B.idx_kabupaten_instansi_id = D.id
					LEFT JOIN idx_instansi E ON B.idx_instansi_id = E.id
					LEFT JOIN idx_pangkat F ON B.idx_pangkat_id = F.id
					WHERE B.tbl_data_peserta_id = '".$p1."' 
					ORDER BY id DESC LIMIT 0,1
				";
			break;
			case "tbl_record_diklat":
				$sql = "
					SELECT B.*, C.nama_aparatur
					FROM tbl_data_diklat B
					LEFT JOIN idx_aparatur_sipil_negara C ON B.idx_sertifikasi_id = C. id
					WHERE B.tbl_data_peserta_id = '".$p1."' 
				";
			break;
			case "folder_sertifikasi":
				$sql = "
					SELECT nama_aparatur, kode_sertifikasi
					FROM idx_aparatur_sipil_negara
					WHERE id = '".$p1."'
				";
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
			case "tbl_data_peserta_diklat":
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, E.idx_sertifikasi_id,
						E.kdreg_diklat
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
					WHERE B.step_registrasi = '2'
				";
			break;
			case "tbl_peserta_asesmen":
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur,
						DATE_FORMAT( D.tgl_ujian,  '%d-%m-%Y' ) AS tanggal_ujian, E.idx_sertifikasi_id, 
						E.kdreg_diklat
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_asessmen_mandiri_header WHERE status_data=1) D ON A.id = D.tbl_data_peserta_id
					WHERE B.step_asesmen_mandiri = '2'
				";
			break;
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
			case "tbl_peserta_pembayaran":
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
					WHERE B.step_pembayaran = '2'
				";
			break;
			case "tbl_penjadwalan":
				$sql = "
					SELECT A.*, DATE_FORMAT( A.tanggal_wawancara,  '%d-%m-%Y' ) AS tgl_wawancara,
						B.nama_tuk
					FROM tbl_jadwal_wawancara A
					LEFT JOIN idx_tuk B ON A.idx_tuk_id = B.id
					WHERE A.status = 'A'
				";
			break;
			case "tbl_peserta_ujitulis_sekarang":
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, E.idx_sertifikasi_id,
						E.kdreg_diklat
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_daftar_test WHERE status_data=1) F ON A.id = F.tbl_data_peserta_id
					WHERE B.step_uji_test = '4'
				";
			break;			
			case "tbl_peserta_ujitulis":
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, 
						DATE_FORMAT( D.tgl_test,  '%d-%m-%Y' ) AS tanggal_ujian, D.jumlah_salah, D.jumlah_benar,
						E.idx_sertifikasi_id
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_ujitest_header WHERE status_data=1) D ON A.id = D.tbl_data_peserta_id
					WHERE B.step_uji_test = '2'
				";
			break;
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
			
			case "tbl_uji_simulasi_header":
				$sql = "
					SELECT *, DATE_FORMAT( tanggal_verifikasi,  '%d-%m-%Y' ) AS tgl_verifikasi
					FROM tbl_uji_simulasi_header
					WHERE tbl_data_peserta_id = '".$p1."' 
					AND idx_sertifikasi_id = '".$p2."' 
					AND kdreg_diklat = '".$p3."'
				";
			break;
			
			case "tbl_peserta_simulasi":
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, E.idx_sertifikasi_id,
						E.kdreg_diklat
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_uji_simulasi_header WHERE status_data=1) D ON A.id = D.tbl_data_peserta_id
					WHERE B.step_uji_simulasi = '2'
				";
			break;
			
			case "tbl_wawancara_header":
				$sql = "
					SELECT nilai, status, nama_asesor, memo,
						DATE_FORMAT( tgl_verifikasi,  '%d-%m-%Y' ) AS tanggal_verifikasi
					FROM tbl_wawancara_header 
					WHERE tbl_data_peserta_id = '".$p1."'
					AND idx_sertifikasi_id = '".$p2."'
				";
			break;
			case "tbl_peserta_wawancara":
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, E.idx_sertifikasi_id,
						E.kdreg_diklat
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_wawancara_header WHERE status_data=1) D ON A.id = D.tbl_data_peserta_id
					WHERE B.step_wawancara = '2'
				";
			break;
			case "tbl_peserta_hasil":
			case "tbl_peserta_cetak_sertifikat":
				$no_registrasi = $this->input->post('nre');
				
				if($type == 'tbl_peserta_hasil'){
					$where = " WHERE B.step_hasil = '2' ";
				}elseif($type == 'tbl_peserta_cetak_sertifikat'){
					$where = " WHERE B.step_hasil = '1' ";
				}
				
				if($no_registrasi){
					$where_join = " ";
					$where_join2 = " ";
					$where .= "
						AND A.no_registrasi = '".$no_registrasi."'
					";
				}else{
					$where_join = " WHERE status=1 ";
					$where_join2 = " WHERE status_data=1 ";
				}
				
				
				$sql = "
					SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, B.idx_sertifikasi_id,
						D.status_penilaian as lulus_tidak, B.kdreg_diklat
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_step_peserta $where_join ) B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
					LEFT JOIN idx_aparatur_sipil_negara C ON B.idx_sertifikasi_id = C.id
					LEFT JOIN (SELECT * FROM tbl_hasil_akhir $where_join2 ) D ON E.id = D.tbl_data_peserta_id AND E.idx_sertifikasi_id = D.idx_sertifikasi_id
					$where
				";
				//echo $sql;exit;
			break;
			case "tbl_detail_peserta_cetak":
				$sql = "
					SELECT 	A.*, H.nama_pendidikan, I.nama_programstudi, C.name as nama_provinsi, D.name as nama_kabupaten, 
						E.nama_instansi, F.nama_pangkat, G.nama_aparatur, B.jabatan, B.alamat_instansi 
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
			case "idx_voucher":
				if($p1 == 'cetak'){
					$where = " AND status_data = '1' AND is_cetak = '1' ";
				}else{
					$where = " AND status_data = '1' ";
				}
				$sql = "
					SELECT kode_voucher, DATE_FORMAT( tgl_terbit,  '%d-%m-%Y' ) AS tanggal_terbit,
						status_data, is_cetak
					FROM idx_voucher
					WHERE 1=1 $where
				";
			break;
			
			case "tbl_petunjukdokumen":
				if($p1 == 'limit'){
					$limit = " ORDER BY RAND() LIMIT 5 ";
				}else{
					$limit = "";
				}
				
				$sql = "
					SELECT *
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
						
		}
		
		if($balikan == "result_array"){
			return $this->db->query($sql)->result_array();
		}elseif($balikan == "row_array"){
			return $this->db->query($sql)->row_array();
		}
		
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
					$CI =& get_instance();
					$CI->load->model('why/mportal');
					$kode_unik = $CI->mportal->randomString('5');
					$kode_unik = "KDP-".$kode_unik;
					$status = "L";
					
					$array_pembayaran = array(
						"tbl_data_peserta_id"=>$post['usid'],
						"idx_sertifikasi_id"=>$post['sertid'],
						"kode_pembayaran"=>$kode_unik,
						"status_data"=>1,
						"kdreg_diklat"=>$post['kdr']
					);
					$this->db->insert('tbl_pembayaran_header', $array_pembayaran);
					$this->db->update("tbl_step_peserta", array("step_asesmen_mandiri"=>1,"step_pembayaran"=>3), array('tbl_data_peserta_id'=>$post['usid'], 'idx_sertifikasi_id'=>$post['sertid'], "kdreg_diklat"=>$post['kdr']) );
				}elseif($post['hsl_as'] == "TL"){
					//action kalo gak lulus
					//$this->db->update("tbl_step_peserta", array("status"=>0), array('tbl_data_peserta_id'=>$post['usid'], 'idx_sertifikasi_id'=>$post['sertid'], "kdreg_diklat"=>$post['kdr']) );
					$status = "BV";
				}
				
				$array_asesmen_header = array(
					'status'=>$status, 
					'nama_asesor'=>$this->auth['username'], 
					'tgl_verifikasi'=>date('Y-m-d H:i:s')
				);
				$this->db->update("tbl_asessmen_mandiri_header", $array_asesmen_header, array('tbl_data_peserta_id'=>$post['usid'], 'idx_sertifikasi_id'=>$post['sertid'], "kdreg_diklat"=>$post['kdr']) );
			break;
			case "ver_konfpembayaran":
				$array_pembayaran_header = array(
					"status" => $post['hsl_konf'],
					'nama_asesor'=>$this->auth['username'],
					'tgl_verifikasi'=>date('Y-m-d H:i:s'),
				);
				
				if($post['hsl_konf'] == "L"){
					$this->db->update("tbl_step_peserta", array("step_pembayaran"=>1,"step_penjadwalan"=>2), array('tbl_data_peserta_id'=>$post['usiid'], 'idx_sertifikasi_id'=>$post['sertaidd'], "kdreg_diklat"=>$post['kdr']) );
				}elseif($post['hsl_uj'] == "TL"){
					//action kalo gak lulus
					//$this->db->update("tbl_step_peserta", array("status"=>0), array('tbl_data_peserta_id'=>$post['usiid'], 'idx_sertifikasi_id'=>$post['sertaidd']) );
				}
				
				$this->db->update("tbl_pembayaran_header", $array_pembayaran_header, array('tbl_data_peserta_id'=>$post['usiid'], 'idx_sertifikasi_id'=>$post['sertaidd'], "kdreg_diklat"=>$post['kdr']) );
			break;	
			case "savejadwal":
				$array_save = array(
					"idx_tuk_id" => $post['edtuk'],
					"tanggal_wawancara" => $post['tggw'],
					"jam" => $post['jmg'],
					"kuota" => $post['ktpp'],
				);
				if($post['broedst'] == 'add'){
					$array_save['status'] = 'A';
					$this->db->insert('tbl_jadwal_wawancara', $array_save);
				}elseif($post['broedst'] == 'edit'){
					
				}
			break;	
			case "ver_ikt_ujol":
				$this->db->update("tbl_step_peserta", array("step_uji_test"=>3), array('tbl_data_peserta_id'=>$post['usiid'], 'idx_sertifikasi_id'=>$post['sertaidd'], "kdreg_diklat"=>$post['kdr']) );
			break;
			case "ver_ujol":
				/*
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
					"tbl_data_peserta_id" => $post['usid'],
					"idx_sertifikasi_id" => $post['sertid'],
					"total_skor" => $post['nl_ujsm'],
					"status_penilaian" => $post['hsl_ujsm'],
					"nama_asesor" => $this->auth['username'],
					"tanggal_verifikasi" => date('Y-m-d H:i:s'),
					"status_data" => "1",
					"kdreg_diklat" => $post['kdr'],
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
				$this->db->insert("tbl_uji_simulasi_header", $array_header);
				$this->db->insert("tbl_wawancara_header", $array_wawancara_header);
			break;
			case "savewawancara":
				$array_update = array(
					"nilai" => $post['nilai_wa'],
					"status" => $post['hsl_wa'],
					"memo" => $post['ed_memo'],
					"nama_asesor" => $this->auth['username'],
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
				$array_insert = array(
					"tbl_data_peserta_id" => $post['ibdff'],
					"idx_sertifikasi_id" => $post['idxsrt'],
					//"nilai" => $post['nilai_hs'],
					"status_penilaian" => $post['hsl_hs'],
					"nama_asesor" => $this->auth['username'],
					"tgl_verifikasi" => date('Y-m-d H:i:s'),
					"status_data" => '1',
					"kdreg_diklat" => $post['kdr'],
				);
				
				/*
				if($post['hsl_hs'] == "L"){
					$this->db->update("tbl_step_peserta", array("step_hasil"=>1), array('tbl_data_peserta_id'=>$post['ibdff'], 'idx_sertifikasi_id'=>$post['idxsrt']) );
				}elseif($post['hsl_hs'] == "TL"){
					//action kalo gak lulus
					$this->db->update("tbl_step_peserta", array("status"=>0), array('tbl_data_peserta_id'=>$post['ibdff'], 'idx_sertifikasi_id'=>$post['idxsrt']) );
				}
				*/
				
				$this->db->update("tbl_step_peserta", array("step_hasil"=>1), array('tbl_data_peserta_id'=>$post['ibdff'], 'idx_sertifikasi_id'=>$post['idxsrt'], 'kdreg_diklat'=>$post['kdr']) );
				$this->db->insert('tbl_hasil_akhir', $array_insert);
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
						'is_cetak'=>1
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
			
		}
		
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			return "Data not saved";
		} else{
			return $this->db->trans_commit();
		}
	
	}
	
	
}