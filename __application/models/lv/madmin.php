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
					A.password, A.level_admin, A.aktif as stats, A.idx_tuk_id ";
				}
				
				if ($p1){ $where = "WHERE id = $p1 ";}
				
				$sql = "
					SELECT $select 
					FROM tbl_user_admin A 
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
				
				$insert_reg = $this->db->where("id", $post['kode']);
				$insert_reg = $this->db->update("tbl_user_admin", $post_bnr);
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
		}
		
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			return "Data not saved";
		} else{
			return $this->db->trans_commit();
		}
	
	}
	
	
}