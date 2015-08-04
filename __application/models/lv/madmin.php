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
/////////////////****Levi
			case "tbl_master_pejabat":
                            if ($p1){ $where = "WHERE id = $p1 ";}
				$sql = "
					SELECT id, nama_pejabat, jabatan_pejabat, kode_pangkat
					FROM tbl_master_pejabat
                                        $where
				";
			break;
			case "folder_sertifikasi":
				$sql = "
					SELECT nama_aparatur, kode_sertifikasi
					FROM idx_aparatur_sipil_negara
					WHERE id = '".$p1."'
				";
			break;
			case "tbl_user_admin":
				if($type == "tbl_user_admin"){
					$select = " A.id, A.real_name, A.username, A.level_admin, A.aktif, A.nip_user, A.email, 
					A.password, A.level_admin, A.aktif as stats, A.idx_tuk_id, A.idx_keahlian,
					B.nama_level";
					$join = "LEFT JOIN idx_level_user B ON A.level_admin = B.id";
				}
				
				if ($p1){ $where = "WHERE A.id = $p1 ";}
				
				$sql = "
					SELECT $select 
					FROM tbl_user_admin A 
					$join
					$where";
			break;
			case "idx_sertifikat":
				$select = " A.id as kode, A.nama_aparatur as txt ";
				$other = '';
				$where .= " AND (A.level = '3' OR A.level = '4') ";					
									
				$sql = "
					SELECT $select 
					FROM idx_aparatur_sipil_negara A 
					$join
					$where";
			break;
			case "idx_aparatur_sipil_negara":
				if($type == "idx_aparatur_sipil_negara"){
					$select = " A.id, A.id_asn, A.id_asn_child_tk1, A.id_asn_child_tk2,A.id_asn_child_tk2, A.nama_aparatur, A.level ";
					$other = '';
					if ($p1 == 'asn'){
						$where .= " AND A.level = '1' ";
					}else if ($p1 == 'asn_tk1'){
						$where .= " AND A.level = '2'";					
					}else if ($p1 == 'asn_tk2'){
						if ($p2){$other = "AND A.id_asn = '$p2'";}
						$where .= " AND A.level = '3' $other ";					
					}else if ($p1 == 'asn_tk3'){
						if ($p2 && $p3){$other = "AND A.id_asn_child_tk2 = '$p2'";}
						elseif ($p2){$other = "AND A.id_asn = '$p2'";}
						$where .= " AND A.level = '4' $other ";					
					}
				}
				
				$sql = "
					SELECT $select 
					FROM idx_aparatur_sipil_negara A 
					$join
					$where";
			break;
			case 'idx_level_user':
				$sql = "
					SELECT id as kode, nama_level as txt
					FROM $type
				";
			break;
			case 'idx_tuk':
				$sql = "
					SELECT A.id as kode, A.nama_tuk as txt, A.*
					FROM $type AS A
				";
			break;
			case 'idx_prov':
				if ($p1 == 'prop'){$where = "WHERE level = 1 ";}
				if ($p1 == 'kab'){$where = "WHERE level = 2 ";}
				$sql = "
					SELECT id as kode, name as txt, idprov, level
					FROM idx_area
					$where
				";
			break;
			case "idx_persyaratan_registrasi";
				if($type == "idx_persyaratan_registrasi"){
					$select = " A.id, A.idx_asn_id, A.nama_persyaratan";
				}
				
				$sql = "
					SELECT $select 
					FROM idx_persyaratan_registrasi A 
					$join
					$where";
			break;
			case "idx_unit_kompetensi";
				$select = " A.id, A.idx_aparatur_id, A.kode_unit, A.judul_unit";
				if($p1 != ''){$where .= " AND A.idx_aparatur_id = '$p1'";	}		
				if ($p2){$where .= " AND A.id = $p2"; }
				$sql = "
					SELECT $select 
					FROM idx_unit_kompetensi A 
					$join
					$where";
			break;
			case "idx_instansi":
				if ($p1){$where = "WHERE A.id = $p1";}
				
				// $sql = "
					// SELECT A.id, A.nama_instansi, A.idx_provinsi_id, R.name
					// FROM idx_instansi A 
					// LEFT JOIN idx_area R ON A.idx_provinsi_id = R.idprov AND R.level = 1
					// $join
					// $where";
					
				$sql = "
					SELECT A.id, A.nama_instansi
					FROM idx_instansi A 
					$join
					$where";
			break;
			case "idx_pangkat":
				if ($p1){ $where = "WHERE A.id = $p1";}
				$sql = "
					SELECT A.id, A.nama_pangkat
					FROM idx_pangkat A 
					$join
					$where";
			break;
			case "tbl_tuk":
				if ($p1){ $where = "WHERE A.id = $p1";}
				$sql = "
					SELECT A.id, A.nama_tuk, P.name as prop, k.name as kab, A.alamat_tuk, 
					A.idx_provinsi_id, A.idx_kab_id, A.is_aktif
					FROM idx_tuk A 
					LEFT JOIN idx_area P ON P.idprov = A.idx_provinsi_id AND P.level = 1
					LEFT JOIN idx_area k ON k.id = A.idx_kab_id AND k.level = 2
					$join
					$where";
			break;
			case "jadwal_tuk":
				$sql = "SELECT j.id as kode, j.idx_tuk_id, j.idx_sertifikasi_id, CONCAT(t.nama_tuk, ' - ', DATE_FORMAT(j.tanggal_wawancara, '%d %b %Y'))as txt, 
				DATE_FORMAT(j.tanggal_wawancara, '%d %b %Y') as tgl_jadwal, j.status
				FROM `tbl_jadwal_wawancara` j
				LEFT JOIN idx_aparatur_sipil_negara a ON a.id = j.idx_sertifikasi_id
				LEFT JOIN idx_tuk t ON t.id = j.idx_tuk_id
				-- WHERE status = 'A'
				GROUP BY txt
                                ORDER BY j.id DESC";
			break;
			case "laporan":
				//$where = '';
                                $where_aktif = "AND W.status = 'A'";
				$where_admin = '';
				if ($p1){$where = " AND W.id = '".$p1."' "; $where_aktif = "";}
				if ($p2){ if ($p3 != '99'){$where_admin = " AND idx_asesor_id = '".$p2."' ";}}
				$sql = "SELECT COUNT(T.tbl_data_peserta_id) as jum_peserta, A.nama_aparatur, W.tanggal_wawancara, W.id as id_jadwal,
                                        A.id as idx_sertifikasi, D.idx_tuk_id, D.idx_sertifikasi_id
                                        FROM tbl_daftar_test T
                                        INNER JOIN tbl_data_diklat D ON D.tbl_data_peserta_id = T.tbl_data_peserta_id
                                        INNER JOIN idx_aparatur_sipil_negara A ON A.id = D.idx_sertifikasi_id
                                        INNER JOIN tbl_jadwal_wawancara W ON W.id = T.tbl_jadwal_wawancara_id
                                        $where $where_admin $where_aktif 
                                        GROUP BY A.nama_aparatur;";
			break;
			case "tbl_data_peserta":
				$sql = "
						SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, E.idx_sertifikasi_id,
							E.kdreg_diklat, D.nama_tuk as tuknya, F.real_name as nama_asesor, A.username, A.password
						FROM tbl_data_peserta A
						LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
						LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
						LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
						LEFT JOIN idx_tuk D ON E.idx_tuk_id = D.id
						LEFT JOIN (SELECT * FROM tbl_user_admin WHERE level_admin = '2') AS F ON F.id = E.idx_asesor_id
					";
			break;
			case "tbl_data_peserta_detail":
			
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
			
				
				$sql = "
					SELECT $select 
					FROM tbl_data_peserta A 
					$join
					$where
				";
				//echo $sql;exit;
			break;
			case "progress":
				//$where = '';\
                                $where_jadwal = 'AND T.tbl_jadwal_wawancara_id =  j.id AND S.idx_sertifikasi_id = a.id';
				$where_admin = '';
                                $where_aktif = "AND J.status = 'A'";
				if ($p1){
                                    $where = " AND j.id = '".$p1."' "; 
                                    $where_jadwal = 'AND T.tbl_jadwal_wawancara_id = '.$p1.' AND S.idx_sertifikasi_id = a.id';
                                    $where_aktif = '';
                                    
                                }
				if ($p2){ if ($p3 != '99'){$where_admin = " AND idx_asesor_id = '".$p2."' ";}}
				$sql = "SELECT  a.nama_aparatur, a.id as idx_sertifikasi, d.idx_tuk_id, d.idx_sertifikasi_id, j.id as id_jadwal,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id 
						WHERE step_registrasi = 1 $where_jadwal) as step_reg,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_asesmen_mandiri = 1 $where_jadwal) as step_assesment,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_pembayaran = 1 $where_jadwal) as step_pembayaran,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_penjadwalan = 1 $where_jadwal) as step_penjadwalan,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_uji_test = 1 $where_jadwal) as step_uji_online,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_uji_simulasi = 1  $where_jadwal) as step_simulasi,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_wawancara = 1  $where_jadwal) as step_wawancara,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_hasil = 1 $where_jadwal) as step_sertifikat,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE 1=1 $where_jadwal $where_admin) as jum_peserta 
					FROM tbl_data_diklat d
					INNER JOIN idx_aparatur_sipil_negara a ON a.id = d.idx_sertifikasi_id
					INNER JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = d.tbl_data_peserta_id
					INNER JOIN tbl_jadwal_wawancara j ON j.id = T.tbl_jadwal_wawancara_id
					$where $where_aktif
					GROUP BY a.id;";
			break;
                        case "hasil_akhir":
				//$where = '';\
                                $where_jadwal = 'AND T.tbl_jadwal_wawancara_id =  j.id AND S.idx_sertifikasi_id = a.id';
				$where_admin = '';
                                $where_aktif = "AND J.status = 'A'";
				if ($p1){
                                    $where = " AND j.id = '".$p1."' "; 
                                    $where_jadwal = 'AND T.tbl_jadwal_wawancara_id = '.$p1.' AND S.idx_sertifikasi_id = a.id';
                                    $where_aktif = '';
                                    
                                }
				if ($p2){ if ($p3 != '99'){$where_admin = " AND idx_asesor_id = '".$p2."' ";}}
				$sql = "SELECT  a.nama_aparatur, a.id as idx_sertifikasi, d.idx_tuk_id, d.idx_sertifikasi_id, j.id as id_jadwal,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id 
						WHERE step_registrasi = 1 $where_jadwal) as step_reg,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_asesmen_mandiri = 1 $where_jadwal) as step_assesment,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_pembayaran = 1 $where_jadwal) as step_pembayaran,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_penjadwalan = 1 $where_jadwal) as step_penjadwalan,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_uji_test = 1 $where_jadwal) as step_uji_online,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_uji_simulasi = 1  $where_jadwal) as step_simulasi,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_wawancara = 1  $where_jadwal) as step_wawancara,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE step_hasil = 1 $where_jadwal) as step_sertifikat,
					(select count(S.id) as jumlah_peserta from tbl_step_peserta S JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = S.tbl_data_peserta_id
						WHERE 1=1 $where_jadwal $where_admin) as jum_peserta 
					FROM tbl_data_diklat d
					INNER JOIN idx_aparatur_sipil_negara a ON a.id = d.idx_sertifikasi_id
					INNER JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = d.tbl_data_peserta_id
					INNER JOIN tbl_jadwal_wawancara j ON j.id = T.tbl_jadwal_wawancara_id
					$where $where_aktif
					GROUP BY a.id;";
			break;
                        case "biodata_peserta":
                            $where_admin ='';
                            if ($p2){ if ($p3 != '99'){$where_admin = " AND E.idx_asesor_id = '".$p2."' ";}}
                            $sql = "SELECT A.id, A.no_registrasi, A.nama_lengkap, A.nip, C.nama_aparatur, E.idx_sertifikasi_id,
                                    E.kdreg_diklat, D.nama_tuk as tuknya, F.real_name as nama_asesor
                                    FROM tbl_data_peserta A
                                    LEFT JOIN (SELECT * FROM tbl_step_peserta WHERE status=1) B ON A.id = B.tbl_data_peserta_id
                                    LEFT JOIN (SELECT * FROM tbl_data_diklat WHERE status=1) E ON A.id = E.tbl_data_peserta_id
                                    LEFT JOIN idx_aparatur_sipil_negara C ON E.idx_sertifikasi_id = C.id
                                    LEFT JOIN idx_tuk D ON E.idx_tuk_id = D.id
                                    LEFT JOIN (SELECT * FROM tbl_user_admin WHERE level_admin = '2') AS F ON F.id = E.idx_asesor_id
                                    LEFT JOIN tbl_daftar_test T ON T.tbl_data_peserta_id = A.id
                                    WHERE T.tbl_jadwal_wawancara_id = '$p1' $where_admin  
                                    ORDER BY A.nama_lengkap;";
                                
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
		///////////////////*****LEVI	
			case "sv_admin":
				$this->load->library('encrypt');
				$post_bnr = array();
				$post_bnr['nip_user'] = $post['nip'];
				$post_bnr['real_name'] = $post['realName'];
				$post_bnr['username'] = $post['username'];
				$post_bnr['password'] = $this->encrypt->encode($post['pass']);
				$post_bnr['email'] = $post['email'];
				$post_bnr['level_admin'] = $post['level'];
				$post_bnr['aktif'] = $post['status'];
				$post_bnr['idx_tuk_id'] = $post['tuk'];
				$post_bnr['idx_keahlian'] = $post['sertifikasi_id'];
				
				$insert_reg = $this->db->insert("tbl_user_admin", $post_bnr);
			break;
			case "up_admin":
				$this->load->library('encrypt');
				$post_bnr = array();
				$post_bnr['nip_user'] = $post['nip'];
				$post_bnr['real_name'] = $post['realName'];
				$post_bnr['username'] = $post['username'];
				$post_bnr['password'] = $this->encrypt->encode($post['pass']);
				$post_bnr['email'] = $post['email'];
				$post_bnr['level_admin'] = $post['level'];
				$post_bnr['aktif'] = $post['status'];
				$post_bnr['idx_tuk_id'] = $post['tuk'];
				$post_bnr['idx_keahlian'] = $post['sertifikasi_id'];
				
				$insert_reg = $this->db->where("id", $post['kode']);
				$insert_reg = $this->db->update("tbl_user_admin", $post_bnr);
			break;
			case "del_admin":
				$insert_reg = $this->db->where("id", $post['kode']);
				$insert_reg = $this->db->delete("tbl_user_admin");
			break;
			case "up_peserta":
				$this->load->library('encrypt');
				$post_bnr = array();
				$post_bnr['password'] = $this->encrypt->encode($post['newpass']);
				
				$insert_reg = $this->db->where("id", $post['kode']);
				$insert_reg = $this->db->update("tbl_data_peserta", $post_bnr);
			break;
			case "sv_aparat":
				$post_bnr = array();
				
				$post_bnr['id_asn'] = $post['ap_tk_1'];
				$post_bnr['id_asn_child_tk1'] = $post['sb_ap_tk2'];
				$post_bnr['level'] = '3';
				$post_bnr['nama_aparatur'] = $post['sertifikasi'];
				
				$insert_reg = $this->db->insert("idx_aparatur_sipil_negara", $post_bnr);
			break;
			case "sv_syarat":
				$post_bnr = array();
				if (isset($post['sb_ap_tk3'])){
					$post_bnr['idx_asn_id'] = $post['sb_ap_tk3'];
				}else{
					$post_bnr['idx_asn_id'] = $post['sb_ap_tk2'];
				}
				$post_bnr['nama_persyaratan'] = $post['syarat'];
				
				$insert_reg = $this->db->insert("idx_persyaratan_registrasi", $post_bnr);
			break;
			case "sv_uji_man":
				$post_bnr = array();
				if (isset($post['ap_tk4'])){
					$id_aparatur = $post['ap_tk4'];
				}else{
					$id_aparatur = $post['sb_ap_tk3'];
				}
				$post_bnr['idx_aparatur_id'] = $id_aparatur;
				$post_bnr['kode_unit'] = $post['no_unit'];
				$post_bnr['judul_unit'] = preg_replace('#(\\\r\\\n)#', '<p/>', $post['nama_unit']);
				
				$insert_reg = $this->db->insert("idx_unit_kompetensi", $post_bnr);
			break;
			case "sv_instansi":
				$post_bnr = array();
				$post_bnr['nama_instansi'] = $post['nama_ins'];
				// $post_bnr['idx_provinsi_id'] = $post['prv'];
				//$post_bnr['idx_kab_id'] = $post['ka'];
				if ($p1 == 'sv'){
					$insert_reg = $this->db->insert("idx_instansi", $post_bnr);
				}elseif ($p1 == 'up'){
					$insert_reg = $this->db->where("id", $post['kode']);
					$insert_reg = $this->db->update("idx_instansi", $post_bnr);
				}
			break;
			case "sv_pangkat":
				$post_bnr = array();
				$post_bnr['nama_pangkat'] = $post['nama_pangkat'];
				if ($p1 == 'sv'){
					$insert_reg = $this->db->insert("idx_pangkat", $post_bnr);
				}elseif ($p1 == 'up'){
					$insert_reg = $this->db->where("id", $post['kode']);
					$insert_reg = $this->db->update("idx_pangkat", $post_bnr);
				}
			break;
			case "sv_tuk":
				$post_bnr = array();
				$post_bnr['idx_provinsi_id'] = $post['prv'];
				$post_bnr['idx_kab_id'] = $post['ka'];
				$post_bnr['nama_tuk'] = $post['nama_tuk'];
				$post_bnr['alamat_tuk'] = $post['al_tuk'];
				$post_bnr['is_aktif'] = $post['status'];
				
				if ($p1 == 'sv'){				
					$insert_reg = $this->db->insert("idx_tuk", $post_bnr);
				}elseif ($p1 == 'up'){
					$insert_reg = $this->db->where("id", $post['kode']);
					$insert_reg = $this->db->update("idx_tuk", $post_bnr);
				}
			break;
			case "sv_pejabat":
				$post_bnr = array();
                                if ($p1 != 'del'){
                                    $post_bnr['nama_pejabat'] = $post['nama_pejabat'];
                                    $pangkat = $post['pangkat_pejabat'];
                                    $arr = explode("_", $pangkat);
                                    $kode_pangkat = $arr[0];
                                    $nama_pangkat = $arr[1];
                                    $post_bnr['kode_pangkat'] = $kode_pangkat;
                                    $post_bnr['jabatan_pejabat'] = $nama_pangkat;
                                }
				if ($p1 == 'sv'){
					$insert_reg = $this->db->insert("tbl_master_pejabat", $post_bnr);
				}elseif ($p1 == 'up'){
					$insert_reg = $this->db->where("id", $post['kode']);
					$insert_reg = $this->db->update("tbl_master_pejabat", $post_bnr);
				}elseif ($p1 == 'del'){
					$insert_reg = $this->db->where("id", $post['kode']);
                                        $insert_reg = $this->db->delete("tbl_master_pejabat");                                
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