<?php

use App\Model\Master\WajibPajakModel;
use App\Model\Setting\SettingModel;
use PhpOffice\PhpSpreadsheet\Cell\DataType as PhpSpreadsheetDataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpSpreadsheetDate;

function getSetting($settingKey = null) {
    if($settingKey) {
        if(is_array($settingKey)) {
            return SettingModel::where(['setting_active' => 1])->whereIn('setting_key', $settingKey)->get();
        } else {
            return SettingModel::where(['setting_key' => $settingKey, 'setting_active' => 1])->first();
        }
    } else {
        return SettingModel::where(['setting_active' => 1])->get();
    }
}

function getAkunNpwp() {
    if(!isset(session()->get('wajibpajak_current')['wajibpajak_id'])) {
        return null;
    }
    return WajibPajakModel::with(['wajibpajaksubscription', 'userorder', 'user'])->where(['wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'], 'wajibpajak_active' => 1])->first();
}

function getMonths() {
    return [
        1 => 'Januari',
        2 => 'Pebruari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'Nopember',
        12 => 'Desember',
    ];
}

function mask($str, $first, $last) {
    $len = strlen($str);
    $toShow = $first + $last;
    return substr($str, 0, $len <= $toShow ? 0 : $first).str_repeat("*", $len - ($len <= $toShow ? 0 : $toShow)).substr($str, $len - $last, $len <= $toShow ? 0 : $last);
}

function mask_email($email, $firstletter = 1, $lastletter = 0, $firstdomletter = 0, $lastdomletter = 0) {
    $mail_parts = explode("@", $email);
    $domain_parts = explode('.', $mail_parts[1]);

    $mail_parts[0] = mask($mail_parts[0], $firstletter, $lastletter); // show first 2 letters and last 1 letter
    $domain_parts[0] = mask($domain_parts[0], $firstdomletter, $lastdomletter); // same here
    $mail_parts[1] = implode('.', $domain_parts);

    return implode("@", $mail_parts);
}

function formatDate($data, $regex = '/^\d{4}-\d{2}-\d{2}$/') {
    if (is_string($data) && preg_match($regex, $data)) {
        // Date is in the format "DD Month YYYY"
        // Keep the date as is
        $dateString = true;
    }  else {
        // Handle cases where the date format is not as expected
        $dateString = false; // Set to an empty string or handle the error as needed
    }

    return $dateString;
}

function convertDateStringToYYYYMMDD($dateString) {
    if ($dateString === null || $dateString === '') {
        return null; // Return null for empty input
    } else {
        // Split the date string into day, month, and year
        list($day, $month, $year) = explode('-', $dateString);

        // Create a new date in the "YYYY-MM-DD" format
        $newDateString = "{$year}-{$month}-{$day}";

        return $newDateString;
    }
}

function capitalizeFirstLetterOfWords($string) {
    // Convert the string to lowercase
    $lowercaseString = strtolower($string);
    
    // Use ucwords() to capitalize the first letter of each word
    $capitalizedString = ucwords($lowercaseString);
    
    return $capitalizedString;
}

function convertExcelDate($cell) {
    $cellValue = $cell->getValue();

    if (PhpSpreadsheetDataType::TYPE_NUMERIC === $cell->getDataType()) {
        $dateObj = PhpSpreadsheetDate::excelToDateTimeObject($cellValue);

        return $dateObj->format('Y-m-d');
    } else {

        $dateObj = DateTime::createFromFormat('m-Y', $cellValue);
        return $dateObj ? $dateObj->format('Y-m-d') : $cellValue;
    }
}

function isNotCurrentMonth($dateString) {
    $currentMonthYear = date('Y-m', strtotime('now'));
    
    // Extract month and year from the input date
    $inputMonthYear = date('Y-m', strtotime($dateString));

    return $inputMonthYear !== $currentMonthYear;
}