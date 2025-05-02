<?php

function bpjsKaryawan($karyawan_bpjs, $karyawan_salary) {
    $data = [
        'penghasilan_kes_nominal' => 0,
        'penghasilan_tk_nominal' => 0,
    ];
    // dd($karyawan_bpjs);
    // $decode_karyawan_bpjs = ($karyawan_bpjs) ? json_decode($karyawan_bpjs) : null;
    if($karyawan_bpjs) {
        if(isset($karyawan_bpjs->TENAGA_KERJA) && $karyawan_bpjs->TENAGA_KERJA) {
            $setting_tk = (array) $karyawan_bpjs->TENAGA_KERJA;
            foreach($setting_tk as $key => $val) {
                if($key == 'LAINNYA') {
                    $data['penghasilan_tk_nominal'] = intval($karyawan_bpjs->TENAGA_KERJA->LAINNYA);
                    // dd($key);
                    break;
                } else if($key == 'GAJI_POKOK') {
                    $data['penghasilan_tk_nominal'] += $karyawan_salary;
                } else {
                    $data['penghasilan_tk_nominal'] += intval($val);
                }
            }
        }
        
        if(isset($karyawan_bpjs->KESEHATAN) && $karyawan_bpjs->KESEHATAN) {
            $setting_kes = (array) $karyawan_bpjs->KESEHATAN;
            foreach($setting_kes as $key => $val) {
                if($key == 'LAINNYA') {
                    $data['penghasilan_kes_nominal'] = intval($val);
                    break;
                } else if($key == 'GAJI_POKOK') {
                    $data['penghasilan_kes_nominal'] += $karyawan_salary;
                } else {
                    $data['penghasilan_kes_nominal'] += intval($val);
                }
            }
        }
    }
    return $data;
}