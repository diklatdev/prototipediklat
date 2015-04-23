function fillCmb(url, SelID, value, value2, value3, value4){
	if (value == undefined) value = "";
	if (value2 == undefined) value2 = "";
	if (value3 == undefined) value3 = "";
	if (value4 == undefined) value4 = "";
	
	$('#'+SelID).empty();
	$.post(url, {"v": value, "v2": value2, "v3": value3, "v4": value4},function(data){
		$('#'+SelID).append(data);
	});
}

function processCmb(type){
	switch(type){
		case "prv-kb":
			//fillCmb(hostir+"combo/ka/", 'edpt', "", $('#edpr').val() );
		break;
	}
}

function loadUrl(urls,func){	
    //$("#tMain").html("").addClass("loading");
	$.get(urls,function (html){
	    $("#tMain").html(html)//.removeClass("loading");
    }).done(function(){
       // func;
    });
}

function ajxamsterfrm(objid, func){
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

function loadUrl_adds(type, urlnya, domnya, p1, p2, p3, p4, p5, p6, p7){
	switch(type){
		case "ps_det":
			$.post(urlnya, { 'id_u' : p1 , 'idx_s' : p2, 'kdr' : p3 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "as_dt":
			$.post(urlnya, { 'id_uny' : p1, 'nm_l':p2, 'ap_n':p3, 'rg':p4, 'id_sert':p5, 'tu':p6, 'kdr':p7 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "kpo_dt":
			var nreg = $('#nreg').val();
			var nmlng = $('#nmlng').val();
			var pin = $('#pin').val();
			var nmsert = $('#nmsert').val();
			var tgpem = $('#tgpem').val();
			var tgkon = $('#tgkon').val();
			var fl_pm = $('#fl').val();
			var mtd_pm = $('#mtd').val();
			var kdp_pm = $('#kdp').val();
			var kdv_pm = $('#kdv').val();
			var kdr_pm = $('#kdr').val();
			
			$.post(urlnya, { 'id_uny':p1, 'nr':nreg, 'nm_l':nmlng, 'np':pin, 'ap_n':nmsert, 'tgp':tgpem, 'tgk':tgkon, 'flpm':fl_pm, 'idxsert':p2, 'mtdpm':mtd_pm, 'kdppm':kdp_pm, 'kdvpm':kdv_pm, 'kdrpm':kdr_pm,   }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;		
		case "uj_dt":
			var nreg = $('#nreg').val();
			var nmlng = $('#nmlng').val();
			var pin = $('#pin').val();
			var nmsert = $('#nmsert').val();
			var idxsert = $('#idxsert').val();
			var tguj = $('#tguj').val();
			var jms = $('#jms').val();
			var jmb = $('#jmb').val();
			$.post(urlnya, { 'id_uny':p1, 'nr':nreg, 'nm_l':nmlng, 'np':pin, 'ap_n':nmsert, 'ap_dx':idxsert, 'tgu':tguj, 'js':jms, 'jb':jmb }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "tm_jd":
			$.post(urlnya, {  }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "ww_dt":
			$.post(urlnya, { 'cdn':p1, 'xdiser':p2, 'kdr':p3 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "hs_dt":
			var nreg = $('#nreg_'+p1).val();
			var nmlng = $('#nmlng_'+p1).val();
			var pin = $('#upin_'+p1).val();
			var kdr = $('#kdr_'+p1).val();
			$.post(urlnya, { 'cdn':p1, 'dxisert':p2, 'nreg':nreg, 'nmlng':nmlng, 'ipin':pin, 'kdr':kdr }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "uj_sm":
			$.post(urlnya, { 'cdn':p1, 'nmlng':p2, 'nmapart':p3, 'nreg':p4, 'dxisert':p5, 'kdr':p6 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "vcf":
			$.post(urlnya, { }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		//**************levi		
		case "add_uji":
			$.post(urlnya, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
	}
	return false;
}

function kumpulPost($type, p1, p2, p3){
	switch($type){
		case "ver_r_ox":
			$.post(hostir+"verifikasi-registrasi", { 'st_v':'OK', 'idus':p1, 'idxus':p2 }, function(rspp){
				if(rspp == 1){
					alert('Registrasi Diklat Peserta Diterima.');
					loadUrl(hostir+'manajemen-peserta');
				}else{
					alert(rspp);
				}
			});
		break;
		case "ver_dokper":
			if($('#kep_'+p1+':checked').length == 0){
				$('#kep_'+p1).focus(); 
				$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Isi Hasil Keputusan Dokumen No. "+p2 });
				return false;
			}
			
			$.post(hostir+"verifikasi-dokumen-syarat", { 'idws':p1, 'kp':$('#kep_'+p1+':checked').val(), 'mm':$('#memo_'+p1).val() }, function(rpp){
				if(rpp == 1){
					$('#flag_'+p1).html('<font color="green">Sudah Verifikasi</font>');
					if($('#kep_'+p1+':checked').val() == 1){
						$('#sts_'+p1).html('<font color="green">Terpenuhi</font>');
						$('#btn_'+p1).remove();
					}
					$('sts_nilai_'+p1).val($('#kep_'+p1+':checked').val());
				}
			});
			
		break;
		case "ver_uj_ikt":
			$.post(hostir+"verifikasi-ujitulis-iktujian", { 'usiid':p1, 'sertaidd':p2, 'kdr':p3 }, function(rspp){
				if(rspp == 1){
					alert('Peserta Dipersilahkan Mengikuti Ujian!');
					loadUrl(hostir+'ujitulis-peserta');
				}else{
					alert(rspp);
				}
			});
		break;		

//*******Levi
		case "sv_syarat":			
			$.post(hostir+"submit-syarat", $('#regSyarat').serialize(),function (rspp){
				if(rspp == 1){
						alert('Sertifikat Berhasil Ditambahkan!');
						loadUrl(hostir+'manajemen-sertifikasi');
					}else{
						alert(rspp);
				}			
			});
		break;
		case "sv-uji":		
			//alert('Sertifikat Berhasil Ditambahkan!');
			$.post(hostir+"submit-uji-man", $('#regUji').serialize(),function (rspp){
				if(rspp == 1){
						if (confirm("Unit Kompetensi di Tambahkan. Apakah Anda akan menambahkan kompetensi lagi?") == true) {
							$('#no_unit').value("");
							$('#nama_unit').value("fuck");
							//return false;
						} else {							
							loadUrl(hostir+'manajemen-uji-mandiri');
						}
					}else{
						alert(rspp);
				}			
			});
		break;
		case "sv_aparat":			
			$.post(hostir+"submit-aparat", $('#regAparat').serialize(),function (rspp){
				if(rspp == 1){
						alert('Aparatur Berhasil Ditambahkan!');
						loadUrl(hostir+'manajemen-aparatur');
					}else{
						alert(rspp);
				}			
			});
		break;
		case "sv_instansi":			
			$.post(hostir+"submit-instansi", $('#regInstansi').serialize(),function (rspp){
				if(rspp == 1){
						alert('Instansi Berhasil Ditambahkan!');
						loadUrl(hostir+'manajemen-instansi');
					}else{
						alert(rspp);
				}			
			});
		break;
		case "sv_pangkat":			
			$.post(hostir+"submit-pangkat", $('#regPangkat').serialize(),function (rspp){
				if(rspp == 1){
						alert('Pangkat Berhasil Ditambahkan!');
						loadUrl(hostir+'manajemen-pangkat');
					}else{
						alert(rspp);
				}			
			});
		break;
		case "sv_tuk":			
			$.post(hostir+"submit-tuk", $('#regTuk').serialize(),function (rspp){
				if(rspp == 1){
						alert('TUK Berhasil Ditambahkan!');
						loadUrl(hostir+'manajemen-tuk');
					}else{
						alert(rspp);
				}			
			});
		break;		
	}
}

function asses(kl){
	var jm = eval((kl-1));
	for (i = 0; i <= jm; i++) {
		if($('#rek_'+i+':checked').length == 0){
			$('#rek_'+i).focus();
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Kolom Rekomendasi No. "+(i+1)+" Tidak Boleh Kosong!" });
			return false;
		}
	}
	
	if($('#hsl_as').val() == ""){
		$("#hsl_as").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Isi Hasil Keputusan Ujian!" });
		return false;
	}
	if($('#nilai_as').val() == ""){
		$("#nilai_as").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Nilai Ujian Tidak Boleh Kosong!" });
		return false;
	}

	
	ajxamsterfrm("detass", function(respo){
		if(respo == 1){
			alert("Data Tersimpan");
			loadUrl(hostir+'asesmen-peserta');
		}else{
			alert(respo);
		}
	});
}

function ujnya(){
	if($('#hsl_uj').val() == ""){
		$("#hsl_uj").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Isi Hasil Keputusan Ujian!" });
		return false;
	}
	if($('#nilai_uj').val() == ""){
		$("#nilai_uj").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Nilai Ujian Tidak Boleh Kosong!" });
		return false;
	}
	
	ajxamsterfrm("detuj", function(respo){
		if(respo == 1){
			alert("Data Tersimpan");
			loadUrl(hostir+'ujitulis-peserta');
		}else{
			alert(respo);
		}
	});

}

function pemnya(){
	if($('#hsl_konf').val() == ""){
		$("#hsl_konf").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Isi Combo Hasil Keputusan Konfirmasi!" });
		return false;
	}
	
	ajxamsterfrm("detpem", function(respo){
		if(respo == 1){
			alert("Data Tersimpan");
			loadUrl(hostir+'pembayaran-peserta');
		}else{
			alert(respo);
		}
	});

}

function sbmjdw(){
	if($('#edpr').val() == ""){
		$("#edpr").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Combo Provinsi Tidak Boleh Kosong!" });
		return false;
	}
	if($('#edpt').val() == ""){
		$("#edpt").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Combo Kabupaten Tidak Boleh Kosong!" });
		return false;
	}
	if($('#edtk').val() == ""){
		$("#edtk").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Field TUK Tidak Boleh Kosong!" });
		return false;
	}
	if($('#tggw').val() == ""){
		$("#tggw").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Field Tanggal Wawancara Tidak Boleh Kosong!" });
		return false;
	}
	if($('#ktpp').val() == ""){
		$("#ktpp").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Field Kuota Wawancara Tidak Boleh Kosong!" });
		return false;
	}
	
	ajxamsterfrm("jdw", function(respo){
		if(respo == 1){
			alert("Data Tersimpan");
			loadUrl(hostir+'penjadwalan-peserta');
		}else{
			alert(respo);
		}
	});

}

function smls(){
	if($('#nl_ujsm').val() == ""){
		$("#nl_ujsm").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Field Nilai Tidak Boleh Kosong!" });
		return false;
	}
	if($('#hsl_ujsm').val() == ""){
		$("#hsl_ujsm").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Combo Hasil Keputusan Ujian Tidak Boleh Kosong!" });
		return false;
	}
	
	ajxamsterfrm("detujsm", function(respo){
		if(respo == 1){
			alert("Data Tersimpan");
			loadUrl(hostir+'ujisimulasi-peserta');
		}else{
			alert(respo);
		}
	});
}

function wawanya(){
	if($('#hsl_wa').val() == ""){
		$("#hsl_wa").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Combo Hasil Wawancara Tidak Boleh Kosong!" });
		return false;
	}
	if($('#nilai_wa').val() == ""){
		$("#nilai_wa").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Field Nilai Tidak Boleh Kosong!" });
		return false;
	}

	ajxamsterfrm("dewac", function(respo){
		if(respo == 1){
			alert("Data Tersimpan");
			loadUrl(hostir+'wawancara-peserta');
		}else{
			alert(respo);
		}
	});	
}

function hsnya(){
	if($('#hsl_hs').val() == ""){
		$("#hsl_hs").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Combo Hasil Sertifikasi Tidak Boleh Kosong!" });
		return false;
	}
	/*
	if($('#nilai_wa').val() == ""){
		$("#nilai_wa").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Field Nilai Tidak Boleh Kosong!" });
		return false;
	}
	*/

	ajxamsterfrm("dehs", function(respo){
		if(respo == 1){
			alert("Data Tersimpan");
			loadUrl(hostir+'hasil-peserta');
		}else{
			alert(respo);
		}
	});	
}

function vcfnya(){
	if($('#jml').val() == ""){
		$("#jml").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Jumlah Voucher Tidak Boleh Kosong!" });
		return false;
	}
	
	ajxamsterfrm("vcf_act", function(respo){
		if(respo == 1){
			alert("Data Tersimpan");
			loadUrl(hostir+'manajemen-voucher');
			//window.open(hostir+'cetak-voucher','_blank');
		}else{
			alert(respo);
		}
	});	
}

function search_data(type, p1, p2){
	$('#'+p2).html('');
	$.post(hostir+"modul_admin/getdatasearch/"+type, { 'nre':$('#'+p1).val() }, function(rspp){
		$('#'+p2).html(rspp);
	});
}

//****************LEVI
function sv_admin(){
	if($('#nip').val() == ""){
		$("#nip").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "NIP Harus Diisi!" });
		return false;
	}
	if($('#username').val() == ""){
		$("#username").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Username Harus Diisi!" });
		return false;
	}
	if($('#pass').val() == ""){
		$("#pass").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Password Harus Diisi!" });
		return false;
	}
	if($('#repass').val() == ""){
		$("#repass").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Konfirmasi Password Anda!" });
		return false;
	}
	if($('#email').val() == ""){
		$("#email").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Email Harus Diisi!" });
		return false;
	}
	if($('#pass').val() != $('#repass').val()){
		$("#repass").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Password Tidak Sama!" });
		return false;
	}
	document.regAdmin.submit();
}

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
		case "ap_tk_1":
			fillCombo(hostir+"combos/sb_ap_tk2/", 'sb_ap_tk2', "", $('#'+type).val() );
			$('#fl_s').remove();
			$('#sert_tam').remove();
			$(".portlet").hide();
		break;
		case "sb_ap_tk2":
			$.post(hostir+"chk", { 'id_asn_child_tk1':$('#'+type).val(), 'tuk':'list' }, function(resp){
				if(resp == 0){
					$('#fl_s').remove();
					$('#sert_tam').remove();
					// $.post(hostir+"reg-file", { 'id_asn':$('#'+type).val() }, function(resp){
						// $('#sertifikasi_1').append(resp);
					// });	
					alert("Data Tingkat Sertifikasi kosong!");
				}else{
					$('#fl_s').remove();
					$('#sert_tam').remove();
					$('.anuan').append(resp);
					$(".portlet").hide();
				}
			});
			$("#sb_jns_nxx").val($('#'+type+" :selected").text());
		break;
		case "sb_ap_tk3":
			$.post(hostir+"uji-mandiri-list", { 'id_asn':$('#'+type).val() }, function(resp){
				$(".portlet").show();
				$(".portlet").html(resp);
			});
		break;		
		case "prv":
			fillCombo(hostir+"combo/ka/", 'ka', "", $('#'+type).val() );
			fillCombo(hostir+"combo/ins/", 'ins', "", $('#'+type).val() );
		break;
	}
	//clr();
}

function modalOpt($type){
	//e.preventDefault();
	switch($type){
		case "aparat":
			$( "#dialog-message" ).removeClass('hide').dialog({
				resizable: false,
				modal: true,
				title: "<i class='fa fa-check text-danger'></i> Pilih Level sertifikasi!",
				title_html: true,
				buttons: [ 
						{
							text: "Level 2",
							"class" : "btn btn-info btn-sm",
							click: function() {
								loadUrl(hostir+'form-aparatur');
								$( this ).dialog( "close" ); 
							} 
						},
						{
							text: "Level 3",
							"class" : "btn btn-info btn-sm",
							click: function() {						
								loadUrl(hostir+'form-aparatur');
								$( this ).dialog( "close" ); 
							} 
						}
					]
			});
		break;
		case "sert":
			$( "#dialog-message" ).removeClass('hide').dialog({
				resizable: false,
				modal: true,
				title: "<i class='fa fa-check text-danger'></i> Pilih Level sertifikasi!",
				title_html: true,
				buttons: [ 
						{
							text: "Level 2",
							"class" : "btn btn-info btn-sm",
							click: function() {
								loadUrl(hostir+'form-sertifikat');
								$( this ).dialog( "close" ); 
							} 
						},
						{
							text: "Level 3",
							"class" : "btn btn-info btn-sm",
							click: function() {						
								loadUrl(hostir+'form-sertifikat-2');
								$( this ).dialog( "close" ); 
							} 
						}
					]
			});
		break;
	}
}
