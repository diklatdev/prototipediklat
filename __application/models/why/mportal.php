<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class mportal extends SHIPMENT_Model{
	function __construct(){
		parent::__construct();
		$this->auth = unserialize(base64_decode($this->session->userdata('d1kl4tkem3nd49r1-p0rt4L')));
	}
	
	function get_data($type="", $balikan="", $p1="", $p2="", $p3="", $p4=""){
		$where = "";
		switch($type){
			//Untuk combobox
			//levi
			case 'idx_level_user':
				$sql = "
					SELECT id as kode, nama_level as txt
					FROM $type
				";
			break;
			//--------
			case 'idx_pendidikan':
				$sql = "
					SELECT id as kode, nama_pendidikan as txt
					FROM $type
				";
			break;
			case 'idx_programstudi':
				$sql = "
					SELECT id as kode, nama_programstudi as txt
					FROM $type
				";
			break;
			case 'idx_provinsi':
				$sql = "
					SELECT idprov as kode, name as txt
					FROM idx_area
					WHERE level = '1'
				";
			break;
			case 'idx_instansi':
				/*
				$provinsi = $this->input->post("v2");
				if(!$provinsi){
					$provinsi = $p2;
				}
				WHERE idx_provinsi_id = '".$provinsi."'
				*/
				
				$sql = "
					SELECT id as kode, nama_instansi as txt
					FROM idx_instansi
				";
			break;			
			case 'idx_kementerian':
				$sql = "
					SELECT id as kode, nama_kementerian as txt
					FROM idx_kementerian
				";
			break;			
			case 'idx_formasi':
				$sql = "
					SELECT id as kode, nama_formasi as txt
					FROM idx_formasi
				";
			break;			
			case 'ka': //idx_kabupaten
				$provinsi = $this->input->post("v2");
				if(!$provinsi){
					$provinsi = $p2;
				}
				$sql = "
					SELECT id as kode, name as txt
					FROM idx_area
					WHERE level = '2' AND idprov = '".$provinsi."'
				";
			break;
			
			case 'idx_pangkat':
				$sql = "
					SELECT id as kode, nama_pangkat as txt
					FROM $type
				";
			break;
			
			case 'idx_aparatur':
				$sql = "
					SELECT id as kode, nama_aparatur as txt
					FROM idx_aparatur_sipil_negara
					WHERE level = '1'
				";
			break;
			case 'sb_ap_tk2': // sub_jenis_aparatur
				$aparatur = $this->input->post("v2");
				if($this->auth){
					$diklat = $this->db->get_where('tbl_data_diklat', array('tbl_data_peserta_id'=>$this->auth['id']) )->result_array();
					$array = array();
					foreach($diklat as $k => $v){
						$array[] = $v['idx_sertifikasi_id'];
					}
					$where = " AND id NOT IN ('".join("','",$array)."') ";
				}else{
					$where = "";
				}
				$sql = "
					SELECT id as kode, nama_aparatur as txt
					FROM idx_aparatur_sipil_negara
					WHERE LEVEL = '2'
					AND id_asn = '".$aparatur."'
					$where
				";
			break;
			case 'idx_asn_child_tk2': // sub_jenis_aparatur
				if($this->auth){
					$diklat = $this->db->get_where('tbl_data_diklat', array('tbl_data_peserta_id'=>$this->auth['id']) )->result_array();
					$array = array();
					foreach($diklat as $k => $v){
						$array[] = $v['idx_sertifikasi_id'];
					}
					$where = " AND id NOT IN ('".join("','",$array)."') ";
				}else{
					$where = "";
				}
				$sql = "
					SELECT id as kode, nama_aparatur as txt
					FROM idx_aparatur_sipil_negara
					WHERE LEVEL = '3'
					AND id_asn_child_tk1 = '".$p2."'
					$where
				";
			break;
			case "idx_asn_child_tk3":
				$sql = "
					SELECT id as kode, nama_aparatur as txt
					FROM idx_aparatur_sipil_negara
					WHERE LEVEL = '4'
					AND id_asn_child_tk2 = '".$p2."'
					$where
				";
			break;
			case "idx_asn_child_tk4":
				$sql = "
					SELECT id as kode, nama_aparatur as txt
					FROM idx_aparatur_sipil_negara
					WHERE LEVEL = '5'
					AND id_asn_child_tk3 = '".$p2."'
					$where
				";
			break;
			case "idx_tuk":
				$sql = "
					SELECT id as kode, nama_tuk as txt
					FROM idx_tuk
					WHERE is_aktif = '1'
				";
			break;
			//end combobox
			//untuk data login peserta
			case "data_login":
				/*
				$sql = "
					SELECT A.*, B.name as nama_provinsi, C.name as nama_kabupaten,
						D.nama_instansi, E.nama_pangkat
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT idprov, name FROM idx_area WHERE level='1') AS B ON A.idx_provinsi_instansi_id = B.idprov
					LEFT JOIN idx_area AS C ON A.idx_kabupaten_instansi_id = C.id
					LEFT JOIN idx_instansi D ON A.idx_instansi_id = D.id
					LEFT JOIN idx_pangkat E ON A.idx_pangkat_id = E.id
					WHERE A.username = '".$p1."'
				";
				*/
				$sql = "
					SELECT A.*, B.idx_sertifikasi_id, B.status as status_diklat_aktif, C.name as nama_provinsi, D.name as nama_kabupaten,
						E.nama_instansi, F.nama_pangkat, B.kdreg_diklat, G.nama_aparatur, B.idx_pangkat_id, B.idx_tuk_id, H.nama_kementerian,
						I.nama_formasi, B.idx_lokasi_id
					FROM tbl_data_peserta A
					LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status='1') B ON A.id = B.tbl_data_peserta_id
					LEFT JOIN (SELECT idprov, name FROM idx_area WHERE level='1') AS C ON B.idx_provinsi_instansi_id = C.idprov
					LEFT JOIN idx_area AS D ON B.idx_kabupaten_instansi_id = D.id
					LEFT JOIN idx_instansi E ON B.idx_instansi_id = E.id
					LEFT JOIN idx_pangkat F ON B.idx_pangkat_id = F.id
					LEFT JOIN idx_aparatur_sipil_negara G ON B.idx_sertifikasi_id = G.id
					LEFT JOIN idx_kementerian H ON B.idx_kementerian_id = H.id
					LEFT JOIN idx_formasi I ON B.idx_formasi_id = I.id
					WHERE A.username = '".$p1."'
				";
			break;
			//end data login
			
			//ambil data checking & data additional
			case "checking_kuota":
				$sql = "
					SELECT kuota
					FROM tbl_jadwal_wawancara A
					WHERE A.idx_tuk_id = '".$p1."' AND A.kuota <> '0' AND A.status = 'A'
				";
			break;
			case "dashboard_jadwal":
				$sql = "
					SELECT B.tanggal_wawancara, DATE_FORMAT( B.tanggal_wawancara,  '%d-%m-%Y' ) AS tgl_beneran
					FROM tbl_daftar_test A
					LEFT JOIN tbl_jadwal_wawancara B ON A.tbl_jadwal_wawancara_id = B.id
					WHERE tbl_data_peserta_id = '".$p1."' AND idx_sertifikasi_id = '".$p2."' 
					AND kdreg_diklat = '".$p3."' AND A.status_data = '1'
				";
			break;			
			//end ambil data checking
			
			//untuk ambil2 data dah
			case "data_unit_kompetensi":
				$where = "";
				if($this->auth['idx_sertifikasi_id'] == 21){
					if($this->auth['idx_pangkat_id'] == 5){
						$where .= " AND golongan = '4' ";
					}
					
					if($this->auth['idx_pangkat_id'] == 6 || $this->auth['idx_pangkat_id'] == 7 || $this->auth['idx_pangkat_id'] == 8){
						$where .= " AND golongan = '3' ";
					}
					
					if($this->auth['idx_pangkat_id'] == 9 || $this->auth['idx_pangkat_id'] == 10){
						$where .= " AND golongan = '2' ";
					}
					
					if($this->auth['idx_pangkat_id'] == 11 || $this->auth['idx_pangkat_id'] == 12){
						$where .= " AND golongan = '2' ";
					}
				}
				
				if($this->auth['idx_sertifikasi_id'] == 22){
					if($this->auth['idx_pangkat_id'] == 9 || $this->auth['idx_pangkat_id'] == 10){
						$where .= " AND golongan = '3' ";
					}
					
					if($this->auth['idx_pangkat_id'] == 11 || $this->auth['idx_pangkat_id'] == 12){
						$where .= " AND golongan = '2' ";
					}
					
					if($this->auth['idx_pangkat_id'] == 13 || $this->auth['idx_pangkat_id'] == 14 || $this->auth['idx_pangkat_id'] == 15){
						$where .= " AND golongan = '1' ";
					}
				}
				
				$sql = "
					SELECT *
					FROM idx_unit_kompetensi
					WHERE idx_aparatur_id = '".$this->auth['idx_sertifikasi_id']."' 
					$where
				";
			break;
			case "status_peserta":
				$sql = "
					SELECT A.*, B.nama_aparatur, B.kode_sertifikasi
					FROM tbl_step_peserta A
					LEFT JOIN idx_aparatur_sipil_negara B ON A.idx_sertifikasi_id = B.id
					WHERE A.tbl_data_peserta_id = '".$this->auth['id']."' AND A.status = '1'
				";
			break;
			
			case "tbl_pembayaran":
				$sql = "
					SELECT kode_pembayaran
					FROM tbl_pembayaran_header
					WHERE tbl_data_peserta_id = '".$this->auth['id']."' AND idx_sertifikasi_id = '".$this->auth['idx_sertifikasi_id']."' 
					AND kdreg_diklat = '".$this->auth['kdreg_diklat']."'
				";
			break;
			
			case "data_soal":
				//$start = 0;//$this->input->post('st');
				//$end = 5;//$this->input->post('ed');
				//LIMIT ".$start.", ".$end."
				
				$limit_soal = 5;
				$beda_soal = $this->input->post("so");
				$id_sertifikasi = $this->input->post("ids");
				
				if($beda_soal){
					$join_soal = join("','",$beda_soal);
					$where .= "
						AND A.id NOT IN ('".$join_soal."') 
					";
				}
				
				$sql = "
					SELECT A.*
					FROM idx_bank_soal A
					WHERE 1=1
					AND A.idx_aparatur_negara = '".$id_sertifikasi."' 
					AND A.status = '1'
					$where
					ORDER BY RAND()
					LIMIT ".$limit_soal."
				";
				$query1 = $this->db->query($sql)->result_array();
				$arraynya = array();
				$idx1 = 0;
				foreach($query1 as $k => $p){
					$arraynya[$idx1] = array();
					$arraynya[$idx1]['idnya'] =  $p['id'];
					$arraynya[$idx1]['soalnya'] =  $p['soal'];
					$arraynya[$idx1]['jawaban'] =  array();
					
					$sql2 = "
						SELECT *
						FROM idx_bank_jawaban
						WHERE idx_bank_soal_id = '".$p['id']."'
						ORDER BY RAND()
					";
					$query2 = $this->db->query($sql2)->result_array();
					$idx2 = 0;
					foreach($query2 as $s => $v){
						$arraynya[$idx1]['jawaban'][$idx2] = array();
						$arraynya[$idx1]['jawaban'][$idx2]['id_jwb'] = $v['id'];
						$arraynya[$idx1]['jawaban'][$idx2]['jawabannya'] = $v['jawaban'];
						$idx2++;
					}
					$idx1++;
				}
				
				return $arraynya;
				exit;
				
				/*
				echo "<pre>";
				print_r($arraynya);
				exit;
				//*/
				
			break;
			
			case "tbl_penjadwalan_peserta":
				if($p1){
					$where .= " AND A.id = '".$p1."' ";
				}
				if($p2){
					$where .= " AND A.idx_tuk_id = '".$p2."' AND status = 'A' ";
				}
				$sql = "
					SELECT A.*, DATE_FORMAT( A.tanggal_wawancara,  '%d-%m-%Y' ) AS tgl_wawancara,
						B.nama_tuk
					FROM tbl_jadwal_wawancara A
					LEFT JOIN idx_tuk B ON A.idx_tuk_id = B.id
					WHERE 1=1 $where
				";
			break;
			case 'tuk_peserta':
				$sql = "
					SELECT C.nama_tuk, DATE_FORMAT( B.tanggal_wawancara,  '%d-%m-%Y' ) AS tgl_wawancara, B.jam
						FROM tbl_daftar_test A
					LEFT JOIN tbl_jadwal_wawancara B ON A.tbl_jadwal_wawancara_id = B.id
					LEFT JOIN idx_tuk C ON B.idx_tuk_id = C.id
					WHERE tbl_data_peserta_id = '".$this->auth['id']."' AND idx_sertifikasi_id = '".$this->auth['idx_sertifikasi_id']."'
				";
			break;
			case "cekdataujian":
				$sql = "	
					SELECT A.*, B.soal
					FROM tbl_ujitest_temp A
					LEFT JOIN idx_bank_soal B ON A.idx_bank_soal_id = B.id
					WHERE A.tbl_data_peserta_id = '".$this->auth['id']."' AND A.idx_sertifikasi_id = '".$this->auth['idx_sertifikasi_id']."'
				";
			break;
			case "soal_sudah":
				$array = array();
				$idx = 1;
				foreach($p1 as $k => $v){
					$array[$idx] = array();
					$array[$idx]['id_soal'] = $v['idx_bank_soal_id'];
					$array[$idx]['id_jawaban'] = $v['idx_bank_jawaban_id'];
					$array[$idx]['soalnya'] = $v['soal'];
					$array[$idx]['jawaban'] = array();
					
					$sqljwb = "
						SELECT *
						FROM idx_bank_jawaban
						WHERE idx_bank_soal_id = '".$v['idx_bank_soal_id']."'
					";
					$queryjwb = $this->db->query($sqljwb)->result_array();
					$idxjwb = 1;
					foreach($queryjwb as $s => $l){
						$array[$idx]['jawaban'][$idxjwb] = array();
						$array[$idx]['jawaban'][$idxjwb]['id_jwb'] = $l['id'];
						$array[$idx]['jawaban'][$idxjwb]['jawab'] = $l['jawaban'];
						if($v['idx_bank_jawaban_id'] ==  $l['id']){
							$array[$idx]['jawaban'][$idxjwb]['selected'] = 1;
						}else{
							$array[$idx]['jawaban'][$idxjwb]['selected'] = 0;
						}
						$idxjwb++;
					}
					
					$idx++;
				}
				
				return $array;
				exit;
				
				/*
				echo "<pre>";
				print_r($array);
				exit;
				//*/
				
			break;
		
			//end
		}
		
		if($balikan == "result_array"){
			return $this->db->query($sql)->result_array();
		}elseif($balikan == "row_array"){
			return $this->db->query($sql)->row_array();
		}elseif($balikan == "row"){
		
		}
	}
	
	function simpansavedatabase($type="", $post="", $p1="", $p2="", $p3=""){
		$this->load->library('lib');
		$this->db->trans_begin();
		
		switch($type){
			case "registrasi":
				$this->load->library('encrypt');
				$sqlreg = "
					SELECT MAX(kode_reg) as reg
					FROM tbl_data_peserta
				";
				$queryreg = $this->db->query($sqlreg)->row_array();
				if($queryreg['reg'] != null){
					$number_reg = ($queryreg['reg'] + 1);
					$number_reg = sprintf('%07d', $number_reg);
				}else{
					$number_reg = "0000001";
				}
				
				if(isset($post['sb_ap_tk4'])){
					$kode_sertifikasi = $post['sb_ap_tk4'];
				}else{
					if(isset($post['sb_ap_tk3'])){
						$kode_sertifikasi = $post['sb_ap_tk3'];
					}else{
						$kode_sertifikasi = $post['sb_ap_tk2'];
					}
				}
				
				$sqlkdsert = "
					SELECT kode_sertifikasi
					FROM idx_aparatur_sipil_negara
					WHERE id = '".$kode_sertifikasi."'
				";
				$querysert = $this->db->query($sqlkdsert)->row_array();
				
				$no_reg = "NOREG.".strtoupper($querysert['kode_sertifikasi']).".".$number_reg."-".date('Y');
				$post_bnr = array();
				$post_bnr['no_registrasi'] = $no_reg;
				$post_bnr['kode_reg'] = $number_reg;
				$post_bnr['nama_lengkap'] = $post['ed_namalengkap'];
				$post_bnr['nip'] = $post['ed_nonip'];
				$post_bnr['nik'] = $post['ed_nonik'];
				$post_bnr['tempat_lahir'] = $post['ed_tmpLahir'];
				$post_bnr['tanggal_lahir'] = $post['thn_lahir']."-".$post['bln_lahir']."-".$post['tgl_lahir'];
				$post_bnr['jenis_kelamin'] = $post['ed_jnsKel'];
				$post_bnr['kebangsaan'] = $post['ed_bangsa'];
				$post_bnr['alamat_rumah'] = $post['ed_alamatRmh'];
				$post_bnr['kode_pos'] = $post['ed_kdPosRmh'];
				$post_bnr['no_telepon'] = $post['ed_tlpRmh'];
				$post_bnr['no_handphone'] = $post['ed_hempon'];
				$post_bnr['email'] = $post['ed_mailer'];
				$post_bnr['idx_pendidikan_id'] = $post['ed_pend'];
				$post_bnr['idx_programstudi_id'] = $post['ed_prodi'];
				$post_bnr['tahun_lulus'] = $post['ed_thLulus'];
				
				$cek_dir = "__repository/dokumen_peserta/".$no_reg."/";
				if(!is_dir($cek_dir)) {
					mkdir($cek_dir, 0777);         
				}				
				
				if(!empty($_FILES['edFile_ijazah']['name'])){					
					$upload_path = "./__repository/dokumen_peserta/".$no_reg."/file_data_diri/";
					mkdir($upload_path, 0777);
					
					$file = "file-ijazah_".str_replace(" ", "", $post['ed_nonip'])."_".str_replace(" ", "_", $post['ed_namalengkap']);
					$filename =  $this->lib->uploadnong($upload_path, 'edFile_ijazah', $file); //$file.'.'.$extension;

					$post_bnr['file_ijazah'] = $filename;
				}
				
				if(!empty($_FILES['edFile_passFoto']['name'])){
					$passfoto_path = "./__repository/dokumen_peserta/".$no_reg."/file_data_diri/";
					$file_passfoto = "file-passfoto_".str_replace(" ", "", $post['ed_nonip'])."_".str_replace(" ", "_", $post['ed_namalengkap']);
					$filename_passfoto =  $this->lib->uploadnong($passfoto_path, 'edFile_passFoto', $file_passfoto); //$file.'.'.$extension;

					$post_bnr['foto_profil'] = $filename_passfoto;
				}
				
				$username = str_replace(" ", "", $post['ed_nonip']);
				$password = "12345"; //strtolower($this->randomString(10));
				$post_bnr['username'] = $username;
				$post_bnr['password'] = $this->encrypt->encode($password);
				$post_bnr['status'] = "BV";
				
				//$this->kirimemail("email_registrasi", $post["ed_mailer"], $username, $password);
				
				$insert_reg = $this->db->insert("tbl_data_peserta", $post_bnr);
				if($insert_reg){					
					if(isset($post['sb_ap_tk4'])){
						$code_sert = $post['sb_ap_tk4'];
					}else{
						if(isset($post['sb_ap_tk3'])){
							$code_sert = $post['sb_ap_tk3'];
						}else{
							$code_sert = $post['sb_ap_tk2'];
						}
					}
					
					$n_sert = str_replace(" ", "_", $post['sb_jns_sert']);
					$folder_sertifikasi = $querysert['kode_sertifikasi']."-".strtolower($n_sert);

					$sql_id_peserta = "
						SELECT id
						FROM tbl_data_peserta
						WHERE nip = '".str_replace(" ", "", $post['ed_nonip'])."'
					";
					$querynya_peserta = $this->db->query($sql_id_peserta)->row_array();
					
					$kdreg_diklat = "DK.".$number_reg.".".$code_sert.".001";
					
					$array_step = array(
						"tbl_data_peserta_id" => $querynya_peserta['id'],
						"idx_sertifikasi_id" => $code_sert,
						"step_registrasi" => "2",
						"step_asesmen_mandiri" => "0",
						"step_pembayaran" => "1",
						"step_penjadwalan" => "1",
						"step_uji_test" => "0",
						"step_uji_simulasi" => "0",
						"step_wawancara" => "0",
						"step_hasil" => "0",
						"status" => "1",
						"kdreg_diklat" => $kdreg_diklat,
						
					);
					$this->db->insert("tbl_step_peserta", $array_step);
					
					$pak_path = "./__repository/dokumen_peserta/".$no_reg."/file_penentuan_angka_kredit/";
					mkdir($pak_path, 0777);

					if(!empty($_FILES['edFile_pak']['name'])){
						$pak_sertifikasi_path_1 = "./__repository/dokumen_peserta/".$no_reg."/file_penentuan_angka_kredit/".$folder_sertifikasi."/";
						mkdir($pak_sertifikasi_path_1, 0777);
						
						$pak_sertifikasi_path = "./__repository/dokumen_peserta/".$no_reg."/file_penentuan_angka_kredit/".$folder_sertifikasi."/".$kdreg_diklat."/";
						mkdir($pak_sertifikasi_path, 0777);
						
						$file_pak = "file-pak_".str_replace(" ", "", $post['ed_nonip'])."_".str_replace(" ", "_", $post['ed_namalengkap']);
						$filename_pak =  $this->lib->uploadnong($pak_sertifikasi_path, 'edFile_pak', $file_pak); 
					}else{
						$filename_pak = "";
					}
					
					$sql_asesor = "
						SELECT id
						FROM tbl_user_admin
						WHERE idx_tuk_id = '".$post['tku_dxi']."' AND idx_keahlian = '".$code_sert."'
						ORDER BY RAND() LIMIT 1
					";
					$query_asesor = $this->db->query($sql_asesor)->row_array();
					
					$array_sert = array(
						"idx_sertifikasi_id" => $code_sert,
						"tbl_data_peserta_id" => $querynya_peserta['id'],
						"file_pak" => $filename_pak,
						"idx_kementerian_id" => $post['kmnt'],
						"idx_formasi_id" => $post['frms'],
						"idx_lokasi_id" => $post['lks'],
						"idx_provinsi_instansi_id" => $post['prv'],
						"idx_kabupaten_instansi_id" => $post['ka'],
						"idx_instansi_id" => $post['ins'],
						"idx_pangkat_id" => $post['ed_pangkat'],
						"jabatan" => $post['ed_jabatan'],
						"alamat_instansi" => $post['ed_alamatKtr'],
						"status" => 1,
						"tahun" => date('Y'),
						"tanggal_daftar" => date('Y-m-d'),
						"jml_coba" => 1,
						"kdreg_diklat" => $kdreg_diklat,
						"idx_tuk_id" => $post['tku_dxi'],
						"idx_asesor_id" => $query_asesor['id']
					);
					$this->db->insert("tbl_data_diklat", $array_sert);
					
					$sql_datajadwal = "
						SELECT A.*
						FROM tbl_jadwal_wawancara A
						WHERE A.idx_tuk_id = '".$post['tku_dxi']."' AND A.status = 'A'
					";
					$data_jadwal = $this->db->query($sql_datajadwal)->row_array();
					$array_daftar_test = array(
						"idx_sertifikasi_id" => $code_sert,
						"tbl_data_peserta_id" => $querynya_peserta['id'],
						'tbl_jadwal_wawancara_id'=> $data_jadwal['id'],
						'status_data'=> 1,
						"kdreg_diklat" => $kdreg_diklat,
					);
					$kurangi_kuota = ($data_jadwal['kuota']-1);
					$this->db->insert('tbl_daftar_test', $array_daftar_test);
					$this->db->update('tbl_jadwal_wawancara', array('kuota'=>$kurangi_kuota), array('id'=>$data_jadwal['id']) );
					
					$serfifikasi_path = "./__repository/dokumen_peserta/".$no_reg."/file_persyaratan/";
					mkdir($serfifikasi_path, 0777);
					
					if(isset($post['idprs'])){
						$count = count($post['idprs']) - 1;
						$target_path_1 = "./__repository/dokumen_peserta/".$no_reg."/file_persyaratan/".$folder_sertifikasi."/";
						mkdir($target_path_1, 0777);
						
						$target_path = "./__repository/dokumen_peserta/".$no_reg."/file_persyaratan/".$folder_sertifikasi."/".$kdreg_diklat."/";
						mkdir($target_path, 0777);
						
						for($i = 0; $i <= $count; $i++){
							if($_FILES['fl_prsyrt']['name'][$i] != ''){								
								$file_p = "file_persyaratan_".$i."(".$post['idprs'][$i].")"; //"file-pak_".str_replace(" ", "", $post['ed_nonip'])."_".str_replace(" ", "_", $post['ed_namalengkap']);
								$filename_p =  $this->lib->uploadmultiplenong($target_path, 'fl_prsyrt', $file_p, $i); 
								
								$array_persyaratan = array(
									"tbl_data_peserta_id" => $querynya_peserta['id'],
									"idx_persyaratan_id" => $post['idprs'][$i],
									"nama_file" => $filename_p,
									"flag"=>"BV",
									"idx_sertifikasi_id"=>$code_sert,
									"kdreg_diklat" => $kdreg_diklat,
									"status_penilaian" => "0",
								);
								$this->db->insert("tbl_persyaratan_sertifikasi", $array_persyaratan);
							}
						}
					}
				}
			break;
			case "revpersyaratan":
				if($this->auth){
					$ci =& get_instance();
					$ci->load->model('why/madmin');
					
					$no_reg = $this->auth['no_registrasi'];
					$id 	= $this->auth['id'];
					$kdreg_diklat = $this->auth['kdreg_diklat'];
					$nm_aparatur = $this->auth['nama_aparatur'];
					$idx_sertifikasi_id = $this->auth['idx_sertifikasi_id'];
					
					$n_sert = str_replace(" ", "_", $nm_aparatur);
					$querysert = $ci->madmin->get_data('folder_sertifikasi', 'row_array', $idx_sertifikasi_id);
					$folder_sertifikasi = $querysert['kode_sertifikasi']."-".strtolower($n_sert);
					
					$target_path = "./__repository/dokumen_peserta/".$no_reg."/file_persyaratan/".$folder_sertifikasi."/".$kdreg_diklat."/";
					if(!empty($_FILES['filenya']['name'])){
						$file = explode('.', $post['nama_file']);
						$this->lib->uploadnong($target_path, 'filenya', $file[0]);
						$array_update = array(
							'flag' => 'BV',
							'status_penilaian' => 0
						);
						$this->db->update('tbl_persyaratan_sertifikasi', $array_update, array('id'=>$post['idtbl']) );
					}
				}
			break;
			case "registrasi_baru":
				if($this->auth){
					$ci =& get_instance();
					$ci->load->model('why/madmin');
					if(isset($post['sb_ap_tk3'])){
						$code_sert = $post['sb_ap_tk3'];
					}else{
						$code_sert = $post['sb_ap_tk2'];
					}
					
					$id_peserta = $post['idusrx'];
					$no_reg = $post['ktkregspso'];

					$sqlreg = "
						SELECT kode_reg as reg
						FROM tbl_data_peserta
						WHERE id = '".$id_peserta."' 
					";
					$queryreg = $this->db->query($sqlreg)->row_array();
					
					$number_reg = $queryreg['reg'];
					//$number_reg = sprintf('%07d', $number_reg);
									
					$kdreg_diklat = "DK.".$number_reg.".".$code_sert.".001";
					
					$querysert = $ci->madmin->get_data('folder_sertifikasi', 'row_array', $code_sert);
					$n_sert = str_replace(" ", "_", $post['sb_jns_sert']);
					$folder_sertifikasi = $querysert['kode_sertifikasi']."-".strtolower($n_sert);				
					
					if(!empty($_FILES['edFile_pak']['name'])){
						$pak_sertifikasi_path_1 = "./__repository/dokumen_peserta/".$no_reg."/file_penentuan_angka_kredit/".$folder_sertifikasi."/";
						mkdir($pak_sertifikasi_path_1, 0777);
						
						$pak_sertifikasi_path = "./__repository/dokumen_peserta/".$no_reg."/file_penentuan_angka_kredit/".$folder_sertifikasi."/".$kdreg_diklat."/";
						mkdir($pak_sertifikasi_path, 0777);
						
						$file_pak = "file-pak_".str_replace(" ", "", $post['ed_nonip'])."_".str_replace(" ", "_", $post['ed_namalengkap']);
						$filename_pak =  $this->lib->uploadnong($pak_sertifikasi_path, 'edFile_pak', $file_pak); 
					}else{
						$filename_pak = "";
					}				
					
					$sql_asesor = "
						SELECT id
						FROM tbl_user_admin
						WHERE idx_tuk_id = '".$post['tku_dxi']."' AND idx_keahlian = '".$code_sert."'
						ORDER BY RAND() LIMIT 1
					";
					$query_asesor = $this->db->query($sql_asesor)->row_array();
					
					$array_step = array(
						"tbl_data_peserta_id" => $id_peserta,
						"idx_sertifikasi_id" => $code_sert,
						"step_registrasi" => "2",
						"step_asesmen_mandiri" => "0",
						"step_pembayaran" => "0",
						"step_penjadwalan" => "0",
						"step_uji_test" => "0",
						"step_wawancara" => "0",
						"step_hasil" => "0",
						"status" => "1",
						"kdreg_diklat" => $kdreg_diklat,
					);
					$this->db->insert("tbl_step_peserta", $array_step);
					
					$array_sert = array(
						"idx_sertifikasi_id" => $code_sert,
						"tbl_data_peserta_id" => $id_peserta,
						"file_pak" => $filename_pak,
						"idx_kementerian_id" => $post['kmnt'],
						"idx_formasi_id" => $post['frms'],
						"idx_lokasi_id" => $post['lks'],
						"idx_provinsi_instansi_id" => $post['prv'],
						"idx_kabupaten_instansi_id" => $post['ka'],
						"idx_instansi_id" => $post['ins'],
						"idx_pangkat_id" => $post['ed_pangkat'],
						"jabatan" => $post['ed_jabatan'],
						"alamat_instansi" => $post['ed_alamatKtr'],
						"status" => 1,
						"tahun" => date('Y'),
						"tanggal_daftar" => date('Y-m-d'),
						"jml_coba" => $jml_coba,
						"kdreg_diklat" => $kdreg_diklat,
						"idx_tuk_id" => $post['tku_dxi'],
						"idx_asesor_id" => $query_asesor['id']
					);
					$this->db->insert("tbl_data_diklat", $array_sert);
					
					$sql_datajadwal = "
						SELECT A.*
						FROM tbl_jadwal_wawancara A
						WHERE A.idx_tuk_id = '".$post['tku_dxi']."' AND A.status = 'A'
					";
					$data_jadwal = $this->db->query($sql_datajadwal)->row_array();
					$array_daftar_test = array(
						"idx_sertifikasi_id" => $code_sert,
						"tbl_data_peserta_id" => $id_peserta,
						'tbl_jadwal_wawancara_id'=> $data_jadwal['id'],
						'status_data'=> 1,
						"kdreg_diklat" => $kdreg_diklat,
					);
					$kurangi_kuota = ($data_jadwal['kuota']-1);
					$this->db->insert('tbl_daftar_test', $array_daftar_test);
					$this->db->update('tbl_jadwal_wawancara', array('kuota'=>$kurangi_kuota), array('id'=>$data_jadwal['id']) );					
					
					if(isset($post['idprs'])){
						$count = count($post['idprs']) - 1;
						$target_path_1 = "./__repository/dokumen_peserta/".$no_reg."/file_persyaratan/".$folder_sertifikasi."/";
						mkdir($target_path_1, 0777);
						
						$target_path = "./__repository/dokumen_peserta/".$no_reg."/file_persyaratan/".$folder_sertifikasi."/".$kdreg_diklat."/";
						mkdir($target_path, 0777);
						
						for($i = 0; $i <= $count; $i++){
							if($_FILES['fl_prsyrt']['name'][$i] != ''){								
								$file_p = "file_persyaratan_".$i."(".$post['idprs'][$i].")"; 
								$filename_p =  $this->lib->uploadmultiplenong($target_path, 'fl_prsyrt', $file_p, $i); 
							}else{
								$filename_p = null;
							}
							
							$array_persyaratan = array(
									"tbl_data_peserta_id" => $id_peserta,
									"idx_persyaratan_id" => $post['idprs'][$i],
									"nama_file" => $filename_p,
									"flag"=>"BV",
									"idx_sertifikasi_id"=>$code_sert,
									"kdreg_diklat" => $kdreg_diklat,
									"status_penilaian" => "0",
							);
							$this->db->insert("tbl_persyaratan_sertifikasi", $array_persyaratan);
							
						}
					}
					
				}
			break;
			case "registrasi_ngulang":
				if($this->auth){
					$ci =& get_instance();
					$ci->load->model('why/madmin');
					
					$id_peserta = $post['idusrx'];
					$code_sert = $post['idxsr_lm'];
					$no_reg = $post['ktkregspso'];

					$sqlreg = "
						SELECT kode_reg as reg
						FROM tbl_data_peserta
						WHERE id = '".$id_peserta."' 
					";
					$queryreg = $this->db->query($sqlreg)->row_array();
					
					$number_reg = $queryreg['reg'];
					//$number_reg = sprintf('%07d', $number_reg);
					
					$jml_coba = ($post['cb_dbc'] + 1);
					$code_jml_coba = sprintf('%03d', $jml_coba);
					
					$kdreg_diklat = "DK.".$number_reg.".".$code_sert.".".$code_jml_coba;
					
					$querysert = $ci->madmin->get_data('folder_sertifikasi', 'row_array', $code_sert);
					$n_sert = str_replace(" ", "_", $post['sb_jns_sert']);
					$folder_sertifikasi = $querysert['kode_sertifikasi']."-".strtolower($n_sert);
					
					if(!empty($_FILES['edFile_pak']['name'])){
						$pak_sertifikasi_path = "./__repository/dokumen_peserta/".$no_reg."/file_penentuan_angka_kredit/".$folder_sertifikasi."/".$kdreg_diklat."/";
						mkdir($pak_sertifikasi_path, 0777);
						
						$file_pak = "file-pak_".str_replace(" ", "", $post['ed_nonip'])."_".str_replace(" ", "_", $post['ed_namalengkap']);
						$filename_pak =  $this->lib->uploadnong($pak_sertifikasi_path, 'edFile_pak', $file_pak); 
					}else{
						$filename_pak = "";
					}
					
					$sql_asesor = "
						SELECT id
						FROM tbl_user_admin
						WHERE idx_tuk_id = '".$post['tku_dxi']."' AND idx_keahlian = '".$code_sert."'
						ORDER BY RAND() LIMIT 1
					";
					$query_asesor = $this->db->query($sql_asesor)->row_array();
					
					$array_step = array(
						"tbl_data_peserta_id" => $id_peserta,
						"idx_sertifikasi_id" => $code_sert,
						"step_registrasi" => "2",
						"step_asesmen_mandiri" => "0",
						"step_pembayaran" => "0",
						"step_penjadwalan" => "0",
						"step_uji_test" => "0",
						"step_wawancara" => "0",
						"step_hasil" => "0",
						"status" => "1",
						"kdreg_diklat" => $kdreg_diklat,
					);
					$this->db->insert("tbl_step_peserta", $array_step);
					
					$array_sert = array(
						"idx_sertifikasi_id" => $code_sert,
						"tbl_data_peserta_id" => $id_peserta,
						"file_pak" => $filename_pak,
						"idx_kementerian_id" => $post['kmnt'],
						"idx_formasi_id" => $post['frms'],
						"idx_lokasi_id" => $post['lks'],
						"idx_provinsi_instansi_id" => $post['prv'],
						"idx_kabupaten_instansi_id" => $post['ka'],
						"idx_instansi_id" => $post['ins'],
						"idx_pangkat_id" => $post['ed_pangkat'],
						"jabatan" => $post['ed_jabatan'],
						"alamat_instansi" => $post['ed_alamatKtr'],
						"status" => 1,
						"tahun" => date('Y'),
						"tanggal_daftar" => date('Y-m-d'),
						"jml_coba" => $jml_coba,
						"kdreg_diklat" => $kdreg_diklat,
						"idx_tuk_id" => $post['tku_dxi'],
						"idx_asesor_id" => $query_asesor['id']
					);
					$this->db->insert("tbl_data_diklat", $array_sert);
					
					$sql_datajadwal = "
						SELECT A.*
						FROM tbl_jadwal_wawancara A
						WHERE A.idx_tuk_id = '".$post['tku_dxi']."' AND A.status = 'A'
					";
					$data_jadwal = $this->db->query($sql_datajadwal)->row_array();
					$array_daftar_test = array(
						"idx_sertifikasi_id" => $code_sert,
						"tbl_data_peserta_id" => $id_peserta,
						'tbl_jadwal_wawancara_id'=> $data_jadwal['id'],
						'status_data'=> 1,
						"kdreg_diklat" => $kdreg_diklat,
					);
					$kurangi_kuota = ($data_jadwal['kuota']-1);
					$this->db->insert('tbl_daftar_test', $array_daftar_test);
					$this->db->update('tbl_jadwal_wawancara', array('kuota'=>$kurangi_kuota), array('id'=>$data_jadwal['id']) );
					
					if(isset($post['idprs'])){
						$count = count($post['idprs']) - 1;
						
						$target_path = "./__repository/dokumen_peserta/".$no_reg."/file_persyaratan/".$folder_sertifikasi."/".$kdreg_diklat."/";
						mkdir($target_path, 0777);
						
						for($i = 0; $i <= $count; $i++){
							if($_FILES['fl_prsyrt']['name'][$i] != ''){								
								$file_p = "file_persyaratan_".$i."(".$post['idprs'][$i].")"; //"file-pak_".str_replace(" ", "", $post['ed_nonip'])."_".str_replace(" ", "_", $post['ed_namalengkap']);
								$filename_p =  $this->lib->uploadmultiplenong($target_path, 'fl_prsyrt', $file_p, $i); 
							}else{
								$filename_p = null;
							}
							
							$array_persyaratan = array(
									"tbl_data_peserta_id" => $id_peserta,
									"idx_persyaratan_id" => $post['idprs'][$i],
									"nama_file" => $filename_p,
									"flag"=>"BV",
									"idx_sertifikasi_id"=>$code_sert,
									"kdreg_diklat" => $kdreg_diklat,
									"status_penilaian" => "0",
							);
							$this->db->insert("tbl_persyaratan_sertifikasi", $array_persyaratan);
							
						}
					}
				}
				
			break;
			case "asesmen":
				if($this->auth){
					$this->load->model('why/madmin');
					$target_path = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_asesmen_mandiri/";
					if(!is_dir($target_path)) {
						mkdir($target_path, 0777);
					}
										
					$query_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $this->auth['idx_sertifikasi_id']);
					$n_sert = str_replace(" ", "_", $query_sertifikasi['nama_aparatur']);
					$folder_sertifikasi = $query_sertifikasi['kode_sertifikasi']."-".strtolower($n_sert);
					$target_path2_1 = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_asesmen_mandiri/".$folder_sertifikasi."/";
					if(!is_dir($target_path2_1)) {
						mkdir($target_path2_1, 0777);
					}
					
					$target_path2 = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_asesmen_mandiri/".$folder_sertifikasi."/".$this->auth['kdreg_diklat']."/";
					if(!is_dir($target_path2)) {
						mkdir($target_path2, 0777);
					}
					
					$countol = count($post['idxkomp'])-1;
					for($i = 0; $i <= $countol; $i++){
						$id_kmp = $post['idxkomp'][$i];
						if($_FILES['fl_pdk']['name'][$i] != ''){			
							$file_a = "file_kompetensi_".$i."(".$post['idxkomp'][$i].")"; 
							$filename_a =  $this->lib->uploadmultiplenong($target_path2, 'fl_pdk', $file_a, $i); 
						}else{
							$filename_a = null;
						}
						
						$arayinserting = array(
							"idx_unit_kompetensi_id" => $post['idxkomp'][$i],
							"tbl_data_peserta_id" => $this->auth['id'],
							"idx_sertifikasi_id" => $this->auth['idx_sertifikasi_id'],
							"penilaian" => $post['st_kmp_'.$id_kmp],
							"status_ver" => -1,
							"file_pendukung"=> $filename_a,
							"kdreg_diklat" => $this->auth['kdreg_diklat']
						);
						$this->db->insert("tbl_asessmen_mandiri", $arayinserting);
					}
					$this->db->update("tbl_step_peserta", array("step_asesmen_mandiri"=>2), array('tbl_data_peserta_id'=>$this->auth['id']) );
					$array_header = array(
						"tbl_data_peserta_id" => $this->auth['id'],
						"idx_sertifikasi_id" => $this->auth['idx_sertifikasi_id'],
						"tgl_ujian" => date('Y-m-d H:i:s'),
						"status" => 'BV',
						"status_data"=>'1',
						"kdreg_diklat" => $this->auth['kdreg_diklat']
					);
					$this->db->insert("tbl_asessmen_mandiri_header", $array_header);
				}else{
					header("Location: " . $this->host);
				}
			break;
			case "rev_asesmen":
				if($this->auth){
					$ci =& get_instance();
					$ci->load->model('why/madmin');
					
					$no_reg = $this->auth['no_registrasi'];
					$id 	= $this->auth['id'];
					$kdreg_diklat = $this->auth['kdreg_diklat'];
					$nm_aparatur = $this->auth['nama_aparatur'];
					$idx_sertifikasi_id = $this->auth['idx_sertifikasi_id'];
					
					$n_sert = str_replace(" ", "_", $nm_aparatur);
					$querysert = $ci->madmin->get_data('folder_sertifikasi', 'row_array', $idx_sertifikasi_id);
					$folder_sertifikasi = $querysert['kode_sertifikasi']."-".strtolower($n_sert);
					
					$target_path = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_asesmen_mandiri/".$folder_sertifikasi."/".$this->auth['kdreg_diklat']."/";
					if(!empty($_FILES['fl_pdk']['name'])){
						$id_asesmen = $post['idtbl'];
						$memo_asli = str_replace(' (Revisi Upload Dokumen, Tunggu Verifikasi Asesor)', '', $post['me']);
						
						if($post['nama_file'] != ""){
							$file = explode('.', $post['nama_file']);
							$expnya = sizeof($file) - 1;
							$file = $file[0];
						}else{
							$file = "file_kompetensi_".$post['ind']."(".$post['komp_i'].")"; 
						}
						$filename = $this->lib->uploadnong($target_path, 'fl_pdk', $file);
						
						$array_update = array(
							'penilaian' => $post['st_kmp_'.$id_asesmen],
							'file_pendukung' => $filename,
							'status_ver' => -1,
							'memo' => $memo_asli." (Revisi Upload Dokumen, Tunggu Verifikasi Asesor)",
						);
						
						$this->db->update('tbl_asessmen_mandiri', $array_update, array('id'=>$post['idtbl']) );
					}
				}
			break;
			case "savepembayaran":
				if($this->auth){
					if(!empty($_FILES['edBukti_byr']['name'])){					
						$this->load->model('why/madmin');
						
						$ext = explode('.',$_FILES['edBukti_byr']['name']);
						$exttemp = sizeof($ext) - 1;
						$extension = $ext[$exttemp];
																	
						$upload_path = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_pembayaran/";
						if(!is_dir($upload_path)) {
							mkdir($upload_path, 0777);
						}
						
						$query_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $this->auth['idx_sertifikasi_id']);
						$n_sert = str_replace(" ", "_", $query_sertifikasi['nama_aparatur']);
						$folder_sertifikasi = $query_sertifikasi['kode_sertifikasi']."-".strtolower($n_sert);
						$upload_path2_1 = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_pembayaran/".$folder_sertifikasi."/";
						mkdir($upload_path2_1, 0777);
						
						$upload_path2 = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_pembayaran/".$folder_sertifikasi."/".$this->auth['kdreg_diklat']."/";
						mkdir($upload_path2, 0777);
						
						$file = "file-buktipembayaran_".$this->auth['no_registrasi']."_".$this->auth['nama_lengkap']."_".$folder_sertifikasi;
						$filename =  $this->lib->uploadnong($upload_path2, 'edBukti_byr', $file); //$file.'.'.$extension;
						
						$post_bnr['file_bukti_pembayaran'] = $filename;
					}else{
						$post_bnr['file_bukti_pembayaran'] = null;
					}
					
					$sql_asesor = "
						SELECT id
						FROM tbl_user_admin
						WHERE idx_tuk_id = '".$this->auth['idx_tuk_id']."' AND level_admin = '5'
						ORDER BY RAND() LIMIT 1
					";
					$query_asesor = $this->db->query($sql_asesor)->row_array();
					
					$post_bnr['idx_bendahara_id'] = $query_asesor['id'];
					$post_bnr['tbl_data_peserta_id'] = $post['usaid'];
					$post_bnr['idx_sertifikasi_id'] = $post['sertaid'];
					$post_bnr['metode_pembayaran'] = $post['mtdp'];
					
					if($post['mtdp'] == 'pnbp'){
						$post_bnr['tanggal_pembayaran'] = $post['thn_byr']."-".$post['bln_byr']."-".$post['tgl_byr'];
					}elseif($post['mtdp'] == 'apbn'){
						$post_bnr['kode_voucher'] = $post['edKd_vcr'];
					}
					
					$post_bnr['tgl_konfirm'] = date('Y-m-d H:i:s');
					$post_bnr['status'] = "BV";
					
					$this->db->update('tbl_pembayaran_header', $post_bnr, array('tbl_data_peserta_id'=>$post['usaid'], 'idx_sertifikasi_id'=>$post['sertaid'], 'kdreg_diklat'=>$post['kdr']) );
					$this->db->update("tbl_step_peserta", array("step_pembayaran"=>2), array('tbl_data_peserta_id'=>$post['usaid'], 'kdreg_diklat'=>$post['kdr']) );
				}else{
					header("Location: " . $this->host);
				}
			break;	
			case "savedaftarwawancara":
				if($this->auth){
					$idx_tuk_id = $this->input->post('tkuid');
					
					$array_header = array(
						'tbl_data_peserta_id' => $this->auth['id'],
						'idx_sertifikasi_id' => $this->auth['idx_sertifikasi_id'],
						'tbl_jadwal_wawancara_id' => $post['iddf'],
						'tgl_daftar' => date('Y-m-d H:i:s'),
						'status_data' => 1,
						'kdreg_diklat' => $this->auth['kdreg_diklat']
					);
					$cek_kuota = $this->db->get_where('tbl_jadwal_wawancara', array('id'=>$post['iddf']))->row_array();
					$kurangi_kuota = ($cek_kuota['kuota']-1);
					
					$this->db->update('tbl_jadwal_wawancara', array('kuota'=>$kurangi_kuota), array('id'=>$post['iddf']) );
					$this->db->insert('tbl_daftar_test', $array_header);
					//$this->db->insert('tbl_wawancara_detail', $array_detail);
					$this->db->update("tbl_step_peserta", array("step_penjadwalan"=>1, "step_uji_test"=>4), array('tbl_data_peserta_id'=>$this->auth['id'], 'idx_sertifikasi_id'=>$this->auth['idx_sertifikasi_id']) );
				}
			break;
			case "saveujian":
				if($this->auth){
					/*
					$countjawaban = count($post['idrws'])-1;
					$jwb_bnr = 0;
					$jwb_slh = 0;
					for($i = 0; $i <= $countjawaban; $i++){
						$id_soal = $post['idrws'][$i];
						if(isset($post['idj_'.$id_soal])){
							$sqlcekjwbn = "
								SELECT stat_flag_bnr_slh
								FROM idx_bank_jawaban
								WHERE id = '".$post['idj_'.$id_soal]."'
							";
							$querycekjwbn = $this->db->query($sqlcekjwbn)->row_array();
							if($querycekjwbn['stat_flag_bnr_slh'] == 0){
								$st_jawaban = "S";
								$jwb_slh++;
							}elseif($querycekjwbn['stat_flag_bnr_slh'] == 1){
								$st_jawaban = "B";
								$jwb_bnr++;
							}
							$jawabannya = $post['idj_'.$id_soal];
						}else{
							$jwb_slh++;
							$jawabannya = null;
						}
						
						 $array_ujitest = array(
							"tbl_data_peserta_id"=>$this->auth['id'],
							"idx_sertifikasi_id"=>$this->auth['idx_sertifikasi_id'],
							"idx_bank_soal_id"=>$post['idrws'][$i],
							"idx_bank_jawaban_id"=>$jawabannya,
							"status"=>$st_jawaban,
						 );
						 $this->db->insert("tbl_ujitest", $array_ujitest);
					}
					
					$total_skor = ($jwb_bnr/$countjawaban) * 100;
					if($total_skor > 60){
						$penilaian = 'L';
					}else{
						$penilaian = 'TL';
					}
					*///ALGORITMA LAEN DAN BERUBAH.
					//$join_soal = join("','",$beda_soal);
					
					$sqljwbbnr = "
						SELECT status
						FROM tbl_ujitest_temp
						WHERE tbl_data_peserta_id = '".$this->auth['id']."'
						AND idx_sertifikasi_id = '".$this->auth['idx_sertifikasi_id']."'
						AND kdreg_diklat = '".$this->auth['kdreg_diklat']."'
						AND status = 'B'
					";
					$queryjwbbnr = $this->db->query($sqljwbbnr)->result_array();
					$jwb_bnr = count($queryjwbbnr);
					
					$sqljwbslh = "
						SELECT status
						FROM tbl_ujitest_temp
						WHERE tbl_data_peserta_id = '".$this->auth['id']."'
						AND idx_sertifikasi_id = '".$this->auth['idx_sertifikasi_id']."'
						AND kdreg_diklat = '".$this->auth['kdreg_diklat']."'
						AND status = 'S'
					";
					$queryjwbslh = $this->db->query($sqljwbslh)->result_array();
					$jwb_slh = count($queryjwbslh);
					
					$total_soal = ($jwb_bnr + $jwb_slh);
					$total_skor = ($jwb_bnr/$total_soal) * 100;
					if($total_skor > 60){
						$penilaian = 'L';
					}else{
						$penilaian = 'TL';
					}
					
					$sqlcopydata = "
						INSERT INTO tbl_ujitest (tbl_data_peserta_id, idx_sertifikasi_id, idx_bank_soal_id, idx_bank_jawaban_id, status, kdreg_diklat)
						SELECT tbl_data_peserta_id, idx_sertifikasi_id, idx_bank_soal_id, idx_bank_jawaban_id, status, kdreg_diklat
						FROM tbl_ujitest_temp
						WHERE tbl_data_peserta_id = '".$this->auth['id']."' AND idx_sertifikasi_id = '".$this->auth['idx_sertifikasi_id']."' 
						AND kdreg_diklat = '".$this->auth['kdreg_diklat']."'
					";
					$querycopydata = $this->db->query($sqlcopydata);
					if($querycopydata){
						$this->db->delete('tbl_ujitest_temp', array('tbl_data_peserta_id'=>$this->auth['id'], 'idx_sertifikasi_id'=>$this->auth['idx_sertifikasi_id'], "kdreg_diklat" => $this->auth['kdreg_diklat']) );
					}
					
					$array_ujitestheader = array(
						"tbl_data_peserta_id"=>$this->auth['id'],
						"idx_sertifikasi_id"=>$this->auth['idx_sertifikasi_id'],
						"tgl_test"=>date("Y-m-d H:i"),
						"jumlah_salah"=>$jwb_slh,
						"jumlah_benar"=>$jwb_bnr,
						"total_skor"=>$total_skor,
						"status"=>$penilaian,
						"status_data"=>"1",
						"kdreg_diklat" => $this->auth['kdreg_diklat']
					);
					$this->db->insert("tbl_ujitest_header", $array_ujitestheader);
					$this->db->update("tbl_step_peserta", array("step_uji_test"=>1, "step_uji_simulasi"=>2), array('tbl_data_peserta_id'=>$this->auth['id'], 'idx_sertifikasi_id'=>$this->auth['idx_sertifikasi_id'], "kdreg_diklat" => $this->auth['kdreg_diklat']) );
				}
			break;
			case "savesoalsisa":
				if(isset($post['so'])){
					$soal_tampil = $post['so'];
				}else{
					$soal_tampil = array();
					$sqlloadsoal = "
						SELECT id
						FROM idx_bank_soal
						WHERE idx_aparatur_negara = '".$this->auth['idx_sertifikasi_id']."'
					";
					$queryloadsoal = $this->db->query($sqlloadsoal)->result_array();
					$idx_s = 0;
					foreach($queryloadsoal as $r => $y){
						$soal_tampil[$idx_s] = $y['id'];
						$idx_s++;
					}
				}
				
				$sqlvalidjmlsoal = "
					SELECT idx_bank_soal_id
					FROM tbl_ujitest_temp
					WHERE tbl_data_peserta_id = '".$this->auth['id']."'
					AND idx_sertifikasi_id = '".$this->auth['idx_sertifikasi_id']."' 
					AND kdreg_diklat = '".$this->auth['kdreg_diklat']."'
				";
				$queryvalidjmlsoal = $this->db->query($sqlvalidjmlsoal)->result_array();
				$arraydifferent = array();
				$idx_diff = 0;
				foreach($queryvalidjmlsoal as $h => $z){
					$arraydifferent[$idx_diff] = $z['idx_bank_soal_id'];
					$idx_diff++;
				}
								
				$array_soal = array_diff($soal_tampil, $arraydifferent);
				
				if($array_soal){
					$array_batch = array();
					foreach($array_soal as $k ){
						$array_insert_slh = array(
							"tbl_data_peserta_id"=>$this->auth['id'],
							"idx_sertifikasi_id"=>$this->auth['idx_sertifikasi_id'],
							"idx_bank_soal_id"=>$k,
							"idx_bank_jawaban_id"=>null,
							"status"=>'S',
							"kdreg_diklat" => $this->auth['kdreg_diklat']
						);
						
						array_push($array_batch, $array_insert_slh);
					}
					$this->db->insert_batch('tbl_ujitest_temp', $array_batch); 
				}else{
					return 1;
				}
			break;
			case "saveujiansatuan":
				if($this->auth){
					$sqlcekjwbn = "
						SELECT stat_flag_bnr_slh
						FROM idx_bank_jawaban
						WHERE id = '".$post['jxb']."'
					";
					$querycekjwbn = $this->db->query($sqlcekjwbn)->row_array();
					if($querycekjwbn['stat_flag_bnr_slh'] == 0){
						$st_jawaban = "S";
					}elseif($querycekjwbn['stat_flag_bnr_slh'] == 1){
						$st_jawaban = "B";
					}
					
					$array_ujitest_temporary = array(
						"tbl_data_peserta_id"=>$this->auth['id'],
						"idx_sertifikasi_id"=>$this->auth['idx_sertifikasi_id'],
						"idx_bank_soal_id"=>$post['sxb'],
						"idx_bank_jawaban_id"=>$post['jxb'],
						"status"=>$st_jawaban,
						"kdreg_diklat" => $this->auth['kdreg_diklat']
					);
					
					$sqlcekdata = "
						SELECT idx_bank_soal_id, tbl_data_peserta_id,
							idx_sertifikasi_id
						FROM tbl_ujitest_temp 
						WHERE tbl_data_peserta_id = '".$this->auth['id']."'
							AND idx_sertifikasi_id = '".$this->auth['idx_sertifikasi_id']."' 
							AND kdreg_diklat = '".$this->auth['kdreg_diklat']."' 
							AND idx_bank_soal_id = '".$post['sxb']."'
					";
					$querycekdata = $this->db->query($sqlcekdata)->row_array();
					
					if($querycekdata){
						$this->db->update("tbl_ujitest_temp", $array_ujitest_temporary, array('tbl_data_peserta_id'=>$this->auth['id'], 'idx_sertifikasi_id'=>$this->auth['idx_sertifikasi_id'], "kdreg_diklat" => $this->auth['kdreg_diklat'], 'idx_bank_soal_id'=>$post['sxb']) );
					}else{
						$this->db->insert("tbl_ujitest_temp", $array_ujitest_temporary);
					}
				}
			break;
			case "saveujianwaktu":
				if($this->auth){
					$cekdata = "
						SELECT tbl_data_peserta_id, idx_sertifikasi_id
						FROM tbl_ujitest_waktu
						WHERE tbl_data_peserta_id = '".$this->auth['id']."' AND idx_sertifikasi_id = '".$this->auth['idx_sertifikasi_id']."' 
						AND kdreg_diklat = '".$this->auth['kdreg_diklat']."'
					";
					$querycekdata = $this->db->query($cekdata)->row_array();
					if(isset($post['tmzon'])){
						$timezone = $post['tmzon'];
					}else{
						$timezone = $post['tmzon2'];
					}
					
					$waktunya = trim($timezone);
					$waktunya = explode(":",$waktunya);
					
					$array_waktu = array(
						'tbl_data_peserta_id' => $this->auth['id'],
						'idx_sertifikasi_id' => $this->auth['idx_sertifikasi_id'],
						'jam' => $waktunya[0],
						'menit' => $waktunya[1],
						'detik' => $waktunya[2],
						"kdreg_diklat" => $this->auth['kdreg_diklat']
					);
					
					if(!$querycekdata){
						$this->db->insert('tbl_ujitest_waktu', $array_waktu);
					}else{
						$this->db->update('tbl_ujitest_waktu', $array_waktu, array('tbl_data_peserta_id' => $this->auth['id'], 'idx_sertifikasi_id' => $this->auth['idx_sertifikasi_id'], "kdreg_diklat" => $this->auth['kdreg_diklat']) );
					}
				}
			break;
			case "save_komplain":
				if($this->auth){
					$array_where = array(
						'tbl_data_peserta_id' => $this->auth['id'], 
						'idx_sertifikasi_id' => $this->auth['idx_sertifikasi_id'], 
						"kdreg_diklat" => $this->auth['kdreg_diklat']
					);
					$array_update = array(
						'komplain' => $post['kmpl']
					);
					$this->db->update('tbl_wawancara_header', $array_update, $array_where);
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
	
	function randomString($length,$parameter="") {
         $str = "";
		 $rangehuruf = range('A','Z');
		 $rangeangka = range('0','9');
		 if($parameter == 'reg'){
			$characters = array_merge($rangeangka);
		 }else{
			$characters = array_merge($rangehuruf, $rangeangka);
		}
         $max = count($characters) - 1;
         for ($i = 0; $i < $length; $i++) {
              $rand = mt_rand(0, $max);
              $str .= $characters[$rand];
         }
         return $str;
    }
	
}
