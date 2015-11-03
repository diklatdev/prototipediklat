<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class mpak extends SHIPMENT_Model{
	function __construct(){
		parent::__construct();
		$this->auth = unserialize(base64_decode($this->session->userdata('d1kl4tkem3nd49r1-p0rt4L')));
	}
	
	function get_data($type="", $balikan="", $p1="", $p2="", $p3="", $p4=""){
		$where = "";
		switch($type){
			//Untuk combobox
			//levi
			case 'tbl_data_peserta':
				$sql = "
					SELECT d.nama_lengkap, d.nip, p.nama_pendidikan, t.nama_pangkat, n.nama_aparatur as nama_tingkat,
					d.idx_pendidikan_id as id_pend, l.idx_pangkat_id as id_gol, n.id as tingkat_id
					FROM tbl_data_peserta d
					LEFT JOIN idx_pendidikan p ON p.id = d.idx_pendidikan_id
					LEFT JOIN tbl_data_diklat l ON l.tbl_data_peserta_id = d.id
					LEFT JOIN idx_pangkat t ON t.id = l.idx_pangkat_id
					LEFT JOIN idx_aparatur_sipil_negara n ON n.id = l.idx_sertifikasi_id
					WHERE d.id = $p1;
				";
			break;	
			case 'idx_masa_kerja':
				$sql = "SELECT * FROM idx_masa_kerja";
			break;
			case 'idx_tingkat_aparatur':			
				$where = "";
				if ($p1 != '') {
					$where = "WHERE idx_aparatur = $p1";
				}
				$sql = "SELECT * FROM idx_tingkat_aparatur $where";
			break;
			case 'idx_angka_kredit_inpassing':
				$sql = "SELECT id, angka_kredit FROM idx_angka_kredit_inpassing 
				WHERE id_tingkat = '$p1' AND id_golongan = '$p2' AND id_pendidikan = '$p3' AND id_masa_kerja = '$p4'";
			break;
			case 'tbl_pengajuan_pak_inpassing':
				if ($p2 == 'temp'){
					$sql = "SELECT 
					(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM angka_unsur_pendidikan) AS char)))) AS angka_unsur_pendidikan,
					(angka_unsur_utama*0.45) as poin_a, (angka_unsur_utama*0.45) as poin_b, (angka_unsur_utama*0.1) as poin_c,
					angka_unsur_penunjang, 
					(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM total_angka_diajukan) AS char)))) AS total_angka_diajukan
					FROM tbl_pengajuan_pak_inpassing 
					WHERE id_peserta = '$p1';";
				}else if ($p2 == 'admin'){
					$sql = "
					SELECT d.id as id_peserta, d.nama_lengkap, d.nip, p.nama_pendidikan, t.nama_pangkat, n.nama_aparatur as nama_tingkat
					, i.id as id_angka_kredit
					FROM tbl_pengajuan_pak_inpassing i 
					LEFT JOIN tbl_data_peserta d ON d.id = i.id_peserta
					LEFT JOIN idx_pendidikan p ON p.id = d.idx_pendidikan_id
					LEFT JOIN tbl_data_diklat l ON l.tbl_data_peserta_id = d.id
					LEFT JOIN idx_pangkat t ON t.id = l.idx_pangkat_id
					LEFT JOIN idx_aparatur_sipil_negara n ON n.id = l.idx_sertifikasi_id
					WHERE i.status = 0;
					";
				}else if ($p2 == 'det_pengajuan'){
					$sql = "SELECT a.id, file_sk_masa_jabatan, b.masa_kerja as masa_kerja, a.id_pendidikan, a.id_tingkat, a.id_golongan, a.catatan,
					(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM angka_unsur_pendidikan) AS char)))) AS angka_unsur_pendidikan,
					(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM (angka_unsur_utama*0.45)) AS char)))) AS poin_a, 
					(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM (angka_unsur_utama*0.45)) AS char))))  as poin_b, 
					(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM (angka_unsur_utama*0.1)) AS char)))) as poin_c,
					(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM angka_unsur_penunjang) AS char)))) AS angka_unsur_penunjang, 
					(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM angka_unsur_utama) AS char)))) AS angka_unsur_utama,
					(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM total_angka_diajukan) AS char)))) AS total_angka_diajukan, 
					(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM total_angka_diterima) AS char)))) AS total_angka_diterima,
					a.keputusan, a.instansi, DATE_FORMAT(a.masa_penilaian, '%d-%m-%Y') as masa_penilaian,DATE_FORMAT(a.masa_penilaian_sd, '%d-%m-%Y') as masa_penilaian_sd, 
					DATE_FORMAT(a.tmt, '%d-%m-%Y') as tmt, pejabat_berwenang,pejabat_pak, ditetapkan_di, nomor_sk_keputusan, k.masa_kerja as masa_kerja_diterima
					FROM tbl_pengajuan_pak_inpassing a
					LEFT JOIN idx_masa_kerja b ON b.id = a.id_masa_kerja
					LEFT JOIN idx_angka_kredit_inpassing i ON i.id = a.id_angka_kredit_diterima
					LEFT JOIN idx_masa_kerja k ON k.id = i.id_masa_kerja
					WHERE a.id = '$p3';";
				}else if ($p2 == 'result'){
					$sql = "SELECT d.id as id_peserta, d.nama_lengkap, d.nip, p.nama_pendidikan, t.nama_pangkat, n.nama_aparatur as nama_tingkat
					, i.id as id_angka_kredit, i.status
					FROM tbl_pengajuan_pak_inpassing i 
					LEFT JOIN tbl_data_peserta d ON d.id = i.id_peserta
					LEFT JOIN idx_pendidikan p ON p.id = d.idx_pendidikan_id
					LEFT JOIN tbl_data_diklat l ON l.tbl_data_peserta_id = d.id
					LEFT JOIN idx_pangkat t ON t.id = l.idx_pangkat_id
					LEFT JOIN idx_aparatur_sipil_negara n ON n.id = l.idx_sertifikasi_id
					WHERE i.status != 0;";
				}
			break;
			case 'data_pribadi_peserta':
				$sql = "SELECT d.id, d.no_registrasi, d.nama_lengkap, d.nip, p.nama_pendidikan, t.nama_pangkat, n.nama_aparatur as nama_tingkat, d.tempat_lahir, 
					DATE_FORMAT(d.tanggal_lahir, '%d-%m-%Y') as tgl_lahir, IF(d.jenis_kelamin = 'L','Laki Laki', 'Perempuan') as sex,
					l.tgl_tmt_pangkat
					FROM tbl_data_peserta d
					LEFT JOIN idx_pendidikan p ON p.id = d.idx_pendidikan_id
					LEFT JOIN tbl_data_diklat l ON l.tbl_data_peserta_id = d.id
					LEFT JOIN idx_pangkat t ON t.id = l.idx_pangkat_id
					LEFT JOIN idx_aparatur_sipil_negara n ON n.id = l.idx_sertifikasi_id
					WHERE d.id = '$p1'";
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
			//end
		}
		
		if($balikan == "result_array"){
			return $this->db->query($sql)->result_array();
		}elseif($balikan == "row_array"){
			return $this->db->query($sql)->row_array();
		}elseif($balikan == "row"){
			return $this->db->query($sql)->row();		
		}elseif($balikan == "result"){
			return $this->db->query($sql)->result();
		
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
				
				if(isset($post['sb_ap_tk3'])){
					$kode_sertifikasi = $post['sb_ap_tk3'];
				}else{
					$kode_sertifikasi = $post['sb_ap_tk2'];
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
				$password = strtolower($this->randomString(10));
				$post_bnr['username'] = $username;
				$post_bnr['password'] = $this->encrypt->encode($password);
				$post_bnr['status'] = "BV";
				
				//$this->kirimemail("email_registrasi", $post["ed_mailer"], $username, $password);
				
				$insert_reg = $this->db->insert("tbl_data_peserta", $post_bnr);
				if($insert_reg){
					if(isset($post['sb_ap_tk3'])){
						$code_sert = $post['sb_ap_tk3'];
					}else{
						$code_sert = $post['sb_ap_tk2'];
					}
					
					$n_sert = str_replace(" ", "_", $post['sb_jns_sert']);
					$folder_sertifikasi = $querysert['kode_sertifikasi']."-".strtolower($n_sert);

					$sql_id_peserta = "
						SELECT id
						FROM tbl_data_peserta
						WHERE nip = '".str_replace(" ", "", $post['ed_nonip'])."'
					";
					$querynya_peserta = $this->db->query($sql_id_peserta)->row_array();
					
					$kdreg_diklat = "KDKL.".$code_sert.".001";
					
					$array_step = array(
						"tbl_data_peserta_id" => $querynya_peserta['id'],
						"idx_sertifikasi_id" => $code_sert,
						"step_registrasi" => "2",
						"step_asesmen_mandiri" => "0",
						"step_pembayaran" => "0",
						"step_penjadwalan" => "0",
						"step_uji_test" => "0",
						"step_uji_simulasi" => "0",
						"step_wawancara" => "0",
						"step_hasil" => "0",
						"status" => "1",
						"kdreg_diklat" => $kdreg_diklat
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
					
					$array_sert = array(
						"idx_sertifikasi_id" => $code_sert,
						"tbl_data_peserta_id" => $querynya_peserta['id'],
						"file_pak" => $filename_pak,
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
						"kdreg_diklat" => $kdreg_diklat
					);
					$this->db->insert("tbl_data_diklat", $array_sert);
					
					
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
									"kdreg_diklat" => $kdreg_diklat
								);
								$this->db->insert("tbl_persyaratan_sertifikasi", $array_persyaratan);
							}
						}
					}
				}
			break;
			case "registrasi_baru":
				if(isset($post['sb_ap_tk3'])){
					$code_sert = $post['sb_ap_tk3'];
				}else{
					$code_sert = $post['sb_ap_tk2'];
				}				
				$sqlkdsert = "
					SELECT kode_sertifikasi
					FROM idx_aparatur_sipil_negara
					WHERE id = '".$code_sert."'
				";
				$querysert = $this->db->query($sqlkdsert)->row_array();
				
				$n_sert = str_replace(" ", "_", $post['sb_jns_sert']);
				$folder_sertifikasi = $querysert['kode_sertifikasi']."-".strtolower($n_sert);				
				
				$array_step = array(
					"tbl_data_peserta_id" => $post['idusrx'],
					"idx_sertifikasi_id" => $code_sert,
					"step_registrasi" => "2",
					"step_asesmen_mandiri" => "0",
					"step_pembayaran" => "0",
					"step_penjadwalan" => "0",
					"step_uji_test" => "0",
					"step_wawancara" => "0",
					"step_hasil" => "0",
					"status" => "1"
				);
				$this->db->insert("tbl_step_peserta", $array_step);
				
				if(!empty($_FILES['edFile_pak']['name'])){
					$pak_sertifikasi_path = "./repository/dokumen_peserta/".$no_reg."/file_penentuan_angka_kredit/".$folder_sertifikasi."/";
					mkdir($pak_sertifikasi_path, 0777);
					
					$file_pak = "file-pak_".str_replace(" ", "", $post['ed_nonip'])."_".str_replace(" ", "_", $post['ed_namalengkap']);
					$filename_pak =  $this->lib->uploadnong($pak_sertifikasi_path, 'edFile_pak', $file_pak); 
				}else{
					$filename_pak = "";
				}
				
				$array_sert = array(
					"idx_sertifikasi_id" => $code_sert,
					"tbl_data_peserta_id" => $post['idusrx'],
					"file_pak" => $filename_pak,
					"idx_provinsi_instansi_id" => $post['prv'],
					"idx_kabupaten_instansi_id" => $post['ka'],
					"idx_instansi_id" => $post['ins'],
					"idx_pangkat_id" => $post['ed_pangkat'],
					"jabatan" => $post['ed_jabatan'],
					"alamat_instansi" => $post['ed_alamatKtr'],
					"status" => 1,
					"tahun" => date('Y'),
					"tanggal_daftar" => date('Y-m-d')
				);
				$this->db->insert("tbl_data_diklat", $array_sert);
				
				if(isset($post['idprs'])){
					$count = count($post['idprs']) - 1;
					$target_path = "./repository/dokumen_peserta/".$post['ktkregspso']."/file_persyaratan/".$folder_sertifikasi."/";
					mkdir($target_path, 0777);
					for($i = 0; $i <= $count; $i++){
						if($_FILES['fl_prsyrt']['name'][$i] != ''){
							
							$file_p = "file_persyaratan_".$i."(".$post['idprs'][$i].")"; 
							$filename_p =  $this->lib->uploadmultiplenong($target_path, 'fl_prsyrt', $file_p, $i); 
								
							$array_persyaratan = array(
								"tbl_data_peserta_id" => $post['idusrx'],
								"idx_persyaratan_id" => $post['idprs'][$i],
								"nama_file" => $filename_p,
								"flag"=>"BV",
								"idx_sertifikasi_id"=>$code_sert
							);
							$this->db->insert("tbl_persyaratan_sertifikasi", $array_persyaratan);
						}
					}
				}
			break;
			case "asesmen":
				if($this->auth){
					$this->load->model('madmin');
					$target_path = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_asesmen_mandiri/";
					if(!is_dir($target_path)) {
						mkdir($target_path, 0777);
					}
										
					$query_sertifikasi = $this->madmin->get_data('folder_sertifikasi', 'row_array', $this->auth['idx_sertifikasi_id']);
					$n_sert = str_replace(" ", "_", $query_sertifikasi['nama_aparatur']);
					$folder_sertifikasi = $query_sertifikasi['kode_sertifikasi']."-".strtolower($n_sert);
					$target_path2_1 = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_asesmen_mandiri/".$folder_sertifikasi."/";
					mkdir($target_path2_1, 0777);
					
					$target_path2 = "./__repository/dokumen_peserta/".$this->auth['no_registrasi']."/file_asesmen_mandiri/".$folder_sertifikasi."/".$this->auth['kdreg_diklat']."/";
					mkdir($target_path2, 0777);
					
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
							"status_ver" => 0,
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
			case "savepembayaran":
				if($this->auth){
					if(!empty($_FILES['edBukti_byr']['name'])){					
						$this->load->model('madmin');
						
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
					$sql_asesor = "
						SELECT id
						FROM tbl_user_admin
						WHERE idx_tuk_id = '".$idx_tuk_id."'
						ORDER BY RAND() LIMIT 1
					";
					$query_asesor = $this->db->query($sql_asesor)->row_array();
					
					$array_header = array(
						'tbl_data_peserta_id' => $this->auth['id'],
						'idx_sertifikasi_id' => $this->auth['idx_sertifikasi_id'],
						'tbl_assesor_id' => $query_asesor['id'],
						'tbl_jadwal_wawancara_id' => $post['iddf'],
						'tgl_daftar' => date('Y-m-d H:i:s'),
						'status_data' => 1
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
					$waktunya = trim($post['tmzon']);
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
		}
		
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			return "Data not saved";
		} else{
			return $this->db->trans_commit();
		}
	}
	
	function convertNumber($number)
	{
		list($integer, $fraction) = explode(".", (string) $number);

		$output = "";

		if ($integer{0} == "-")
		{
			$output = "negative ";
			$integer    = ltrim($integer, "-");
		}
		else if ($integer{0} == "+")
		{
			$output = "positive ";
			$integer    = ltrim($integer, "+");
		}

		if ($integer{0} == "0")
		{
			$output .= "zero";
		}
		else
		{
			$integer = str_pad($integer, 36, "0", STR_PAD_LEFT);
			$group   = rtrim(chunk_split($integer, 3, " "), " ");
			$groups  = explode(" ", $group);

			$groups2 = array();
			foreach ($groups as $g)
			{
				$groups2[] = $this->convertThreeDigit($g{0}, $g{1}, $g{2});
			}

			for ($z = 0; $z < count($groups2); $z++)
			{
				if ($groups2[$z] != "")
				{
					$output .= $groups2[$z] . $this->convertGroup(11 - $z) . (
							$z < 11
							&& !array_search('', array_slice($groups2, $z + 1, -1))
							&& $groups2[11] != ''
							&& $groups[11]{0} == '0'
								? " and "
								: ", "
						);
				}
			}

			$output = rtrim($output, ", ");
		}

		if ($fraction > 0)
		{
			$output .= " point";
			for ($i = 0; $i < strlen($fraction); $i++)
			{
				$output .= " " . $this->convertDigit($fraction{$i});
			}
		}
		
		return $output;
	}

	function convertGroup($index)
	{
		switch ($index)
		{
			case 11:
				return " decillion";
			case 10:
				return " nonillion";
			case 9:
				return " octillion";
			case 8:
				return " septillion";
			case 7:
				return " sextillion";
			case 6:
				return " quintrillion";
			case 5:
				return " quadrillion";
			case 4:
				return " trillion";
			case 3:
				return " Milyar";
			case 2:
				return " Juta";
			case 1:
				return " Ribu";
			case 0:
				return "";
		}
	}

	function convertThreeDigit($digit1, $digit2, $digit3)
	{
		$buffer = "";

		if ($digit1 == "0" && $digit2 == "0" && $digit3 == "0")
		{
			return "";
		}

		if ($digit1 != "0")
		{
			$buffer .= $this->convertDigit($digit1) . " Ratus";
			if ($digit2 != "0" || $digit3 != "0")
			{
				$buffer .= "  ";
			}
		}

		if ($digit2 != "0")
		{
			$buffer .= $this->convertTwoDigit($digit2, $digit3);
		}
		else if ($digit3 != "0")
		{
			$buffer .= $this->convertDigit($digit3);
		}

		return $buffer;
	}

	function convertTwoDigit($digit1, $digit2)
	{
		if ($digit2 == "0")
		{
			switch ($digit1)
			{
				case "1":
					return "Sepuluh";
				case "2":
					return "Dua Puluh";
				case "3":
					return "Tiga Puluh";
				case "4":
					return "Empat Puluh";
				case "5":
					return "Lima Puluh";
				case "6":
					return "Enam Puluh";
				case "7":
					return "Tujuh Puluh";
				case "8":
					return "Delapan Puluh";
				case "9":
					return "Sembilan Puluh";
			}
		} else if ($digit1 == "1")
		{
			switch ($digit2)
			{
				case "1":
					return "Sebelas";
				case "2":
					return "Dua Belas";
				case "3":
					return "Tiga Belas";
				case "4":
					return "Empat Belas";
				case "5":
					return "Lima Belas";
				case "6":
					return "Enam Belas";
				case "7":
					return "Tujuh Belas";
				case "8":
					return "Delepan Belas";
				case "9":
					return "Sembilan Belas";
			}
		} else
		{
			$temp = $this->convertDigit($digit2);
			switch ($digit1)
			{
				case "2":
					return "Dua Puluh $temp";
				case "3":
					return "Tiga Puluh $temp";
				case "4":
					return "Empat Puluh $temp";
				case "5":
					return "Lima Puluh $temp";
				case "6":
					return "Enam Puluh $temp";
				case "7":
					return "Tujuh Puluh $temp";
				case "8":
					return "Delapan Puluh $temp";
				case "9":
					return "Sembilan Puluh $temp";
			}
		}
	}

	function convertDigit($digit)
	{
		switch ($digit)
		{
			case "0":
				return "Nol";
			case "1":
				return "Satu";
			case "2":
				return "Dua";
			case "3":
				return "Tiga";
			case "4":
				return "Empat";
			case "5":
				return "Lima";
			case "6":
				return "Enam";
			case "7":
				return "Tujuh";
			case "8":
				return "Delapan";
			case "9":
				return "Sembilan";
		}
	}
	
}
