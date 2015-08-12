$( document ).ready(function() {
	//kombo kabupaten when refresh
	if( $('#prv').val() != "" ){
		fillCombo(host+"combo/ka/", 'ka', "", $('#prv').val() );
	}
	
});


function fillCombo(url, SelID, value, value2, value3, value4){
	if (value == undefined) value = "";
	if (value2 == undefined) value2 = "";
	if (value3 == undefined) value3 = "";
	if (value4 == undefined) value4 = "";
	
	$('#'+SelID).empty();
	$.post(url, {"v": value, "v2": value2, "v3": value3, "v4": value4},function(data){
		$('#'+SelID).append(data);
	});
}

function processCombo(type){
	switch(type){
		case "prv":
			fillCombo(host+"combo/ka/", 'ka', "", $('#'+type).val() );
			//fillCombo(host+"combo/ins/", 'ins', "", $('#'+type).val() );
		break;
		case "ck_tku":
			$.post(host+"ck-tku", { 'xtu_id':$('#tku_dxi').val() }, function(rsp){
				if(rsp == 0){
					$('#res-cku').html('<font color="red">Maaf, Jadwal Ujian Sertifikasi Tidak Tersedia. Pilih TUK Lain.</font>');
					$('#tku_dxi').val('');
					$('#sertifikasi_1').html("");
					$('#res-pak').html("");
				}else if(rsp == -2){
					$('#res-cku').html('');
					$('#tku_dxi').val('');
					$('#sertifikasi_1').html("");
					$('#res-pak').html("");
				}else if(rsp == -3){
					$('#res-cku').html('<font color="red">Maaf, Jadwal Ujian Sertifikasi Sudah Tidak Berlaku. Pilih TUK Lain.</font>');
					$('#tku_dxi').val('');
					$('#sertifikasi_1').html("");
					$('#res-pak').html("");
				}else{
					var data = $.parseJSON(rsp);
					var nilai_pak = $('#ed_nilaipak').val();
					
					if(data.is_pak == "1"){
						//$('#ed_nilaipak').addClass('validatext');
						if(nilai_pak <= data.min_nilai_pak){
							$('#res-pak').html('<font color="red">Nilai PAK Anda Tidak Memenuhi Syarat, Minimum PAK untuk Sertifikasi '+data.sertifikasi+' adalah '+data.min_nilai_pak+' </font>');
							$('#ed_nilaipak').val('');
						}else{
							$('#res-pak').html('<font color="green">Nilai PAK Anda Memenuhi Syarat</font>');
						}
					}else if(data.is_pak == "0"){
						$('#ed_nilaipak').removeClass('validatextpak');
					}
					
					$('#pnmpng_asp').val(data.sertifikasi);
					$('#sertis_id').val(data.idx_sertifikasi_id);
					$('#res-cku').html('<label style="margin-left:-10px;">Sertifikasi '+data.sertifikasi+'</label> <br><font color="green">Jadwal Ujian TUK Tanggal '+data.tanggal+' - Masih Tersedia.</font>');
					
					$('#sertifikasi_1').html("");
					$('#sertifikasi_1').addClass("loading");
					$.post(host+"reg-file", { 'id_asn':data.idx_sertifikasi_id }, function(respp){
						$('#sertifikasi_1').html(respp).removeClass("loading");
					});
					
					
				}
			});
		break;
		case "ap_tk_1":
			fillCombo(host+"combo/sb_ap_tk2/", 'sb_ap_tk2', "", $('#'+type).val() );
			$('#fl_s').remove();
			$('#sert_tam').remove();
			$('#sert_tam_2').remove();
			$('#sert_tam_3').remove();
			
			if( $('#'+type).val() == 2 ){
				$('#txtsbjn').html('Urusan Pemerintah : ');
			}else{
				$('#txtsbjn').html('Sub Jenis Sertifikasi : ');
			}
			
		break;
		case "sb_ap_tk2":
			$.post(host+"chk", { 'id_asn_child_tk1':$('#'+type).val() }, function(resp){
				if(resp == 0){
					$('#fl_s').remove();
					$('#sert_tam').remove();
					$('#sert_tam_2').remove();
					$.post(host+"reg-file", { 'id_asn':$('#'+type).val() }, function(resp){
						$('#sertifikasi_1').append(resp);
					});
				}else{
					$('#fl_s').remove();
					$('#sert_tam').remove();
					$('#sert_tam_2').remove();
					$('#sertifikasi_1').append(resp);
					if( $('#ap_tk_1').val() == 2 ){
						$('#txtsbjnsrt').html('Tipologi : ');
					}else{
						$('#txtsbjnsrt').html('Jenjang Sertifikasi : ');
					}
				}
			});
			$("#sb_jns_nxx").val($('#'+type+" :selected").text());
			
			
		break;
		case "sb_ap_tk3":
			$('#fl_s').remove();
			$('#sert_tam_2').remove();
			$('#sert_tam_3').remove();
			$.post(host+"chk2", { 'id_asn_child_tk2':$('#'+type).val() }, function(resp){
				if(resp == 0){
					//$('#fl_s').remove();
					//$('#sert_tam_2').remove();
					$.post(host+"reg-file", { 'id_asn':$('#'+type).val() }, function(resp){
						$('#sertifikasi_1').append(resp);
					});
				}else{
					//$('#fl_s').remove();
					$('#sert_tam_2').remove();
					$('#sert_tam_3').remove();
					$('#sertifikasi_1').append(resp);
				}
			});
			
			$("#sb_jns_sert").val($('#'+type+" :selected").text());
		break;
		case "sb_ap_tk4":
			$('#fl_s').remove();
			$.post(host+"chk3", { 'id_asn_child_tk3':$('#'+type).val() }, function(resp){
				if(resp == 0){
					//$('#fl_s').remove();
					//$('#sert_tam_3').remove();
					$.post(host+"reg-file", { 'id_asn':$('#'+type).val() }, function(resp){
						$('#sertifikasi_1').append(resp);
					});
				}else{
					//$('#fl_s').remove();
					//$('#sert_tam').remove();
					$('#sert_tam_3').remove();
					$('#sertifikasi_1').append(resp);
				}
			});
			
			$("#sb_jns_sert").val($('#'+type+" :selected").text());
		break;
		case "sb_ap_tk5":
			$.post(host+"reg-file", { 'id_asn':$('#'+type).val() }, function(resp){
				$('#sertifikasi_1').append(resp);
			});
			$("#sb_jns_sert").val($('#'+type+" :selected").text());
		break;
		case "mtdp":
			if($("#"+type).val() == 'apbn'){
				$("#kdvcr_byr_bry").css({'display':'inline'});
				$("#tgl_byr_bry").css({'display':'none'});
			}else if($("#"+type).val() == 'pnbp'){
				$("#kdvcr_byr_bry").css({'display':'none'});
				$("#tgl_byr_bry").css({'display':'inline'});
			}
		break;
		/***Levi Combo***/
		case "angdit":
			$.post(host+"angka-kredit", { 
				'tingkat':$('#tingkat').val(),'golongan':$('#golongan').val(),
				'pendidikan':$('#pendidikan').val(),'masa':$('#masa').val() 
			}, function(resp){
				$('#didit').html(resp);
			});
		break;
	}
	//clr();
}

function sbtdl_reg(){	
	$.blockUI({ message: '<h5>..Validasi Inputan..</h5>' });
	ajxfm("regdiklat", function(respo){
		$.blockUI({ message: '<h5>..Harap Tunggu, Data Sedang Dikirim..</h5>' });
		if(respo == 1){
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Sukses Tersimpan Dalam Sistem" });
			location.href = host+'registrasi-berhasil';
		}else if(respo == -1){
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Anda Sudah Terdaftar Dalam Sistem Kami." });
		}else if(respo == -2){
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Maaf Anda Terlambat Submit Data, Kuota Jadwal Ujian TUK Sudah Habis, Silahkan Pilih Jadwal Ujian TUK Lain." });
		}else if(respo == -3){
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Jadwal Sertifikasi Sudah Lewat, Silahkan Mendaftar di Jadwal Yang Lain" });
		}else{
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Gagal Tersimpan Dalam Sistem" });
			location.href = host+'registrasi-gagal';
		}
		//
		$.unblockUI();
	});
	setTimeout(function () { $.unblockUI(); }, 1000);
	
	
}

function sbtdl_reg_bars(){
	if($('#edFile_pak').val() == 0){
		$('#edFile_pak').focus();
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "File PAK Tidak Boleh Kosong!" });
		return false;
	}
	
	if($('#kmnt').val() == ""){
		$("#kmnt").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Kementerian Harus Diisi!" });
		return false;
	}
	if($('#frms').val() == ""){
		$("#frms").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Formasi Harus Diisi!" });
		return false;
	}
	if($('#lks').val() == ""){
		$("#lks").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Lokasi Harus Diisi!" });
		return false;
	}
	
	
	if($('#prv').val() == ""){
		$("#prv").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Provinsi Harus Diisi!" });
		return false;
	}
	if($('#ka').val() == ""){
		$("#ka").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Kabupaten Harus Diisi!" });
		return false;
	}
	if($('#ins').val() == ""){
		$("#ins").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Instansi Harus Diisi!" });
		return false;
	}
	if($('#ed_pangkat').val() == ""){
		$("#ed_pangkat").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Pangkat Harus Diisi!" });
		return false;
	}
	if($('#ed_jabatan').val() == ""){
		$("#ed_jabatan").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Jabatan Harus Diisi!" });
		return false;
	}
	if($('#ed_alamatKtr').val() == ""){
		$("#ed_alamatKtr").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Alamat Kantor Harus Diisi!" });
		return false;
	}
	
	if($('#tku_dxi').val() == ""){
		$("#tku_dxi").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "TUK Terdekat Diisi!" });
		return false;
	}
	
	if($('#sb_ap_tk2').val() == ""){
		$("#sb_ap_tk2").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Sub Aparatur Harus Diisi!" });
		return false;
	}
	
	if($('#sb_ap_tk3').length != 0){
		if($('#sb_ap_tk3').val() == ""){
			$("#sb_ap_tk3").focus(); 
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Jenis Sertifikasi Harus Diisi!" });
			return false;
		}
	}

	var jml_sert = $('.file-persyaratan-sertifikasi').length;
	if(jml_sert != 0){
		var jms = eval((jml_sert-1));
		for (i = 0; i <= jms; i++) {
			if($('#fl_'+i).val() == 0){
				$('#fl_'+i).focus();
				$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "File Sertikasi No. "+(i+1)+" Tidak Boleh Kosong!" });
				return false;
			}
		}
	}else{
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Persyaratan Sertifikasi Belum Tersedia" });
		return false;
	}
	
	$.post(host+'chkdt-kutoax', { 'xtu_id':$('#tku_dxi').val() }, function(resp){
		if(resp == 0){
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Maaf Anda Terlambat Submit Data, Kuota Jadwal Ujian TUK Sudah Habis, Silahkan Pilih Jadwal Ujian TUK Lain." });
			return false;
		}
	});

	
	$.msg({
		autoUnblock : false,
		clickUnblock : false,
		bgPath : host+"assets/js/plugins/msgplugin/",
		content: '<p>Anda Yakin Mau Mendaftar Diklat Baru Lagi ? <br/> (Setelah Mendaftar Anda Akan Diminta Login Kembali Untuk Pembaharuan Cookies).</p>' +
				'<center>' +
				'<a id="yes_registrasi_baru" class="btn btn-success" onClick="event.preventDefault();">Ya</a>&nbsp;&nbsp;' +
				'<a id="no_registrasi_baru" class="btn btn-primary" onClick="event.preventDefault();">Tidak</a>' +
				'</center>',
		afterBlock : function(){
			var self = this;
			$( '#yes_registrasi_baru' ).bind( 'click', function(){
				self.unblock();
				document.regdiklat_baru.submit();
			});
			$('#no_registrasi_baru').bind( 'click', function(){
				self.unblock();
			});
		},
	});	

}

function sbtdl_reg_ngulang(){
	
	if($('#edFile_pak').val() == 0){
		$('#edFile_pak').focus();
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "File PAK Tidak Boleh Kosong!" });
		return false;
	}
	
	if($('#kmnt').val() == ""){
		$("#kmnt").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Kementerian Harus Diisi!" });
		return false;
	}
	if($('#frms').val() == ""){
		$("#frms").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Formasi Harus Diisi!" });
		return false;
	}
	if($('#lks').val() == ""){
		$("#lks").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Lokasi Harus Diisi!" });
		return false;
	}
	
	
	if($('#prv').val() == ""){
		$("#prv").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Provinsi Harus Diisi!" });
		return false;
	}
	if($('#ka').val() == ""){
		$("#ka").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Kabupaten Harus Diisi!" });
		return false;
	}
	if($('#ins').val() == ""){
		$("#ins").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Instansi Harus Diisi!" });
		return false;
	}
	if($('#ed_pangkat').val() == ""){
		$("#ed_pangkat").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Pangkat Harus Diisi!" });
		return false;
	}
	if($('#ed_jabatan').val() == ""){
		$("#ed_jabatan").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Jabatan Harus Diisi!" });
		return false;
	}
	if($('#ed_alamatKtr').val() == ""){
		$("#ed_alamatKtr").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Alamat Kantor Harus Diisi!" });
		return false;
	}
	
	if($('#tku_dxi').val() == ""){
		$("#tku_dxi").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "TUK Terdekat Diisi!" });
		return false;
	}
	
	var jml_sert = $('.file-persyaratan-sertifikasi').length;
	if(jml_sert != 0){
		var jms = eval((jml_sert-1));
		for (i = 0; i <= jms; i++) {
			if($('#fl_'+i).val() == 0){
				$('#fl_'+i).focus();
				$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "File Persyaratan Sertifikasi No. "+(i+1)+" Tidak Boleh Kosong!" });
				return false;
			}
		}
	}else{
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Persyaratan Sertifikasi Belum Tersedia" });
		return false;
	}
	
	$.post(host+'chkdt-kutoax', { 'xtu_id':$('#tku_dxi').val() }, function(resp){
		if(resp == 0){
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Maaf Anda Terlambat Submit Data, Kuota Jadwal Ujian TUK Sudah Habis, Silahkan Pilih Jadwal Ujian TUK Lain." });
			return false;
		}
	});
	
	$.msg({
		autoUnblock : false,
		clickUnblock : false,
		bgPath : host+"assets/js/plugins/msgplugin/",
		content: '<p>Anda Yakin Mau Mengulang Diklat Anda Yang Tidak Lulus Sebelumnya ? <br/> (Setelah Mendaftar Anda Akan Diminta Login Kembali Untuk Pembaharuan Cookies).</p>' +
				'<center>' +
				'<a id="yes_registrasi_ngulang" class="btn btn-success" onClick="event.preventDefault();">Ya</a>&nbsp;&nbsp;' +
				'<a id="no_registrasi_ngulang" class="btn btn-primary" onClick="event.preventDefault();">Tidak</a>' +
				'</center>',
		afterBlock : function(){
			var self = this;
			$( '#yes_registrasi_ngulang' ).bind( 'click', function(){
				self.unblock();
				document.regdiklat_ngulang.submit();
			});
			$('#no_registrasi_ngulang').bind( 'click', function(){
				self.unblock();
			});
		},
	});	
		
}

function log_psrt(){
	if($('#ed_usr').val() == ""){
		$("#ed_usr").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Username Harus Diisi!" });
		return false;
	}
	if($('#ed_psd').val() == ""){
		$("#ed_psd").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Password Harus Diisi!" });
		return false;
	}
	
	$.post(host+"chk-login", { 'usr' : $('#ed_usr').val(), "pwd" : $('#ed_psd').val() }, function(rsp){
		if(rsp == "1"){
			document.logdiklat.submit();
			//alert("ada boi");
		}else if(rsp == "0"){
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Anda Sudah Dimatikan Oleh Admin, Silahkan Kontak Ke No. Telp Kami." });
			return false;
		}else if(rsp == "-1"){
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Password Yang Anda Masukkan Salah" });
			return false;
		}else if(rsp == "-2"){
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Anda Tidak Terdaftar Dalam Sistem Kami!" });
			return false;
		}
	} );
	//clr();

}

function regass(kl){
	
	var jm = eval((kl-1));
	for (i = 0; i <= jm; i++) {
		if($('#st_kmp_'+i+':checked').length == 0){
			$('#st_kmp_'+i).focus();
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Kolom Penilaian Diri No. "+(i+1)+" Tidak Boleh Kosong!" });
			return false;
		}
	}
	
	//document.asdik.submit();
	
	ajxfm("asdik", function(respo){
		if(respo == 1){
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Asesmen Mandiri Anda Berhasil Tersimpan Dalam Sistem." });
			location.href = host+'asesmen-berhasil';
		}else{
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Asesmen Mandiri Anda Gagal Tersimpan Dalam Sistem" });
			location.href = host+'asesmen-gagal';
		}
	});
}

function kumpulPoster(type, domnya, p1, p2, p3){
	switch(type){
		case "div-stuj":
			$('#kont_suj').css({'display':'none'});
			$('#kont_uj_heading').css({'display':'inline'});
			$('#tb-so').addClass('loading');
			$.post(host+"minta-soal", { /*'st':0, 'ed':5,*/ 'ids':p1 }, function(tp){
				pars = $.parseJSON(tp);
				//$("#pss_v").val(5);
				$('#tb-so').removeClass('loading');
				$('#tb-so').html(pars.pg);
				$('#sbm').css({'display':'inline'});
			});
			
			$("#timernya").countdowntimer({
				hours : 1,
				//minutes : 5,
				//seconds : p3,
				size : "sm",
				borderColor : "#D15050",
                backgroundColor : "#373737",
                fontColor : "#FFFFFF",
				timeUp : sbmtmUp
			});
			
			return false;
		break;
		case "div-stuj_sudah":
			$('#kont_suj_sudah').css({'display':'none'});
			$('#kont_uj_heading_sudah').css({'display':'inline'});
			$('#sbm').css({'display':'inline'});
			$("#timernya_sudah").countdowntimer({
				hours : p1,
				minutes : p2,
				seconds : p3,
				size : "sm",
				borderColor : "#D15050",
                backgroundColor : "#373737",
                fontColor : "#FFFFFF",
				timeUp : sbmtmUp
			});
			
			return false;
		break;
		case "ld-so":
			var uhynya = new Array();
			$(".cks").each(function () {
			   uhynya.push($(this).val());
			});
			var bts_ats = $("#pss_v").val();
			var bts_bwh = eval(parseInt(bts_ats) + 5);
			$.post(host+"minta-soal", { 'st':bts_ats, 'ed':bts_bwh, 'ids':p1, 'so':uhynya}, function(tp){
				pars = $.parseJSON(tp);
				if(pars.pg != ""){
					$("#pss_v").val(pars.ed);
					$('#tb-so').append(pars.pg);
				}else{
					$('#jrn').html("");
					$('#jrn').html("<h4>Pertanyaan Sudah Semua Ditampilkan, Cek Hasil Pekerjaan Anda Dan Submit Hasil Test Anda..</h4>");
					$('#sbm').css({'display':'inline'});
				}
			});
		break;
		case "div-stuj-sim":
			$('#kont_sim').css({'display':'none'});
			$('#kont_sim_heading').css({'display':'inline'});
			
			$("#timernya_sim").countdowntimer({
				hours : 1,
				size : "sm",
				borderColor : "#D15050",
                backgroundColor : "#373737",
                fontColor : "#FFFFFF",
				timeUp : sbmtmUpSim
			});
			
			return false
		break;
		case "div-stuj-sim_sudah":
			$('#kont_sim_sudah').css({'display':'none'});
			$('#kont_sim_heading_sudah').css({'display':'inline'});
			
			$("#timernya_sudah_sim").countdowntimer({
				hours : p1,
				minutes : p2,
				seconds : p3,
				size : "sm",
				borderColor : "#D15050",
                backgroundColor : "#373737",
                fontColor : "#FFFFFF",
				timeUp : sbmtmUpSim
			});
			return false
		break;
		case "sbm-sim":
			var jjww = tinyMCE.get('jwb_ny_'+p1).getContent();
			$.post(host+"sbm-sim", { 'idxpr':p2, 'jwxdi':jjww }, function(rsssp){
				//$('#'+domnya).html(rsssp);
				if(rsssp == 1){
					$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Jawaban No. "+p1+" Tersimpan" });
				}else{
					$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Gagal, Kesalahan Teknis" });
				}
			});
		break;
		case "dfwa":
			$.post(host+"daftar-jadwal", { 'iddf':p1 }, function(rsssp){
				$('#'+domnya).html(rsssp);
			});
			return false;
		break;
		case "df_wwc":
			$.msg({
				autoUnblock : false,
				clickUnblock : false,
				bgPath : host+"assets/js/plugins/msgplugin/",
				content: '<p>Anda Yakin Mau Mendaftar Wawancara ?</p>' +
						'<center>' +
						'<a id="yes" class="btn btn-success" onClick="event.preventDefault();">Ya</a>&nbsp;&nbsp;' +
						'<a id="no" class="btn btn-primary" onClick="event.preventDefault();">Tidak</a>' +
						'</center>',
				afterBlock : function(){
					var self = this;
					$( '#yes' ).bind( 'click', function(){
						// self.unblock( 2000 );
						//alert('kontil');
						self.unblock();
						$.post(host+"daftar-sekarang", { 'iddf':p1, 'tkuid':p2 }, function(rsssp){
							if(rsssp == 1){
								$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Anda Sudah Terdaftar Wawancara Dalam Sistem Kami!" });
								location.reload();
							}else{
								alert(rsssp);
							}
						});
					});
					$('#no').bind( 'click', function(){
						// this equals to $.msg( 'unblock' );
						//alert('konti');
						self.unblock();
					});
				},
			});			
		break;
		case "sbt-uj-satuan":
			$.post(host+"sbm-uj", { 'sxb':p1, 'jxb':p2 }, function(rsssp){
				if(rsssp == 1){
					console.log('Ok');
				}
			});
		break;
		case "sbt-wkt":
			var temzon = $('#timernya').html();
			var temzon2 = $('#timernya_sudah').html();
			
			$.post(host+"sbm-wkt", { 'tmzon':temzon, 'tmzon2':temzon2 }, function(rsssp){
				if(rsssp == 1){
					console.log('Ok');
				}
			});
		break;
		case "sbt-wkt-sim":
			var temzon = $('#timernya_sim').html();
			var temzon2 = $('#timernya_sudah_sim').html();
			
			$.post(host+"sbm-wkt-sim", { 'tmzon':temzon, 'tmzon2':temzon2 }, function(rsssp){
				if(rsssp == 1){
					console.log('Ok');
				}
			});
		break;
		case "sbt-rev-pers":
			if($('#file_'+p1).val() == ""){
				$("#file_"+p1).focus(); 
				$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "File No. "+p2+" Harus Diisi!" });
				return false;
			}
			
			ajxfm("persy_"+p1, function(respo){
				if(respo == 1){
					$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Tersimpan, Silahkan Tunggu Verifikasi Dokumen Oleh Asesor." });
					location.reload();
				}else{
					//alert(respo);
					$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Gagal, Silahkan Coba Lagi Beberapa Saat." });
					location.reload();
				}
			});	
		break;
		case "sbt-rev-ases":
			if($('#bkt_pndk_'+p1).val() == ""){
				$("#bkt_pndk_"+p1).focus(); 
				$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Bukti Pendukung No. "+p2+" Harus Diisi!" });
				return false;
			}
			if($('#st_kmp_'+p1+':checked').length == 0){
				$('#st_kmp_'+p1).focus();
				$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Checkbox Penilaian Diri No. "+p2+" Harap Dipilih Salah Satu!" });
				return false;
			}
			
			ajxfm("asess_"+p1, function(respo){
				if(respo == 1){
					$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Tersimpan, Silahkan Tunggu Verifikasi Dokumen Oleh Asesor." });
					$('#st_kmp_'+p1).prop('checked', false);
					$('#bkt_pndk_'+p1).val("");
					location.reload();
				}else{
					//alert(respo);
					$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Gagal, Silahkan Coba Lagi Beberapa Saat." });
					$('#st_kmp_'+p1).prop('checked', false);
					$('#bkt_pndk_'+p1).val("");
					location.reload();
				}
			});	
		break;
		case "sbm-komplain":
			if($('#klm_komplain').val() == ""){
				$("#klm_komplain").focus(); 
				$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Kolom Komplain Tidak Boleh Kosong" });
				return false;
			}
			
			$.post(host+"sbm-komplain-peserta", { 'kmpl':$('#klm_komplain').val() }, function(rsssp){
				if(rsssp == 1){
					$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Komplain Anda Sudah Masuk Dalam Sistem Kami!" });
					location.reload();
				}else{
					$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Gagal, Sistem Error!" });
					//location.reload();
				}
			});
		break;
	}
}

function ajxfm(objid, func){
    var url = $('#'+objid).attr("url");
    $('#'+objid).form('submit',{
            url:url,
            onSubmit: function(){
				return $(this).form('validate');
            },
            success:function(data){
				if (func == undefined ){
                    if (data == "1"){
                                              
                    }else{
                        var pesan = data.replace('1','');
                        //$.messager.alert('Error','Error Saving Data '+pesan,'error');
                    }
                }else{
                    func(data);
                }
            },
            error:function(data){
            }
    });
}

function sbst(){
	var uhynya = new Array();
	$(".cks").each(function () {
	   uhynya.push($(this).val());
	});
	
	$.msg({
		autoUnblock : false,
		clickUnblock : false,
		bgPath : host+"assets/js/plugins/msgplugin/",
		content: '<p>Anda Yakin Sudah Menyelesaikan Ujian ?</p>' +
				'<center>' +
				'<a id="yes" class="btn btn-success" onClick="event.preventDefault();">Ya</a>&nbsp;&nbsp;' +
				'<a id="no" class="btn btn-primary" onClick="event.preventDefault();">Tidak</a>' +
				'</center>',
		afterBlock : function(){
			var self = this;
			$( '#yes' ).bind( 'click', function(){
				$.post(host+"soal-sisa", {'so':uhynya}, function(tp){
					if(tp == 1){
						document.ujdik.submit();
					}else{
						location.href = host+'gagal-submit-soal';
					}
					self.unblock();
				});
			});
			$('#no').bind( 'click', function(){
				self.unblock();
			});
		},
	});
}

function sbmtmUp(){
	$.post(host+"soal-sisa", { }, function(tp){
		if(tp == 1){
			document.ujdik.submit();
		}
	});
}

function sbst_sim(){	
	$.msg({
		autoUnblock : false,
		clickUnblock : false,
		bgPath : host+"assets/js/plugins/msgplugin/",
		content: '<p>Anda Yakin Sudah Menyelesaikan Test Simulasi ?</p>' +
				'<center>' +
				'<a id="yes" class="btn btn-success" onClick="event.preventDefault();">Ya</a>&nbsp;&nbsp;' +
				'<a id="no" class="btn btn-primary" onClick="event.preventDefault();">Tidak</a>' +
				'</center>',
		afterBlock : function(){
			var self = this;
			$( '#yes' ).bind( 'click', function(){
				ajxfm("ujsim", function(respo){
					$.blockUI({ message: '<h5>..Harap Tunggu, Data Sedang Dikirim..</h5>' });
					if(respo == 1){
						$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Test Simulasi Sukses Tersimpan Dalam Sistem" });
						location.href = host+'test-simulasi-berhasil';
					}else{
						$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Test Simulasi Gagal Tersimpan Dalam Sistem" });
						//location.href = host+'test-simulasi-gagal';
					}
					$.unblockUI();
				});
			});
			$('#no').bind( 'click', function(){
				self.unblock();
			});
		},
	});
}

function sbmtmUpSim(){
	ajxfm("ujsim", function(respo){
		$.blockUI({ message: '<h5>..Harap Tunggu, Data Sedang Dikirim..</h5>' });
		if(respo == 1){
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Test Simulasi Sukses Tersimpan Dalam Sistem" });
			location.href = host+'test-simulasi-berhasil';
		}else{
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data Test Simulasi Gagal Tersimpan Dalam Sistem" });
			location.href = host+'test-simulasi-gagal';
		}
		$.unblockUI();
	});
}

function sbtdl_kpm(){
	if($('#mtdp').val() == ""){
		$("#mtdp").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Metode Pembayaran Harus Diisi!" });
		return false;
	}
	
	if($('mtdp').val == 'pnbp'){	
		if($('#tgl_byr').val() == ""){
			$("#tgl_byr").focus(); 
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Combo Tanggal Bayar Harus Diisi!" });
			return false;
		}
		if($('#bln_byr').val() == ""){
			$("#bln_byr").focus(); 
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Combo Bulan Bayar Harus Diisi!" });
			return false;
		}
		if($('#thn_byr').val() == ""){
			$("#thn_byr").focus(); 
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Combo Tahun Bayar Harus Diisi!" });
			return false;
		}
	}else if($('mtdp').val == 'apbn'){
		if($('#edKd_vcr').val() == ""){
			$("#edKd_vcr").focus(); 
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Kode Voucher Harus Diisi!" });
			return false;
		}
	}
	
	
	if($('#edBukti_byr').val() == ""){
		$("#edBukti_byr").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "File Bukti Pembayaran Harus Diisi!" });
		return false;
	}
	
	document.kpm_ko.submit();
}

/*
function kompilater(idxnyoi, cdnid){
	idxnyoi++;
	row_na =	'<tr id="row_bij_'+idxnyoi+'_'+cdnid+'">';
	row_na +=		'<td>';
	row_na +=			'<input type="hidden" name="idxfl[]" value="'+idxnyoi+'">';
	row_na +=			'<input type="file" name="fl_pdk[][]" class="col-sm-3" style="padding:2px;width:250px;border:1px solid #E5E5E5;background:#fff">';
	row_na +=			'<span class="labels">';
	row_na +=				'<a style="padding:5px;" href="#" onClick="javascript:void(0);kompilater('+idxnyoi+', '+cdnid+');return false;" ><img src="'+host+'assets/images/add.png" title="Tambah" /></a>';
	row_na +=				'<a style="padding:5px;" href="#" onClick="javascript:void(0);dkmpilater('+idxnyoi+', '+cdnid+');return false;" ><img src="'+host+'assets/images/delete.png" title="Hapus" /></a>';
	row_na +=			'</span>';
	row_na +=		'</td>';
	row_na +=	'</tr>';
	$('#flpd_'+cdnid).append(row_na);
	return false;
}

function dkmpilater(idxnyoi, cdnid){
	$('#row_bij_'+idxnyoi+'_'+cdnid).remove();
}

function kobochan(idf){
	//var gender=$('#Gender').val();
    if ($("#st_kmp_"+idf).length == 0){
        $.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Pilih Nilai Kompeten/Belum Kompeten!" });
        return false;
    }
	
	ajxfm('sbmf_'+idf, function(rsppp){
		if(rsppp == 1){
			alert('oke');
		}else{
			alert(rsppp);
		}
	});
	return false;
}
*/

function chf(dom, tpy){
	var file =  $("#"+dom).prop("files")[0];
	var fileSize = file.size;
	var sFileName = file.name;
	var filextension = sFileName.split('.')[sFileName.split('.').length - 1].toLowerCase();
	var iConvert = (file.size / 1048576).toFixed(2);
	
	if(tpy == 1){
		var arr = ["pdf", "PDF", "jpg", "JPG", "jpeg", "JPEG"];
		var pesannya = "File Harus Ber-extension .pdf / .PDF / .jpg / .JPG / .jpeg / .JPEG !";
		var mx = 5;
		var pesannya2 = "File Harus Berukuran Maximal 5 MB !";
	}else if(tpy == 2){
		var arr = ["jpg", "JPG", "jpeg", "JPEG"];
		var pesannya = "File Harus Ber-extension .jpg / .JPG / .jpeg / .JPEG !";
		var mx = 0.50;
		var pesannya2 = "File Harus Berukuran Maximal 500 KB !";
	}else if(tpy == 3){
		var arr = ["jpg", "JPG", "jpeg", "JPEG"];
		var pesannya = "File Harus Ber-extension .jpg / .JPG / .jpeg / .JPEG !";
		var mx = 2;
		var pesannya2 = "File Harus Berukuran Maximal 2 MB !";
	}
		
	if( $.inArray(filextension, arr) == -1 ){
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : pesannya });
		$('#'+dom).val('');
		return false;
	}
	
	if(iConvert >= mx){
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : host+"assets/js/plugins/msgplugin/", clickUnblock : false, content : pesannya2 });
		$('#'+dom).val('');
		return false;
	}
	
	$('#'+dom+'_sts').html('- <font color="green">Size File Anda : '+iConvert+' MB </font>');
	
}

function clr() { 
	console.log(window.console);
	if(window.console || window.console.firebug) {
		console.clear();
		//net.clear();
	}
}

// Levi PAK
function pakinass(type){	
	switch(type){
		case "save":
			document.inpas_in.submit();
		break;
		case "approve":
			document.apvpak.submit();
		break;
	}
}

