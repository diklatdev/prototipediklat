<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller'] = "why/modul_portal";

//////////////////////////*** WHY
//Modul Admin
$route['admin-ctrlpnl'] = "why/modul_admin";
$route['loginadm'] = "login/loginadm";
$route['logoutadm'] = "login/logoutadm";

$route['manajemen-peserta'] = "why/modul_admin/getdisplay/manajemen_peserta/";
$route['peserta-detail'] = "why/modul_admin/getdisplay/detail_peserta/";
$route['verifikasi-registrasi'] = "why/modul_admin/simpansavedbx/ver_reg/";
$route['verifikasi-dokumen-syarat'] = "why/modul_admin/simpansavedbx/ver_dok_peryaratan/";

$route['asesmen-peserta'] = "why/modul_admin/getdisplay/asesmen_mandiri/";
$route['asesmen-detail'] = "why/modul_admin/getdisplay/detail_asesmen/";
$route['verifikasi-asesmen'] = "why/modul_admin/simpansavedbx/ver_ass/";

$route['verifikasi-ujitulis-iktujian'] = "why/modul_admin/simpansavedbx/ver_ikt_ujol/";
$route['ujitulis-peserta'] = "why/modul_admin/getdisplay/ujitulis_online/";
$route['ujitulis-detail'] = "why/modul_admin/getdisplay/detail_ujitulis/";
$route['verifikasi-ujitulis'] = "why/modul_admin/simpansavedbx/ver_ujol/";

$route['pembayaran-peserta'] = "why/modul_admin/getdisplay/pembayaran_online/";
$route['pembayaran-detail'] = "why/modul_admin/getdisplay/detail_pembayaran/";
$route['verifikasi-pembayaran'] = "why/modul_admin/simpansavedbx/ver_konfpembayaran/";

$route['penjadwalan-peserta'] = "why/modul_admin/getdisplay/penjadwalan/";
$route['tambah-data-wawancara'] = "why/modul_admin/getdisplay/form_penjadwalan/add";
$route['submit-penjadwalan'] = "why/modul_admin/simpansavedbx/savejadwal";

$route['ujisimulasi-peserta'] = "why/modul_admin/getdisplay/simulasi/";
$route['simulasi-detail'] = "why/modul_admin/getdisplay/detail_simulasi/";
$route['verifikasi-ujisimulasi'] = "why/modul_admin/simpansavedbx/savesimulasi";

$route['wawancara-peserta'] = "why/modul_admin/getdisplay/wawancara/";
$route['wawancara-detail'] = "why/modul_admin/getdisplay/detail_wawancara/";
$route['verifikasi-wawancara'] = "why/modul_admin/simpansavedbx/savewawancara";

$route['hasil-peserta'] = "why/modul_admin/getdisplay/hasil_akhir/";
$route['hasil-detail'] = "why/modul_admin/getdisplay/detail_hasil/";
$route['verifikasi-hasil'] = "why/modul_admin/simpansavedbx/savehasil";

$route['cetak-sertifikat'] = "why/modul_admin/getdisplay/print_sertifikat/";
$route['generate-sertifikat/(:any)/(:any)/(:any)'] = "why/modul_admin/gen_sertifikat/$1/$2/$3";

$route['manajemen-voucher'] = "why/modul_admin/getdisplay/voucher/";
$route['tambah-voucher'] = "why/modul_admin/getdisplay/form_voucher/";
$route['submit-voucher'] = "why/modul_admin/simpansavedbx/savevoucher/";
$route['cetak-voucher'] = "why/modul_admin/generate_voucher/";
$route['form-kirim-voucher'] = "why/modul_admin/getdisplay/form_kirim_voucher/";
$route['submit-kirim-voucher'] = "why/modul_admin/kirimvoucher/";

$route['manajemen-petunjukdokumen'] = "why/modul_admin/getdisplay/petunjuk_dokumen/";
$route['form-petunjukdokumen'] = "why/modul_admin/getdisplay/form_petunjuk_dokumen/";
$route['submit-petunjukdokumen'] = "why/modul_admin/simpansavedbx/savepetunjukdokumen/";

$route['manajemen-berita'] = "why/modul_admin/getdisplay/berita/";
$route['form-berita'] = "why/modul_admin/getdisplay/form_berita/";
$route['submit-berita'] = "why/modul_admin/simpansavedbx/saveberita/";

$route['manajemen-faq'] = "why/modul_admin/getdisplay/faq/";
$route['form-faq'] = "why/modul_admin/getdisplay/form_faq/";
$route['submit-faq'] = "why/modul_admin/simpansavedbx/savefaq/";

$route['cari-data/(:any)'] = "why/modul_admin/getdatasearch/$1";
$route['paging-data/(:any)'] = "why/modul_admin/gettabelpaging/$1";

//Modul Portal
$route['combo/(:any)'] = "why/modul_portal/fillcombo/$1/echo";

$route['registrasi-diklat'] = "why/modul_portal/getdisplay/registrasi";
$route['chk'] = "why/modul_portal/getdisplay/additional/checking_data";
$route['chk2'] = "why/modul_portal/getdisplay/additional/checking_data_2";
$route['chk3'] = "why/modul_portal/getdisplay/additional/checking_data_3";
$route['reg-file'] = "why/modul_portal/getdisplay/additional/registrasi_file_persyaratan";
$route['submit-registrasi'] = "why/modul_portal/simpansavedbs/registrasi";
$route['submit-revisi-persyaratan'] = "why/modul_portal/simpansavedbs/revpersyaratan";

$route['assesmen-mandiri'] = "why/modul_portal/getdisplay/assesmen";
$route['submit-asesmen'] = "why/modul_portal/simpansavedbs/asesmen";
$route['submit-revisi-asesmen'] = "why/modul_portal/simpansavedbs/rev_asesmen";


$route['uji-online-mandiri'] = "why/modul_portal/getdisplay/uji_online";
$route['minta-soal'] = "why/modul_portal/getdisplay/additional/load_soal";
$route['sbm-uj'] = "why/modul_portal/simpansavedbs/saveujiansatuan";
$route['sbm-wkt'] = "why/modul_portal/simpansavedbs/saveujianwaktu";
$route['soal-sisa'] = "why/modul_portal/simpansavedbs/savesoalsisa";
$route['submit-ujian'] = "why/modul_portal/simpansavedbs/saveujian";

$route['konfirmasi-pembayaran'] = "why/modul_portal/getdisplay/pembayaran";
$route['submit-konfirmasi-pembayaran'] = "why/modul_portal/simpansavedbs/savepembayaran";

$route['penjadwalan-wawancara'] = "why/modul_portal/getdisplay/penjadwalan";
$route['daftar-jadwal'] = "why/modul_portal/getdisplay/additional/registrasi_wawancara";
$route['daftar-sekarang'] = "why/modul_portal/simpansavedbs/savedaftarwawancara";

$route['hasil-akhir'] = "why/modul_portal/getdisplay/hasil";
$route['kinerja'] = "why/modul_portal/getdisplay/kinerja_peserta";

$route['cetak-kartu-ujian'] = "why/modul_portal/generate_kartu_ujian";

$route['chk-login'] = "why/modul_portal/getdisplay/additional/checking_data_login";
$route['login-peserta'] = "why/modul_portal/getdisplay/login-portal";
$route['submit-login'] = "login/login_sbm";
$route['logout-peserta'] = "login/logogut";

$route['registrasi-diklat-baru'] = "why/modul_portal/getdisplay/registrasi_diktlatbaru";
$route['submit-registrasi-baru'] = "why/modul_portal/simpansavedbs/registrasi_baru";

$route['sbm-komplain-peserta'] = "why/modul_portal/simpansavedbs/save_komplain";

$route['info-kontak'] = 'why/modul_portal/getdisplay/kontaks';
$route['faq'] = 'why/modul_portal/getdisplay/faqqs';

$route['berita'] = 'why/modul_portal/getdisplay/konten_berita';
$route['berita_detail/(:any)'] = 'why/modul_portal/getdisplay/konten_berita_detail/$1';

$route['petunjuk-dokumen'] = 'why/modul_portal/getdisplay/konten_petunjuk_dokumen';
$route['download-file-petunjuk/(:any)'] = 'why/modul_portal/getdisplay/download_petunjuk_dokumen/$1';


//////////////////////////*** LV
//Modul Admin
$route['combos/(:any)'] = "why/modul_portal/fillcombo/$1/echo";

$route['manajemen-admin'] = "lv/modul_admin/getdisplay/manajemen_admin/";
$route['form-admin'] = "lv/modul_admin/getdisplay/form_admin/";
$route['submit-admin'] = "lv/modul_admin/simpansavedbx/sv_admin";
$route['edit-admin'] = "lv/modul_admin/getdisplay/form_edit_admin";
$route['update-admin'] = "lv/modul_admin/simpansavedbx/up_admin";

$route['manajemen-aparatur'] = "lv/modul_admin/getdisplay/manajemen_aparatur/";
$route['form-aparatur'] = "lv/modul_admin/getdisplay/form_aparatur/";
$route['submit-aparat'] = "lv/modul_admin/simpansavedbx/sv_aparat";

$route['manajemen-sertifikasi'] = "lv/modul_admin/getdisplay/manajemen_sertifikasi/";
$route['form-sertifikat'] = "lv/modul_admin/getdisplay/form_sertifikat/";
$route['submit-syarat'] = "lv/modul_admin/simpansavedbx/sv_syarat";

$route['manajemen-uji-mandiri'] = "lv/modul_admin/getdisplay/manajemen_uji_mandiri/";
$route['uji-mandiri-list'] = "lv/modul_admin/getdisplay/manajemen_uji_mandiri/";
$route['form-uji-mandiri'] = "lv/modul_admin/getdisplay/form_uji_mandiri/";
$route['submit-uji-man'] = "lv/modul_admin/simpansavedbx/sv_uji_man";

$route['manajemen-instansi'] = "lv/modul_admin/getdisplay/manajemen_instansi/";
$route['form-instansi'] = "lv/modul_admin/getdisplay/form_instansi/";
$route['form-instansi'] = "lv/modul_admin/getdisplay/form_instansi/";
$route['submit-instansi'] = "lv/modul_admin/simpansavedbx/sv_instansi/sv";
$route['edit-instansi'] = "lv/modul_admin/getdisplay/edit_instansi/";
$route['update-instansi'] = "lv/modul_admin/simpansavedbx/sv_instansi/up";

$route['manajemen-pangkat'] = "lv/modul_admin/getdisplay/manajemen_pangkat/";
$route['form-pangkat'] = "lv/modul_admin/getdisplay/form_pangkat/";
$route['submit-pangkat'] = "lv/modul_admin/simpansavedbx/sv_pangkat/sv";
$route['update-pangkat'] = "lv/modul_admin/simpansavedbx/sv_pangkat/up";
$route['edit-pangkat'] = "lv/modul_admin/getdisplay/edit_pangkat/";

$route['manajemen-tuk'] = "lv/modul_admin/getdisplay/manajemen_tuk/";
$route['form-tuk'] = "lv/modul_admin/getdisplay/form_tuk/";
$route['submit-tuk'] = "lv/modul_admin/simpansavedbx/sv_tuk";

// Levi PAK routes
$route['pak'] = "lv/modul_pak/view_pak";
$route['submit-pak'] = "lv/modul_pak/savedatatodb/pak";
$route['angka-kredit'] = "lv/modul_pak/combo_angdit";
///--- yang baru untuk inpassing

$route['pak_inpassing'] = "lv/modul_pak/getdisplay/pak_inpassing";
$route['submit-pak-inpassing'] = "lv/modul_pak/savedatatodb/pak";
$route['hasil-pak-temp'] = "lv/modul_pak/getdisplay/hasil_pak_inpassing_temp";
$route['hasil-pak'] = "lv/modul_pak/getdisplay/hasil_validasi_pak";
## Admin PAK
$route['pak-temp'] = "lv/modul_pak/getdisplayadmin/pak_temp_admin";
$route['pak-pengajuan'] = "lv/modul_pak/getdisplayadmin/pak_pengajuan_det";
$route['pak-validasi'] = "lv/modul_pak/getdisplayadmin/validasi_pak";
$route['approve-pak'] = "lv/modul_pak/savedatatodb/approve_pak";
$route['gen-pak'] = "lv/modul_pak/gen_sertifikat";
$route['gen-sertifikat'] = "lv/modul_pak/gen_sertifikat";
$route['gen-sk'] = "lv/modul_pak/gen_keputusan";

$route['pak-result'] = "lv/modul_pak/getdisplayadmin/pak_result_admin";
$route['peripikasi-pak'] = "lv/modul_pak/savedatatodb/peripikasi_pak";



$route['404_override'] = '';

/* End of file routes.php */
/* Location: ./application/config/routes.php */