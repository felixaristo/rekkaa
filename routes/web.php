<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Model\Transaction\NotificationModel;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

Route::middleware(['isloggedin'])->get('/', function () {
    return redirect('/login');
});

Route::prefix('/kalkulator')
// ->middleware('usergrouppermission')
->group(function () {
    Route::prefix('/pph-pasal-4-ayat-2')->group(function () {
        Route::get('/', 'Calculator\Pph4a2Controller@index')->middleware(['usergrouppermission', 'accesspermission:R|COPY|Q_SEND_EMAIL'])->name('kalkulator.pph4a2');
        Route::get('/cetak', 'Calculator\Pph4a2Controller@cetak')->middleware(['usergrouppermission', 'accesspermission:R']);
        Route::post('/send-email', 'Calculator\Pph4a2Controller@sendEmail')->middleware(['usergrouppermission', 'accesspermission:Q_SEND_EMAIL,tr_notification:CALCULATOR_PPH4a2_SEND_EMAIL']);
    });
    Route::prefix('/pph-21')->group(function () {
        Route::get('/', 'Calculator\PPh21Controller@index')->middleware(['usergrouppermission', 'accesspermission:R|COPY|Q_SEND_EMAIL'])->name('kalkulator.pph21');
        // Route::get('/non', 'Calculator\PPh21Controller@index');
        Route::get('/cetak', 'Calculator\PPh21Controller@pph21Cetak')->middleware(['usergrouppermission', 'accesspermission:R']);
        Route::get('/non-karyawan', 'Calculator\PPh21Controller@indexNonKaryawan')->middleware(['usergrouppermission', 'accesspermission:R|COPY|Q_SEND_EMAIL'])->name('kalkulator.pph21nonkaryawan');
        Route::get('/non-karyawan/cetak', 'Calculator\PPh21Controller@pph21NonCetak')->middleware(['usergrouppermission', 'accesspermission:R']);
        Route::post('/send-email', 'Calculator\PPh21Controller@sendEmail')->middleware(['usergrouppermission', 'accesspermission:Q_SEND_EMAIL,tr_notification:CALCULATOR_PPH21_KARYAWAN_SEND_EMAIL|CALCULATOR_PPH21_NONKARYAWAN_SEND_EMAIL']);
    });
});
Route::get('mailable', function () {
    // $options = [
    //     'from' => env("MAIL_FROM_ADDRESS"),
    //     // 'subject' => '$notif->notification_title',
    //     'data' => [
    //         'user' => User::where(['user_id' => 20])->first(),
    //         'notification' => NotificationModel::where(['notification_id' => 32])->first()
    //     ],
    //     'view' => 'email.email-berlangganan' // 'email.email-lupa-password'
    // ];
    // if($options['data']['notification']['notification_data']) {
    //     $decode_data = json_decode($options['data']['notification']['notification_data']);
    //     foreach($decode_data as $key => $val) {
    //         $options['data'][$key] = $val;
    //     }
    // }
    // return view($options['view'], $options['data']);

    // $options = [
    //     'from' => env("MAIL_FROM_ADDRESS"),
    //     'subject' => 'testing email',
    //     'view' => 'email.email-kalkulator'
    // ];
    // Mail::to("dyas.nata.91@gmail.com")->send(new App\Mail\RekkaaMail($options));
    return view('email.email-sample');
});

//subscription for admin only, for dev purpose only
Route::prefix('/admin/subscriptionpermission')->middleware('aksesuser')->group(function () {
    Route::get('/', 'User\Dashboard\SubscriptionPermissionController@index')->name('admin.subscription.permission.index');
    Route::get('/datatable', 'User\Dashboard\SubscriptionPermissionController@datatable')->name('admin.subscription.permission.datatable'); 
    Route::get('/getmenu', 'User\Dashboard\SubscriptionPermissionController@getmenu')->name('admin.subscription.permission.getmenu'); 
    Route::post('/store', 'User\Dashboard\SubscriptionPermissionController@store')->name('admin.subscription.permission.master.store'); 
    Route::post('/update/{subscriptionId}', 'User\Dashboard\SubscriptionPermissionController@update')->name('admin.subscription.permission.master.update'); 
});

//subscription wajib pajak for admin only, for dev purpose only
Route::prefix('/admin/subscriptionpermission-wajibpajak')->middleware('aksesuser')->group(function () {
    Route::get('/', 'User\Dashboard\SubscriptionPermissionWajibpajakController@index')->name('admin.subscription.permission.wajibpajak.index');
    Route::get('/datatable', 'User\Dashboard\SubscriptionPermissionWajibpajakController@datatable')->name('admin.subscription.permission.wajibpajak.datatable'); 
    Route::get('/getmenu', 'User\Dashboard\SubscriptionPermissionWajibpajakController@getmenu')->name('admin.subscription.permission.wajibpajak.getmenu'); 
    // Route::post('/store', 'User\Dashboard\SubscriptionPermissionWajibpajakController@store')->name('admin.subscription.permission.wajibpajak.master.store'); 
    Route::post('/update/{wajibpajakId}', 'User\Dashboard\SubscriptionPermissionWajibpajakController@update')->name('admin.subscription.permission.wajibpajak.update'); 
    // Route::post('/orderupdate/{wajibpajakId}', 'User\Dashboard\SubscriptionPermissionWajibpajakController@orderupdate')->name('admin.subscription.permission.wajibpajak.orderupdate'); 
    Route::post('/orderextend/{wajibpajakId}', 'User\Dashboard\SubscriptionPermissionWajibpajakController@orderextend')->name('admin.subscription.permission.wajibpajak.orderextend'); 
});

Route::prefix('/admin/menu')->middleware('aksesuser')->group(function () {
    Route::get('/', 'User\Dashboard\SubscriptionMenuController@index')->name('admin.subscription.menu.index');
    Route::get('/datatable', 'User\Dashboard\SubscriptionMenuController@datatable')->name('admin.subscription.menu.datatable'); 
    Route::get('/select', 'User\Dashboard\SubscriptionMenuController@select')->name('admin.subscription.menu.select'); 
    Route::post('/store', 'User\Dashboard\SubscriptionMenuController@store')->name('admin.subscription.menu.store'); 
    Route::post('/update/{menuId}', 'User\Dashboard\SubscriptionMenuController@update')->name('admin.subscription.menu.update'); 
    Route::post('/delete/{menuId}', 'User\Dashboard\SubscriptionMenuController@delete')->name('admin.subscription.menu.delete'); 
});

Route::prefix('/admin/calculate-payslip')->middleware('aksesuser')->group(function () {
    Route::get('/', 'User\Dashboard\CalculatePayslipController@index')->name('admin.calculate.payslip.index');
    Route::get('/select-stpenggajian', 'User\Dashboard\CalculatePayslipController@select_stpenggajian')->name('admin.calculate.payslip.selectstpenggajian'); 
    Route::get('/select-wajibpajak', 'User\Dashboard\CalculatePayslipController@select_wajibpajak')->name('admin.calculate.payslip.selectwajibpajak'); 
    Route::post('/calculate', 'User\Dashboard\CalculatePayslipController@calculate')->name('admin.calculate.payslip.calculate'); 
});

Route::prefix('/admin')->middleware('aksesadmin')->group(function () {

    Route::prefix('/login')->middleware(['isloggedinadmin'])->group(function () {
        Route::get('/', 'Admin\LoginController@index')->name('admin.login');
        Route::post('/', 'Admin\LoginController@login')->name('dologin');
    });

    Route::prefix('/master')->group(function () {
        Route::prefix('/bpjsrate')->group(function () {
            Route::get('/', 'Admin\Master\BpjsRateController@index')->name('admin.page.master.bpjsrate.index');
            Route::get('/datatable', 'Admin\Master\BpjsRateController@datatable')->name('admin.page.master.bpjsrate.datatable');
            Route::post('/store', 'Admin\Master\BpjsRateController@store')->name('admin.page.master.bpjsrate.store');
            Route::post('/update/{bpjsrateId}', 'Admin\Master\BpjsRateController@update')->name('admin.page.master.bpjsrate.update');
            Route::post('/delete/{bpjsrateId}', 'Admin\Master\BpjsRateController@delete')->name('admin.page.master.bpjsrate.delete');
        });

        Route::prefix('/kepemilikannpwp')->group(function () {
            Route::get('/', 'Admin\Master\KepemilikanNpwpController@index')->name('admin.page.master.kepemilikannpwp.index');
            Route::get('/datatable', 'Admin\Master\KepemilikanNpwpController@datatable')->name('admin.page.master.kepemilikannpwp.datatable');
            Route::post('/store', 'Admin\Master\KepemilikanNpwpController@store')->name('admin.page.master.kepemilikannpwp.store');
            Route::post('/update/{kepemilikannpwpId}', 'Admin\Master\KepemilikanNpwpController@update')->name('admin.page.master.kepemilikannpwp.update');
            Route::post('/delete/{kepemilikannpwpId}', 'Admin\Master\KepemilikanNpwpController@delete')->name('admin.page.master.kepemilikannpwp.delete');
        });

        Route::prefix('/ptkp')->group(function () {
            Route::get('/', 'Admin\Master\PtkpController@index')->name('admin.page.master.ptkp.index');
            Route::get('/datatable', 'Admin\Master\PtkpController@datatable')->name('admin.page.master.ptkp.datatable');
            Route::post('/store', 'Admin\Master\PtkpController@store')->name('admin.page.master.ptkp.store');
            Route::post('/update/{ptkpId}', 'Admin\Master\PtkpController@update')->name('admin.page.master.ptkp.update');
            Route::post('/delete/{ptkpId}', 'Admin\Master\PtkpController@delete')->name('admin.page.master.ptkp.delete');
        });

        Route::prefix('/tarifpph21')->group(function () {
            Route::get('/', 'Admin\Master\Tarif21Controller@index')->name('admin.page.master.tarifpph21.index');
            Route::get('/datatable', 'Admin\Master\Tarif21Controller@datatable')->name('admin.page.master.tarifpph21.datatable');
            Route::post('/store', 'Admin\Master\Tarif21Controller@store')->name('admin.page.master.tarifpph21.store');
            Route::post('/update/{tarif21Id}', 'Admin\Master\Tarif21Controller@update')->name('admin.page.master.tarifpph21.update');
            Route::post('/delete/{tarif21Id}', 'Admin\Master\Tarif21Controller@delete')->name('admin.page.master.tarifpph21.delete');
        });

        Route::prefix('/tarifnonnpwp')->group(function () {
            Route::get('/', 'Admin\Master\TarifNonNpwpController@index')->name('admin.page.master.tarifnonnpwp.index');
            Route::get('/datatable', 'Admin\Master\TarifNonNpwpController@datatable')->name('admin.page.master.tarifnonnpwp.datatable');
            Route::post('/store', 'Admin\Master\TarifNonNpwpController@store')->name('admin.page.master.tarifnonnpwp.store');
            Route::post('/update/{tarifnonnpwpId}', 'Admin\Master\TarifNonNpwpController@update')->name('admin.page.master.tarifnonnpwp.update');
            Route::post('/delete/{tarifnonnpwpId}', 'Admin\Master\TarifNonNpwpController@delete')->name('admin.page.master.tarifnonnpwp.delete');
        });

        Route::prefix('/tunjanganjabatan')->group(function () {
            Route::get('/', 'Admin\Master\TunjanganJabatanController@index')->name('admin.page.master.tunjanganjabatan.index');
            Route::get('/datatable', 'Admin\Master\TunjanganJabatanController@datatable')->name('admin.page.master.tunjanganjabatan.datatable');
            Route::post('/store', 'Admin\Master\TunjanganJabatanController@store')->name('admin.page.master.tunjanganjabatan.store');
            Route::post('/update/{tunjanganjabatanId}', 'Admin\Master\TunjanganJabatanController@update')->name('admin.page.master.tunjanganjabatan.update');
            Route::post('/delete/{tunjanganjabatanId}', 'Admin\Master\TunjanganJabatanController@delete')->name('admin.page.master.tunjanganjabatan.delete');
        });

        Route::prefix('/subscription')->group(function () {
            Route::get('/', 'Admin\Master\SubscriptionController@index')->name('admin.page.master.subscription.index');
            Route::get('/datatable', 'Admin\Master\SubscriptionController@datatable')->name('admin.page.master.subscription.datatable');
            Route::post('/store', 'Admin\Master\SubscriptionController@store')->name('admin.page.master.subscription.store');
            Route::post('/update/{subscriptionId}', 'Admin\Master\SubscriptionController@update')->name('admin.page.master.subscription.update');
            Route::post('/delete/{subscriptionId}', 'Admin\Master\SubscriptionController@delete')->name('admin.page.master.subscription.delete');
        });
    });

    Route::prefix('/setting')->group(function () {
        Route::prefix('/alamat')->group(function () {
            Route::get('/', 'Admin\Setting\AlamatController@index')->name('admin.page.setting.alamat.index');
            Route::post('/update/{setttingKey}', 'Admin\Setting\AlamatController@update')->name('admin.page.setting.alamat.update');
        });
    });

    Route::prefix('/user')->group(function () {
        Route::prefix('/aktif')->group(function () {
            Route::get('/', 'Admin\User\UserController@index')->name('admin.page.user.aktif.index');
            Route::get('/datatable', 'Admin\User\UserController@datatable')->name('admin.page.user.aktif.datatable');
        });

        // Route::prefix('/wajib-pajak/{userId}')->group(function () {
            // user/wajib-pajak/upgrade/
        Route::prefix('/wajib-pajak')->group(function () {
            Route::get('/', 'Admin\User\WajibPajakController@index')->name('admin.page.user.wajibpajak.index');
            Route::get('/datatable', 'Admin\User\WajibPajakController@datatable')->name('admin.page.user.wajibpajak.datatable');
        });
    });

    Route::prefix('/profil')->group(function () {
        Route::get('/', 'Admin\Profil\ProfilController@index')->name('admin.page.profil');
        Route::post('/', 'Admin\Profil\ProfilController@update')->name('admin.page.profil.update');
        Route::get('/password', 'Admin\Profil\PasswordController@index')->name('admin.page.password');
        Route::post('/password', 'Admin\Profil\PasswordController@update')->name('admin.page.password.update');
    });


    Route::get('/beranda', 'Admin\DashboardController@index')->name('admin.page');

    Route::get('logout', 'Admin\LoginController@logout');
});

Route::get('/migratedata', 'User\Dashboard\DashboardController@migratedata')->name('user.migratedata');

Route::prefix('/user')->middleware('aksesuser')->group(function () {
    // Route::prefix('/lawan-transaksi')->group(function () {
    //     Route::get('/', 'User\Master\LawanTransaksiController@index')->name('user.page.lawan-transaksi.index');
    //     Route::get('/datatable', 'User\Master\LawanTransaksiController@datatable')->name('user.page.lawan-transaksi.datatable');
    //     Route::get('/create', 'User\Master\LawanTransaksiController@create')->name('user.page.lawan-transaksi.create');
    //     Route::get('/edit/{tradeexchangeId}', 'User\Master\LawanTransaksiController@edit')->name('user.page.lawan-transaksi.edit');
    //     Route::post('/store', 'User\Master\LawanTransaksiController@save')->name('user.page.lawan-transaksi.store');
    //     Route::post('/update', 'User\Master\LawanTransaksiController@update')->name('user.page.lawan-transaksi.update');
    //     Route::post('/deactive', 'User\Master\LawanTransaksiController@deactive')->name('user.page.lawan-transaksi.deactive');
    //     Route::post('/reactive', 'User\Master\LawanTransaksiController@reactive')->name('user.page.lawan-transaksi.reactive');
    // });

    Route::get('/sidebar', function() {
    //     return view('user/includes/sidebar');
        return '';
    })->name('user.sidebar');
    Route::prefix('/info')->group(function () {
        Route::get('/getwajibpajakgroup', 'User\Info\InfoController@getwajibpajakgroup')->name('user.info.getwajibpajakgroup');
        Route::get('/wajibpajakgroupactivate', 'User\Info\InfoController@wajibpajakgroupactivate')->name('user.info.wajibpajakgroupactivate');
        Route::post('/getkaryawanbyemail', 'User\Info\InfoController@getkaryawanbyemail')->name('user.info.getkaryawanbyemail');
        Route::post('/getkaryawanbykode', 'User\Info\InfoController@getkaryawanbykode')->name('user.info.getkaryawanbykode');
        Route::post('/getkaryawanbynik', 'User\Info\InfoController@getkaryawanbynik')->name('user.info.getkaryawanbynik');
        Route::post('/getkaryawanbynpwp', 'User\Info\InfoController@getkaryawanbynpwp')->name('user.info.getkaryawanbynpwp');
    });
    
    Route::prefix('/profil')->group(function () {
        Route::get('/', 'User\ProfilController@index')->name('user.page.profil');
        Route::post('/', 'User\ProfilController@update')->name('user.page.profil.update');
        Route::get('/password', 'User\PasswordController@index')->name('user.page.password');
        Route::post('/password', 'User\PasswordController@update')->name('user.page.password.update');
    });

    Route::prefix('/log-aktifitas')->group(function () {
        Route::get('/', 'User\LogActivityController@index')->name('user.page.log-aktifitas');
        Route::get('/datatable', 'User\LogActivityController@datatable')->name('user.page.log-aktifitas.datatable');
    });

    Route::prefix('/beranda')->group(function () {
        Route::get('/', 'User\Dashboard\DashboardController@index')->name('user.page');
        Route::get('/data', 'User\Dashboard\DashboardController@data')->name('user.beranda.data');
        Route::get('/datamasuk', 'User\Dashboard\DashboardController@datamasuk')->name('user.beranda.datamasuk');
        Route::get('/dataterlambat', 'User\Dashboard\DashboardController@dataterlambat')->name('user.beranda.dataterlambat');
        Route::get('/datamia', 'User\Dashboard\DashboardController@datamia')->name('user.beranda.datamia');
        Route::get('/datacuti', 'User\Dashboard\DashboardController@datacuti')->name('user.beranda.datacuti');
    });
    

    Route::prefix('/master')->middleware(['usergrouppermission'])->group(function () {
        Route::prefix('/karyawan')->group(function () {
            Route::get('/', 'User\Master\KaryawanController@index')->middleware(['accesspermission:R'])->name('user.page.karyawan.index');
            Route::get('/select', 'User\Master\KaryawanController@select')->name('user.page.karyawan.select');
            Route::get('/create', 'User\Master\KaryawanController@create')->middleware(['accesspermission:C'])->name('user.page.karyawan.create');
            Route::post('/store', 'User\Master\KaryawanController@store')->middleware(['accesspermission:C-Q,ms_karyawan:KARYAWAN'])->name('user.page.karyawan.store');
            Route::get('/datatable', 'User\Master\KaryawanController@datatable')->middleware(['accesspermission:R'])->name('user.page.karyawan.datatable');
            Route::get('/edit/{karyawanId}', 'User\Master\KaryawanController@edit')->middleware(['accesspermission:U'])->name('user.page.karyawan.edit');
            Route::post('/update/{karyawanId}', 'User\Master\KaryawanController@update')->middleware(['accesspermission:U'])->name('user.page.karyawan.update');
            Route::post('/delete/{karyawanId}', 'User\Master\KaryawanController@delete')->middleware(['accesspermission:SD'])->name('user.page.karyawan.delete');
            Route::post('/download-template', 'User\Master\KaryawanController@importTemplate')->middleware(['accesspermission:IMPORT'])->name('user.page.karyawan.download-template');
            Route::post('/generate-history', 'User\Master\KaryawanController@generateHistory')->middleware(['accesspermission:IMPORT'])->name('user.karyawan.generate-history');
            Route::get('/import-history', 'User\Master\KaryawanController@importHistory')->middleware(['accesspermission:IMPORT'])->name('user.karyawan.import-history');
            Route::post('/import', 'User\Master\KaryawanController@import')->middleware(['accesspermission:IMPORT-Q,ms_karyawan:KARYAWAN'])->name('user.page.karyawan.import');
            Route::get('/profile/{karyawanId}', 'User\Master\KaryawanController@profile')->middleware(['accesspermission:R'])->name('user.page.karyawan.profile');

            Route::prefix('/nonaktif')->group(function () {
                Route::get('/', 'User\Master\KaryawanNonaktifController@index')->middleware(['accesspermission:RD'])->name('user.page.karyawannonaktif.index');
                Route::get('/datatable', 'User\Master\KaryawanNonaktifController@datatable')->middleware(['accesspermission:RD'])->name('user.page.karyawannonaktif.datatable');
                Route::post('/activate/{karyawanId}', 'User\Master\KaryawanNonaktifController@activate')->middleware(['accesspermission:ACTIVATE'])->name('user.page.karyawannonaktif.activate');
                Route::get('/profile/{karyawanId}', 'User\Master\KaryawanNonaktifController@profile')->middleware(['accesspermission:RD'])->name('user.page.karyawannonaktif.profile');
            });
        });

        Route::prefix('/non-karyawan')->group(function () {
            Route::get('/', 'User\Master\NonKaryawanController@index')->middleware(['accesspermission:R'])->name('user.page.nonkaryawan.index');
            Route::get('/select', 'User\Master\NonKaryawanController@select')->name('user.page.nonkaryawan.select');
            Route::get('/create', 'User\Master\NonKaryawanController@create')->middleware(['accesspermission:C'])->name('user.page.nonkaryawan.create');
            Route::post('/store', 'User\Master\NonKaryawanController@store')->middleware(['accesspermission:C-Q,ms_karyawan:NONKARYAWAN'])->name('user.page.nonkaryawan.store');
            Route::get('/datatable', 'User\Master\NonKaryawanController@datatable')->middleware(['accesspermission:R'])->name('user.page.nonkaryawan.datatable');
            Route::get('/edit/{karyawanId}', 'User\Master\NonKaryawanController@edit')->middleware(['accesspermission:U'])->name('user.page.nonkaryawan.edit');
            Route::post('/update/{karyawanId}', 'User\Master\NonKaryawanController@update')->middleware(['accesspermission:U'])->name('user.page.nonkaryawan.update');
            Route::post('/delete/{karyawanId}', 'User\Master\NonKaryawanController@delete')->middleware(['accesspermission:SD'])->name('user.page.nonkaryawan.delete');
            Route::post('/download-template', 'User\Master\NonKaryawanController@importTemplate')->middleware(['accesspermission:IMPORT'])->name('user.page.nonkaryawan.download-template');
            Route::post('/generate-history', 'User\Master\NonKaryawanController@generateHistory')->middleware(['accesspermission:IMPORT'])->name('user.nonkaryawan.generate-history');
            Route::get('/import-history', 'User\Master\NonKaryawanController@importHistory')->middleware(['accesspermission:IMPORT'])->name('user.nonkaryawan.import-history');
            Route::post('/import', 'User\Master\NonKaryawanController@import')->middleware(['accesspermission:IMPORT-Q,ms_karyawan:NONKARYAWAN'])->name('user.page.nonkaryawan.import');
            Route::get('/profile/{karyawanId}', 'User\Master\NonKaryawanController@profile')->middleware(['accesspermission:R'])->name('user.page.nonkaryawan.profile');
            // Route::post('/import', 'User\Import\NonKaryawanImportProfilController@import')->name('user.page.nonkaryawan.import');
            Route::prefix('/nonaktif')->group(function () {
                Route::get('/', 'User\Master\NonKaryawanNonaktifController@index')->middleware(['accesspermission:RD'])->name('user.page.nonkaryawannonaktif.index');
                Route::get('/datatable', 'User\Master\NonKaryawanNonaktifController@datatable')->middleware(['accesspermission:RD'])->name('user.page.nonkaryawannonaktif.datatable');
                // same method as employee
                Route::post('/activate/{karyawanId}', 'User\Master\KaryawanNonaktifController@activate')->middleware(['accesspermission:ACTIVATE'])->name('user.page.nonkaryawannonaktif.activate');
                Route::get('/profile/{karyawanId}', 'User\Master\NonKaryawanNonaktifController@profile')->middleware(['accesspermission:RD'])->name('user.page.nonkaryawannonaktif.profile');
            });
        });
        
        Route::prefix('/lawan-transaksi')->group(function () {
            Route::get('/', 'User\Master\LawanTransaksiController@index')->middleware(['accesspermission:R'])->name('user.page.lawan-transaksi.index');
            Route::get('/datatable', 'User\Master\LawanTransaksiController@datatable')->middleware(['accesspermission:R'])->name('user.page.lawan-transaksi.datatable');
            Route::get('/create', 'User\Master\LawanTransaksiController@create')->name('user.page.lawan-transaksi.create');
            Route::get('/edit/{tradeexchangeId}', 'User\Master\LawanTransaksiController@edit')->name('user.page.lawan-transaksi.edit');
            Route::post('/store', 'User\Master\LawanTransaksiController@save')->name('user.page.lawan-transaksi.store');
            Route::post('/update', 'User\Master\LawanTransaksiController@update')->name('user.page.lawan-transaksi.update');
            Route::post('/deactive', 'User\Master\LawanTransaksiController@deactive')->name('user.page.lawan-transaksi.deactive');
            Route::post('/reactive', 'User\Master\LawanTransaksiController@reactive')->name('user.page.lawan-transaksi.reactive');
            Route::post('/download-template', 'User\Master\LawanTransaksiController@importTemplate')->middleware(['accesspermission:IMPORT'])->name('user.page.lawan-transaksi.download-template');
            Route::post('/import', 'User\Master\LawanTransaksiController@importTradeExchange')->middleware(['accesspermission:IMPORT'])->name('user.page.lawan-transaksi.import');
            Route::get('/import-history', 'User\Master\LawanTransaksiController@importHistory')->middleware(['accesspermission:IMPORT'])->name('user.lawan-transaksi.import-history');
            Route::post('/generate-history', 'User\Master\LawanTransaksiController@generateHistory')->middleware(['accesspermission:IMPORT'])->name('user.lawan-transaksi.generate-history');
        });

        Route::prefix('/user')->group(function () {
            Route::get('/', 'User\Master\UserController@select')->name('user.page.user.select');
        });

        
        Route::prefix('/divisi')->group(function () {
            Route::get('/datatable', 'User\Master\KaryawanDivisiController@datatable')->name('user.page.karyawan.divisi.datatable');
            Route::get('/select', 'User\Master\KaryawanDivisiController@select')->name('user.page.karyawan.divisi.select');
            Route::post('/store', 'User\Master\KaryawanDivisiController@store')->name('user.page.karyawan.divisi.store');
            Route::post('/update/{karyawandivisiId}', 'User\Master\KaryawanDivisiController@update')->name('user.page.karyawan.divisi.update');
            Route::post('/delete/{karyawandivisiId}', 'User\Master\KaryawanDivisiController@delete')->name('user.page.karyawan.divisi.delete');
        });

        Route::prefix('/jabatan')->group(function () {
            Route::get('/datatable', 'User\Master\KaryawanJabatanController@datatable')->name('user.page.karyawan.jabatan.datatable');
            Route::get('/select', 'User\Master\KaryawanJabatanController@select')->name('user.page.karyawan.jabatan.select');
            Route::post('/store', 'User\Master\KaryawanJabatanController@store')->name('user.page.karyawan.jabatan.store');
            Route::post('/update/{karyawanjabatanId}', 'User\Master\KaryawanJabatanController@update')->name('user.page.karyawan.jabatan.update');
            Route::post('/delete/{karyawanjabatanId}', 'User\Master\KaryawanJabatanController@delete')->name('user.page.karyawan.jabatan.delete');
        });

        Route::prefix('/akun')->group(function () {
            // Route::get('/', 'User\Master\AkunController@index')->middleware(['accesspermission:R'])->name('user.page.karyawan.index');
            Route::get('/select', 'User\Master\AkunController@select')->name('user.page.master.akun.select');
            Route::prefix('/aruskas')->group(function () {
                // Route::get('/', 'User\Master\AkunController@index')->middleware(['accesspermission:R'])->name('user.page.karyawan.index');
                Route::get('/select', 'User\Master\AkunAruskasController@select')->name('user.page.master.akun.aruskas.select');
            });
        });
        
    });
    
    // REMAP FOLDER
    Route::prefix('/karyawan')->middleware(['usergrouppermission'])->group(function () {
        // Route::get('/', 'User\KaryawanController@index')->name('user.page.karyawan.index');
        // Route::get('/select', 'User\KaryawanController@select')->name('user.page.karyawan.select');
        // Route::get('/create', 'User\KaryawanController@create')->name('user.page.karyawan.create');
        // Route::post('/store', 'User\KaryawanController@store')->name('user.page.karyawan.store');
        // Route::get('/datatable', 'User\KaryawanController@datatable')->name('user.page.karyawan.datatable');
        // Route::get('/edit/{karyawanmasakerjaId}', 'User\KaryawanController@edit')->name('user.page.karyawan.edit');
        // Route::post('/update/{karyawanmasakerjaId}', 'User\KaryawanController@update')->name('user.page.karyawan.update');
        // Route::post('/delete/{karyawanmasakerjaId}', 'User\KaryawanController@delete')->name('user.page.karyawan.delete');
        // Route::post('/resign/{karyawanId}', 'User\KaryawanController@resign')->name('user.page.karyawan.resign');
        Route::post('/lock-kalkulasi', 'User\KaryawanController@lockKalkulasi')->name('user.page.karyawan.lockkalkulasi');

        Route::prefix('/export')->group(function () {
            Route::get('/kalkulasi-pajak', 'User\KaryawanExportKalkulasiController@index')->name('user.page.karyawan-export-kalkulasi.index');
            Route::post('/kalkulasi-pajak', 'User\KaryawanExportKalkulasiController@export')->name('user.page.karyawan-export-kalkulasi.export');
            Route::get('/karyawan', 'User\KaryawanExportController@index')->name('user.page.karyawan-export.index');
        });

        Route::prefix('/import')->group(function () {
            Route::prefix('/profil')->group(function () {
                // Route::get('/', 'User\KaryawanImportProfilController@index')->name('user.page.karyawan-import-profil.index');
                // Route::post('/import', 'User\KaryawanImportProfilController@import')->name('user.page.karyawan.import');
            });

            Route::prefix('/tunjangan')->group(function () {
                Route::get('/', 'User\KaryawanImportTunjanganController@index')->name('user.page.karyawan-import-tunjangan.index');
                Route::post('/import', 'User\KaryawanImportTunjanganController@import')->name('user.page.karyawan-import-tunjangan.import');
                Route::get('/download-template', 'User\KaryawanImportTunjanganController@download_template')->name('user.page.karyawan-import-tunjangan.downloadtemplate');
            });

            Route::get('/tunjangan', 'User\KaryawanImportTunjanganController@index')->name('user.page.karyawan-import-tunjangan.index');
        });

        // Route::prefix('{karyawanmasakerjaId}/kalkulasi')->group(function () {
        //     Route::get('/', 'User\KaryawanKalkulasiController@index')->name('user.page.karyawan-kalkulasi.index');
        //     Route::get('/datatable', 'User\KaryawanKalkulasiController@datatable')->name('user.page.karyawan-kalkulasi.datatable');
        //     Route::get('/datatable_nonkaryawan', 'User\KaryawanKalkulasiController@datatable_nonkaryawan')->name('user.page.karyawan-kalkulasi.datatable_nonkaryawan');
        //     Route::get('/generate', 'User\KaryawanKalkulasiController@generate')->name('user.page.karyawan-kalkulasi.generate');
        //     Route::post('/lock', 'User\KaryawanKalkulasiController@lock')->name('user.page.karyawan-kalkulasi.lock');
        //     Route::post('/locknonkaryawan', 'User\KaryawanKalkulasiController@locknonkaryawan')->name('user.page.karyawan-kalkulasi.locknonkaryawan');
        //     Route::post('/locknonkaryawanupdate/{kalkulasiId}', 'User\KaryawanKalkulasiController@locknonkaryawanupdate')->name('user.page.karyawan-kalkulasi.locknonkaryawanupdate');
        //     Route::post('/locknonkaryawandelete/{kalkulasiId}', 'User\KaryawanKalkulasiController@locknonkaryawandelete')->name('user.page.karyawan-kalkulasi.locknonkaryawandelete');

        //     Route::prefix('/detail/{kalkulasiId}')->group(function () {
        //         Route::get('/', 'User\KaryawanKalkulasiDetailController@index')->name('user.page.karyawan-kalkulasi-detail.index');
        //         Route::post('/update', 'User\KaryawanKalkulasiDetailController@update')->name('user.page.karyawan-kalkulasi-detail.update');
        //     });
        // });

        

    });

    Route::prefix('/kehadiran')->middleware(['usergrouppermission'])->group(function () {
        Route::post('/edit', 'User\KehadiranController@editAttendance')->middleware(['accesspermission:U'])->name('user.kehadiran.edit');
        Route::get('/absensi-admin', 'User\KehadiranController@attendanceAdmin')->middleware(['accesspermission:R'])->name('user.kehadiran.list.admin');
        Route::get('/view/admin', 'User\KehadiranController@indexAdmin')->middleware(['accesspermission:R'])->name('user.kehadiran.view.admmin');
        Route::post('/export', 'User\KehadiranController@export')->middleware(['accesspermission:EXPORT'])->name('user.kehadiran.export');
        Route::post('/import-template', 'User\KehadiranController@importTemplate')->middleware(['accesspermission:IMPORT'])->name('user.kehadiran.import-template');
        Route::post('/generate-history', 'User\KehadiranController@generateHistory')->middleware(['accesspermission:IMPORT'])->name('user.kehadiran.generate-history');
        Route::post('/import-absen', 'User\KehadiranController@importAbsen')->middleware(['accesspermission:IMPORT'])->name('user.kehadiran.import-absen');
        Route::get('/import-history', 'User\KehadiranController@importHistory')->middleware(['accesspermission:IMPORT'])->name('user.kehadiran.import-history');
        Route::get('/import-handler', 'User\KehadiranController@importHandler')->middleware(['accesspermission:IMPORT'])->name('user.kehadiran.import-handler');
    });

    Route::prefix('/cuti')->middleware(['usergrouppermission'])->group(function () {
        Route::post('/pengajuan', 'User\CutiController@store')->middleware(['accesspermission:R'])->name('user.cuti.pengajuan');
        Route::get('/view/karyawan', 'User\CutiController@indexEmployee')->middleware(['accesspermission:R'])->name('user.cuti.view.karyawan');
        Route::get('/cuti-karyawan', 'User\CutiController@leaveEmployee')->middleware(['accesspermission:R'])->name('user.cuti.list.karyawan');
        Route::get('/sisa-cuti', 'User\CutiController@leaveEmployeeLeft')->middleware(['accesspermission:R'])->name('user.cuti.list.kuota');
        Route::get('/view/manager', 'User\CutiController@indexManager')->middleware(['accesspermission:R'])->name('user.cuti.view.manager');
        Route::get('/cuti-manager', 'User\CutiController@leaveManager')->middleware(['accesspermission:R'])->name('user.cuti.list.manager');
        Route::get('/view/admin', 'User\CutiController@indexAdmin')->middleware(['accesspermission:R'])->name('user.cuti.view.admin');
        Route::get('/cuti-admin', 'User\CutiController@leaveAdmin')->middleware(['accesspermission:R'])->name('user.cuti.list.admin');
        Route::post('/export-manager', 'User\CutiController@exportManager')->middleware(['accesspermission:EXPORT'])->name('user.cuti.manager.export');
        Route::post('/export-admin', 'User\CutiController@exportAdmin')->middleware(['accesspermission:EXPORT'])->name('user.cuti.admin.export');
        Route::post('/import-template', 'User\CutiController@importTemplate')->middleware(['accesspermission:IMPORT'])->name('user.cuti.import-template');
        Route::post('/import-cuti', 'User\CutiController@importCuti')->middleware(['accesspermission:IMPORT'])->name('user.cuti.import-cuti');
        Route::post('/generate-history', 'User\CutiController@generateHistory')->middleware(['accesspermission:IMPORT'])->name('user.cuti.generate-history');
        Route::get('/import-history', 'User\CutiController@importHistory')->middleware(['accesspermission:IMPORT'])->name('user.cuti.import-history');
        Route::get('/total-leave', 'User\CutiController@checkWeekend')->name('user.cuti.total-leave');
        Route::get('/import-handler', 'User\CutiController@importHandler')->middleware(['accesspermission:IMPORT'])->name('user.cuti.import-handler');
    });

    Route::prefix('/lembur')->middleware(['usergrouppermission'])->group(function () {
        Route::post('/pengajuan/create', 'User\LemburController@store')->middleware(['accesspermission:C'])->name('user.lembur.pengajuan.create');
        Route::post('/pengajuan/update/{lemburId}', 'User\LemburController@update')->name('user.lembur.pengajuan.update');
        Route::post('/pengajuan/approve/{lemburId}', 'User\LemburController@approve')->name('user.lembur.pengajuan.approve');
        Route::post('/pengajuan/cancel/{lemburId}', 'User\LemburController@cancel')->name('user.lembur.pengajuan.cancel');
        
        Route::get('/view/admin', 'User\LemburController@indexAdmin')->middleware(['accesspermission:R'])->name('user.lembur.view.admin');
        Route::get('/lembur-admin', 'User\LemburController@lemburAdmin')->middleware(['accesspermission:R'])->name('user.lembur.list.admin');
    });

    // Route::prefix('/npwp')->group(function () {
        // Route::get('/', 'User\NpwpController@index')->name('user.page.npwp.index');
        // Route::get('/create', 'User\NpwpController@create')->name('user.page.npwp.create');
        // Route::post('/store', 'User\NpwpController@store')->name('user.page.npwp.store');
        // Route::post('/update/{wajibpajakId}', 'User\NpwpController@update')->name('user.page.npwp.update');
        // Route::get('/datatable', 'User\NpwpController@datatable')->name('user.page.npwp.datatable');
        // Route::get('/activate', 'User\NpwpController@activate')->name('user.page.npwp.activate');
        // Route::post('/bayartagihan/{wajibpajakId}', 'User\NpwpController@bayartagihan')->middleware('usergrouppermission')->name('user.page.npwp.bayartagihan');
        // // Route::post('/lanjutlangganan/{wajibpajakId}', 'User\NpwpController@lanjutlangganan')->middleware('usergrouppermission')->name('user.page.npwp.lanjutlangganan');
        // Route::post('/lanjutlangganan/{wajibpajakId}', 'User\NpwpController@lanjutlangganan')->name('user.page.npwp.lanjutlangganan');
        // Route::post('/upgrade', 'User\NpwpController@upgrade')
        // // ->middleware('usergrouppermission')
        // ->name('user.page.npwp.upgrade');
    // });

    Route::prefix('/pengaturan')
    ->middleware('usergrouppermission')
    ->group(function () {
        Route::prefix('/tunjangan')->group(function () {
            Route::get('/', 'User\Pengaturan\PengaturanTunjanganController@index')->middleware(['accesspermission:R'])->name('user.page.pengaturan.tunjangan.index');
            Route::get('/datatable', 'User\Pengaturan\PengaturanTunjanganController@datatable')->middleware(['accesspermission:R'])->name('user.page.pengaturan.tunjangan.datatable');
            Route::get('/select-group', 'User\Pengaturan\PengaturanTunjanganController@select_group')->name('user.page.pengaturan.tunjangan.selectgroup');
            // Route::middleware(['actionpermission', 'quotapermission'])->post('/store', 'User\Pengaturan\PengaturanTunjanganController@store')->name('user.page.pengaturan.tunjangan.store');
            Route::post('/store', 'User\Pengaturan\PengaturanTunjanganController@store')->middleware(['accesspermission:C-Q,st_tunjangan_karyawan'])->name('user.page.pengaturan.tunjangan.store');
            // Route::middleware(['actionpermission'])->post('/update/{sttunjangankaryawanId}', 'User\Pengaturan\PengaturanTunjanganController@update')->name('user.page.pengaturan.tunjangan.update');
            Route::post('/update/{sttunjangankaryawanId}', 'User\Pengaturan\PengaturanTunjanganController@update')->middleware(['accesspermission:U'])->name('user.page.pengaturan.tunjangan.update');
            // Route::middleware(['actionpermission'])->post('/delete/{sttunjangankaryawanId}', 'User\Pengaturan\PengaturanTunjanganController@delete')->name('user.page.pengaturan.tunjangan.delete');
            Route::post('/delete/{sttunjangankaryawanId}', 'User\Pengaturan\PengaturanTunjanganController@delete')->middleware(['accesspermission:SD'])->name('user.page.pengaturan.tunjangan.delete');
            Route::get('/select', 'User\Pengaturan\PengaturanTunjanganController@select')->name('user.page.pengaturan.tunjangan.select');
            Route::get('/list-karyawan/{sttunjangankaryawanId}', 'User\Pengaturan\PengaturanTunjanganController@listkaryawan')->name('user.page.pengaturan.tunjangan.listkaryawan');
            Route::get('/list-divisijabatan/{sttunjangankaryawanId}', 'User\Pengaturan\PengaturanTunjanganController@listdivisijabatan')->name('user.page.pengaturan.tunjangan.listdivisijabatan');
            
            // Route::post('/update/karyawan/{sttunjangankaryawanId}', 'User\Pengaturan\PengaturanTunjanganController@updatekaryawan')->name('user.page.pengaturan.tunjangan.updatekaryawan');
            // Route::post('/delete/karyawan/{sttunjangankaryawanId}', 'User\Pengaturan\PengaturanTunjanganController@deletekaryawan')->name('user.page.pengaturan.tunjangan.deletekaryawan');

            Route::prefix('/group')->group(function () {
                Route::get('/datatable', 'User\Pengaturan\PengaturanGroupTunjanganController@datatable')->name('user.page.pengaturan.tunjangan.group.datatable');
                Route::get('/select', 'User\Pengaturan\PengaturanGroupTunjanganController@select')->name('user.page.pengaturan.tunjangan.group.select');
                Route::post('/store', 'User\Pengaturan\PengaturanGroupTunjanganController@store')->name('user.page.pengaturan.tunjangan.group.store');
                Route::post('/update/{stgrouptunjangankaryawanId}', 'User\Pengaturan\PengaturanGroupTunjanganController@update')->name('user.page.pengaturan.tunjangan.group.update');
                Route::post('/delete/{stgrouptunjangankaryawanId}', 'User\Pengaturan\PengaturanGroupTunjanganController@delete')->name('user.page.pengaturan.tunjangan.group.delete');
            });
        });

        
        Route::prefix('/potongan')->group(function () {
            Route::get('/', 'User\Pengaturan\PengaturanPotonganController@index')->middleware(['accesspermission:R'])->name('user.page.pengaturan.potongan.index');
            Route::get('/datatable', 'User\Pengaturan\PengaturanPotonganController@datatable')->middleware(['accesspermission:R'])->name('user.page.pengaturan.potongan.datatable');
            Route::get('/show/{stpotongankaryawanId}', 'User\Pengaturan\PengaturanPotonganController@show')->middleware(['accesspermission:R'])->name('user.page.pengaturan.potongan.show');
            Route::get('/select-group', 'User\Pengaturan\PengaturanPotonganController@select_group')->name('user.page.pengaturan.potongan.selectgroup');
            // Route::middleware(['actionpermission', 'quotapermission'])->post('/store', 'User\Pengaturan\PengaturanPotonganController@store')->name('user.page.pengaturan.potongan.store');
            Route::post('/store', 'User\Pengaturan\PengaturanPotonganController@store')->middleware(['accesspermission:C-Q,st_potongan_karyawan'])->name('user.page.pengaturan.potongan.store');
            // Route::middleware(['actionpermission'])->post('/update/{stpotongankaryawanId}', 'User\Pengaturan\PengaturanPotonganController@update')->name('user.page.pengaturan.potongan.update');
            Route::post('/update/{stpotongankaryawanId}', 'User\Pengaturan\PengaturanPotonganController@update')->middleware(['accesspermission:U'])->name('user.page.pengaturan.potongan.update');
            // Route::middleware(['actionpermission'])->post('/delete/{stpotongankaryawanId}', 'User\Pengaturan\PengaturanPotonganController@delete')->name('user.page.pengaturan.potongan.delete');
            Route::post('/delete/{stpotongankaryawanId}', 'User\Pengaturan\PengaturanPotonganController@delete')->middleware(['accesspermission:SD'])->name('user.page.pengaturan.potongan.delete');
            Route::get('/select', 'User\Pengaturan\PengaturanPotonganController@select')->name('user.page.pengaturan.potongan.select');
            Route::get('/list-karyawan/{stpotongankaryawanId}', 'User\Pengaturan\PengaturanPotonganController@listkaryawan')->name('user.page.pengaturan.potongan.listkaryawan');
            Route::get('/list-divisijabatan/{stpotongankaryawanId}', 'User\Pengaturan\PengaturanPotonganController@listdivisijabatan')->name('user.page.pengaturan.potongan.listdivisijabatan');

            Route::prefix('/group')->group(function () {
                Route::get('/datatable', 'User\Pengaturan\PengaturanGroupPotonganController@datatable')->name('user.page.pengaturan.potongan.group.datatable');
                Route::get('/select', 'User\Pengaturan\PengaturanGroupPotonganController@select')->name('user.page.pengaturan.potongan.group.select');
                Route::post('/store', 'User\Pengaturan\PengaturanGroupPotonganController@store')->name('user.page.pengaturan.potongan.group.store');
                Route::post('/update/{stgrouppotongankaryawanId}', 'User\Pengaturan\PengaturanGroupPotonganController@update')->name('user.page.pengaturan.potongan.group.update');
                Route::post('/delete/{stgrouppotongankaryawanId}', 'User\Pengaturan\PengaturanGroupPotonganController@delete')->name('user.page.pengaturan.potongan.group.delete');
            });
        });

        Route::prefix('/bpjs')->group(function () { 
            Route::get('/', 'User\Pengaturan\PengaturanBpjsController@index')->middleware(['accesspermission:R'])->name('user.page.pengaturan.bpjs.index');
            Route::post('/save', 'User\Pengaturan\PengaturanBpjsController@save')->middleware(['accesspermission:U'])->name('user.page.pengaturan.bpjs.save');
        });

        Route::prefix('/tapera')->group(function () { 
            Route::get('/', 'User\Pengaturan\PengaturanTaperaController@index')->middleware(['accesspermission:R'])->name('user.page.pengaturan.tapera.index');
            Route::post('/save', 'User\Pengaturan\PengaturanTaperaController@save')->middleware(['accesspermission:U'])->name('user.page.pengaturan.tapera.save');
        });

        Route::prefix('/lembur')->group(function () { 
            Route::get('/', 'User\Pengaturan\PengaturanLemburController@index')->middleware(['accesspermission:R'])->name('user.page.pengaturan.lembur.index');
            Route::post('/save', 'User\Pengaturan\PengaturanLemburController@save')->middleware(['accesspermission:U'])->name('user.page.pengaturan.lembur.save');
            Route::post('/savetax', 'User\Pengaturan\PengaturanLemburController@savetax')->middleware(['accesspermission:U'])->name('user.page.pengaturan.lembur.savetax');
        });

        Route::prefix('/cuti')->group(function () {
            Route::get('/', 'User\Pengaturan\PengaturanCutiController@index')->middleware(['accesspermission:R'])->name('user.page.pengaturan.cuti.index');
            Route::get('/datatable', 'User\Pengaturan\PengaturanCutiController@datatable')->middleware(['accesspermission:R'])->name('user.page.pengaturan.cuti.datatable');
            Route::get('/show', 'User\Pengaturan\PengaturanCutiController@show')->middleware(['accesspermission:R'])->name('user.page.pengaturan.cuti.show');
            Route::post('/save', 'User\Pengaturan\PengaturanCutiController@save')->middleware(['accesspermission:C-Q-U,st_leave'])->name('user.page.pengaturan.cuti.save');
            Route::delete('/delete/{id}', 'User\Pengaturan\PengaturanCutiController@destroy')->middleware(['accesspermission:SD'])->name('user.page.pengaturan.cuti.destroy');
            Route::get('/list-karyawan/{id}', 'User\Pengaturan\PengaturanCutiController@listkaryawan')->name('user.page.pengaturan.cuti.listkaryawan');
            Route::get('/list-divisijabatan/{id}', 'User\Pengaturan\PengaturanCutiController@listdivisijabatan')->name('user.page.pengaturan.cuti.listdivisijabatan');
        });

        Route::prefix('/libur')->group(function () {
            Route::get('/', 'User\Pengaturan\PengaturanLiburController@index')->middleware(['accesspermission:R'])->name('user.page.pengaturan.libur.index');
            Route::get('/datatable', 'User\Pengaturan\PengaturanLiburController@datatable')->middleware(['accesspermission:R'])->name('user.page.pengaturan.libur.datatable');
            Route::get('/show', 'User\Pengaturan\PengaturanLiburController@show')->middleware(['accesspermission:R'])->name('user.page.pengaturan.libur.show');
            Route::post('/save', 'User\Pengaturan\PengaturanLiburController@save')->middleware(['accesspermission:C'])->name('user.page.pengaturan.libur.save');
            Route::delete('/delete/{id}', 'User\Pengaturan\PengaturanLiburController@destroy')->middleware(['accesspermission:SD'])->name('user.page.pengaturan.libur.destroy');
            Route::get('/download-template', 'User\Pengaturan\PengaturanLiburController@generateTemplate')
            ->name('user.page.pengaturan.libur.download-template');
            Route::post('/import-libur', 'User\Pengaturan\PengaturanLiburController@importLibur')
            ->name('user.page.pengaturan.libur.importlibur');
            Route::get('/generate-history', 'User\Pengaturan\PengaturanLiburController@generateHistory')
            ->name('user.page.pengaturan.libur.generatehistory');
            Route::get('/import-history', 'User\Pengaturan\PengaturanLiburController@importHistory')
            ->name('user.page.pengaturan.libur.importhistory');

            Route::post('/import', 'User\Pengaturan\PengaturanLiburController@import')
            ->name('user.page.pengaturan.libur.import');
        });

        Route::prefix('/pengumuman')->group(function () {
            Route::get('/', 'User\Pengaturan\PengaturanPengumumanController@index')->middleware(['accesspermission:R'])->name('user.page.pengaturan.pengumuman.index');
            Route::get('/datatable', 'User\Pengaturan\PengaturanPengumumanController@datatable')->middleware(['accesspermission:R'])->name('user.page.pengaturan.pengumuman.datatable');
            Route::get('/show', 'User\Pengaturan\PengaturanPengumumanController@show')->middleware(['accesspermission:R'])->name('user.page.pengaturan.pengumuman.show');
            Route::post('/save', 'User\Pengaturan\PengaturanPengumumanController@save')->middleware(['accesspermission:C'])->name('user.page.pengaturan.pengumuman.save');
            Route::delete('/delete/{id}', 'User\Pengaturan\PengaturanPengumumanController@destroy')->middleware(['accesspermission:SD'])->name('user.page.pengaturan.pengumuman.destroy');
        });

        Route::prefix('/kehadiran')->group(function () {
            Route::get('/jadwal', 'User\Pengaturan\PengaturanKehadiranController@index')->middleware(['accesspermission:R'])->name('user.page.pengaturan.kehadiran.jadwal');
            Route::get('/datatable', 'User\Pengaturan\PengaturanKehadiranController@datatable')->middleware(['accesspermission:R'])->name('user.page.pengaturan.kehadiran.datatable');
            Route::get('/show', 'User\Pengaturan\PengaturanKehadiranController@show')->middleware(['accesspermission:R'])->name('user.page.pengaturan.kehadiran.show');
            Route::post('/save', 'User\Pengaturan\PengaturanKehadiranController@save')->middleware(['accesspermission:C'])->name('user.page.pengaturan.kehadiran.save');
            Route::delete('/delete/{id}', 'User\Pengaturan\PengaturanKehadiranController@destroy')->middleware(['accesspermission:SD'])->name('user.page.pengaturan.kehadiran.destroy');
            Route::get('/select', 'User\Pengaturan\PengaturanKehadiranController@select')->name('user.page.pengaturan.kehadiran.select');
        });

        Route::prefix('/group-akses')->group(function () {
            Route::get('/', 'User\Pengaturan\PengaturanGroupAksesController@index')->middleware(['accesspermission:R'])->name('user.page.pengaturan.groupakses.index');
            Route::get('/datatable', 'User\Pengaturan\PengaturanGroupAksesController@datatable')->middleware(['accesspermission:R'])->name('user.page.pengaturan.groupakses.datatable');
            Route::get('/select', 'User\Pengaturan\PengaturanGroupAksesController@select')->name('user.page.pengaturan.groupakses.select');
            Route::post('/store', 'User\Pengaturan\PengaturanGroupAksesController@store')->middleware(['accesspermission:C-Q,ms_user_group'])->name('user.page.pengaturan.groupakses.store');
            Route::post('/update/{usergroupId}', 'User\Pengaturan\PengaturanGroupAksesController@update')->middleware(['accesspermission:U'])->name('user.page.pengaturan.groupakses.update');
            Route::delete('/delete/{usergroupId}', 'User\Pengaturan\PengaturanGroupAksesController@destroy')->middleware(['accesspermission:SD'])->name('user.page.pengaturan.groupakses.destroy');
        });

        // Route::prefix('/group-anggota')->group(function () {
        //     Route::get('/', 'User\Pengaturan\PengaturanGroupAnggotaController@index')->name('user.page.pengaturan.groupanggota.index');
        //     Route::get('/datatable', 'User\Pengaturan\PengaturanGroupAnggotaController@datatable')->name('user.page.pengaturan.groupanggota.datatable');
        //     Route::post('/store', 'User\Pengaturan\PengaturanGroupAnggotaController@store')->name('user.page.pengaturan.groupanggota.store');
        //     Route::post('/store-karyawan', 'User\Pengaturan\PengaturanGroupAnggotaController@storekaryawan')->name('user.page.pengaturan.groupanggota.storekaryawan');
        //     Route::post('/get-karyawan', 'User\Pengaturan\PengaturanGroupAnggotaController@getkaryawan')->name('user.page.pengaturan.groupanggota.getkaryawan');
        //     Route::post('/update/{userId}', 'User\Pengaturan\PengaturanGroupAnggotaController@update')->name('user.page.pengaturan.groupanggota.update');
        //     Route::delete('/delete/{usergroupId}', 'User\Pengaturan\PengaturanGroupAnggotaController@destroy')->name('user.page.pengaturan.groupanggota.destroy');
        // });

        Route::prefix('/penggajian')->group(function () {
            Route::get('/', 'User\Pengaturan\PengaturanPenggajianController@index')->middleware(['accesspermission:R'])->name('user.page.pengaturan.penggajian.index');
            Route::get('/datatable', 'User\Pengaturan\PengaturanPenggajianController@datatable')->middleware(['accesspermission:R'])->name('user.page.pengaturan.penggajian.datatable');
            Route::get('/select', 'User\Pengaturan\PengaturanPenggajianController@select')->name('user.page.pengaturan.penggajian.select');
            Route::get('/list-karyawan/{stpenggajiankaryawanId}', 'User\Pengaturan\PengaturanPenggajianController@listkaryawan')->name('user.page.pengaturan.penggajian.listkaryawan');
            Route::post('/store', 'User\Pengaturan\PengaturanPenggajianController@store')->middleware(['accesspermission:C-Q,st_penggajian_karyawan'])->name('user.page.pengaturan.penggajian.store');
            Route::post('/update/{stpenggajiankaryawanId}', 'User\Pengaturan\PengaturanPenggajianController@update')->middleware(['accesspermission:U'])->name('user.page.pengaturan.penggajian.update');
            Route::post('/delete/{stpenggajiankaryawanId}', 'User\Pengaturan\PengaturanPenggajianController@delete')->middleware(['accesspermission:SD'])->name('user.page.pengaturan.penggajian.delete');

        });

        Route::prefix('/pajak')->group(function () {
            Route::get('/', 'User\Pengaturan\PengaturanPajakController@index')->middleware(['accesspermission:R'])->name('user.page.pengaturan.pajak.index');
            Route::post('/save', 'User\Pengaturan\PengaturanPajakPPh21Controller@save')->middleware(['accesspermission:U'])->name('user.page.pengaturan.pajakpph21.save');
        });
    });

    Route::prefix('pengaturan/profil-entitas')
    // ->middleware('usergrouppermission')
    ->group(function () {
        // Route::get('/', 'User\Pengaturan\PengaturanProfilEntitasController@index')->name('user.page.pengaturan.profilentitas.index');
        // Route::get('/datatable', 'User\Pengaturan\PengaturanProfilEntitasController@datatable')->name('user.page.pengaturan.profilentitas.datatable');
        Route::post('/save', 'User\Pengaturan\PengaturanProfilEntitasController@save')
        // ->middleware(['accesspermission:U'])
        ->name('user.page.pengaturan.profilentitas.save');
    });

    Route::prefix('/subscription')->group(function () {
        Route::get('/riwayat-subscription', 'User\Subscription\SubscriptionController@index')->name('user.page.subscription.riwayatsubscription.index');
        Route::get('/datatable', 'User\Subscription\SubscriptionController@datatable')->name('user.page.subscription.riwayatsubscription.datatable');
        Route::get('/riwayat-order', 'User\Subscription\OrderController@index')->name('user.page.subscription.riwayatorder.index');
        Route::get('/datatable-order', 'User\Subscription\OrderController@datatable')->name('user.page.subscription.riwayatorder.datatable');
        
        Route::prefix('/perpanjang')->group(function () {
            Route::get('/', 'User\Subscription\SubscriptionController@extend')->name('user.page.subscription.extend');
            Route::post('/', 'User\Subscription\SubscriptionController@doextend')->name('user.page.subscription.doextend');
            Route::post('/addon', 'User\Subscription\SubscriptionController@doextendaddon')->name('user.page.subscription.doextendaddon');
            Route::post('/confirmation', 'User\Subscription\SubscriptionController@doextendconfirmation')->name('user.page.subscription.doextendconfirmation');
        });

        Route::prefix('/upgrade')->group(function () {
            Route::get('/', 'User\Subscription\SubscriptionUpgradeController@upgrade')->name('user.page.subscription.upgrade');
            Route::post('/', 'User\Subscription\SubscriptionUpgradeController@doupgrade')->name('user.page.subscription.doupgrade');
            Route::post('/addon', 'User\Subscription\SubscriptionUpgradeController@doupgradeaddon')->name('user.page.subscription.doupgradeaddon');
            Route::post('/confirmation', 'User\Subscription\SubscriptionUpgradeController@doupgradeconfirmation')->name('user.page.subscription.doupgradeconfirmation');
        });
    });

    Route::prefix('/penggajian')->middleware(['usergrouppermission'])->group(function () {
        Route::post('/import-template', 'User\Penggajian\PenggajianController@importTemplate')->name('user.penggajian.import-template');
        Route::get('/', 'User\Penggajian\PenggajianController@index')->middleware(['accesspermission:R'])->name('user.page.penggajian.index');
        Route::get('/detail/{payrollId}', 'User\Penggajian\PenggajianController@detail')->middleware(['accesspermission:R'])->name('user.page.penggajian.detail');
        Route::get('/datatable', 'User\Penggajian\PenggajianController@datatable')->name('user.page.penggajian.datatable');
        Route::post('/calculate', 'User\Penggajian\PenggajianController@calculate')->middleware(['accesspermission:CALCULATE_PAYSLIP'])->name('user.page.penggajian.calculate');
        Route::post('/final', 'User\Penggajian\PenggajianController@final')->middleware(['accesspermission:CALCULATE_PAYSLIP'])->name('user.page.penggajian.final');
        Route::post('/calculatenew', 'User\Penggajian\PenggajianController@calculatenew')->middleware(['accesspermission:CALCULATE_PAYSLIP'])->name('user.page.penggajian.calculatenew');
        
        Route::get('/calculatecronjob', 'User\Penggajian\PenggajianController@calculatecronjob')->name('user.page.penggajian.calculatecronjob');
        Route::post('/confirmation', 'User\Penggajian\PenggajianController@confirmation')->middleware(['accesspermission:CONFIRM_PAYSLIP'])->name('user.page.penggajian.confirmation');
        Route::post('/paid', 'User\Penggajian\PenggajianController@paid')->middleware(['accesspermission:PAY_PAYSLIP'])->name('user.page.penggajian.paid');
        Route::post('/kirim', 'User\Penggajian\PenggajianController@kirim')->middleware(['accesspermission:SEND_PAYSLIP'])->name('user.page.penggajian.kirim');
        Route::get('/cetak/{payrollUuid}', 'User\Penggajian\PenggajianController@cetak')->middleware(['accesspermission:DOWNLOAD_PAYSLIP'])->name('user.page.penggajian.cetak');
        Route::get('/multi-cetak', 'User\Penggajian\PenggajianController@multicetak')->middleware(['accesspermission:DOWNLOAD_PAYSLIP'])->name('user.page.penggajian.multicetak');
        Route::post('/import-additional', 'User\Penggajian\PenggajianController@importAdditional')->middleware(['accesspermission:IMPORT_CUSTOM_ALL_DED'])->name('user.page.penggajian.import');
        Route::post('/generate-history', 'User\Penggajian\PenggajianController@generateHistory')->middleware(['accesspermission:IMPORT_CUSTOM_ALL_DED'])->name('user.penggajian.generate-history');
        Route::get('/import-history', 'User\Penggajian\PenggajianController@importHistory')->middleware(['accesspermission:IMPORT_CUSTOM_ALL_DED'])->name('user.page.penggajian.importhistory');

        Route::prefix('/nonkaryawan')->group(function() {
            Route::get('/', 'User\Penggajian\PenggajianNonKaryawanController@index')->middleware(['accesspermission:R'])->name('user.page.penggajian.nonkaryawan.index');
            Route::get('/detail/{karyawanId}', 'User\Penggajian\PenggajianNonKaryawanController@detail')->middleware(['accesspermission:R'])->name('user.page.penggajian.nonkaryawan.detail');
            Route::get('/datatable', 'User\Penggajian\PenggajianNonKaryawanController@datatable')->name('user.page.penggajian.nonkaryawan.datatable');
            Route::get('/datatable-detail', 'User\Penggajian\PenggajianNonKaryawanController@datatable_detail')->middleware(['accesspermission:R'])->name('user.page.penggajian.nonkaryawan.datatable_detail');
            Route::post('/calculate/{karyawanId}', 'User\Penggajian\PenggajianNonKaryawanController@calculate')->middleware(['accesspermission:CALCULATE_PAYSLIP'])->name('user.page.penggajian.nonkaryawan.calculate');
            Route::post('/confirmation', 'User\Penggajian\PenggajianNonKaryawanController@confirmation')->middleware(['accesspermission:CONFIRM_PAYSLIP'])->name('user.page.penggajian.nonkaryawan.confirmation');
            Route::post('/paid', 'User\Penggajian\PenggajianNonKaryawanController@paid')->middleware(['accesspermission:PAY_PAYSLIP'])->name('user.page.penggajian.nonkaryawan.paid');
            Route::post('/update/{payrollUuid}', 'User\Penggajian\PenggajianNonKaryawanController@update')->middleware(['accesspermission:U'])->name('user.page.penggajian.nonkaryawan.update');
            Route::post('/delete/{payrollUuid}', 'User\Penggajian\PenggajianNonKaryawanController@delete')->middleware(['accesspermission:SD'])->name('user.page.penggajian.nonkaryawan.delete');
            Route::get('/cetak/{payrollUuid}', 'User\Penggajian\PenggajianNonKaryawanController@cetak')->middleware(['accesspermission:DOWNLOAD_PAYSLIP'])->name('user.page.penggajian.nonkaryawan.cetak');
            Route::get('/cetakperiode', 'User\Penggajian\PenggajianNonKaryawanController@cetakperiode')->middleware(['accesspermission:DOWNLOAD_PAYSLIP'])->name('user.page.penggajian.nonkaryawan.cetakperiode');
            Route::get('/multicetak-periode', 'User\Penggajian\PenggajianNonKaryawanController@multicetakperiode')->middleware(['accesspermission:DOWNLOAD_PAYSLIP'])->name('user.page.penggajian.nonkaryawan.multicetakperiode');
            Route::post('/import-penggajian', 'User\Penggajian\PenggajianNonKaryawanController@importPenggajian')->middleware(['accesspermission:IMPORT_PAYSLIP'])->name('user.page.penggajian.nonkaryawan.importpenggajian');
            Route::get('/generate-history', 'User\Penggajian\PenggajianNonKaryawanController@generateHistory')->middleware(['accesspermission:IMPORT_PAYSLIP'])->name('user.page.penggajian.nonkaryawan.generatehistory');
            Route::get('/generate-template', 'User\Penggajian\PenggajianNonKaryawanController@generateTemplate')->middleware(['accesspermission:IMPORT_PAYSLIP'])->name('user.page.penggajian.nonkaryawan.generatetemplate');
            Route::get('/import-history', 'User\Penggajian\PenggajianNonKaryawanController@importHistory')->middleware(['accesspermission:IMPORT_PAYSLIP'])->name('user.page.penggajian.nonkaryawan.importhistory');
            
        });
    });

    Route::prefix('/pajak-penghasilan')->middleware(['usergrouppermission'])->group(function () {
        Route::prefix('/pph21')->group(function () {
            Route::prefix('/karyawan')->group(function () {
                Route::get('/', 'User\PajakPenghasilan\PPh21KaryawanController@index')->middleware(['accesspermission:R'])->name('user.page.pajakpenghasilan.pph21.karyawan.index');
                Route::get('/datatable', 'User\PajakPenghasilan\PPh21KaryawanController@datatable')->name('user.page.pajakpenghasilan.pph21.karyawan.datatable');
                Route::get('/cetak', 'User\PajakPenghasilan\PPh21KaryawanController@export')
                ->middleware(['accesspermission:EXPORT_EXCEL_ESPT|EXPORT_EXCEL_REKAP|EXPORT_BUKTIPOTONG'])
                ->name('user.page.pajakpenghasilan.pph21.karyawan.export');

                Route::post('/download-template', 'User\PajakPenghasilan\PPh21KaryawanController@importTemplate')
                // ->middleware(['accesspermission:IMPORT'])
                    ->name('user.page.pajakpenghasilan.pph21.karyawan.pembetulan.download-template');
                    
                Route::post('/import', 'User\PajakPenghasilan\PPh21KaryawanController@import')
                // ->middleware(['accesspermission:IMPORT-Q,ms_karyawan:KARYAWAN'])
                    ->name('user.page.pajakpenghasilan.pph21.karyawan.pembetulan.import');
                    
                Route::get('/import-history', 'User\PajakPenghasilan\PPh21KaryawanController@importHistory')
                    // ->middleware(['accesspermission:IMPORT'])
                    ->name('user.page.pajakpenghasilan.pph21.karyawan.pembetulan.import-history');

                Route::post('/generate-history', 'User\Master\KaryawanController@generateHistory')
                    // ->middleware(['accesspermission:IMPORT'])
                    ->name('user.page.pajakpenghasilan.pph21.karyawan.pembetulan.generate-history');
            });
            Route::prefix('/non-karyawan')->group(function () {
                Route::get('/', 'User\PajakPenghasilan\PPh21NonKaryawanController@index')->middleware(['accesspermission:R'])->name('user.page.pajakpenghasilan.pph21.nonkaryawan.index');
                Route::get('/datatable', 'User\PajakPenghasilan\PPh21NonKaryawanController@datatable')->name('user.page.pajakpenghasilan.pph21.nonkaryawan.datatable');
                Route::get('/cetak', 'User\PajakPenghasilan\PPh21NonKaryawanController@export')
                ->middleware(['accesspermission:EXPORT_EXCEL_ESPT|EXPORT_EXCEL_REKAP|EXPORT_BUKTIPOTONG'])
                ->name('user.page.pajakpenghasilan.pph21.nonkaryawan.export');
            });
        });
    });
    Route::prefix('/download')->group(function () {
        Route::get('/locale', 'User\Dashboard\DashboardController@downloadLocale')
        // ->middleware(['accesspermission:IMPORT'])
        ->name('user.download-locale');
    });

    Route::prefix('/akuntansi')->middleware(['usergrouppermission'])->group(function () {
        Route::prefix('/biaya')->group(function () {
            Route::get('/', 'User\Akuntansi\BiayaController@index')->middleware(['accesspermission:R'])->name('user.page.akuntansi.biaya.index');
            Route::get('/create', 'User\Akuntansi\BiayaController@create')->middleware(['accesspermission:C'])->name('user.page.akuntansi.biaya.create');
            Route::post('/store', 'User\Akuntansi\BiayaController@store')->middleware(['accesspermission:C-Q'])->name('user.page.akuntansi.biaya.store');
            Route::get('/datatable', 'User\Akuntansi\BiayaController@datatable')->middleware(['accesspermission:R'])->name('user.page.akuntansi.biaya.datatable');
            // Route::get('/edit/{biayaId}', 'User\Akuntansi\BiayaController@edit')->middleware(['accesspermission:U'])->name('user.page.akuntansi.biaya.edit');
            Route::post('/update/{biayaId}', 'User\Akuntansi\BiayaControllerController@update')->middleware(['accesspermission:U'])->name('user.page.akuntansi.biaya.update');
            Route::post('/delete/{biayaId}', 'User\Akuntansi\BiayaControllerController@delete')->middleware(['accesspermission:SD'])->name('user.page.akuntansi.biaya.delete');
        });

        Route::prefix('/jurnal-umum')->group(function () {
            Route::get('/', 'User\Akuntansi\JurnalUmumController@index')->middleware(['accesspermission:R'])->name('user.page.akuntansi.jurnalumum.index');
            Route::get('/create', 'User\Akuntansi\JurnalUmumController@create')->middleware(['accesspermission:C'])->name('user.page.akuntansi.jurnalumum.create');
            // Route::post('/store', 'User\Akuntansi\BiayaController@store')->middleware(['accesspermission:C-Q'])->name('user.page.akuntansi.biaya.store');
            Route::get('/datatable', 'User\Akuntansi\JurnalUmumController@datatable')->middleware(['accesspermission:R'])->name('user.page.akuntansi.jurnalumum.datatable');
            // // Route::get('/edit/{biayaId}', 'User\Akuntansi\BiayaController@edit')->middleware(['accesspermission:U'])->name('user.page.akuntansi.biaya.edit');
            // Route::post('/update/{biayaId}', 'User\Akuntansi\BiayaControllerController@update')->middleware(['accesspermission:U'])->name('user.page.akuntansi.biaya.update');
            // Route::post('/delete/{biayaId}', 'User\Akuntansi\BiayaControllerController@delete')->middleware(['accesspermission:SD'])->name('user.page.akuntansi.biaya.delete');
        });
    });

    Route::post('/kirim-email-verifikasi', 'User\Info\InfoController@sendemailverification')->name('user.sendemailverification');
});
Route::prefix('/master')
// ->middleware('aksesuser')
->group(function () {
    Route::get('/wajib-pajak/list', 'Master\WajibPajakController@list')->name('master.wajibpajak.list');
    Route::get('/kota/select', 'Master\RegencyController@select')->name('master.kota.select');
    Route::get('/provinsi/select', 'Master\ProvinceController@select')->name('master.provinsi.select');
    Route::get('/negara/select', 'Master\CountryController@select')->name('master.negara.select');
    Route::get('/klu/select', 'Master\KluController@select')->name('master.klu.select');
    Route::get('/ptkp/select', 'Master\PtkpController@select')->name('master.ptkp.select');
    
    Route::get('/objek-category/select', 'Master\ObjekPajakController@selectCategory')->name('master.objekcategory.select');
    Route::get('/objek-pajak/select', 'Master\ObjekPajakController@select')->name('master.objekpajak.select');
    Route::get('/bank/select', 'Master\BankController@select')->name('master.bank.select');
});

// Route:
// /

Route::prefix('/login')->middleware(['isloggedin'])->group(function () {
    Route::get('/', function() {
        $data = [
            'title' => 'Rekkaa - Halaman Login'
        ];
        return view('auth/login', $data);
    });
    Route::post('/', 'Auth\LoginController@login')->name('dologin');
});

Route::prefix('/register')->middleware(['isloggedin'])->group(function () {
    // Route::get('/', function() {
    //     $data = [
    //         'title' => 'Rekkaa - Halaman Registrasi'
    //     ];
    //     return view('auth/register', $data);
    // });
    // if(env('APP_ENV') != 'productionxx') { // for internal used for now.
        Route::get('/', 'Auth\RegisterController@index')->name('register');
        Route::post('/', 'Auth\RegisterController@register')->name('doregister');

        Route::post('/register-addon', 'Auth\RegisterController@registeraddon')->name('doregisteraddon');
        Route::post('/register-confirmation', 'Auth\RegisterController@registerconfirmation')->name('doregisterconfirmation');
    // }

    // for internal only for now.
    Route::get('/free', 'Auth\RegisterController@indexsinglepage')->name('registerfree');
    Route::post('/calculator', 'Auth\RegisterController@registercalculator')->name('doregistercalculator');

});

Route::prefix('/lupa-password')->middleware(['isloggedin'])->group(function () {
    Route::get('/', function() {
        $data = [
            'title' => 'Rekkaa - Halaman Lupa Password'
        ];
        return view('auth/lupa-password', $data);
    });
    Route::post('/', 'Auth\ForgotPasswordController@forgot')->name('doforgot');
});

Route::prefix('/reset-password')->middleware(['isloggedin'])->group(function () {
    Route::get('/', 'Auth\ResetPasswordController@index')->name('reset.page');
    Route::post('/', 'Auth\ResetPasswordController@reset')->name('doreset');
});

Route::get('verifikasi', 'Auth\VerificationController@index');
Route::get('logout', 'Auth\LoginController@logout');


// Karyawan Account
Route::prefix('/karyawan')->group(function () {
    Route::get('/login', function() {
        $data = [
            'title' => 'Rekkaa - Halaman Login Karyawan'
        ];
        return view('karyawan/auth/login', $data);
    })->middleware(['isloggedinkaryawan']);
    Route::post('/login', 'Karyawan\Auth\LoginController@login')->name('karyawan.dologin')->middleware(['isloggedinkaryawan']);
    Route::post('/getaccess', 'Karyawan\Auth\LoginController@getAccess')->name('karyawan.getaccess')->middleware(['isloggedinkaryawan']);
    Route::get('logout', 'Karyawan\Auth\LoginController@logout')->middleware('akseskaryawan');

    
    Route::prefix('/lupa-password')->middleware(['isloggedinkaryawan'])->group(function () {
        Route::get('/', 'Karyawan\Auth\ForgotPasswordController@index')->name('karyawan.page.reset');
        Route::post('/', 'Karyawan\Auth\ForgotPasswordController@forgot')->name('karyawan.doforgot');
    });

    Route::prefix('/reset-password')->middleware(['isloggedinkaryawan'])->group(function () {
        Route::get('/', 'Karyawan\Auth\ResetPasswordController@index')->name('karyawan.page.reset');
        Route::post('/', 'Karyawan\Auth\ResetPasswordController@reset')->name('karyawan.doreset');
    });

    Route::prefix('/')->middleware('akseskaryawan')->group(function () {
        Route::get('/beranda', 'Karyawan\Dashboard\DashboardController@index')->name('karyawan.page.beranda');
        Route::prefix('/profil')->group(function () {
            Route::get('/', 'Karyawan\Profil\ProfilController@index')->name('karyawan.page.profil');
            Route::get('/password', 'Karyawan\Profil\PasswordController@index')->name('karyawan.page.password.index');
            Route::post('/password', 'Karyawan\Profil\PasswordController@update')->name('karyawan.page.password.update');
        });
        
        Route::prefix('/kehadiran')->group(function () {
            Route::post('/check-in', 'Karyawan\KehadiranController@capturePhoto')->name('karyawan.kehadiran.check-in');
            Route::post('/edit', 'Karyawan\KehadiranController@editAttendance')->name('karyawan.kehadiran.edit');
            Route::get('/absensi', 'Karyawan\KehadiranController@showCamera')->name('karyawan.kehadiran.absensi');
            Route::get('/detail-karyawan/{inputDate}/{karyawanId}', 'Karyawan\KehadiranController@checkKaryawan')->name('karyawan.kehadiran.detail.karyawan');
            Route::get('/check-karyawan/{userId}', 'Karyawan\KehadiranController@getKaryawan')->name('karyawan.kehadiran.check.karyawan');
            Route::get('/absensi-karyawan', 'Karyawan\KehadiranController@attendanceEmployee')->name('karyawan.kehadiran.list.karyawan');
            Route::get('/list-jadwal', 'Karyawan\KehadiranController@listJadwal')->name('karyawan.kehadiran.list.jadwal');
            Route::get('/view/karyawan', 'Karyawan\KehadiranController@indexEmployee')->name('karyawan.kehadiran.view.karyawan');
            Route::get('/absensi-manager', 'Karyawan\KehadiranController@attendanceManager')->name('karyawan.kehadiran.list.manager');
            Route::get('/view/manager', 'Karyawan\KehadiranController@indexManager')->name('karyawan.kehadiran.view.manager');
            Route::get('/check-date', 'Karyawan\KehadiranController@checkIncludeDate')->name('karyawan.kehadiran.check.date');
            Route::get('/list-jadwal', 'Karyawan\KehadiranController@listJadwal')->name('karyawan.kehadiran.list-jadwal');
        });

        Route::prefix('/pengumuman')->group(function () {
            Route::get('/list-pengumuman', 'Karyawan\PengumumanController@datatable')->name('karyawan.pengumuman.list-pengumuman');
            Route::post('/update-pengumuman', 'Karyawan\PengumumanController@update')->name('karyawan.pengumuman.update-pengumuman');
        });
    
        Route::prefix('/cuti')->group(function () {
            Route::post('/pengajuan', 'Karyawan\CutiController@store')->name('karyawan.cuti.pengajuan');
            Route::get('/view/karyawan', 'Karyawan\CutiController@indexEmployee')->name('karyawan.cuti.view.karyawan');
            Route::get('/cuti-karyawan', 'Karyawan\CutiController@leaveEmployee')->name('karyawan.cuti.list.karyawan');
            Route::get('/sisa-cuti', 'Karyawan\CutiController@leaveEmployeeLeft')->name('karyawan.cuti.list.kuota');
            Route::get('/view/manager', 'Karyawan\CutiController@indexManager')->name('karyawan.cuti.view.manager');
            Route::get('/cuti-manager', 'Karyawan\CutiController@leaveManager')->name('karyawan.cuti.list.manager');
            Route::post('/export-manager', 'Karyawan\CutiController@exportManager')->name('karyawan.cuti.manager.export');
            Route::get('/select-karyawan', 'Karyawan\CutiController@selectEmployee')->name('karyawan.cuti.karyawan.select');
            Route::get('/total-leave', 'Karyawan\CutiController@checkWeekend')->name('karyawan.cuti.total-leave');
        });

        Route::prefix('/lembur')->group(function () {
            Route::post('/pengajuan/create', 'Karyawan\LemburController@store')->name('karyawan.lembur.pengajuan.create');
            Route::post('/pengajuan/update/{lemburId}', 'Karyawan\LemburController@update')->name('karyawan.lembur.pengajuan.update');
            Route::post('/pengajuan/cancel/{lemburId}', 'Karyawan\LemburController@cancel')->name('karyawan.lembur.pengajuan.cancel');


            Route::get('/view/karyawan', 'Karyawan\LemburController@indexEmployee')->name('karyawan.lembur.view.karyawan');
            Route::get('/lembur-karyawan', 'Karyawan\LemburController@overtimeEmployee')->name('karyawan.lembur.list.karyawan');
            // Route::get('/sisa-cuti', 'Karyawan\CutiController@leaveEmployeeLeft')->name('karyawan.cuti.list.kuota');
            
            Route::post('/pengajuan/createmanager', 'Karyawan\LemburController@storemanager')->name('karyawan.lembur.pengajuan.createmanager');
            Route::post('/pengajuan/updatemanager/{lemburId}', 'Karyawan\LemburController@updatemanager')->name('karyawan.lembur.pengajuan.updatemanager');
            Route::post('/pengajuan/cancelmanager/{lemburId}', 'Karyawan\LemburController@cancelmanager')->name('karyawan.lembur.pengajuan.cancelmanager');
            Route::post('/pengajuan/rejectmanager/{lemburId}', 'Karyawan\LemburController@rejectmanager')->name('karyawan.lembur.pengajuan.rejectmanager');
            
            Route::post('/pengajuan/approvemanager/{lemburId}', 'Karyawan\LemburController@approvemanager')->name('karyawan.lembur.pengajuan.approvemanager');
            Route::get('/view/manager', 'Karyawan\LemburController@indexManager')->name('karyawan.lembur.view.manager');
            Route::get('/lembur-manager', 'Karyawan\LemburController@overtimeManager')->name('karyawan.lembur.list.manager');
            Route::post('/export-manager', 'Karyawan\LemburController@exportManager')->name('karyawan.lembur.manager.export');
            Route::get('/select-karyawan', 'Karyawan\LemburController@selectEmployee')->name('karyawan.lembur.karyawan.select');
        });

        Route::prefix('/gaji')->group(function () {
            Route::get('/view/karyawan', 'Karyawan\GajiController@index')->name('karyawan.gaji.view.karyawan');
            Route::get('/detail/{payrollId}', 'Karyawan\GajiController@detail')->name('karyawan.gaji.view.karyawan.detail');
            Route::get('/datatable', 'Karyawan\GajiController@datatable')->name('karyawan.gaji.view.karyawan.datatable');
            Route::get('/cetak/{payrollUuid}', 'Karyawan\GajiController@cetak')->name('karyawan.gaji.view.karyawan.cetak');
        });
    });
});
