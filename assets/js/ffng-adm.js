function getClientHeight(){
	var theHeight;
	if (window.innerHeight)
		theHeight=window.innerHeight;
	else if (document.documentElement && document.documentElement.clientHeight) 
		theHeight=document.documentElement.clientHeight;
	else if (document.body) 
		theHeight=document.body.clientHeight;
	
	return theHeight;
}
function getClientWidth(){
	var theWidth;
	if (window.innerWidth) 
		theWidth=window.innerWidth;
	else if (document.documentElement && document.documentElement.clientWidth) 
		theWidth=document.documentElement.clientWidth;
	else if (document.body) 
		theWidth=document.body.clientWidth;

	return theWidth;
}

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
		case "apxx_tk_1":
			fillCombo(hostir+"combo/sb_ap_tk2/", 'sbxx_ap_tk2', "", $('#'+type).val() );
			$('#fl_s').remove();
			$('#sert_tam').remove();
			$('#sert_tam_2').remove();
			$('#sert_tam_3').remove();
			$('#loader-soal').html('');
			
			if( $('#'+type).val() == 2 ){
				$('#txtsbjn').html('Urusan Pemerintah : ');
			}else{
				$('#txtsbjn').html('Sub Jenis Sertifikasi : ');
			}
			
		break;
		case "sbxx_ap_tk2":
			$('#loader-soal').html('');
			$.post(hostir+"chk-adm", { 'id_asn_child_tk1':$('#'+type).val() }, function(resp){
				if(resp == 0){
					$('#fl_s').remove();
					$('#sert_tam').remove();
					$('#sert_tam_2').remove();
					$.post(hostir+"tampil-soal", { 'id_asn':$('#'+type).val() }, function(resp){
						$('#loader-soal').append(resp);
					});
				}else{
					$('#fl_s').remove();
					$('#sert_tam').remove();
					$('#sert_tam_2').remove();
					$('#sertifikasi_1').append(resp);
					if( $('#apxx_tk_1').val() == 2 ){
						$('#txtsbjnsrt').html('Tipologi : ');
					}else{
						$('#txtsbjnsrt').html('Jenjang Sertifikasi : ');
					}
				}
			});
			$("#sb_jns_nxx").val($('#'+type).val());
		break;
		case "sbxx_ap_tk3":
			$('#fl_s').remove();
			$('#sert_tam_2').remove();
			$('#sert_tam_3').remove();
			$('#loader-soal').html('');
			$.post(hostir+"chk2-adm", { 'id_asn_child_tk2':$('#'+type).val() }, function(resp){
				if(resp == 0){
					$.post(hostir+"tampil-soal", { 'id_asn':$('#'+type).val() }, function(resp){
						$('#loader-soal').append(resp);
					});					
				}else{
					//$('#fl_s').remove();
					$('#sert_tam_2').remove();
					$('#sert_tam_3').remove();
					$('#sertifikasi_1').append(resp);
				}
			});
			
			$("#sb_jns_nxx").val($('#'+type).val());
		break;
		case "sbxx_ap_tk4":
			$('#fl_s').remove();
			$('#loader-soal').html('');
			$.post(hostir+"chk3-adm", { 'id_asn_child_tk3':$('#'+type).val() }, function(resp){
				if(resp == 0){
					$.post(hostir+"tampil-soal", { 'id_asn':$('#'+type).val() }, function(resp){
						$('#loader-soal').append(resp);
					});
				}else{
					$('#sert_tam_3').remove();
					$('#sertifikasi_1').append(resp);
				}
			});
			
			$("#sb_jns_nxx").val($('#'+type).val());
		break;
		case "sbxx_ap_tk5":
			$("#sb_jns_nxx").val($('#'+type).val());
		break;
	}
}

function genGrid(modnya, lebarnya, tingginya){
	if(lebarnya == undefined){
		lebarnya = getClientWidth-230;
	}
	if(tingginya == undefined){
		tingginya = getClientHeight-300
	}

	var kolom ={};
	var frozen ={};
	var judulnya;
	var param={};
	var urlnya;
	var urlglobal="";
	var fitnya;
	var pagesizeboy = 10;
	
	switch(modnya){
		case "data_peserta":
			judulnya = "";
			fitnya = true;
			pagesizeboy = 50;
			kolom[modnya] = [	
				{field:'nama_lengkap',title:'Nama Peserta',width:250, halign:'center',align:'left'},
				{field:'step_registrasi',title:'Registrasi',width:150, halign:'center',align:'center',
					formatter: function(value,row,index){
						if(row.step_registrasi == 1){
							return "<button href='#' ><img src='"+hostir+"assets/images/ok.png' width='16px' height='16px' /></button>";
						}else if(row.step_registrasi == 2){
							return "<button href='#'>Preview</button>";
						}
					}
				},
				{field:'step_asesmen_mandiri',title:'Asesmen Mandiri',width:150, halign:'center',align:'center',
					formatter: function(value,row,index){
						if(row.step_asesmen_mandiri == 1){
							return "<button href='#' ><img src='"+hostir+"assets/images/ok.png' width='16px' height='16px' /></button>";
						}else if(row.step_asesmen_mandiri == 2){
							return "<button href='#'>Preview</button>";
						}else if(row.step_asesmen_mandiri == 3){
							return "Dalam Proses";
						}else if(row.step_asesmen_mandiri == 0){
							return "<img src='"+hostir+"assets/images/cancel.png' width='16px' height='16px' />";
						}
					}
				},
				{field:'step_uji_test',title:'Uji Test',width:150, halign:'center',align:'center',
					formatter: function(value,row,index){
						if(row.step_uji_test == 1){
							return "<button href='#' ><img src='"+hostir+"assets/images/ok.png' width='16px' height='16px' /></button>";
						}else if(row.step_uji_test == 2){
							return "<button href='#'>Preview</button>";
						}else if(row.step_uji_test == 3){
							return "Dalam Proses";
						}else if(row.step_uji_test == 4){
							return "Verifikasi Admin";
						}else if(row.step_uji_test == 0){
							return "<img src='"+hostir+"assets/images/cancel.png' width='16px' height='16px' />";
						}
					}
				},
				{field:'step_uji_simulasi',title:'Uji Simulasi',width:150, halign:'center',align:'center',
					formatter: function(value,row,index){
						if(row.step_uji_simulasi == 1){
							return "<button href='#' ><img src='"+hostir+"assets/images/ok.png' width='16px' height='16px' /></button>";
						}else if(row.step_uji_simulasi == 2){
							return "<button href='#'>Preview</button>";
						}else if(row.step_uji_simulasi == 0){
							return "<img src='"+hostir+"assets/images/cancel.png' width='16px' height='16px' />";
						}
					}
				},
				{field:'step_wawancara',title:'Wawancara',width:150, halign:'center',align:'center',
					formatter: function(value,row,index){
						if(row.step_wawancara == 1){
							return "<button href='#' ><img src='"+hostir+"assets/images/ok.png' width='16px' height='16px' /></button>";
						}else if(row.step_wawancara == 2){
							return "<button href='#'>Preview</button>";
						}else if(row.step_wawancara == 0){
							return "<img src='"+hostir+"assets/images/cancel.png' width='16px' height='16px' />";
						}
					}
				},
				{field:'nama_asesor',title:'Petugas Asesor',width:200, halign:'center',align:'left'},
			];
		break;
		case "file_registrasi":
			judulnya = "";
			fitnya = true;
			pagesizeboy = 50;
			kolom[modnya] = [	
				{field:'nama_lengkap',title:'Nama Peserta',width:250, halign:'center',align:'left'},
				{field:'tbl_data_peserta_id',title:'Cek File',width:150, halign:'center',align:'center',
					formatter: function(value,row,index){
						return "<button href='#'>Preview Data</button>";
					}
				},
			];
		break;
		case "file_asesmen":
			judulnya = "";
			fitnya = true;
			pagesizeboy = 50;
			kolom[modnya] = [	
				{field:'nama_lengkap',title:'Nama Peserta',width:250, halign:'center',align:'left'},
				{field:'tbl_data_peserta_id',title:'Cek File',width:150, halign:'center',align:'center',
					formatter: function(value,row,index){
						return "<button href='#'>Preview Data</button>";
					}
				},
			];
		break;
	}
	
	$("#"+modnya).datagrid({
		title:judulnya,
        height:tingginya,
        width:lebarnya,
		rownumbers:true,
		iconCls:'database',
        fit:fitnya,
        striped:true,
        pagination:true,
        remoteSort: false,
        url: hostir+'datagrid/'+modnya,
		nowrap: true,
        singleSelect:true,
		pageSize:pagesizeboy,
		pageList:[10,20,30,40,50,75,100,200],
		queryParams:param,
		columns:[
            kolom[modnya]
        ],
		toolbar: '#toolbar_'+modnya,
	});

}


function loadUrl(urls,func){	
    $("#tMain").html("").addClass("loading");
	$.get(urls,function (html){
	    $("#tMain").html(html).removeClass("loading");
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
		case "htm_jd":
			var r = confirm("Anda Yakin Menghapus Data ini?");
			if(r == true){
				$.post(urlnya, { 'idx_jd':p1 }, function(resp){
					if(resp == 1){
						alert('Data Terhapus');
					}else{
						alert('Data Gagal Terhapus');
					}
					loadUrl(hostir+'penjadwalan-peserta');
				});
			}
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
		case "vcf-krm":
			$.post(urlnya, { 'idvcf':p1 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "kcppt":
		case "fqa":
		case "brt":
			$.post(urlnya, { 'editstatus':'add' }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "edt_kcppt":
		case "edt_brt":
		case "edt_fqa":
			$.post(urlnya, { 'editstatus':'edit', 'isdx':p1 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		
		case "tm-soal":
			idx_ser = $('#sb_jns_nxx').val();
			$.post(urlnya, { 'editstatus':'add', 'idx_sert':idx_ser }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "ed-soal":
			idx_ser = $('#sb_jns_nxx').val();
			$.post(urlnya, { 'editstatus':'edit', 'idx_sert':idx_ser, 'idx_sl':p1 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "km-soal":
			idx_ser = $('#sb_jns_nxx').val();
			$('#loader-soal').html('');
			$.post(hostir+"tampil-soal", { 'id_asn':idx_ser }, function(resp){
				$('#loader-soal').html(resp);
			});
		break;
		
		//**************levi		
		case "add_uji":
			$.post(urlnya, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "pst_chg":
			$.post(urlnya, { 'id_u' : p1 , 'idx_s' : p2, 'kdr' : p3 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
	}
	return false;
}

function kumpulPost($type, p1, p2, p3, p4){
	switch($type){
		case "ver_r_ox":
			for (i = 1; i <= p3; i++) {
				/*if($('#sts_nilai_'+i+':checked').length == 0){$('#sts_nilai_'+i).focus();}*/
				if($('#sts_nilai_'+i).val() == 0){
					$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Data File Persyaratan No. "+i+" Harus Terpenuhi!" });
					return false;
				}
			}
				
			
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
			
			var kepnya = $('#kep_'+p1+':checked').val();
			$.post(hostir+"verifikasi-dokumen-syarat", { 'idws':p1, 'kp':kepnya, 'mm':$('#memo_'+p1).val() }, function(rpp){
				if(rpp == 1){
					$('#flag_'+p1).html('<font color="green">Sudah Verifikasi</font>');
					if($('#kep_'+p1+':checked').val() == 1){
						$('#sts_'+p1).html('<font color="green">Terpenuhi</font>');
						$('#btn_'+p1).remove();
					}
					
					//console.log(kepnya);
					$('#sts_nilai_'+p2).val(kepnya);
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
		case "krmvcf":
			ajxamsterfrm("vcf_kr_act", function(respo){
				if(respo == 1){
					alert("Data Tersimpan");
					loadUrl(hostir+'manajemen-voucher');
				}else{
					alert(respo);
				}
			});	
		break;		
		case "del-soal":
			$.post(hostir+"hapus-banksoal", { 'editstatus':'delete', 'usiid':p1 }, function(rspp){
				if(rspp == 1){
					alert('Data Sudah Dihapus');
					loadUrl_adds('km-soal');
				}else{
					alert('Gagal Menghapus Data');
					//loadUrl_adds('km-soal');
				}
			});
		break;
		case "chk-soal":
			$(".chk_soal").each(function () {
			   $(this).val(0);
			});
			$('#ed_flagbener_'+p1).val(1);
		break;
		case "cet_uj_dt":
			window.open(hostir+'generate-dokumen-ujian/dokumen_ujian_test/'+p1+'/'+p2+'/'+p3+'/'+p4);
			$('#hdr_'+p1).removeAttr("disabled");
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
		case "up_instansi":			
			$.post(hostir+"update-instansi", $('#regInstansi').serialize(),function (rspp){
				if(rspp == 1){
						alert('Instansi Berhasil Diperbaharui!');
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
		case "up_pangkat":			
			$.post(hostir+"update-pangkat", $('#regPangkat').serialize(),function (rspp){
				if(rspp == 1){
						alert('Pangkat Berhasil Diperbaharui!');
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
		case "up_tuk":			
			$.post(hostir+"update-tuk", $('#regTuk').serialize(),function (rspp){
				if(rspp == 1){
						alert('TUK Berhasil Diperbaharui!');
						loadUrl(hostir+'manajemen-tuk');
					}else{
						alert(rspp);
				}			
			});
		break;	
		case "sv_pejabat":			
			$.post(hostir+"submit-pejabat", $('#regPejabat').serialize(),function (rspp){
				if(rspp == 1){
						alert('Pejabat Berhasil Ditambahkan!');
						loadUrl(hostir+'manajemen-pejabat');
					}else{
						alert(rspp);
				}			
			});
		break;	
		case "up_pejabat":			
			$.post(hostir+"update-pejabat", $('#regPejabat').serialize(),function (rspp){
				if(rspp == 1){
						alert('Pejabat Berhasil Diperbaharui!');
						loadUrl(hostir+'manajemen-pejabat');
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
	
	
	
	/*
	if($('#nilai_as').val() == ""){
		$("#nilai_as").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Nilai Ujian Tidak Boleh Kosong!" });
		return false;
	}
	*/
	
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

	if($('#apxx_tk_1').val() == ""){
		$("#apxx_tk_1").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Pilih Jenis Sertifikasi" });
		return false;
	}
	
	if($('#sbxx_ap_tk2').val() == ""){
		$("#sbxx_ap_tk2").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Sub Jenis Sertifikasi Harus Diisi!" });
		return false;
	}
	
	if($('#sbxx_ap_tk3').length != 0){
		if($('#sbxx_ap_tk3').val() == ""){
			$("#sbxx_ap_tk3").focus(); 
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Jenjang Sertifikasi Harus Diisi!" });
			return false;
		}
	}
	
	if($('#sbxx_ap_tk4').length != 0){
		if($('#sbxx_ap_tk4').val() == ""){
			$("#sbxx_ap_tk4").focus(); 
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Jenjang Sertifikasi Harus Diisi!" });
			return false;
		}
	}


	if($('#edtuk').val() == ""){
		$("#edtuk").focus(); 
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
		}else if(respo == 2){
			alert("Data Sudah Ada Dalam Sistem! Ubah TUK / Tanggal Pelaksanaan.");
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

function sb_ptjk(){
	if($('#edjnka').val() == ""){
		$("#edjnka").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Jenis Dokumen Tidak Boleh Kosong!" });
		return false;
	}
	
	if($('#nm_ser').val() == ""){
		$("#nm_ser").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Nama Sertifikasi Tidak Boleh Kosong!" });
		return false;
	}
	
	if($('#editstatus').val() == "add"){
		if($('#edFile_ptjk').val() == ""){
			$("#edFile_ptjk").focus(); 
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "File Petunjuk Sertifikasi Tidak Boleh Kosong!" });
			return false;
		}
	}
	
	ajxamsterfrm("kcp_act", function(respo){
		if(respo == 1){
			alert("Data Tersimpan");
			loadUrl(hostir+'manajemen-petunjukdokumen');
		}else{
			alert(respo);
		}
	});	
}

function sb_brt(){
	if($('#jd_ed').val() == ""){
		$("#jd_ed").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Judul Berita Tidak Boleh Kosong!" });
		return false;
	}	
	if($('#editstatus').val() == "add"){
		if($('#edFile_ptjk').val() == ""){
			$("#edFile_ptjk").focus(); 
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "File Gambar Tidak Boleh Kosong!" });
			return false;
		}
	}
	if($('#isbrt_ed').val() == ""){
		$("#isbrt_ed").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Isi Berita Tidak Boleh Kosong!" });
		return false;
	}

	ajxamsterfrm("brt_act", function(respo){
		if(respo == 1){
			alert("Data Tersimpan");
			loadUrl(hostir+'manajemen-berita');
		}else{
			alert(respo);
		}
	});	
	
}

function sb_faq(){
	if($('#prtny_ed').val() == ""){
		$("#prtny_ed").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Pertanyaan Tidak Boleh Kosong!" });
		return false;
	}	
	if($('#jwb_ed').val() == ""){
		$("#jwb_ed").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Jawaban Tidak Boleh Kosong!" });
		return false;
	}
	ajxamsterfrm("faq_act", function(respo){
		if(respo == 1){
			alert("Data Tersimpan");
			loadUrl(hostir+'manajemen-faq');
		}else{
			alert(respo);
		}
	});	
	
}

function smpn_xoal(){
	if($('#ed_soal').val() == ""){
		$("#ed_soal").focus(); 
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Soal Tidak Boleh Kosong" });
		return false;
	}
	
	for (i = 0; i <= 2; i++) {
		if($('#ed_jawaban_'+i).val() == ""){
			$('#ed_jawaban_'+i).focus();
			$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Pilihan Jawaban No. "+(i+1)+" Tidak Boleh Kosong!" });
			return false;
		}
	}
	
	if($('#ckdong:checked').length == 0){
		$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Flag Jawaban Benar Harus Ditentukan, Check Radio Button di Sebelah Pilihan Jawaban." });
		return false;
	}
	
	ajxamsterfrm("dataSoal", function(respo){
		if(respo == 1){
			alert("Data Tersimpan");
			loadUrl_adds('km-soal');
		}else{
			alert("Gagal Tersimpan");
			//loadUrl_adds('km-soal');
		}
	});	
	
}

function search_data(type, p1, p2){
	$('#'+p2).html('');
	$.post(hostir+"why/modul_admin/getdatasearch/"+type, { 'nre':$('#'+p1).val() }, function(rspp){
		$('#'+p2).html(rspp);
	});
}

//****************LEVI
function sv_admin($type, urlnya){
	switch(type){
		case 'up':
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
			// document.regAdmin.submit();
			
			$.post( urlnya, $( "#regAdmin" ).serialize(), function(resp){
				if (resp == 1){					
					alert('Data Admin User Tersimpan!.');
					loadUrl(hostir+'manajemen-admin');
				}else{
					alert(resp);
				}
			});
		break;
		case 'up_us':
			if($('#newpass').val() == ""){
				$("#newpass").focus(); 
				$.msg({fadeIn : 100,fadeOut : 100,bgPath : hostir+"assets/js/plugins/msgplugin/", clickUnblock : false, content : "Password Baru Harus Diisi!" });
				return false;
			}
			
			$.post( urlnya, $( "#upPesert" ).serialize(), function(resp){
				if (resp == 1){					
					alert('Data Admin User Tersimpan!.');
					loadUrl(hostir+'manajemen-user');
				}else{
					alert(resp);
				}
			});
		break;
	}
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
			$(".topologi").hide();
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
					$(".topologi").hide();
				}
			});
			$("#sb_jns_nxx").val($('#'+type+" :selected").text());
		break;
		case "sb_ap_tk3":
			$.post(hostir+"uji-mandiri-list", { 'id_asn':$('#'+type).val(), 'id_tk1':$('#ap_tk_1').val()}, function(resp){
				if ($('#ap_tk_1').val() == '2'){
					$(".topologi").show();
					$(".topologi").html(resp);
				}else{
					$(".portlet").show();
					$(".portlet").html(resp);
				}
			});
		break;	
		case "ap_tk4":
			$.post(hostir+"uji-mandiri-list", { 'id_tkn':$('#'+type).val(),'id_tk1':$('#ap_tk_1').val()}, function(resp){				
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

//fungsi paging
function prevPage(type, p1, p2, p3, p4){
	if(eval($('#page_'+type).val()) <= 1){
		return false;
	}
	
	var jml_page = (eval($('#page_'+type).val())-1);
	$.post(hostir+'paging-data/'+type, { 'page':jml_page-1, 'idxss':p1 , 'idxsert':p2 , 'kdr':p3 , 'lmt':10 }, function(respshit){
		$('#'+type).removeClass('loading-page');
		$('#'+type).html(respshit);
		$('#page_'+type).val(jml_page);
	});
}

function nextPage(type, p1, p2, p3, p4){
	var jml_page = (eval($('#page_'+type).val())+1);
	$.post(hostir+'paging-data/'+type, { 'page':$('#page_'+type).val(), 'idxss':p1 , 'idxsert':p2 , 'kdr':p3 , 'lmt':10 }, function(respshit){
		$('#'+type).removeClass('loading-page');
		$('#'+type).html(respshit);
		$('#page_'+type).val(jml_page);
	});
}
//end fungsi paging

//LEVI JS

function tampil_view(type, urlnya, domnya, p1, p2, p3, p4, p5, p6, p7){
	switch(type){
		case "pak_det":
			$.post(urlnya, { 'id_peserta' : p1 , 'id_angdit' : p2 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "pak_result":
			$.post(urlnya, { 'id_peserta' : p1 , 'id_angdit' : p2 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "pak_sk":
			$.post(urlnya, { 'id_peserta' : p1 , 'id_angdit' : p2 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "pak_ver":
			$.post(urlnya, { 'id_peserta' : p1 , 'id_angdit' : p2 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "pdf_keputusan":
			// $.post(urlnya, { 'id_peserta' : p1 , 'id_angdit' : p2 }, function(resp){
				window.open(urlnya, p1, p2);
			// });
		break;
		case "absensi_tuk":
			var val = $('#select_tuk').val();
			id_jadwal = val.substr(val.indexOf("_") + 1, val.indexOf(".")-1);
			id_sertifikat = val.substr(val.indexOf(",") + 1);
			id_tuk = val.substr(val.indexOf(".") + 1);
			id_tuk = id_tuk.substr(0, id_tuk.indexOf(','));
			
			$.post(urlnya, { 'id_tuk' : val, 'tuk': $('#select_tuk  option:selected').text() }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "progress_tuk":
			var val = $('#select_tuk').val();			
			$.post(urlnya, { 'id_jadwal' : val, 'tuk': $('#select_tuk  option:selected').text()}, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "hasil_akhir":
			var val = $('#select_tuk').val();			
			$.post(urlnya, { 'id_jadwal' : val, 'tuk': $('#select_tuk  option:selected').text()}, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "biodata_peserta":
			var val = $('#select_tuk').val();			
			$.post(urlnya, { 'id_jadwal' : val, 'tuk': $('#select_tuk  option:selected').text()}, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
	}
}

function pakinass(type, urlnya){	
	switch(type){
		// case "save":
			// document.inpas_in.submit();
		// break;
		case "approve":
			$.post( urlnya, $( "#apvpak" ).serialize(), function(resp){
				if (resp == 1){					
					alert('Approval PAK Tersimpan!.');
					loadUrl(hostir+'pak-temp');
				}else{
					alert(resp);
				}
			});
		break;
		case "peripik":
			$.post( urlnya, $( "#verpak" ).serialize(), function(resp){
				if (resp == 1){					
					alert('Verifikasi PAK Tersimpan!.');
					loadUrl(hostir+'pak-result');
				}else{
					alert(resp);
				}
			});
		break;
	}
}


function tampo_data(type, urlnya, p1, p2, p3, p4, p5, p6, p7){
	switch(type){
		case "ureng":
			if (confirm('Apakah Anda Yakin akan menghapus Data User?')) {
				$.post(urlnya, { 'kode' : p1 }, function(resp){
					if (resp == 1){					
						alert('Data Admin Berahasil Dihapus!.');
						loadUrl(hostir+'manajemen-admin');
					}else{
						alert(resp);
					}
				});	
			}
		break;
		case "pejabat":
			if (confirm('Apakah Anda Yakin akan menghapus Data Pejabat?')) {
				$.post(urlnya, { 'kode' : p1 }, function(resp){
					if (resp == 1){					
						alert('Data Pejabat Berahasil Dihapus!.');
						loadUrl(hostir+'manajemen-pejabat');
					}else{
						alert(resp);
					}
				});	
			}
		break;
	}
}

function loadMan_edit(type, urlnya, domnya, p1, p2, p3, p4, p5, p6, p7){
	switch(type){
		case "uj_man_ed":
			$.post(urlnya, { 'id_row' : p1 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "tuk_ed":
			$.post(urlnya, { 'id_row' : p1 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "us_ed":
			$.post(urlnya, { 'id_u' : p1 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "ins_ed":
			$.post(urlnya, { 'id_row' : p1 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "pang_ed":
			$.post(urlnya, { 'id_row' : p1 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "ap_tk_1":
			$.post(urlnya, { 'id_tk1' : $('#ap_tk_1').val() }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
		case "pej_ed":
			$.post(urlnya, { 'id_row' : p1 }, function(resp){
				$("#"+domnya).html(resp);
			});
		break;
	}
}

