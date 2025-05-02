<?php
	$cbegindate = \Carbon\Carbon::parse($karyawan->karyawan_contract_begin);
	$dt_cbegin = $cbegindate->translatedFormat('d-m-Y');

	$dt_birthdate = null;
	$dt_bpjskesdate = null;
	$dt_bpjstkdate = null;
	$dt_cend = null;

    // dd($karyawan);

	$tunjangan = ($karyawan->tunjangan_json) ? json_decode($karyawan->tunjangan_json) : null; 
    $potongan = ($karyawan->potongan_json) ? json_decode($karyawan->potongan_json) : null; 

	if($karyawan->karyawan_birthdate) {
		$birthdate = \Carbon\Carbon::parse($karyawan->karyawan_birthdate);
		$dt_birthdate = $birthdate->translatedFormat('d-m-Y');
	}

	if($karyawan->karyawan_bpjskesdate) {
		$bpjskesdate = \Carbon\Carbon::parse($karyawan->karyawan_bpjskesdate);
		$dt_bpjskesdate = $bpjskesdate->translatedFormat('d-m-Y');
	}

	if($karyawan->karyawan_bpjstkdate) {
		$bpjstkdate = \Carbon\Carbon::parse($karyawan->karyawan_bpjstkdate);
		$dt_bpjstkdate = $bpjstkdate->translatedFormat('d-m-Y');
	}

	if($karyawan->karyawan_contract_end) {
		$cend = \Carbon\Carbon::parse($karyawan->karyawan_contract_end);
		$dt_cend = $cend->translatedFormat('d-m-Y');
	}

	$additional_info = [];
	$additional_arr = [];
	if($karyawan->karyawan_additionalinfo) {
		$additional_info = json_decode($karyawan->karyawan_additionalinfo);
		if($additional_info) {
			foreach($additional_info as $info) {
				$additional_arr[] = $info->nama;
			}
		}
	}
?>

<div class="row">
    <div class="col-lg-12 mb-3 order-0">
        <div class="card" style="box-shadow:none">
            <h3 class="title" style="margin-left:20px;margin-top:20px;">{{$title}}</h3>
            <div class="user-profile-header-banner" style="margin-top:20px">
                    <!--<img href="http://localhost:8000/assets/img/avatars/1.png" alt="Banner image" class="rounded-top">-->
            </div>
            <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-3">
                <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                    @if($karyawan->karyawan_photo)
                        <img src="{{$karyawan->karyawan_photo}}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" width="140px">
                    @else
                        <img src="{{asset('assets/img/avatars/avatar.png')}}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" width="140px">
                    @endif
                </div>
                <div class="flex-grow-1">
                <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                    <div class="user-profile-info">
                    <h4 class="m-0 ">{{$karyawan->karyawan_name}}</h4>
                    <span>{{$karyawan->karyawan_enid}}</span>
                    <ul class="list-group mt-2">
                        <li class="list-group-item d-flex align-items-center pl-0 " style="border:0px;padding:0px;">
                        <span class="badge rounded-pill bg-danger mb-1 mr-1">Tidak Aktif</span>
                        @if($karyawan->karyawan_status == 'NONKARYAWAN')
                            <span class="badge rounded-pill bg-info mb-1">Bukan Karyawan</span>
                        @elseif($karyawan->karyawan_status == 'TETAP')
                            <span class="badge rounded-pill bg-primary mb-1">Tetap</span>
                        @elseif($karyawan->karyawan_status == 'KONTRAK')
                            <span class="badge rounded-pill bg-secondary mb-1">Kontrak</span>
                        @elseif($karyawan->karyawan_status == 'PERCOBAAN')
                            <span class="badge rounded-pill bg-warning mb-1">Percobaan</span>
                        @endif
                        <li class="list-group-item d-flex align-items-center pl-0" style="border:0px;padding:0px">
                        <i class="bx bx-briefcase me-2"></i>
                            {{$karyawan->jabatan ? $karyawan->jabatan->karyawanjabatan_name : '-'}}
                        </li>
                        <li class="list-group-item d-flex align-items-center pl-0" style="border:0px;padding:0px">
                        <i class="bx bx-calendar me-2"></i>
                            Joined {{$dt_cbegin}}
                        </li>
                    </ul>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 mb-3 order-0">
        <div class="card" style="box-shadow:none">
            <!-- Bootstrap Table with Header - Light -->
            <div class="card-header row pt-2 pb-1">
                <div class="col-lg-12">
                <h6 class="text-uppercase p-2 m-0 text-bold text-warning"><i class="bx bx-user" style="margin-right:10px"></i> Data Identitas</h6>
                </div>
            </div>
            <hr class="my-1">
            <div class="card-body pb-2">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_nik" class="col-sm-6 lbl-req text-bold">NIK <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->karyawan_nik}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_nik" class="col-sm-6 lbl-req text-bold">NPWP <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->karyawan_npwp}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_birth" class="col-sm-6 lbl-req text-bold">Tempat/Tanggal Lahir <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>
                                    @if(empty($karyawan->karyawan_birthplace) && empty($dt_birthdate))
                                        -
                                    @else
                                        {{ empty($karyawan->karyawan_birthplace) ? '-' : $karyawan->karyawan_birthplace }},
                                        {{ empty($dt_birthdate) ? '-' : $dt_birthdate }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_gender" class="col-sm-6 lbl-req text-bold">Jenis Kelamin <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->karyawan_gender == 'L' ? 'Laki-laki' : 'Perempuan'}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="ptkp_marriage_status" class="col-sm-6 lbl-req text-bold">Status Perkawinan <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->ptkp ? $karyawan->ptkp->ptkp_marriage_status : '-'}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_citizenship" class="col-sm-6 lbl-req text-bold">Kewarganegaraan <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->karyawan_citizenship ?? '-'}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_country" class="col-sm-6 lbl-req text-bold">Negara <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->country ? $karyawan->country->country_nicename : 'Indonesia'}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_email" class="col-sm-6 lbl-req text-bold">Email <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->karyawan_email ?? '-'}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_phone" class="col-sm-6 lbl-req text-bold">No. Telepon <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->karyawan_phone ?? '-'}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_address" class="col-sm-6 lbl-req text-bold">Alamat <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->karyawan_address ?? '-'}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bootstrap Table with Header - Light -->
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12 mb-3 order-0">
        <div class="card" style="box-shadow:none">
            <!-- Bootstrap Table with Header - Light -->
            <div class="card-header row pt-2 pb-1">
                <div class="col-lg-12">
                <h6 class="text-uppercase p-2 m-0 text-bold text-warning"><i class="bx bx-briefcase" style="margin-right:10px"></i> Data Pekerjaan</h6>
                </div>
            </div>
            <hr class="my-1">
            <div class="card-body  pb-2">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawanjabatan_name" class="col-sm-6 lbl-req text-bold">Jabatan <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->jabatan ? $karyawan->jabatan->karyawanjabatan_name : '-'}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawandivisi_name" class="col-sm-6 lbl-req text-bold">Divisi <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->divisi ? $karyawan->divisi->karyawandivisi_name : '-'}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_managername" class="col-sm-6 lbl-req text-bold">Atasan <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->manager ? $karyawan->manager->karyawan_name : '-'}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_status" class="col-sm-6 lbl-req text-bold">Status Karyawan <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->karyawan_status}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="dt_cbegin" class="col-sm-6 lbl-req text-bold">Tanggal Masuk <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$dt_cbegin}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="dt_cend" class="col-sm-6 lbl-req text-bold">Tanggal Berakhir Kontrak <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$dt_cend ?? '-'}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_attendance" class="col-sm-6 lbl-req text-bold">Absensi <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                @if($karyawan->attendance)
                                    <span>{{$karyawan->attendance->attendance_description}}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bootstrap Table with Header - Light -->
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12 mb-3 order-0">
        <div class="card" style="box-shadow:none">
            <!-- Bootstrap Table with Header - Light -->
            <div class="card-header row pt-2 pb-1">
                <div class="col-lg-12">
                <h6 class="text-uppercase p-2 m-0 text-bold text-warning"><i class="bx bx-money" style="margin-right:10px"></i> Data Penggajian</h6>
                </div>
            </div>
            <hr class="my-1">
            <div class="card-body  pb-2">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_salary" class="col-sm-6 lbl-req text-bold">Gaji Pokok <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>Rp {{ number_format($karyawan->karyawan_salary, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_enid" class="col-sm-6 lbl-req text-bold">Tunjangan <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                @if($tunjangan)
                                <ul class="list-group">
                                    @foreach($tunjangan as $tj)
                                    <li class="list-group-item d-flex align-items-center pl-0 " style="border:0px;padding:0px;">
                                    {{$tj->sttunjangankaryawan_name}} 
                                    </li>
                                    @endforeach
                                </ul> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_enid" class="col-sm-6 lbl-req text-bold">Potongan <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                @if($potongan)
                                <ul class="list-group">
                                    @foreach($potongan as $pt)
                                    <li class="list-group-item d-flex align-items-center pl-0 " style="border:0px;padding:0px;">
                                    {{$pt->stpotongankaryawan_name}} 
                                    </li>
                                    @endforeach
                                </ul> 
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_enid" class="col-sm-6 lbl-req text-bold">Penggajian <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                @if($karyawan->penggajian)
                                    <span>{{$karyawan->penggajian->stpenggajiankaryawan_name}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_enid" class="col-sm-6 lbl-req text-bold">Metode PPh 21 <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->karyawan_calculation_method}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_enid" class="col-sm-6 lbl-req text-bold">Nama Bank <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>
                                    {{ $karyawan->bank->bank_name ?? '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_enid" class="col-sm-6 lbl-req text-bold">No. Rekening <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>
                                    {{ $karyawan->karyawan_bankno ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="divider divider-info">
                    <div class="divider-text text-bold">BPJS Kesehatan</div>
                </div>

                
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_enid" class="col-sm-6 lbl-req text-bold">Mendapatkan BPJS KS<span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                @if($karyawan->karyawan_isbpjskes == 1)
                                    <span>Ya</span>
                                @else
                                    <span>Tidak</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_enid" class="col-sm-6 lbl-req text-bold">No Kartu <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->karyawan_bpjskesno ?? '-'}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_enid" class="col-sm-6 lbl-req text-bold">Tanggal Berlaku <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$dt_bpjskesdate ?? '-'}}</span>
                            </div>
                        </div>
                    </div>
                </div>

                

                <div class="divider divider-info">
                    <div class="divider-text text-bold">BPJS Tenaga Kerja</div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_enid" class="col-sm-6 lbl-req text-bold">Mendapatkan BPJS TK<span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                @if($karyawan->karyawan_isbpjstk == 1)
                                    <span>Ya</span>
                                @else
                                    <span>Tidak</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_enid" class="col-sm-6 lbl-req text-bold">No Kartu <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$karyawan->karyawan_bpjstkno ?? '-'}}</span>
                            </div>
                        </div>
                        <div class="form-group row pt-1" style="padding-left:10px;">
                            <label for="karyawan_enid" class="col-sm-6 lbl-req text-bold">Tanggal Berlaku <span style="float:right">:</span></label>
                            <div class="col-sm-6">
                                <span>{{$dt_bpjstkdate ?? '-'}}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Bootstrap Table with Header - Light -->
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12 mb-3 order-0">
        <div class="card" style="box-shadow:none">
            <!-- Bootstrap Table with Header - Light -->
            <div class="card-header row pt-2 pb-1">
                <div class="col-lg-12">
                <h6 class="text-uppercase p-2 m-0 text-bold text-warning"><i class="bx bx-message-square-add" style="margin-right:10px"></i> Data Tambahan</h6>
                </div>
            </div>
            <hr class="my-1">
            <div class="card-body  pb-2">
                <div class="row">
                    <div class="col-sm-6">
                        <div id="formtambahanbox">
                            <?php 
                            $fields = null;
                            if($karyawan_infofield) :
                                $i = 0;
                                $fields = ($karyawan_infofield->karyawaninfofield_fields) ? explode(',', $karyawan_infofield->karyawaninfofield_fields) : null;
                                if($fields) :
                                    foreach($fields as $ifield) :
                                        $idx = (array_keys($additional_arr, $ifield)) ? array_keys($additional_arr, $ifield)[0] : null;
                                        $idxfieldvalue = isset($additional_info[$idx]) ? $additional_info[$idx]->keterangan : null;
                            ?>
                            <div class="form-group row pt-1" style="padding-left:10px;">
                                <label for="karyawan_enid" class="col-sm-6 lbl-req text-bold">{{$ifield}} <span style="float:right">:</span></label>
                                <div class="col-sm-6">
                                    <span>{{$idxfieldvalue}}</span>
                                </div>
                            </div>
                            <?php $i++;
                                    endforeach;
                                endif;
                            endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bootstrap Table with Header - Light -->
        </div>
    </div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>