<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	LIBRARY CIPTAAN ABANG DJENONKS DKK
	KONTEN LIBRARY :
	- Upload File
	- Upload File Multiple
*/
class lib {
	public function __construct(){
		
	}
	
	//class Upload File Version 1.0 - Beta
	function uploadnong($upload_path="", $object="", $file=""){
		//$upload_path = "./__repository/".$folder."/";
		
		$ext = explode('.',$_FILES[$object]['name']);
		$exttemp = sizeof($ext) - 1;
		$extension = $ext[$exttemp];
		
		$filename =  $file.'.'.$extension;
		
		$files = $_FILES[$object]['name'];
		$tmp  = $_FILES[$object]['tmp_name'];
		if(file_exists($upload_path.$filename)){
			unlink($upload_path.$filename);
			$uploadfile = $upload_path.$filename;
		}else{
			$uploadfile = $upload_path.$filename;
		} 
		
		move_uploaded_file($tmp, $uploadfile);
		if (!chmod($uploadfile, 0775)) {
			echo "Gagal mengupload file";
			exit;
		}
		
		return $filename;
	}
	// end class Upload File
	
	//class Upload File Multiple Version 1.0 - Beta
	function uploadmultiplenong($upload_path="", $object="", $file="", $idx=""){
		$ext = explode('.',$_FILES[$object]['name'][$idx]);
		$exttemp = sizeof($ext) - 1;
		$extension = $ext[$exttemp];
		
		$filename =  $file.'.'.$extension;
		
		$files = $_FILES[$object]['name'][$idx];
		$tmp  = $_FILES[$object]['tmp_name'][$idx];
		if(file_exists($upload_path.$filename)){
			unlink($upload_path.$filename);
			$uploadfile = $upload_path.$filename;
		}else{
			$uploadfile = $upload_path.$filename;
		} 
		
		move_uploaded_file($tmp, $uploadfile);
		if (!chmod($uploadfile, 0775)) {
			echo "Gagal mengupload file";
			exit;
		}
		
		return $filename;
	}
	//end Class Upload File
	
	//class Random String Version 1.0
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
	//end Class Random String
	
	//Class CutString
	function cutstring($text, $length) {
		$isi_teks = htmlentities(strip_tags($text));
		$isi = substr($isi_teks,0,$length);
		$isi = substr($isi_teks,0,strrpos($isi," "));
		$isi = $isi.' ...';
		return $isi;
	}
	//end Class CutString
	
	//Class Kirim Email
	function kirimemail($type="", $email="", $p1="", $p2="", $p3=""){
		$ci =& get_instance();
		
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
		
		$ci->load->library('email', $config);
		$ci->load->library('smarty');
		$html = "";
		$subject = "";
		switch($type){
			case "email_registrasi":
				$ci->smarty->assign('username', $p1);
				$ci->smarty->assign('password', $p2);
				$html = $ci->smarty->fetch('modul-portal/template_email.html');
				$subject = "Registrasi Sistem Informasi Sertifikasi dan Penilaian Kementerian Dalam Negeri";
			break;
			case "email_voucher":
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
								Kode Voucher : <b>".$p1."</b> <br/>
								Tanggal Terbit : <b>".$p2."</b> <br/>
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
			break;
		}
		
		/*
		$config = array(
			"protocol"	=>"smtp"
			,"mailtype" => "html"
			,"smtp_host" => "smtp.gmail.com"
			,"smtp_user" => "triwahyunugros@gmail.com"
			,"smtp_pass" => "ms6713saa"
			,"smtp_port" => 465
		);
		*/
		
		//$ci->email->initialize($config);
		$ci->email->from("lsp@kemendagri.go.id", "LSP PEMDA - KEMENDAGRI");
		$ci->email->cc("triwahyunugros@gmail.com");
		$ci->email->cc("rahmadsyalevi@gmail.com");		
		$ci->email->to($email);
		$ci->email->subject($subject);
		$ci->email->message($html);
		$ci->email->set_newline("\r\n");
		if($ci->email->send())
			//echo "<h3> SUKSES EMAIL ke $email </h3>";
			return 1;
		else
			//echo $this->email->print_debugger();
			return $ci->email->print_debugger();
	}	
	//End Class KirimEmail
	
	function jsondata($sql, $type){
		$ci =& get_instance();
		
		$page = (integer) (($ci->input->post('page')) ? $ci->input->post('page') : "1");
		$limit = (integer) (($ci->input->post('rows')) ? $ci->input->post('rows') : "10");
		$count = $ci->db->query($sql)->num_rows();
		
		if( $count >0 ) { $total_pages = ceil($count/$limit); } else { $total_pages = 0; } 
		if ($page > $total_pages) $page=$total_pages; 
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		if($start<0) $start=0;
		 
		$sql = $sql . " LIMIT $start, $limit";
					
		$data = $ci->db->query($sql)->result_array();  
				
		if($data){
		   $responce = new stdClass();
		   $responce->rows= $data;
		   $responce->total =$count;
		   return json_encode($responce);
		}else{ 
		   $responce = new stdClass();
		   $responce->rows = 0;
		   $responce->total = 0;
		   return json_encode($responce);
		} 
	}
	
}