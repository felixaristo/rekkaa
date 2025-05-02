@if(!$user->user_email_verified_at)
<div class="alert alert-danger alert-dismissible" role="alert">
    <h3 class="text-danger">Verifikasi Akun</h3>
    <p>Halo <b>{{$user->userwajibpajak->wajibpajak->wajibpajak_name}}</b>! Kami ingin memastikan alamat email Anda benar, silakan lakukan verifikasi akun yang telah kami kirimkan ke email anda di <b>{{$user->user_email}}</b></p>
    <p>Belum menerima email verifikasi? <a href="#" class="btn-kirim-ulang-verifikasi"><u>Kirim Ulang</u></a></p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
@endif

<?php

use Carbon\Carbon;
$joindate = Carbon::parse($user->user_created_at);
$joindatey = $joindate->format('Y');

if($stpenggajian) :
    if($stpenggajian->stpenggajiankaryawan_period == 'TANGGAL')
        $date = Carbon::parse(date('Y-m').'-'.$stpenggajian->stpenggajiankaryawan_enddate);
        // $date = Carbon::parse(date('Y').'-03-10');
    else
        $date = Carbon::parse(date('Y-m').'-'.'01')->addMonths(1);

    $now = Carbon::parse(date('Y-m-d'));
    // $now = Carbon::parse(date('Y').'-03-14');

    $diff = $now->diffInDays($date, false) + 1;


    $paiddate = Carbon::parse(date('Y-m').'-'.$stpenggajian->stpenggajiankaryawan_paymentdate);

    $paiddiff = $now->diffInDays($paiddate, false);
    // dd($paiddiff, $totalnotpaid);
?>
@if($diff >= 1 && $diff <= 3)
<div class="alert alert-dark alert-dismissible" role="alert">
    <h3 class="text-dark">Peringatan!</h3>
    <p>H-{{$diff}} sebelum penggajian.</p>
</div>
@endif
@if($diff == 0)
<div class="alert alert-warning alert-dismissible" role="alert">
    <h3 class="text-warning">Peringatan!</h3>
    <p>Hari ini adalah waktu untuk penggajian, silahkan lakukan <a href="{{route('user.page.penggajian.index')}}?menu_id=19">Finalisasi Perhitungan</a>.</p>
</div>
@endif
@if($diff <= -1 && $totalnotfinal > 0)
<div class="alert alert-danger alert-dismissible" role="alert">
    <h3 class="text-danger">Peringatan!</h3>
    <p>Anda belum menyelesaikan Finalisasi Perhitungan penggajian, klik <a href="{{route('user.page.penggajian.index')}}?menu_id=19">disini</a> untuk melakukan <a href="{{route('user.page.penggajian.index')}}?menu_id=19">Finalisasi Perhitungan</a>.</p>
</div>
@endif

@if($paiddiff >= 0 && $paiddiff <= 1)
<div class="alert alert-warning alert-dismissible" role="alert">
    <h3 class="text-warning">Peringatan!</h3>
    <p>{!!$paiddiff == 0 ? '<b>Sekarang</b>' : 'Besok' !!} adalah hari pembayaran penggajian, pastikan Anda telah melakukan  <a href="{{route('user.page.penggajian.index')}}?menu_id=19">Finalisasi Perhitungan</a> untuk semua karyawan Anda.</p>
</div>
@endif

@if($paiddiff < 1 && $totalnotpaid > 0)
<div class="alert alert-danger alert-dismissible" role="alert">
    <h3 class="text-danger">Peringatan!</h3>
    <p>Anda belum menyelesaikan pembayaran penggajian untuk karyawan Anda, segera selesaikan pembayaran <a href="{{route('user.page.penggajian.index')}}?menu_id=19">disini</a>.</p>
</div>
@endif

<?php endif; ?>

@if($user->userwajibpajak->wajibpajak->wajibpajak_type == 'INDIVIDU')
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-warning">Selamat Datang di REKKAA!</h5>
                        <p class="mb-4">
                            Nantikan fitur-fitur menarik lainnya di REKKAA!
                        </p>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="{{asset('assets/img/illustrations/girl-doing-yoga-light.png')}}" height="120" alt="View Badge User" data-app-dark-img="{{asset('assets/img/illustrations/girl-doing-yoga-light.png')}}" data-app-light-img="{{asset('assets/img/illustrations/girl-doing-yoga-light.png')}}" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-6">
                        <h5>Kalkulator REKKAA</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4 col-lg-4 mb-4">
                        <div class="card card-border-shadow-primary h-100" id="total_attendance_card">
                            <div class="card-body">
                                <!-- <div class="d-flex align-items-center mb-2 pb-1"> -->
                                <a href="{{route('kalkulator.pph21')}}?menu_id=26" class="rekkaa-page-link d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-calculator"></i></span>
                                    </div>
                                    <h5 class="ms-1 mb-0">PPh 21 Karyawan</h5>
                                </a>
                                <!-- </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-lg-34 mb-4">
                        <div class="card card-border-shadow-warning h-100" id="total_terlambat_card">
                            <div class="card-body">
                                <!-- <div class="d-flex align-items-center mb-2 pb-1"> -->
                                <a href="{{route('kalkulator.pph21nonkaryawan')}}?menu_id=27" class="rekkaa-page-link d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-calculator"></i></span>
                                    </div>
                                    <h5 class="ms-1 mb-0">PPh 21 Non Karyawan</h5>
                                </a>
                                <!-- </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-lg-4 mb-4">
                        <div class="card card-border-shadow-danger h-100" id="total_mia_card">
                            <div class="card-body">
                                <!-- <div class="d-flex align-items-center mb-2 pb-1"> -->
                                    <a href="{{route('kalkulator.pph4a2')}}?menu_id=28" class="rekkaa-page-link d-flex align-items-center mb-2 pb-1">
                                        <div class="avatar me-2">
                                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-calculator"></i></span>
                                        </div>
                                        <h5 class="ms-1 mb-0">PPh 4 Ayat 2</h5>
                                    </a>
                                <!-- </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@elseif($user->userwajibpajak->wajibpajak->wajibpajak_type == 'BADAN')
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="card-header m-2">
                <div class="row">
                    <div class="col-sm-6">
                        <h5>Absensi Harian</h5>
                        <div class="row">
                            <div class="col-sm-5">
                                <!-- <label for="start-date">Tanggal Awal</label> -->
                                <input type="text" id="attendance-date" name="attendance-date" class="form-control" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <div class="card card-border-shadow-primary h-100" id="total_attendance_card">
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
                        <div class="card card-border-shadow-warning h-100" id="total_terlambat_card">
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
                        <div class="card card-border-shadow-danger h-100" id="total_mia_card">
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
                        <div class="card card-border-shadow-info h-100" id="total_cuti_card">
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
    <div class="col-lg-12 col-md-12 mb-4 order-1 card-custom-hg">
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-sm-5">
                                <label for="periode-gaji">Periode</label>
                                <input type="text" id="periode-gaji" name="periode-gaji" class="form-control" autocomplete="off" style="width: 100%;">
                                <!-- </select> -->
                            </div>
                            <div class="col-sm-7">
                                <span id="gaji-notif"></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-title d-flex align-items-start justify-content-between">
                                            <div class="avatar rounded middle-box bg-warning text-white">
                                                <i class="bx bx-user"></i>
                                            </div>
                                        </div>
                                        <h5 class="mb-2 d-flex justify-content-between" style="line-height: 1.65;">Total Karyawan <span id="total_karyawan">0</span></h5>
                                        <hr>
                                        <span class="mb-2 d-flex justify-content-between" style="line-height: 1.65;">Tetap : <span id="total_karyawan_tetap">0</span></span>
                                        <span class="mb-2 d-flex justify-content-between" style="line-height: 1.65;">Kontrak : <span id="total_karyawan_kontrak">0</span></span>
                                        <span class="mb-5 d-flex justify-content-between">Percobaan : <span id="total_karyawan_percobaan">0</span></span>
                                        <div class="mt-5">
                                            <h5 class="mb-2 d-flex justify-content-between" style="line-height: 1.65;">Total Non Karyawan <span id="total_karyawan_bukan">0</span></h5>
                                            <hr>
                                            <!-- <span class="mb-1 d-flex justify-content-between">Bukan Karyawan : <span id="total_karyawan_bukan">0</span></span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-title d-flex align-items-start justify-content-between">
                                            <div class="avatar rounded middle-box bg-warning text-white">
                                                <i class="bx bx-chart"></i>
                                            </div>
                                        </div>
                                        <h5 class="mb-2 d-flex justify-content-between" style="line-height: 1.65;">Total Pengeluaran <span id="total_pengeluaran">0</span></h5>
                                        <hr>
                                        <span class="mb-2 d-flex justify-content-between" style="line-height: 1.65;">Total Gaji Bersih : <span id="total_salary_bulan_kemarin">0</span></span>
                                        <span class="mb-2 d-flex justify-content-between" style="line-height: 1.65;">Total Pph21 : <span id="total_pajak_bulan_ini">0</span></span>
                                        <!-- <span class="mb-2 d-flex justify-content-between" style="line-height: 1.65;">Total BPJS Kesehatan : <span id="total_pengeluaran_bpjskes">0</span></span>
                                        <span class="mb-2 d-flex justify-content-between" style="line-height: 1.65;">Total BPJS Tenaga Kerja : <span id="total_pengeluaran_bpjstk">0</span></span> -->
                                        <span class="mb-2 d-flex justify-content-between" style="line-height: 1.65;">Total BPJS : <span id="total_pengeluaran_bpjs">0</span></span>
                                    </div>
                                </div>
                                <!-- <div class="row">
                                    <div class="col-lg col-md-4 col-6 ml-4">
                                        <div class="card">
                                            <div class="card-body text-center" style="max-height: 150px;">
                                                <div class="card-title d-flex align-items-center justify-content-center">
                                                    <div class="avatar rounded middle-box bg-warning text-white">
                                                        <i class="bx bx-id-card"></i>
                                                    </div>
                                                </div>
                                                <span>Total Gaji Bersih</span>
                                                <hr>
                                                <h5 class="card-title text-nowrap" style="font-size: 2rem; line-height: 1.65;" id="total_salary_bulan_kemarin">0</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg col-md-4 col-6 ml-4">
                                        <div class="card">
                                            <div class="card-body text-center" style="max-height: 150px;">
                                                <div class="card-title d-flex align-items-center justify-content-center">
                                                    <div class="avatar rounded middle-box bg-warning text-white">
                                                        <i class="bx bx-wallet-alt"></i>
                                                    </div>
                                                </div>
                                                <span>Total Pph21</span>
                                                <hr>
                                                <h5 class="card-title text-nowrap" style="font-size: 2rem; line-height: 1.65;" id="total_pajak_bulan_ini">0</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <label for="periode-chart-tahun">Tahun</label>
                                <select id="periode-chart-tahun" name="periode-chart-tahun" class="form-control" autocomplete="off" style="width: 40%;">
                                    <!-- Add years dynamically or manually as needed -->
                                    <!-- <option value="2022">2022</option> -->
                                    <!-- <option value="2023">2023</option> -->
                                    <!-- Add more years as needed -->
                                </select>
                            </div>
                            <!-- <div class="col-md-6 mt-md-4 mt-sm-3 text-md-start text-sm-end">
                                <button id="searchButton" class="btn btn-search btn-outline-warning me-2 btn-sm">
                                    <i class="bx bx-search-alt"></i> Hitung
                                </button>
                                <button id="resetButton" class="btn btn-reset btn-outline-secondary btn-sm" style="background-color: white; color: red; border-color: red;">
                                    <i class="bx bx-reset"></i> Reset
                                </button>
                            </div> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <!-- <div class="avatar rounded middle-box bg-warning text-white">
                                <i class="bx bx-chart"></i>
                            </div> -->
                            <!-- <h5>Pengeluaran Tahun Berjalan</h5> -->
                        </div>
                        <div id="chart_tahun_berjalan"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="masukModal" data-bs-backdrop="static" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="masukModalLabel">Daftar Karyawan Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeMasuk"></button>
                </div>
                <div class="modal-body">
                    <!-- DataTable container -->
                    <table id="table-masuk" class="table table-hover display nowrap small-text-datatable" style="width: 100%">
                        <!-- Your DataTable content goes here -->
                        <thead>
                            <tr>
                                <th>Id Karyawan</th>
                                <th>Nama Karyawan</th>
                                <th>Jam Masuk</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="terlambatModal" data-bs-backdrop="static" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="terlambatModalLabel">Daftar Karyawan Terlambat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeTerlambat"></button>
                </div>
                <div class="modal-body">
                    <!-- DataTable container -->
                    <table id="table-terlambat" class="table table-hover display nowrap small-text-datatable" style="width: 100%">
                        <!-- Your DataTable content goes here -->
                        <thead>
                            <tr>
                                <th>Id Karyawan</th>
                                <th>Nama Karyawan</th>
                                <th>Durasi Terlambat</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tanpaketeranganModal" data-bs-backdrop="static" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tanpaketeranganModalLabel">Daftar Karyawan Tanpa Keterangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeTanpaKeterangan"></button>
                </div>
                <div class="modal-body">
                    <!-- DataTable container -->
                    <table id="table-tanpaketerangan" class="table table-hover display nowrap small-text-datatable" style="width: 100%">
                        <!-- Your DataTable content goes here -->
                        <thead>
                            <tr>
                                <th>Id Karyawan</th>
                                <th>Nama Karyawan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cutiModal" data-bs-backdrop="static" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cutiModalLabel">Daftar Karyawan Cuti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeCuti"></button>
                </div>
                <div class="modal-body">
                    <!-- DataTable container -->
                    <table id="table-cuti" class="table table-hover display nowrap small-text-datatable" style="width: 100%">
                        <!-- Your DataTable content goes here -->
                        <thead>
                            <tr>
                                <th>Id Karyawan</th>
                                <th>Nama Karyawan</th>
                                <th>Jenis Cuti</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            function formatTimeToHHmm(time) {
                const splitTime = time.split(' ')
                if (splitTime[1]) {
                    const timeParts = splitTime[1].split(":");
                    return timeParts[0] + ":" + timeParts[1];
                } else {
                    return null
                }
            }
            let tblMasuk = $("#table-masuk").DataTable({
                "searching": false,
                "language": {
                    // "searchPlaceholder": "Cari Nama Karyawan",
                    // "emptyTable": "Tidak ada data"
                },
                "lengthChange": false,
                "processing": true,
                "serverSide": true,
                // "paging": true, // Enable pagination
                "lengthMenu": [10], // Set number of records to display per page
                "info": false,
                "ordering": false,
                "ajax": {
                    "url": `{{ route('user.beranda.datamasuk') }}?periode_attendance=` + $('#attendance-date').val(), // Replace with your actual route to fetch data
                    "type": "GET",
                    "data": function(data) {
                        data.page = data.start / data.length + 1; // Calculate the current page based on start and length
                        data.per_page = data.length; // Set the number of records per page
                    },
                },
                "fnInitComplete": function() {
                    // this.fnAdjustColumnSizing(true);
                },
                "autoWidth": true,
                "columns": [{
                        "data": "karyawan_enid",
                        "title": "Id Karyawan",
                        "className": "text-center",
                    },
                    {
                        "data": "karyawan_name",
                        "title": "Nama Karyawan",
                        "className": "text-center",
                    },
                    {
                        "data": "attendancekaryawan_check_in",
                        "title": "Jam Masuk",
                        "className": "text-center",
                        "render": function(data, type, row) {
                            return formatTimeToHHmm(data)
                        }
                    }
                ]
            });
            $('#total_attendance_card').click(function() {
                // Trigger the modal to open
                $('#masukModal').modal('show');
            });
            $('#masukModal').on("show.bs.modal", function() {
                tblMasuk.draw();
            })

            let tblTerlambat = $("#table-terlambat").DataTable({
                "searching": false,
                "language": {
                    // "searchPlaceholder": "Cari Nama Karyawan",
                    // "emptyTable": "Tidak ada data"
                },
                "lengthChange": false,
                "processing": true,
                "serverSide": true,
                // "paging": true, // Enable pagination
                "lengthMenu": [10], // Set number of records to display per page
                "info": false,
                "ordering": false,
                "ajax": {
                    "url": `{{ route('user.beranda.dataterlambat') }}?periode_attendance=` + $('#attendance-date').val(), // Replace with your actual route to fetch data
                    "type": "GET",
                    "data": function(data) {
                        data.page = data.start / data.length + 1; // Calculate the current page based on start and length
                        data.per_page = data.length; // Set the number of records per page
                    },
                },
                "fnInitComplete": function() {
                    // this.fnAdjustColumnSizing(true);
                },
                "autoWidth": true,
                "columns": [{
                        "data": "karyawan_enid",
                        "title": "Id Karyawan",
                        "className": "text-center",
                    },
                    {
                        "data": "karyawan_name",
                        "title": "Nama Karyawan",
                        "className": "text-center",
                    },
                    {
                        "data": "attendancekaryawan_check_in_late",
                        "title": "Durasi Terlambat",
                        "className": "text-center",
                        "render": function(data, type, row) {
                            if (data && (type === "display" || type === "filter")) {
                                // Split the HH:mm format into hours and minutes
                                var parts = data.split(":");
                                var hours = parseInt(parts[0], 10);
                                var minutes = parseInt(parts[1], 10);

                                // Format the duration
                                var formattedDuration = "<span style='color: red;'>";
                                if (hours > 0) {
                                    formattedDuration += hours + " Jam ";
                                }
                                if (minutes > 0) {
                                    formattedDuration += minutes + " Menit";
                                }
                                formattedDuration += "</span>";

                                return formattedDuration;
                            }

                            return data; // Return original data if type is not "display" or "filter"
                        }
                    }
                ]
            });
            $('#total_terlambat_card').click(function() {
                
                // Trigger the modal to open
                $('#terlambatModal').modal('show');
            });
            
            $('#terlambatModal').on("show.bs.modal", function() {
                tblTerlambat.draw();
            })

            let tblMia = $("#table-tanpaketerangan").DataTable({
                "searching": false,
                "language": {
                    // "searchPlaceholder": "Cari Nama Karyawan",
                    // "emptyTable": "Tidak ada data"
                },
                "lengthChange": false,
                "processing": true,
                "serverSide": true,
                // "paging": true, // Enable pagination
                "lengthMenu": [10], // Set number of records to display per page
                "info": false,
                "ordering": false,
                "ajax": {
                    "url": `{{ route('user.beranda.datamia') }}?periode_attendance=` + $('#attendance-date').val(), // Replace with your actual route to fetch data
                    "type": "GET",
                    "data": function(data) {
                        data.page = data.start / data.length + 1; // Calculate the current page based on start and length
                        data.per_page = data.length; // Set the number of records per page
                    },
                },
                "fnInitComplete": function() {
                    // this.fnAdjustColumnSizing(true);
                },
                "autoWidth": true,
                "columns": [{
                        "data": "karyawan_enid",
                        "title": "Id Karyawan",
                        "className": "text-center",
                    },
                    {
                        "data": "karyawan_name",
                        "title": "Nama Karyawan",
                        "className": "text-center",
                    }
                ]
            });
            $('#total_mia_card').click(function() {

                // Trigger the modal to open
                $('#tanpaketeranganModal').modal('show');
            });
            
            $('#tanpaketeranganModal').on("show.bs.modal", function() {
                tblMia.draw();
            })

            let tblCuti = $("#table-cuti").DataTable({
                "searching": false,
                "language": {
                    // "searchPlaceholder": "Cari Nama Karyawan",
                    // "emptyTable": "Tidak ada data"
                },
                "lengthChange": false,
                "processing": true,
                "serverSide": true,
                // "paging": true, // Enable pagination
                "lengthMenu": [10], // Set number of records to display per page
                "info": false,
                "ordering": false,
                "ajax": {
                    "url": `{{ route('user.beranda.datacuti') }}?periode_attendance=` + $('#attendance-date').val(), // Replace with your actual route to fetch data
                    "type": "GET",
                    "data": function(data) {
                        data.page = data.start / data.length + 1; // Calculate the current page based on start and length
                        data.per_page = data.length; // Set the number of records per page
                    },
                },
                "fnInitComplete": function() {
                    // this.fnAdjustColumnSizing(true);
                },
                "autoWidth": true,
                "columns": [{
                        "data": "karyawan_enid",
                        "title": "Id Karyawan",
                        "className": "text-center",
                    },
                    {
                        "data": "karyawan_name",
                        "title": "Nama Karyawan",
                        "className": "text-center",
                    },
                    {
                        "data": "leave_description",
                        "title": "Jenis Cuti",
                        "className": "text-center"
                    }
                ]
            });

            $('#total_cuti_card').click(function() {
                

                // Trigger the modal to open
                $('#cutiModal').modal('show');
            });
            
            $('#cutiModal').on("show.bs.modal", function() {
                tblCuti.draw();
            })

            $("#attendance-date").daterangepicker({
                singleDatePicker: true,
                // showDropdowns: true,
                locale: {
                    format: 'DD-MM-YYYY', // Display only day and month
                },
                minDate: '01-01-<?php echo $joindatey ?>', // 2 years ago from the current date
                maxDate: '01-12-'+moment().format('Y'), // 2 years in the future from the current date
            });

            var currentMonth = new Date().getMonth() + 1; // Months are 0-indexed
            var previousMonth = (currentMonth - 1).toString().padStart(2, '0');
            // var monthOptions = ` <option value="01">Januari</option>
            //                     <option value="02">Februari</option>
            //                     <option value="03">Maret</option>
            //                     <option value="04">April</option>
            //                     <option value="05">Mei</option>
            //                     <option value="06">Juni</option>
            //                     <option value="07">Juli</option>
            //                     <option value="08">Agustus</option>
            //                     <option value="09">September</option>
            //                     <option value="10">Oktober</option>
            //                     <option value="11">November</option>
            //                     <option value="12">Desember</option>`

            // $('#periode-gaji').html(monthOptions);
            // $('#periode-gaji').val(previousMonth);

            let filterPeriode = moment().format('MM-YYYY');
            $("#periode-gaji").datepicker({
                    language: "id-ID",
                    format: "MM-yyyy",
                    startView: "months",
                    minViewMode: "months",
                    startDate: '01-<?php echo $joindatey ?>',
                    endDate: '12-'+moment().format('Y'),
                }).datepicker("setDate", filterPeriode)
                .on('hide', function(e) {
                    // `e` here contains the extra attributes
                    let dt = $('#periode-gaji').datepicker("getDate");
                    filterPeriode = moment(dt).format('MM-YYYY');
                });

            console.log('gaji', $('#periode-gaji').val())

            // Generate and auto-select years
            var currentYear = new Date().getFullYear();
            var yearOptions = '';
            for (var i = '<?php echo $joindatey ?>'; i <= currentYear; i++) {
                yearOptions += '<option value="' + i + '">' + i + '</option>';
            }

            $('#periode-chart-tahun').select2();
            $('#periode-chart-tahun').html(yearOptions);
            $('#periode-chart-tahun').val(currentYear);

            let refresh = {
                absensi: true,
                chart: true,
                gaji: true,
                karyawan: true
            }

            $('#periode-gaji').change(function(e) {
                e.preventDefault();
                const selectedMonthYear = $('#periode-gaji').val();
                const [monthName, year] = selectedMonthYear.split('-');

                // console.log(year, 'year');
                // return
                if(year > moment().format('YYYY')) {
                    toastr.error('Tahun tidak boleh kurang atau melebihi tahun sekarang!');
                    $('#periode-gaji').datepicker('setDate', moment().format('MMMM YYYY'));
                    return false;
                }

                $(".spinner-box").css({
                    'display': 'table'
                });
                refresh.absensi = false
                refresh.karyawan = false
                refresh.chart = false
                refresh.gaji = true
                // Fetch data and render when dropdown values change
                fetchAndRender(refresh);
            });

            $("#attendance-date").on('apply.daterangepicker', function(e) {
                e.preventDefault();

                $(".spinner-box").css({
                    'display': 'table'
                });

                refresh.absensi = true
                refresh.karyawan = false
                refresh.chart = false
                refresh.gaji = false
                // Fetch data and render when dropdown values change
                fetchAndRender(refresh);
            });

            $('#periode-chart-tahun').change(function(e) {
                e.preventDefault();


                $(".spinner-box").css({
                    'display': 'table'
                });

                refresh.absensi = false
                refresh.karyawan = false
                refresh.chart = true
                refresh.gaji = false
                // Fetch data and render when dropdown values change
                fetchAndRender(refresh);
            });

            fetchAndRender(refresh);

            function fetchAndRender(refresh) {
                const monthNameToNumber = {
                    'Januari': '01',
                    'Pebruari': '02',
                    'Maret': '03',
                    'April': '04',
                    'Mei': '05',
                    'Juni': '06',
                    'Juli': '07',
                    'Agustus': '08',
                    'September': '09',
                    'Oktober': '10',
                    'Nopember': '11',
                    'Desember': '12'
                };
                
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

                const selectedMonthYear = $('#periode-gaji').val();
                const [monthName, year] = selectedMonthYear.split('-');
                const selectedMonth = monthNameToNumber[monthName] + '-' + year;
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                const selectedAttendance = $('#attendance-date').val();
                const selectedChart = $('#periode-chart-tahun').val();

                // Define the URL with query parameters
                const url = "{{ route('user.beranda.data') }}?periode_gaji=" + selectedMonth + "&periode_chart=" + selectedChart + "&periode_attendance=" + selectedAttendance;

                $.ajax({
                    method: 'get',
                    url: url,
                    headers: {
                        "X-CSRF-TOKEN": csrfToken // Include the CSRF token in the refresh headers
                    },
                    contentType: "application/json",
                    // data: JSON.stringify(refreshData),
                    success: function(res) {
                        console.log(res);
                        if (!res.success || res.data == null) {
                            $(".spinner-box").fadeOut();
                            return false;
                        }

                        if (refresh.karyawan === true) {
                            let total_karyawan_tetap = (res.data['total_karyawan_tetap']) ? res.data['total_karyawan_tetap'] : 0;
                            let total_karyawan_kontrak = (res.data['total_karyawan_kontrak']) ? res.data['total_karyawan_kontrak'] : 0;
                            let total_karyawan_bukan = (res.data['total_karyawan_bukan']) ? res.data['total_karyawan_bukan'] : 0;
                            let total_karyawan_percobaan = (res.data['total_karyawan_percobaan']) ? res.data['total_karyawan_percobaan'] : 0;
                            let total_karyawan = total_karyawan_kontrak + total_karyawan_percobaan + total_karyawan_tetap

                            $("#total_karyawan_tetap").html(total_karyawan_tetap);
                            $("#total_karyawan_kontrak").html(total_karyawan_kontrak);
                            $("#total_karyawan_bukan").html(total_karyawan_bukan);
                            $("#total_karyawan_percobaan").html(total_karyawan_percobaan);
                            $("#total_karyawan").html(total_karyawan);
                        }

                        if (refresh.absensi === true) {
                            let total_leave = (res.data['total_leave']) ? res.data['total_leave'] : 0
                            let total_attendance = (res.data['total_attendance']) ? res.data['total_attendance'] : 0
                            let total_attendance_late = (res.data['total_attendance_late']) ? res.data['total_attendance_late'] : 0
                            let total_karyawan_onduty = (res.data['total_karyawan_onduty']) ? res.data['total_karyawan_onduty'] : 0
                            let total_tanpa_keterangan = total_karyawan_onduty - total_attendance - total_leave

                            $("#total_leave").html(total_leave);
                            $("#total_attendance").html(total_attendance);
                            $("#total_attendance_late").html(total_attendance_late);
                            $("#total_tanpa_keterangan").html(total_tanpa_keterangan);
                        }

                        if (refresh.gaji === true) {
                            let total_salary = (res.data['total_salary_periode_terakhir']) ? parseInt(res.data['total_salary_periode_terakhir']) : 0;
                            // let total_gajipokok = (res.data['total_gajipokok']) ? res.data['total_gajipokok'] : 0;
                            let total_pajak_periode_terakhir = (res.data['total_pajak_periode_terakhir']) ? parseInt(res.data['total_pajak_periode_terakhir']) : 0;
                            let total_bpjskes_periode_terakhir = (res.data['total_bpjskes_periode_terakhir']) ? parseInt(res.data['total_bpjskes_periode_terakhir']) : 0;
                            let total_bpjstk_periode_terakhir = (res.data['total_bpjstk_periode_terakhir']) ? parseInt(res.data['total_bpjstk_periode_terakhir']) : 0;
                            let total_salary_bulan_kemarin = total_salary;
                            let total_pengeluaran = total_pajak_periode_terakhir + total_bpjskes_periode_terakhir + total_bpjstk_periode_terakhir + total_salary_bulan_kemarin;

                            $("#total_salary_bulan_kemarin").html('Rp. ' + formatCurrency(total_salary_bulan_kemarin));
                            // $("#total_pengeluaran_bpjskes").html('Rp. ' + formatCurrency(total_bpjskes_periode_terakhir));
                            // $("#total_pengeluaran_bpjstk").html('Rp. ' + formatCurrency(total_bpjstk_periode_terakhir));
                            $("#total_pajak_bulan_ini").html('Rp. ' + formatCurrency(total_pajak_periode_terakhir));
                            $("#total_pengeluaran").html('Rp. ' + formatCurrency(total_pengeluaran));
                            $("#total_pengeluaran_bpjs").html('Rp. ' + formatCurrency(total_bpjskes_periode_terakhir + total_bpjstk_periode_terakhir));


                            if(total_pajak_periode_terakhir == 0) {
                                // $("#gaji-notif").html(`
                                // <div class="alert alert-info" role="alert">
                                //     <h6 class="alert-heading d-flex align-items-center mb-1">Info</h6>
                                //     <p class="mb-0">Silahkan lakukan perhitungan di menu <a href="{{route('user.page.penggajian.index')}}?menu_id=19">penggajian</a> terlebih dahulu.</p>
                                // </div>
                                // `);
                            } else {
                                $("#gaji-notif").html('');
                            }
                        }

                        if (refresh.chart === true) {
                            let dtExpanseTahunBerjalan = [];

                            let dtTahunBerjalanParse = JSON.parse(res.data['pengeluaran_tahunberjalan_json']);
                            dtTahunBerjalanParse.sort(function(a, b) {
                                var monthA = a.mn.trim().toLowerCase();
                                var monthB = b.mn.trim().toLowerCase();

                                return monthOrder[monthA] - monthOrder[monthB];
                            });
                            let dtSalaryTahunBerjalan = [];
                            let dtBulanBerjalan = [];
                            dtTahunBerjalanParse.forEach((dt, i) => {
                                console.log('dt.payroll_total_netto', dt.payroll_total_netto)
                                let nominalSalary = (dt.payroll_total_netto) ? dt.payroll_total_netto : 0;
                                dtSalaryTahunBerjalan.push(nominalSalary)
                                dtBulanBerjalan.push(dt.mn)
                                if(dtExpanseTahunBerjalan[i]) {
                                    dtExpanseTahunBerjalan[i] += nominalSalary;
                                } else {
                                    dtExpanseTahunBerjalan[i] = nominalSalary;
                                }
                                
                            });

                            // pengeluaran pph berjalan chart
                            let dtPPhBerjalanParse = JSON.parse(res.data['pengeluaran_pphberjalan_json']);
                            dtPPhBerjalanParse.sort(function(a, b) {
                                var monthA = a.mn.trim().toLowerCase();
                                var monthB = b.mn.trim().toLowerCase();

                                return monthOrder[monthA] - monthOrder[monthB];
                            });
                            let dtPPhTahunBerjalan = [];
                            dtPPhBerjalanParse.forEach((dt, i) => {
                                let nominalPPh = (dt.payroll_deduction_pph21 > 0) ? dt.payroll_deduction_pph21 : 0;
                                // console.log('nominalPPh', nominalPPh)
                                // console.log('nominalPPh', formatCurrency(nominalPPh))
                                dtPPhTahunBerjalan.push(nominalPPh)
                                if(dtExpanseTahunBerjalan[i]) {
                                    dtExpanseTahunBerjalan[i] += nominalPPh;
                                } else {
                                    dtExpanseTahunBerjalan[i] = nominalPPh;
                                }
                            });

                            // pengeluaran bpjskes berjalan chart
                            let dtBpjskesBerjalanParse = JSON.parse(res.data['pengeluaran_bpjskesberjalan_json']);
                            dtBpjskesBerjalanParse.sort(function(a, b) {
                                var monthA = a.mn.trim().toLowerCase();
                                var monthB = b.mn.trim().toLowerCase();

                                return monthOrder[monthA] - monthOrder[monthB];
                            });
                            let dtBpjskesBerjalan = [];
                            dtBpjskesBerjalanParse.forEach((dt, i) => {
                                let nominalBPJSKES = (dt.payroll_allowance_bpjskes > 0) ? dt.payroll_allowance_bpjskes : 0;
                                // console.log('nominalPPh', nominalPPh)
                                // console.log('nominalPPh', formatCurrency(nominalPPh))
                                dtBpjskesBerjalan.push(nominalBPJSKES)
                                if(dtExpanseTahunBerjalan[i]) {
                                    dtExpanseTahunBerjalan[i] += nominalBPJSKES;
                                } else {
                                    dtExpanseTahunBerjalan[i] = nominalBPJSKES;
                                }
                            });

                            // pengeluaran bpjstk berjalan chart
                            let dtBpjstkBerjalanParse = JSON.parse(res.data['pengeluaran_bpjstkberjalan_json']);
                            
                            dtBpjstkBerjalanParse.sort(function(a, b) {
                                var monthA = a.mn.trim().toLowerCase();
                                var monthB = b.mn.trim().toLowerCase();

                                return monthOrder[monthA] - monthOrder[monthB];
                            });
                            console.log('dtBpjstkBerjalanParse', dtBpjstkBerjalanParse)
                            let dtBpjstkBerjalan = [];
                            dtBpjstkBerjalanParse.forEach((dt, i) => {
                                let nominalBPJSTK = (dt.payroll_allowance_bpjstk > 0) ? dt.payroll_allowance_bpjstk : 0;
                                // console.log('nominalPPh', nominalPPh)
                                // console.log('nominalPPh', formatCurrency(nominalPPh))
                                dtBpjstkBerjalan.push(nominalBPJSTK)
                                if(dtExpanseTahunBerjalan[i]) {
                                    dtExpanseTahunBerjalan[i] += nominalBPJSTK;
                                } else {
                                    dtExpanseTahunBerjalan[i] = nominalBPJSTK;
                                }
                            });

                            // dtSalaryTahunBerjalan = dtSalaryTahunBerjalan.map((dt, i) => {
                            //     return dt;
                            // })

                            

                            // console.log('Gaji', dtTahunBerjalanParse)
                            // console.log('Pph', dtPPhBerjalanParse)
                            console.log(dtExpanseTahunBerjalan);
                            var options = {
                                series: [{
                                        name: "Gaji Karyawan Bersih",
                                        data: dtSalaryTahunBerjalan
                                    },
                                    {
                                        name: "PPh 21",
                                        data: dtPPhTahunBerjalan
                                    },
                                    {
                                        name: "BPJS Kesehatan",
                                        data: dtBpjskesBerjalan
                                    },
                                    {
                                        name: "BPJS Tenaga Kerja",
                                        data: dtBpjstkBerjalan
                                    },
                                    {
                                        name: "Total Pengeluaran Perusahaan",
                                        data: dtExpanseTahunBerjalan
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
                                colors: ['#77B6EA', '#545454', '#f2b15e', '#f8dab3', '#b23233'],
                                dataLabels: {
                                    enabled: true,
                                    formatter: function(val, opt) {
                                        return formatCurrency(val)
                                    },
                                },
                                stroke: {
                                    curve: 'smooth'
                                },
                                title: {
                                    text: 'Pengeluaran Tahun Berjalan',
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
                                        formatter: function(value) {
                                            return 'Rp.' + formatCurrency(value);
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

                            if (window.myChart) {
                                window.myChart.destroy();
                            }

                            window.myChart = new ApexCharts(document.querySelector("#chart_tahun_berjalan"), options);
                            window.myChart.render();
                        }


                        $(".spinner-box").fadeOut();
                    }
                })
            }

            $(".btn-kirim-ulang-verifikasi").click(function(e) {
                e.preventDefault();

                $(".spinner-box").css({display: 'block'});
                $.ajax({
                    method: 'post',
                    url: "{{route('user.sendemailverification')}}",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') // Include the CSRF token in the refresh headers
                    },
                    contentType: "application/json",
                    success: function(res) {
                        $(".spinner-box").fadeOut();
                        console.log(res);
                        if (!res.success) {
                            toastr.error(res.message);
                            return false;
                        }
                        toastr.success(res.message);
                    }
                });
            })
        })
    </script>

    @endif