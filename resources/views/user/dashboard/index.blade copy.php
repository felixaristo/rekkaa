<!-- @if(!$user->user_email_verified_at)
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-warning">Verifikasi Akun</h5>
                        <p class="mb-4">
                        Verifikasi Akun anda untuk mendapatkan fitur yang lebih lengkap.
                        </p>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img
                        src="{{asset('assets/img/illustrations/girl-doing-yoga-light.png')}}"
                        height="120"
                        alt="View Badge User"
                        data-app-dark-img="{{asset('assets/img/illustrations/girl-doing-yoga-light.png')}}"
                        data-app-light-img="{{asset('assets/img/illustrations/girl-doing-yoga-light.png')}}"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif -->

<!-- <div class="row" style="">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    @if(!isset(session()->get('wajibpajak_current')['wajibpajak_id']))
                    <div class="card-body">
                        <h5 class="card-title text-warning">Masukkan NPWP</h5>
                        <p class="mb-4">
                        Masukkan NPWP untuk menggunakan fitur kami.
                        </p>

                        <a href="{{url('user/npwp')}}" class="btn btn-sm btn-outline-warning rekkaa-page-link">Buat NPWP</a>
                    </div>
                    @else
                    <div class="card-body">
                        <h5 class="card-title text-warning">Tipe Akun</h5>
                        <p class="mb-4">
                        Upgrade Akun anda untuk menggunakan fitur yang lebih lengkap.
                        </p>

                        <a href="#" class="btn btn-sm btn-outline-danger" id="btn-upgradeakun"><i class="bx bx-credit-card"></i> Upgrade Akun</a>
                    </div>
                    @endif
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img
                        src="{{asset('assets/img/illustrations/profile-rekkaa.jpeg')}}"
                        height="140"
                        alt="View Badge User"
                        data-app-dark-img="{{asset('assets/img/illustrations/profile-rekkaa.jpeg')}}"
                        data-app-light-img="{{asset('assets/img/illustrations/profile-rekkaa.jpeg')}}"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="card-header m-2">
                <div class="row">
                    <div class="col-sm-6">
                        <h5>Absensi Hari Ini</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-sun"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0" id="total_attendance">0</h4>
                            </div>
                            <p class="mb-1">Masuk</p>
                            <!-- <p class="mb-0">
                            <span class="fw-medium me-1">+18.2%</span>
                            <small class="text-muted">than last week</small>
                            </p> -->
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <div class="card card-border-shadow-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-time"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0" id="total_attendance_late">0</h4>
                            </div>
                            <p class="mb-1">Terlambat</p>
                            <!-- <p class="mb-0">
                            <span class="fw-medium me-1">-8.7%</span>
                            <small class="text-muted">than last week</small>
                            </p> -->
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <div class="card card-border-shadow-danger h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-error"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0" id="total_tanpa_keterangan">0</h4>
                            </div>
                            <p class="mb-1">Tanpa Keterangan</p>
                            <!-- <p class="mb-0">
                            <span class="fw-medium me-1">+4.3%</span>
                            <small class="text-muted">than last week</small>
                            </p> -->
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <div class="card card-border-shadow-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-calendar"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0" id="total_leave">0</h4>
                            </div>
                            <p class="mb-1">Cuti</p>
                            <!-- <p class="mb-0">
                            <span class="fw-medium me-1">-2.5%</span>
                            <small class="text-muted">than last week</small>
                            </p> -->
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-4 order-1 card-custom-hg" style="">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body" style="min-height: 250px;">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar rounded middle-box bg-warning text-white">
                                <i class="bx bx-user"></i>
                            </div>
                        </div>
                        <span>Total Pegawai</span>
                        <hr>
                        <h5 class="mb-1 d-flex justify-content-between">Tetap : <span id="total_karyawan_tetap">0</span></h5>
                        <h5 class="mb-1 d-flex justify-content-between">Kontrak : <span id="total_karyawan_kontrak">0</span></h5>
                        <h5 class="mb-1 d-flex justify-content-between">Percobaan : <span id="total_karyawan_percobaan">0</span></h5>
                        <h5 class="mb-1 d-flex justify-content-between">Bukan Karyawan : <span id="total_karyawan_bukan">0</span></h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body" style="min-height: 250px;">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar rounded middle-box bg-warning text-white">
                                <i class="bx bx-id-card"></i>
                            </div>
                        </div>
                        <span>Total Gaji Bersih Bulan Lalu</span>
                        <hr>
                        <h5 class="card-title text-nowrap mb-1" id="total_salary_bulan_kemarin">0</h5>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-6 mb-4">
                <div class="card">
                <div class="card-body" style="min-height: 250px;">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar rounded middle-box bg-warning text-white">
                            <i class="bx bx-wallet-alt"></i>
                        </div>
                    </div>
                        <span>Total Pph21 Bulan Lalu</span>
                        <hr>
                        <h5 class="card-title text-nowrap mb-1" id="total_pajak_bulan_ini">0</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar rounded middle-box bg-warning text-white">
                                <i class="bx bx-chart"></i>
                            </div>
                            <h5>Pengeluaran Tahun Berjalan</h5>
                        </div>
                        <div id="chart_tahun_berjalan"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="modalSubcription" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Subscription</h5>
                    <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body py-5">
                    <div class="row subscription-box">
                        
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="70"></th>
                                    <?php 
                                    // $parent_sub = [];
                                    foreach($subscription as $sc) :
                                        // array_push($parent_sub[$sc->subscriptionpermission->])
                                    ?>
                                    <td class="text-bold text-center" style="vertical-align: top;" width="70">
                                        {{$sc->subscription_title}}
                                        <br>
                                        @if($sc->subscription_type != 'PLATINUM' && $sc->subscription_type != 'FREE')
                                        <span class="card-old-subscription text-danger">Rp. {{($sc->subscription_type == 'PLATINUM') ? '-' : number_format($sc->subscription_priceold, 0, ',', '.')}} / bln</span>
                                        <br>
                                        @endif
                                        <b>Rp. {{($sc->subscription_type == 'PLATINUM') ? '-' : number_format($sc->subscription_price, 0, ',', '.')}} / bln</b>
                                        <br>
                                        {!!$sc->subscription_default == '1' ? '<b style="font-size:12px" class="text-warning">Rekomendasi</b>' : ''!!}
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php 
                                $parent = [];
                                $child = [];
                                foreach($permission as $pm) : 
                                    if(!in_array($pm->permission_parent, $parent)) :
                                        array_push($parent, $pm->permission_parent); ?>
                                <tr>
                                    <th colspan="{{count($subscription) + 1}}" style="vertical-align: middle">{{$pm->permission_parent}}</th>
                                </tr>
                                    <?php endif; 
                                    if(!in_array($pm->permission_child, $child)) :
                                        array_push($child, $pm->permission_child);
                                    ?>
                                <tr>
                                    <th colspan="{{count($subscription) + 1}}" style="vertical-align: middle">{{$pm->permission_child}}</th>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td width="70" style="padding-left: 30px; vertical-align: middle">{{$pm->permission_code_name}}</td>
                                    <?php 
                                    $span = '<span class="bx bx-x text-danger fs-3 text-bold"></span>';
                                    foreach($subscription as $sc) : 
                                        
                                        // foreach($sc->subscriptionpermission as $scp) : 
                                        //     if($scp->ms_permission_code == $pm->permission_code) {
                                        //         $span = ($scp->subscriptionpermission_text) ? $scp->subscriptionpermission_text : '<span class="bx bx-check text-success fs-3 text-bold"></span> ';
                                        //         break;
                                        //     }
                                        // endforeach;
                                        ?>
                                    <td width="70" class="text-center" style="vertical-align: middle">{!!$span!!}</td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach ?>
                                <tr>
                                    <td width="70" style="vertical-align: middle">Periode Pembayaran</td>
                                    @foreach($subscription as $sc) : 
                                    <td width="70" class="text-center">
                                        @if($sc->subscription_type == 'PLATINUM')
                                        <div class="card-choose text-center">
                                            <a href="#" class="btn btn-sm btn-warning"><i class='bx bx-phone'></i> Hubungi Kami</a>
                                        </div>
                                        @else
                                        <div class="card-choose text-center">
                                            <div class="row mb-3 text-center periodepembayaran">
                                                <div class="col-sm-12">
                                                    <select name="wajibpajak_periodepembayaran" required style="width: 120px;" class="form-control wajibpajak_periodepembayaran" data-placeholder="-:Pilih Data:-">
                                                        <option value="MONTHLY" selected>1 Bulan Sekali (Diskon {{$sc->subscription_monthlydiscount}} %)</option>
                                                        <option value="QUARTELY">4 Bulan Sekali (Diskon {{$sc->subscription_quarterlydiscount}} %)</option>
                                                        <option value="SEMI_ANNUAL">6 Bulan Sekali (Diskon {{$sc->subscription_semiannualdiscount}} %)</option>
                                                        <option value="YEARLY">12 Bulan Sekali (Diskon {{$sc->subscription_yearlydiscount}} %)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <a href="#" class="btn btn-sm {{$sc->subscription_default == '1' ? 'btn-warning' : 'btn-secondary'}} mb-1 btn-choose-packet" data-json="{{base64_encode(json_encode($sc))}}" id="subscription-modal-btn"><i class='bx bx-credit-card'></i> Pilih Paket</a>
                                        </div>
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    @foreach($subscription as $sc)
                        <!-- <div class="col-sm-4 mb-5">
                            <div class="card shadow-none bg-transparent border {{$sc->subscription_default == '1' ? 'border-warning' : 'border-secondary'}} mb-3">
                                <div class="card-body">
                                    <div class="card-tag">
                                        <i class="bx bx-purchase-tag {{$sc->subscription_default == '1' ? 'text-warning' : 'text-secondary'}}"></i>
                                    </div>
                                    <h5 class="card-title text-center {{$sc->subscription_default == '1' ? 'border-warning' : 'border-secondary'}}">
                                        <span class="card-title-subscription mb-1 {{$sc->subscription_default == '1' ? 'text-warning' : 'text-secondary'}}">{{$sc->subscription_title}}</span>
                                        <br>
                                        @if($sc->subscription_type != 'PLATINUM' && $sc->subscription_type != 'FREE')
                                        <span class="card-old-subscription text-danger">Rp. {{($sc->subscription_type == 'PLATINUM') ? '-' : number_format($sc->subscription_priceold, 0, ',', '.')}} / bulan</span>
                                        <br>
                                        @endif
                                        <b>Rp. {{($sc->subscription_type == 'PLATINUM') ? '-' : number_format($sc->subscription_price, 0, ',', '.')}} / Bln</b>
                                        <br>
                                        {!!$sc->subscription_default == '1' ? '<b style="font-size:12px" class="text-warning">Rekomendasi</b>' : ''!!}
                                    </h5>
                                    <div class="card-text card-subscription-content mb-3" style="min-height: 200px;">{!!$sc->subscription_description!!}</div>
                                    @if($sc->subscription_type == 'PLATINUM')
                                    <div class="card-choose text-center">
                                        <a href="#" class="btn btn-sm btn-warning"><i class='bx bx-phone'></i> Hubungi Kami</a>
                                    </div>
                                    @else
                                    <div class="card-choose text-center">
                                        <div class="row mb-3 text-center periodepembayaran px-5">
                                            <label class="col-sm-12 nodot-label lbl-req text-bold" for="wajibpajak_periodepembayaran">Periode Pembayaran</label>
                                            <div class="col-sm-12">
                                                <select name="wajibpajak_periodepembayaran" required style="width: 100%;" class="form-control wajibpajak_periodepembayaran" data-placeholder="-:Pilih Data:-">
                                                    <option value="MONTHLY" selected>1 Bulan Sekali (Diskon {{$sc->subscription_monthlydiscount}} %)</option>
                                                    <option value="QUARTELY">4 Bulan Sekali (Diskon {{$sc->subscription_quarterlydiscount}} %)</option>
                                                    <option value="SEMI_ANNUAL">6 Bulan Sekali (Diskon {{$sc->subscription_semiannualdiscount}} %)</option>
                                                    <option value="YEARLY">12 Bulan Sekali (Diskon {{$sc->subscription_yearlydiscount}} %)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <a href="#" class="btn btn-sm {{$sc->subscription_default == '1' ? 'btn-warning' : 'btn-secondary'}} mb-1 btn-choose-packet" data-json="{{base64_encode(json_encode($sc))}}" id="subscription-modal-btn"><i class='bx bx-credit-card'></i> Pilih Paket</a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div> -->
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            // upgrade akun
            $(".wajibpajak_periodepembayaran").select2({
                dropdownParent: $("#modalSubcription .modal-body"),
            });
            $("#btn-upgradeakun").click(function(e) {
                e.preventDefault();
                $("#modalSubcription").modal("show");

            });
            $(".btn-choose-packet").click(function(e) {
                e.preventDefault();

                let data = $(this).attr('data-json');
                let parseData = JSON.parse(atob(data));
                let subscription_id = parseData.subscription_id;
                let wajibpajak_periodepembayaran = $(this).parent('.card-choose').find('.periodepembayaran').find('.wajibpajak_periodepembayaran').val();
                console.log('wajibpajak_periodepembayaran', wajibpajak_periodepembayaran)
                console.log('subscription_id', subscription_id)
                if(!wajibpajak_periodepembayaran || !subscription_id) {
                    Swal.fire({
                        html: 'Silahkan pilih paket!',
                        confirmButtonText: "Ok",
                        showCancelButton: false,
                        icon: 'error'
                    })
                    return false;
                }
                Swal.fire({
                    html: 'Apakah anda ingin mengupgrade akun menjadi <b>'+ parseData.subscription_title +'</b>?',
                    icon: 'question',
                    preConfirm: () => {
                        Swal.showLoading();
                        // tblPengaturanTunjangan.row(row).remove();
                        // return true;
                        return fetch(`{{url('/user/npwp/upgrade')}}`, {
                            method: 'POST',
                            body: new URLSearchParams($.param({subscription_id: subscription_id, wajibpajak_periodepembayaran: wajibpajak_periodepembayaran, _token: $("meta[name=csrf-token]").attr('content')}))
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(res => {
                                    throw new Error(res);
                                })
                            }
                            return response.json()
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        })
                    },
                    allowOutsideClick: () => false
                }).then((result) => {
                    console.log('result', result)
                    result = result.value;
                    if(result == undefined) {
                        return false;
                    }
                    
                    if (!result.success) {

                        Swal.fire({
                            title: result.message,
                            confirmButtonText: "Ok",
                            type: 'error'
                        })
                        return false;
                    }

                    toastr.success(result.message);
                    if(result.data.invoice_url) {
						window.open(result.data.invoice_url, '_blank');
					}
                });
            })
            // end upgrade akun
            $.ajax({
                method: 'get',
                url: "{{route('user.beranda.data')}}",
                success: function(res) {
                    console.log(res);
                    if(!res.success || res.data == null) {
                        return false;
                    }

                    let total_karyawan_tetap = (res.data['total_karyawan_tetap']) ? res.data['total_karyawan_tetap'] : 0;
                    let total_karyawan_kontrak = (res.data['total_karyawan_kontrak']) ? res.data['total_karyawan_kontrak'] : 0;
                    let total_karyawan_bukan = (res.data['total_karyawan_bukan']) ? res.data['total_karyawan_bukan'] : 0;
                    let total_karyawan_percobaan = (res.data['total_karyawan_percobaan']) ? res.data['total_karyawan_percobaan'] : 0;
                    let total_salary = (res.data['total_salary_periode_terakhir']) ? parseInt(res.data['total_salary_periode_terakhir']) : 0;
                    // let total_gajipokok = (res.data['total_gajipokok']) ? res.data['total_gajipokok'] : 0;
                    let total_pajak_periode_terakhir = (res.data['total_pajak_periode_terakhir']) ? parseInt(res.data['total_pajak_periode_terakhir']) : 0;
                    let total_salary_bulan_kemarin = total_salary
                    let total_leave = (res.data['total_leave']) ? res.data['total_leave'] : 0
                    let total_attendance = (res.data['total_attendance']) ? res.data['total_attendance'] : 0
                    let total_attendance_late = (res.data['total_attendance_late']) ? res.data['total_attendance_late'] : 0
                    let total_karyawan_onduty = (res.data['total_karyawan_onduty']) ? res.data['total_karyawan_onduty'] : 0
                    let total_tanpa_keterangan = total_karyawan_onduty - total_attendance - total_leave

                    console.log('total_salary', total_salary)
                    console.log('total_pajak_periode_terakhir', total_pajak_periode_terakhir)
                    console.log('total_salary_bulan_kemarin', total_salary_bulan_kemarin)
                    $("#total_karyawan_tetap").html(total_karyawan_tetap);
                    $("#total_karyawan_kontrak").html(total_karyawan_kontrak);
                    $("#total_leave").html(total_leave);
                    $("#total_attendance").html(total_attendance);
                    $("#total_attendance_late").html(total_attendance_late);
                    $("#total_tanpa_keterangan").html(total_tanpa_keterangan);
                    $("#total_karyawan_bukan").html(total_karyawan_bukan);
                    $("#total_karyawan_percobaan").html(total_karyawan_percobaan);
                    $("#total_salary_bulan_kemarin").html('Rp. '+formatCurrency(total_salary_bulan_kemarin));
                    $("#total_pajak_bulan_ini").html('Rp. '+formatCurrency(total_pajak_periode_terakhir));
                    
                    
                    // pengeluaran tahun berjalan chart
                    let dtTahunBerjalanParse = JSON.parse(res.data['pengeluaran_tahunberjalan_json']);
                    dtTahunBerjalanParse.sort(function(a, b) {
                        var monthA = a.mn.trim().toLowerCase();
                        var monthB = b.mn.trim().toLowerCase();

                        // Custom function to compare month names
                        var monthOrder = {
                            "january": 1,
                            "february": 2,
                            "march": 3,
                            "april": 4,
                            "may": 5,
                            "june": 6,
                            "july": 7,
                            "august": 8,
                            "september": 9,
                            "october": 10,
                            "november": 11,
                            "december": 12
                        };

                        return monthOrder[monthA] - monthOrder[monthB];
                    });
                    let dtSalaryTahunBerjalan = [];
                    let dtBulanBerjalan = [];
                    dtTahunBerjalanParse.forEach((dt) => {
                        let nominalSalary = (dt.payroll_total_netto) ? dt.payroll_total_netto : 0;
                        dtSalaryTahunBerjalan.push(nominalSalary)
                        dtBulanBerjalan.push(dt.mn)
                    });

                    // pengeluaran pph berjalan chart
                    let dtPPhBerjalanParse = JSON.parse(res.data['pengeluaran_pphberjalan_json']);
                    dtPPhBerjalanParse.sort(function(a, b) {
                        var monthA = a.mn.trim().toLowerCase();
                        var monthB = b.mn.trim().toLowerCase();

                        // Custom function to compare month names
                        var monthOrder = {
                            "january": 1,
                            "february": 2,
                            "march": 3,
                            "april": 4,
                            "may": 5,
                            "june": 6,
                            "july": 7,
                            "august": 8,
                            "september": 9,
                            "october": 10,
                            "november": 11,
                            "december": 12
                        };

                        return monthOrder[monthA] - monthOrder[monthB];
                    });
                    let dtPPhTahunBerjalan = [];
                    dtPPhBerjalanParse.forEach((dt) => {
                        let nominalPPh = (dt.payroll_deduction_pph21) ? dt.payroll_deduction_pph21 : 0;
                        dtPPhTahunBerjalan.push(nominalPPh)
                    });
                    
                    dtSalaryTahunBerjalan = dtSalaryTahunBerjalan.map((dt, i) => {
                        console.log(dt)
                        console.log(dtPPhTahunBerjalan[i])
                        return dt - dtPPhTahunBerjalan[i];
                    })

                    console.log('Gaji', dtTahunBerjalanParse)
                    console.log('Pph', dtPPhBerjalanParse)
                    // console.log(dtTahunBerjalan);
                    var options = {
                        series: [
                        {
                            name: "Gaji Karyawan Bersih",
                            data: dtSalaryTahunBerjalan
                        },
                        {
                            name: "PPh 21",
                            data: dtPPhTahunBerjalan
                        }
                        ],
                        chart: {
                            height: 350,
                            type: 'line',
                            dropShadow: {
                                enabled: true,
                                color: '#000',
                                top: 18,
                                left: 7,
                                blur: 10,
                                opacity: 0.2
                            },
                            toolbar: {
                                show: false
                            }
                        },
                        colors: ['#77B6EA', '#545454'],
                        dataLabels: {
                            enabled: true,
                        },
                        stroke: {
                            curve: 'smooth'
                        },
                        title: {
                            text: 'Pengeluaran Gaji Karyawan dan PPh 21',
                            align: 'left'
                        },
                        grid: {
                            borderColor: '#e7e7e7',
                            row: {
                                colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                                opacity: 0.5
                            },
                        },
                        markers: {
                            size: 1
                        },
                        xaxis: {
                            categories: dtBulanBerjalan,
                            title: {
                                text: 'Gaji Karyawan & PPh 21'
                            },
                        },
                        yaxis: {
                            title: {
                                text: 'Total'
                            },
                            labels: {
                                formatter: function (value) {
                                    return 'Rp.'+formatCurrency(value);
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right',
                            floating: true,
                            offsetY: -25,
                            offsetX: -5
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#chart_tahun_berjalan"), options);
                    chart.render();
                }
            })
        })
    </script>