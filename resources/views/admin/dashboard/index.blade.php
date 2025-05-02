
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <!-- <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-warning">Masukkan NPWP</h5>
                        <p class="mb-4">
                        Masukkan NPWP untuk menggunakan fitur kami.
                        </p>

                        <a href="{{url('user/npwp')}}" class="btn btn-sm btn-outline-warning rekkaa-page-link">Buat NPWP</a>
                    </div>
                </div> -->
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
</div>
<div class="row">
    <div class="col-lg-12 col-md-4 order-1 card-custom-hg">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar rounded middle-box bg-warning text-white">
                                <i class="bx bx-user"></i>
                            </div>
                        </div>
                        <span>Total Pegawai</span>
                        <hr>
                        <h5 class="mb-1 d-flex justify-content-between">Tetap : <span id="total_karyawan_tetap">0</span></h5>
                        <h5 class="mb-1 d-flex justify-content-between">Kontrak : <span id="total_karyawan_kontrak">0</span></h5>
                        <h5 class="mb-1 d-flex justify-content-between">Bukan Karyawan : <span id="total_karyawan_bukan">0</span></h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar rounded middle-box bg-warning text-white">
                                <i class="bx bx-id-card"></i>
                            </div>
                        </div>
                        <span>Total Pengeluaran Bulan Ini</span>
                        <hr>
                        <h5 class="card-title text-nowrap mb-1" id="total_pengeluaran_bulan_ini">0</h5>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-6 mb-4">
                <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar rounded middle-box bg-warning text-white">
                            <i class="bx bx-wallet-alt"></i>
                        </div>
                    </div>
                        <span>Total Pajak Bulan Ini</span>
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
    <script>
        $(function() {
            // $.ajax({
            //     method: 'get',
            //     url: "{{route('user.beranda.data')}}",
            //     success: function(res) {
            //         console.log(res);
            //         if(!res.success || res.data == null) {
            //             return false;
            //         }

            //         let total_karyawan_tetap = (res.data['total_karyawan_tetap']) ? res.data['total_karyawan_tetap'] : 0;
            //         let total_karyawan_kontrak = (res.data['total_karyawan_kontrak']) ? res.data['total_karyawan_kontrak'] : 0;
            //         let total_karyawan_bukan = (res.data['total_karyawan_bukan']) ? res.data['total_karyawan_bukan'] : 0;
            //         let total_salary_bulan_ini = (res.data['total_salary_bulan_ini']) ? parseInt(res.data['total_salary_bulan_ini']) : 0;
            //         // let total_gajipokok = (res.data['total_gajipokok']) ? res.data['total_gajipokok'] : 0;
            //         let total_pph21_bulan_ini = (res.data['total_pph21_bulan_ini']) ? parseInt(res.data['total_pph21_bulan_ini']) : 0;
            //         let total_pengeluaran_bulan_ini = total_salary_bulan_ini - total_pph21_bulan_ini;

            //         console.log('total_salary_bulan_ini', total_salary_bulan_ini)
            //         console.log('total_pph21_bulan_ini', total_pph21_bulan_ini)
            //         console.log('total_pengeluaran_bulan_ini', total_pengeluaran_bulan_ini)
            //         $("#total_karyawan_tetap").html(total_karyawan_tetap);
            //         $("#total_karyawan_kontrak").html(total_karyawan_kontrak);
            //         $("#total_karyawan_bukan").html(total_karyawan_bukan);
            //         $("#total_pengeluaran_bulan_ini").html('Rp.'+formatCurrency(total_pengeluaran_bulan_ini));
            //         $("#total_pajak_bulan_ini").html('Rp.'+formatCurrency(total_pph21_bulan_ini));
                    
                    
            //         // pengeluaran tahun berjalan chart
            //         let dtTahunBerjalanParse = JSON.parse(res.data['pengeluaran_tahunberjalan_json']);
            //         let dtSalaryTahunBerjalan = [];
            //         let dtBulanBerjalan = [];
            //         dtTahunBerjalanParse.forEach((dt) => {
            //             let nominalSalary = (dt.karyawankalkulasi_salary) ? dt.karyawankalkulasi_salary : 0;
            //             dtSalaryTahunBerjalan.push(nominalSalary)
            //             dtBulanBerjalan.push(dt.mn)
            //         });

            //         // pengeluaran pph berjalan chart
            //         let dtPPhBerjalanParse = JSON.parse(res.data['pengeluaran_pphberjalan_json']);
            //         let dtPPhTahunBerjalan = [];
            //         dtPPhBerjalanParse.forEach((dt) => {
            //             let nominalPPh = (dt.karyawan_pph) ? dt.karyawan_pph : 0;
            //             dtPPhTahunBerjalan.push(nominalPPh)
            //         });
                    
            //         dtSalaryTahunBerjalan = dtSalaryTahunBerjalan.map((dt, i) => {
            //             console.log(dt)
            //             console.log(dtPPhTahunBerjalan[i])
            //             return dt - dtPPhTahunBerjalan[i];
            //         })
            //         // console.log(dtTahunBerjalan);
            //         var options = {
            //             series: [
            //             {
            //                 name: "Gaji Karyawan",
            //                 data: dtSalaryTahunBerjalan
            //             },
            //             {
            //                 name: "PPh 21",
            //                 data: dtPPhTahunBerjalan
            //             }
            //             ],
            //             chart: {
            //                 height: 350,
            //                 type: 'line',
            //                 dropShadow: {
            //                     enabled: true,
            //                     color: '#000',
            //                     top: 18,
            //                     left: 7,
            //                     blur: 10,
            //                     opacity: 0.2
            //                 },
            //                 toolbar: {
            //                     show: false
            //                 }
            //             },
            //             colors: ['#77B6EA', '#545454'],
            //             dataLabels: {
            //                 enabled: true,
            //             },
            //             stroke: {
            //                 curve: 'smooth'
            //             },
            //             title: {
            //                 text: 'Pengeluaran Gaji Karyawan dan PPh 21',
            //                 align: 'left'
            //             },
            //             grid: {
            //                 borderColor: '#e7e7e7',
            //                 row: {
            //                     colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            //                     opacity: 0.5
            //                 },
            //             },
            //             markers: {
            //                 size: 1
            //             },
            //             xaxis: {
            //                 categories: dtBulanBerjalan,
            //                 title: {
            //                     text: 'Gaji Karyawan & PPh 21'
            //                 },
            //             },
            //             yaxis: {
            //                 title: {
            //                     text: 'Total'
            //                 },
            //                 labels: {
            //                     formatter: function (value) {
            //                         return 'Rp.'+formatCurrency(value);
            //                     }
            //                 }
            //             },
            //             legend: {
            //                 position: 'top',
            //                 horizontalAlign: 'right',
            //                 floating: true,
            //                 offsetY: -25,
            //                 offsetX: -5
            //             }
            //         };

            //         var chart = new ApexCharts(document.querySelector("#chart_tahun_berjalan"), options);
            //         chart.render();
            //     }
            // })
        })
    </script>