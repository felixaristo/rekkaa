<?php
namespace App\Http\Controllers\User\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\BankModel;
use App\Model\Master\CountryModel;
use App\Model\Master\ProvinceModel;
use App\Model\Master\RegencyModel;
use App\Model\Master\WajibPajakTradeExchangeModel;
use App\Model\Transaction\HistoryImportModel;
use Illuminate\Http\Request;
use Error;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DOMDocument;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Cell\DataType as PhpSpreadsheetDataType;
use PhpOffice\PhpSpreadsheet\Writer\Xls;


class LawanTransaksiController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $data = [
            'title' => 'Data Lawan Transaksi',
            'content' => 'user.master.lawan-transaksi.index',
        ];
        // dd($calculate_pph21 / 12);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
        $search = (isset($request->input('search')['value']) && $request->input('search')['value']) ? $request->input('search')['value'] : null;
        $order_columns = ['wajibpajaktradeexchange_enid','wajibpajaktradeexchange_fullname', 'wajibpajaktradeexchange_main_number','wajibpajaktradeexchange_email','wajibpajaktradeexchange_main_pic_name','wajibpajaktradeexchange_is_supplier', 'wajibpajaktradeexchange_status'];
        $order_col = $order_columns[0];//(isset($request->input('order')[0]) && isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : null;
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $where = [
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];

        if($request->input('kategori') === 'penyedia') {
            $where['wajibpajaktradeexchange_is_supplier'] = TRUE;
        } else if($request->input('kategori') === 'pelanggan') {
            $where['wajibpajaktradeexchange_is_customer'] = TRUE;
        } 

        if($request->input('status') === 'active') {
            $where['wajibpajaktradeexchange_active'] = TRUE;
        } else if($request->input('status') === 'nonactive') {
            $where['wajibpajaktradeexchange_active'] = FALSE;
        } 


        $list = WajibPajakTradeExchangeModel::select('ms_wajib_pajak_trade_exchange.*')
        ->when($search, function($q, $search) {
            return $q->where(DB::raw('LOWER(wajibpajaktradeexchange_fullname)'), 'like', '%'.strtolower($search).'%');
        })
        ->where($where)
        ->take($limit)->skip($offset)->orderBy($order_col, $order_type)->get();

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = WajibPajakTradeExchangeModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function create(Request $request)
    {
        $data = [
            'title' => 'Lawan Transaksi Baru',
            'content' => 'user.master.lawan-transaksi.create'
        ];

        // dd($data);

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function edit($tradeexchangeId, Request $request)
    {
        $datatradeexchange = WajibPajakTradeExchangeModel::with([
            'billing_country',
            'billing_province',
            'billing_regency',
            'shipping_country',
            'shipping_province',
            'shipping_regency',
            'bank',
        ])->where('wajibpajaktradeexchange_id', $tradeexchangeId)->first();

        // dd($datatradeexchange);
        
        $data = [
            'wajibpajaktradeexchange' => $datatradeexchange,
            'title' => 'Ubah Lawan Transaksi',
            'content' => 'user.master.lawan-transaksi.edit'
        ];

        // dd($data);

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'wajibpajaktradeexchange_enid' => 'required|string',
                'wajibpajaktradeexchange_fullname' => 'required|string',
                'wajibpajaktradeexchange_email' => 'required|email',
                'wajibpajaktradeexchange_main_number' => 'required|string',
                'wajibpajaktradeexchange_main_pic_name' => 'required|string',
                'wajibpajaktradeexchange_main_pic_email' => 'required|email',
                'wajibpajaktradeexchange_main_pic_number' => 'required|string',
                'wajibpajaktradeexchange_billing_address' => 'required|string',
                'wajibpajaktradeexchange_billing_countries' => 'required|string',
                'wajibpajaktradeexchange_billing_province' => 'required|string',
                'wajibpajaktradeexchange_billing_city' => 'required|string',
                'wajibpajaktradeexchange_billing_postal_code' => 'required|string',
                'wajibpajaktradeexchange_is_billing_shipping' => 'required|boolean',
                'ms_bank_id' => 'required|string',
                'wajibpajaktradeexchange_bank_account' => 'required|string',
                'wajibpajaktradeexchange_bank_number' => 'required|string',
                'wajibpajaktradeexchange_npwp' => 'required|string',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            
            $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

            $checkExternalId = WajibPajakTradeExchangeModel::where('wajibpajaktradeexchange_enid', $request->input('wajibpajaktradeexchange_enid'))->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->first();

            if($checkExternalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID Lawan Transaksi sudah digunakan.'
                ], 400); 
            }

            // dd($request->input());

            DB::beginTransaction();
            try {
                $createCustomer = WajibPajakTradeExchangeModel::create([
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'wajibpajaktradeexchange_enid' => $request->input('wajibpajaktradeexchange_enid'),
                    'wajibpajaktradeexchange_fullname' => $request->input('wajibpajaktradeexchange_fullname'),
                    'wajibpajaktradeexchange_email' => $request->input('wajibpajaktradeexchange_email'),
                    'wajibpajaktradeexchange_main_number' => $request->input('wajibpajaktradeexchange_main_number'),
                    'wajibpajaktradeexchange_additional_number' => $request->input('wajibpajaktradeexchange_additional_number'),
                    'wajibpajaktradeexchange_fax' => $request->input('wajibpajaktradeexchange_fax'),
                    'wajibpajaktradeexchange_main_pic_name' => $request->input('wajibpajaktradeexchange_main_pic_name'),
                    'wajibpajaktradeexchange_main_pic_email' => $request->input('wajibpajaktradeexchange_main_pic_email'),
                    'wajibpajaktradeexchange_main_pic_number' => $request->input('wajibpajaktradeexchange_main_pic_number'),
                    'wajibpajaktradeexchange_billing_address' => $request->input('wajibpajaktradeexchange_billing_address'),
                    'wajibpajaktradeexchange_billing_countries' => $request->input('wajibpajaktradeexchange_billing_countries'),
                    'wajibpajaktradeexchange_billing_province' => $request->input('wajibpajaktradeexchange_billing_province'),
                    'wajibpajaktradeexchange_billing_city' => $request->input('wajibpajaktradeexchange_billing_city'),
                    'wajibpajaktradeexchange_billing_postal_code' => $request->input('wajibpajaktradeexchange_billing_postal_code'),
                    'wajibpajaktradeexchange_is_billing_shipping' => $request->input('wajibpajaktradeexchange_is_billing_shipping'),
                    'wajibpajaktradeexchange_shipping_address' => $request->input('wajibpajaktradeexchange_shipping_address'),
                    'wajibpajaktradeexchange_shipping_countries' => $request->input('wajibpajaktradeexchange_shipping_countries'),
                    'wajibpajaktradeexchange_shipping_province' => $request->input('wajibpajaktradeexchange_shipping_province'),
                    'wajibpajaktradeexchange_shipping_city' => $request->input('wajibpajaktradeexchange_shipping_city'),
                    'wajibpajaktradeexchange_shipping_postal_code' => $request->input('wajibpajaktradeexchange_shipping_postal_code'),
                    'wajibpajaktradeexchange_note' => $request->input('wajibpajaktradeexchange_note'),
                    'ms_bank_id' => $request->input('ms_bank_id'),
                    'wajibpajaktradeexchange_bank_account' => $request->input('wajibpajaktradeexchange_bank_account'),
                    'wajibpajaktradeexchange_bank_number' => $request->input('wajibpajaktradeexchange_bank_number'),
                    'wajibpajaktradeexchange_npwp' => $request->input('wajibpajaktradeexchange_npwp'),
                    'wajibpajaktradeexchange_is_supplier' => $request->input('wajibpajaktradeexchange_is_supplier'),
                    'wajibpajaktradeexchange_is_customer' => $request->input('wajibpajaktradeexchange_is_customer'),
                ]);
                
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil menambahkan data Lawan Transaksi.',
                    'data' => $createCustomer
                ], 200);
            } catch (Error $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
                
           
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'wajibpajaktradeexchange_id' => 'required',
                'wajibpajaktradeexchange_enid' => 'required|string',
                'wajibpajaktradeexchange_fullname' => 'required|string',
                'wajibpajaktradeexchange_email' => 'required|email',
                'wajibpajaktradeexchange_main_number' => 'required|string',
                'wajibpajaktradeexchange_main_pic_name' => 'required|string',
                'wajibpajaktradeexchange_main_pic_email' => 'required|email',
                'wajibpajaktradeexchange_main_pic_number' => 'required|string',
                'wajibpajaktradeexchange_billing_address' => 'required|string',
                'wajibpajaktradeexchange_billing_countries' => 'required|string',
                'wajibpajaktradeexchange_billing_province' => 'required|string',
                'wajibpajaktradeexchange_billing_city' => 'required|string',
                'wajibpajaktradeexchange_billing_postal_code' => 'required|string',
                'wajibpajaktradeexchange_is_billing_shipping' => 'required|boolean',
                'ms_bank_id' => 'required|string',
                'wajibpajaktradeexchange_bank_account' => 'required|string',
                'wajibpajaktradeexchange_bank_number' => 'required|string',
                'wajibpajaktradeexchange_npwp' => 'required|string',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            
            $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

            $checkId = WajibPajakTradeExchangeModel::where('wajibpajaktradeexchange_id', $request->input('wajibpajaktradeexchange_id'))->first();

            if(!$checkId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Lawan Transaksi tidak ditemukan.'
                ], 404); 
            }

            $checkExternalId = WajibPajakTradeExchangeModel::where('wajibpajaktradeexchange_enid', $request->input('wajibpajaktradeexchange_enid'))
                ->where('ms_wajibpajak_id', $wajibpajak_id)
                ->where('wajibpajaktradeexchange_id', '!=', $request->input('wajibpajaktradeexchange_id'))
                ->first();

            if($checkExternalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID Lawan Transaksi sudah digunakan.'
                ], 400); 
            }

            // dd($request->input());

            DB::beginTransaction();
            try {
                $checkId->update([
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'wajibpajaktradeexchange_enid' => $request->input('wajibpajaktradeexchange_enid'),
                    'wajibpajaktradeexchange_fullname' => $request->input('wajibpajaktradeexchange_fullname'),
                    'wajibpajaktradeexchange_email' => $request->input('wajibpajaktradeexchange_email'),
                    'wajibpajaktradeexchange_main_number' => $request->input('wajibpajaktradeexchange_main_number'),
                    'wajibpajaktradeexchange_additional_number' => $request->input('wajibpajaktradeexchange_additional_number'),
                    'wajibpajaktradeexchange_fax' => $request->input('wajibpajaktradeexchange_fax'),
                    'wajibpajaktradeexchange_main_pic_name' => $request->input('wajibpajaktradeexchange_main_pic_name'),
                    'wajibpajaktradeexchange_main_pic_email' => $request->input('wajibpajaktradeexchange_main_pic_email'),
                    'wajibpajaktradeexchange_main_pic_number' => $request->input('wajibpajaktradeexchange_main_pic_number'),
                    'wajibpajaktradeexchange_billing_address' => $request->input('wajibpajaktradeexchange_billing_address'),
                    'wajibpajaktradeexchange_billing_countries' => $request->input('wajibpajaktradeexchange_billing_countries'),
                    'wajibpajaktradeexchange_billing_province' => $request->input('wajibpajaktradeexchange_billing_province'),
                    'wajibpajaktradeexchange_billing_city' => $request->input('wajibpajaktradeexchange_billing_city'),
                    'wajibpajaktradeexchange_billing_postal_code' => $request->input('wajibpajaktradeexchange_billing_postal_code'),
                    'wajibpajaktradeexchange_is_billing_shipping' => $request->input('wajibpajaktradeexchange_is_billing_shipping'),
                    'wajibpajaktradeexchange_shipping_address' => $request->input('wajibpajaktradeexchange_shipping_address'),
                    'wajibpajaktradeexchange_shipping_countries' => $request->input('wajibpajaktradeexchange_shipping_countries'),
                    'wajibpajaktradeexchange_shipping_province' => $request->input('wajibpajaktradeexchange_shipping_province'),
                    'wajibpajaktradeexchange_shipping_city' => $request->input('wajibpajaktradeexchange_shipping_city'),
                    'wajibpajaktradeexchange_shipping_postal_code' => $request->input('wajibpajaktradeexchange_shipping_postal_code'),
                    'wajibpajaktradeexchange_note' => $request->input('wajibpajaktradeexchange_note'),
                    'ms_bank_id' => $request->input('ms_bank_id'),
                    'wajibpajaktradeexchange_bank_account' => $request->input('wajibpajaktradeexchange_bank_account'),
                    'wajibpajaktradeexchange_bank_number' => $request->input('wajibpajaktradeexchange_bank_number'),
                    'wajibpajaktradeexchange_npwp' => $request->input('wajibpajaktradeexchange_npwp'),
                    'wajibpajaktradeexchange_is_supplier' => $request->input('wajibpajaktradeexchange_is_supplier'),
                    'wajibpajaktradeexchange_is_customer' => $request->input('wajibpajaktradeexchange_is_customer'),
                ]);
                
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil merubah data Lawan Transaksi.',
                ], 200);
            } catch (Error $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
                
           
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deactive(Request $request)
    {
        try {
            $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

            $checkId = WajibPajakTradeExchangeModel::where('wajibpajaktradeexchange_id', $request->input('wajibpajaktradeexchange_id'))->where('ms_wajibpajak_id', $wajibpajak_id)->first();

            if(!$checkId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Lawan Transaksi tidak ditemukan.'
                ], 400); 
            }

            DB::beginTransaction();
            try {
                $checkId->update(['wajibpajaktradeexchange_active' => false]);
                
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil menonaktifkan data Lawan Transaksi.',
                ], 200);
            } catch (Error $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
                
           
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function reactive(Request $request)
    {
        try {
            $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

            $checkId = WajibPajakTradeExchangeModel::where('wajibpajaktradeexchange_id', $request->input('wajibpajaktradeexchange_id'))->where('ms_wajibpajak_id', $wajibpajak_id)->first();

            if(!$checkId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Lawan Transaksi tidak ditemukan.'
                ], 400); 
            }

            DB::beginTransaction();
            try {
                $checkId->update(['wajibpajaktradeexchange_active' => true]);
                
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil mengaktifkan data Lawan Transaksi.',
                ], 200);
            } catch (Error $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
                
           
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importTemplate(Request $request)
    {
        try {
            // Get the file path from the 'public' directory
            $excelFilePath = public_path('assets/import/Template_Import_LawanTransaksi.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $spreadsheet = $reader->load($excelFilePath);

            $boldFontStyle = [
                'font' => ['bold' => true],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];

            $sheetTemplate = $spreadsheet->createSheet();
            $sheetTemplate->setTitle('Impor Lawan Transaksi');
            $sheetTemplate->setCellValue('A1', 'Profil & Kontak');
            $sheetTemplate->mergeCells('A1:H1');
            $sheetTemplate->getStyle('A1')->applyFromArray($boldFontStyle);
            $sheetTemplate->setCellValue('I1', 'Alamat Lawan Transaksi');
            $sheetTemplate->mergeCells('I1:L1');
            $sheetTemplate->getStyle('I1')->applyFromArray($boldFontStyle);
            $sheetTemplate->setCellValue('N1', 'Info Keuangan');
            $sheetTemplate->mergeCells('N1:Q1');
            $sheetTemplate->getStyle('N1')->applyFromArray($boldFontStyle);
            // Center align cell A1
            $sheetTemplate->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Center align cell I1
            $sheetTemplate->getStyle('I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Center align cell N1
            $sheetTemplate->getStyle('N1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheetTemplate->setCellValue('A2', 'Id Lawan Transaksi*');
            $sheetTemplate->setCellValue('B2', 'Nama Lawan Transaksi*');
            $sheetTemplate->setCellValue('C2', 'Kategori Lawan Transaksi*');
            $sheetTemplate->setCellValue('D2', 'Email Lawan Transaksi*');
            $sheetTemplate->setCellValue('E2', 'No. Tlp Lawan Transaksi*');
            $sheetTemplate->setCellValue('F2', 'Nama PIC*');
            $sheetTemplate->setCellValue('G2', 'Email PIC*');
            $sheetTemplate->setCellValue('H2', 'No. Tlp PIC*');
            $sheetTemplate->setCellValue('I2', 'Alamat Lengkap*');
            $sheetTemplate->setCellValue('J2', 'Id Negara*');
            $sheetTemplate->setCellValue('K2', 'Id Provinsi');
            $sheetTemplate->setCellValue('L2', 'Id Kota');
            $sheetTemplate->setCellValue('M2', 'Kodepos');
            $sheetTemplate->setCellValue('N2', 'Kode Bank*');
            $sheetTemplate->setCellValue('O2', 'Nama Pemilik Bank*');
            $sheetTemplate->setCellValue('P2', 'Nomor Rekening*');
            $sheetTemplate->setCellValue('Q2', 'Nomor NPWP*');

            for ($col = 'A'; $col <= 'Q'; $col++) {
                $sheetTemplate->getStyle($col . '2')->applyFromArray($boldFontStyle);
                $sheetTemplate->getStyle($col . ':' . $col)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            }

            $startRow = 3;

            $listCategory = '"Penyedia,Pelanggan,Penyedia & Pelanggan"';
            $categoryValidation = $sheetTemplate->getCell('C' . $startRow)->getDataValidation();
            $categoryValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $categoryValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $categoryValidation->setShowDropDown(true);
            $categoryValidation->setFormula1($listCategory);

            for ($row = $startRow + 1; $row <= 52; $row++) {
                $cellC = $sheetTemplate->getCell('C' . $row);
                $cellC->setDataValidation(clone $categoryValidation);
            }

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                
                // Set all columns in the sheet to auto width
                if($sheetName !== 'Panduan Pengguna') {
                    foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }
            
            $writer = new Xls($spreadsheet);
            $unixtime = time();
            $filename = 'Templat_Impor_LawanTransaksi_' . $unixtime . '.xls';
            $location = public_path('assets/export/');
            ob_start();
            $writer->save($location.$filename);
            // $xlsData = ob_get_contents();
            ob_end_clean();

            return url('/assets/export/'.$filename);
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importTradeExchange(Request $request)
    {
        if (!$request->hasfile('file')) {
            $res["success"] = false;
            $res["message"] = 'Data excel wajib diisi!';
            return response()->json($res, 500);
        }

        $file = $request->file('file');

        if(!in_array($file->extension(), ['xls','xlsx','csv'])) {
            $res["success"] = false;
            $res["message"] = 'Format harus xls, xlsx atau csv!';
            return response()->json($res, 500);
        }
    
        $originalFileName = $file->getClientOriginalName();

        $filename = rand().'.xlsx';
        $file->move(public_path('uploads/'), $filename);
        $path = public_path('uploads/'.$filename);

        $inputFileType = IOFactory::identify($path);
        $reader = IOFactory::createReader($inputFileType);
        $worksheetData = $reader->listWorksheetInfo($path);

        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
    
        // Get the Sheet D
        $sheet = $spreadsheet->getSheetByName('Impor Lawan Transaksi');
        $maxRows = $sheet->getHighestRow();
        // dd($maxRows);
        // if($maxRows > $quota['maxquota']) {
        //     return response()->json([
        //         'success' => false,
        //         'noquota' => true,
        //         'message' => 'Kuota sudah habis! kuota maksimal adalah '.$quota['maxquota']
        //     ]);
        // }

        $chunkFilter = new ChunkReadFilter();
        $reader->setReadFilter($chunkFilter);

        $chunkSize = 100; // read as chunk
        $startRow = 2; // mulai baris ke 3;

        $result = [];

        for ($startRow; $startRow <= $maxRows; $startRow += $chunkSize) {
            $chunkFilter = new ChunkReadFilter($startRow, $chunkSize);
            // // Tell the Read Filter, the limits on which rows we want to read this iteration
            $chunkFilter->setRows($startRow, $chunkSize);
            // Load only the rows that match our filter from $inputFileName to a PhpSpreadsheet Object
            $spreadsheet_chunk = $reader->load($path);
            $nb = $startRow;
            $max_chunk = $startRow + $chunkSize;

            for($nb; $nb<=$max_chunk; $nb++) {
                $nbi = $nb + 1;
                $columnA = $sheet->getCell("A" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $dataB = $sheet->getCell("B" . $nbi)->getValue();

                if($columnA === null) {
                    if($dataB === null) {
                        break;
                    } else {
                        $spreadsheet_chunk->__destruct();
                        $spreadsheet_chunk = null;
                        unset($spreadsheet_chunk);
                        
                        return response()->json([
                            'success' => false,
                            'message' => "Mohon lengkapi Id Lawan Transaksi yang masih kosong"
                        ]);
                    }
                }

                $columnB = $dataB;
                $columnC = $sheet->getCell("C" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnD = $sheet->getCell("D" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnE = $sheet->getCell("E" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnF = $sheet->getCell("F" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnG = $sheet->getCell("G" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnH = $sheet->getCell("H" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnI = $sheet->getCell("I" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnJ = $sheet->getCell("J" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnK = $sheet->getCell("K" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnL = $sheet->getCell("L" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnM = $sheet->getCell("M" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnN = $sheet->getCell("N" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnO = $sheet->getCell("O" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnP = $sheet->getCell("P" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnQ = $sheet->getCell("Q" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
               


                $result[] = [
                    "tradeexchange_enid" => $columnA,
                    "tradeexchange_fullname" => $columnB,
                    "tradeexchange_category" => $columnC,
                    "tradeexchange_email" => $columnD,
                    "tradeexchange_main_number" => $columnE,
                    "tradeexchange_main_pic_name" => $columnF,
                    "tradeexchange_main_pic_email" => $columnG,
                    "tradeexchange_main_pic_number" => $columnH,
                    "tradeexchange_billing_address" => $columnI,
                    "tradeexchange_billing_countries" => $columnJ,
                    "tradeexchange_billing_province" => $columnK,
                    "tradeexchange_billing_city" => $columnL,
                    "tradeexchange_billing_postal_code" => $columnM,
                    "ms_bank_id" => $columnN,
                    "tradeexchange_bank_account" => $columnO,
                    "tradeexchange_bank_number" => $columnP,
                    "tradeexchange_npwp" => $columnQ,
                    "row" => $nb + 1
                ];
            }

            $spreadsheet_chunk->__destruct();
            $spreadsheet_chunk = null;
            unset($spreadsheet_chunk);
        }
        // then release the memory
        $spreadsheet->__destruct();
        $spreadsheet = null;
        unset($spreadsheet);
        $reader = null;
        unset($reader);

        
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Berhasil import data non karyawan.',
        //     'result' => $result
        // ], 200);

        DB::beginTransaction();
        try {
            $totalComplete = 0;
            $totalFail = 0;
            $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

            if(count($result) > 0) {
                $checkBank = BankModel::all();
                $checkCountry = CountryModel::all();
                $checkProvince = ProvinceModel::all();
                $checkCity = RegencyModel::all();

                for($x = 0; $x < count($result); $x++) {
                    if ($result[$x]['tradeexchange_enid'] === null || $result[$x]['tradeexchange_enid'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Id Lawan Transaksi';
                        continue;
                    }

                    $checkLawanTransaksi = WajibPajakTradeExchangeModel::where('wajibpajaktradeexchange_enid', $result[$x]['tradeexchange_enid'])->where('ms_wajibpajak_id', $wajibpajak_id)->first();
    
                    if($checkLawanTransaksi) {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Id Lawan Transaksi sudah terdaftar';
                        continue;
                    }

                    if ($result[$x]['tradeexchange_fullname'] === null || $result[$x]['tradeexchange_fullname'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Nama Lawan Transaksi';
                        continue;
                    }

                    $isPenyedia = false;
                    $isPelanggan = false;

                    if ($result[$x]['tradeexchange_category'] !== 'Penyedia' && $result[$x]['tradeexchange_category'] !== 'Pelanggan' && $result[$x]['tradeexchange_category'] !== 'Penyedia & Pelanggan') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Jenis Kelamin hanya dengan Penyedia / Pelanggan / Penyedia & Pelanggan';
                        continue;
                    } else {
                        if($result[$x]['tradeexchange_category'] === 'Penyedia') {
                            $isPenyedia = true;
                        }

                        if($result[$x]['tradeexchange_category'] === 'Pelanggan') {
                            $isPelanggan = true;
                        }

                        if($result[$x]['tradeexchange_category'] === 'Penyedia & Pelanggan') {
                            $isPelanggan = true;
                            $isPenyedia = true;
                        }
                    }

                    if ($result[$x]['tradeexchange_email'] === null || $result[$x]['tradeexchange_email'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Email Lawan Transaksi';
                        continue;
                    }

                    if ($result[$x]['tradeexchange_main_number'] === null || $result[$x]['tradeexchange_main_number'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Nomor Tlp Lawan Transaksi';
                        continue;
                    }

                    if ($result[$x]['tradeexchange_billing_address'] === null || $result[$x]['tradeexchange_billing_address'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Alamat Lengkap';
                        continue;
                    }

                    if ($result[$x]['tradeexchange_billing_countries'] === null || $result[$x]['tradeexchange_billing_countries'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Id Negara';
                        continue;
                    } else {
                        $found = false;

                        foreach ($checkCountry as $item) {
                            if (isset($item['country_id']) && $item['country_id'] == $result[$x]['tradeexchange_billing_countries']) {
                                $found = true;
                                break; // Exit the loop when a match is found.
                            }
                        }
    
                        if($found === false) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan Id Negara sesuai sheet Referensi Data Negara';
                            continue;
                        }
                    }

                    if ($result[$x]['tradeexchange_billing_province'] !== null && $result[$x]['tradeexchange_billing_province'] !== '' && $result[$x]['tradeexchange_billing_countries'] == 100) {
                        $found = false;

                        foreach ($checkProvince as $item) {
                            if (isset($item['province_id']) && $item['province_id'] == $result[$x]['tradeexchange_billing_province']) {
                                $found = true;
                                break; // Exit the loop when a match is found.
                            }
                        }
    
                        if($found === false) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan Id Provinsi sesuai sheet Referensi Data Provinsi';
                            continue;
                        }
                    }

                    if ($result[$x]['tradeexchange_billing_city'] !== null && $result[$x]['tradeexchange_billing_city'] !== '' && $result[$x]['tradeexchange_billing_countries'] == 100) {
                        $found = false;

                        foreach ($checkCity as $item) {
                            if (isset($item['regency_id']) && $item['regency_id'] == $result[$x]['tradeexchange_billing_city']) {
                                $found = true;
                                break; // Exit the loop when a match is found.
                            }
                        }
    
                        if($found === false) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan Id Kota sesuai sheet Referensi Data Kota';
                            continue;
                        }
                    }

                    if($result[$x]['ms_bank_id'] !== null && $result[$x]['ms_bank_id'] !== '') {
                        $found = false;

                        foreach ($checkBank as $item) {
                            if (isset($item['bank_id']) && $item['bank_id'] == $result[$x]['ms_bank_id']) {
                                $found = true;
                                break; // Exit the loop when a match is found.
                            }
                        }
    
                        if($found === false) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan Kode Bank sesuai sheet Referensi Kode Bank';
                            continue;
                        }
                    }
                    
                    if ($result[$x]['tradeexchange_bank_account'] === null || $result[$x]['tradeexchange_bank_account'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Nama Pemilik';
                        continue;
                    }

                    if ($result[$x]['tradeexchange_bank_number'] === null || $result[$x]['tradeexchange_bank_number'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Nomor Rekening';
                        continue;
                    }

                    if ($result[$x]['tradeexchange_npwp'] !== null && $result[$x]['tradeexchange_npwp'] !== '') {
                        if(!preg_match('/^\d{2}\.\d{3}\.\d{3}\.\d{1}-\d{3}\.\d{3}$/', $result[$x]['tradeexchange_npwp'])) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan NPWP dengan benar';
                            continue;
                        }
                    } else {
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan nomor NPWP';
                        continue;
                    }

                    WajibPajakTradeExchangeModel::create([
                        'ms_wajibpajak_id' => $wajibpajak_id,
                        'wajibpajaktradeexchange_enid' => $result[$x]['tradeexchange_enid'],
                        'wajibpajaktradeexchange_fullname' => $result[$x]['tradeexchange_fullname'],
                        'wajibpajaktradeexchange_email' => $result[$x]['tradeexchange_email'],
                        'wajibpajaktradeexchange_main_number' => $result[$x]['tradeexchange_main_number'],
                        'wajibpajaktradeexchange_main_pic_name' => $result[$x]['tradeexchange_main_pic_name'],
                        'wajibpajaktradeexchange_main_pic_email' => $result[$x]['tradeexchange_main_pic_email'],
                        'wajibpajaktradeexchange_main_pic_number' => $result[$x]['tradeexchange_main_pic_number'],
                        'wajibpajaktradeexchange_billing_address' => $result[$x]['tradeexchange_billing_address'],
                        'wajibpajaktradeexchange_billing_countries' => $result[$x]['tradeexchange_billing_countries'],
                        'wajibpajaktradeexchange_billing_province' => $result[$x]['tradeexchange_billing_province'],
                        'wajibpajaktradeexchange_billing_city' => $result[$x]['tradeexchange_billing_city'],
                        'wajibpajaktradeexchange_billing_postal_code' => $result[$x]['tradeexchange_billing_postal_code'],
                        'wajibpajaktradeexchange_is_billing_shipping' => FALSE,
                        'ms_bank_id' => $result[$x]['ms_bank_id'],
                        'wajibpajaktradeexchange_bank_account' => $result[$x]['tradeexchange_bank_account'],
                        'wajibpajaktradeexchange_bank_number' => $result[$x]['tradeexchange_bank_number'],
                        'wajibpajaktradeexchange_npwp' => $result[$x]['tradeexchange_npwp'],
                        'wajibpajaktradeexchange_is_supplier' => $isPenyedia,
                        'wajibpajaktradeexchange_is_customer' => $isPelanggan,
                    ]);
    
                    $totalComplete = $totalComplete + 1;
                    $result[$x]['status'] = 'Sukses';
                    $result[$x]['remark'] = '-';
                }
            }

            $createHistory = HistoryImportModel::create([
                'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                'ms_user_id' => session()->get('user_data')['user_id'],
                'historyimport_type' => "LAWAN_TRANSAKSI",
                'historyimport_date' => date('Y-m-d H:i:s'),
                'historyimport_detail_import' => $totalComplete . ' Sukses, ' . $totalFail . ' Gagal', 
                'historyimport_content' => json_encode($result),
                'historyimport_file_name' => $originalFileName
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil import data lawan transaksi.',
                'result' => $result,
                'totalsuccess' => $totalComplete,
                'totalfail'=> $totalFail,
                'totaldata' => $totalComplete + $totalFail,
                'history' => $createHistory->historyimport_id
            ], 200);
        } catch (\Exception $e) {
            unlink(public_path('uploads/'). $filename); // remove file
            Log::error('Error occurred: ' . $e->getMessage());
            DB::rollback();
            // something went wrong
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function importHistory(Request $request)
    {
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
        
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        
        // Get the start_date and end_date inputs
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        
        $dataHistory = HistoryImportModel::where('historyimport_type', 'LAWAN_TRANSAKSI')
            ->where('tr_history_import.ms_wajibpajak_id', $wajibpajak_id)
            ->orderBy('historyimport_date', 'DESC')
            ->leftjoin('mr_user_wajib_pajak', 'tr_history_import.ms_user_id', '=', 'mr_user_wajib_pajak.ms_user_id')
            ->select('tr_history_import.historyimport_date', 'tr_history_import.ms_user_id', 'tr_history_import.historyimport_status', 'tr_history_import.historyimport_id', 'tr_history_import.historyimport_file_name', 'tr_history_import.historyimport_detail_import', 'mr_user_wajib_pajak.userwajibpajak_name');
        
        // Apply date filtering if start_date and end_date are provided
        if ($start_date && $end_date) {
            $dataHistory->whereBetween('tr_history_import.historyimport_date', [$start_date, $end_date]);
        }
        
        $dataHistory = $dataHistory->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data history.',
            'data' => $dataHistory->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $dataHistory->total(),
            'recordsTotal' => $dataHistory->total(),
        ], 200);
    }

    public function generateHistory(Request $request) 
    {
        try {
            // Get the file path from the 'public' directory
            $excelFilePath = public_path('assets/import/Template_Import_LawanTransaksi.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $unixtime = time();
            $title = 'Riwayat_Impor_LawanTransaksi_'.$unixtime;

            $spreadsheet = $reader->load($excelFilePath);

            $boldFontStyle = [
                'font' => ['bold' => true],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];

            $sheetImpor = $spreadsheet->createSheet();
            $sheetImpor->setTitle('Impor Lawan Transaksi');
            $sheetImpor->setCellValue('A1', 'Profil & Kontak');
            $sheetImpor->mergeCells('A1:H1');
            $sheetImpor->getStyle('A1')->applyFromArray($boldFontStyle);
            $sheetImpor->setCellValue('I1', 'Alamat Lawan Transaksi');
            $sheetImpor->mergeCells('I1:L1');
            $sheetImpor->getStyle('I1')->applyFromArray($boldFontStyle);
            $sheetImpor->setCellValue('N1', 'Info Keuangan');
            $sheetImpor->mergeCells('N1:Q1');
            $sheetImpor->getStyle('N1')->applyFromArray($boldFontStyle);
            $sheetImpor->setCellValue('R1', 'Info Keuangan');
            $sheetImpor->mergeCells('R1:S1');
            $sheetImpor->getStyle('R1')->applyFromArray($boldFontStyle);
            // Center align cell A1
            $sheetImpor->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Center align cell I1
            $sheetImpor->getStyle('I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Center align cell N1
            $sheetImpor->getStyle('N1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Center align cell R1
            $sheetImpor->getStyle('R1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheetImpor->setCellValue('A2', 'Id Lawan Transaksi*');
            $sheetImpor->setCellValue('B2', 'Nama Lawan Transaksi*');
            $sheetImpor->setCellValue('C2', 'Kategori Lawan Transaksi*');
            $sheetImpor->setCellValue('D2', 'Email Lawan Transaksi*');
            $sheetImpor->setCellValue('E2', 'No. Tlp Lawan Transaksi*');
            $sheetImpor->setCellValue('F2', 'Nama PIC*');
            $sheetImpor->setCellValue('G2', 'Email PIC*');
            $sheetImpor->setCellValue('H2', 'No. Tlp PIC*');
            $sheetImpor->setCellValue('I2', 'Alamat Lengkap*');
            $sheetImpor->setCellValue('J2', 'Id Negara*');
            $sheetImpor->setCellValue('K2', 'Id Provinsi');
            $sheetImpor->setCellValue('L2', 'Id Kota');
            $sheetImpor->setCellValue('M2', 'Kodepos');
            $sheetImpor->setCellValue('N2', 'Kode Bank*');
            $sheetImpor->setCellValue('O2', 'Nama Pemilik Bank*');
            $sheetImpor->setCellValue('P2', 'Nomor Rekening*');
            $sheetImpor->setCellValue('Q2', 'Nomor NPWP*');
            $sheetImpor->setCellValue('R2', 'Status');
            $sheetImpor->setCellValue('S2', 'Catatan');
            // $sheetImpor->getStyle('A2:AB2')->applyFromArray($boldFontStyle);

            $id = $request->input('id');

            $getHistory = HistoryImportModel::find($id);

            $content = json_decode($getHistory['historyimport_content']);

            $rowIndex = 3;  

            foreach($content as $item) {
                $listImpor = [
                    'A' => isset($item->tradeexchange_enid) ? $item->tradeexchange_enid : '',
                    'B' => isset($item->tradeexchange_fullname) ? $item->tradeexchange_fullname : '',
                    'C' => isset($item->tradeexchange_category) ? $item->tradeexchange_category : '',
                    'D' => isset($item->tradeexchange_email) ? $item->tradeexchange_email : '',
                    'E' => isset($item->tradeexchange_main_number) ? $item->tradeexchange_main_number : '',
                    'F' => isset($item->tradeexchange_main_pic_name) ? $item->tradeexchange_main_pic_name : '',
                    'G' => isset($item->tradeexchange_main_pic_email) ? $item->tradeexchange_main_pic_email : '',
                    'H' => isset($item->tradeexchange_main_pic_nummber) ? $item->tradeexchange_main_pic_nummber : '',
                    'I' => isset($item->tradeexchange_billing_address) ? $item->tradeexchange_billing_address : '',
                    'J' => isset($item->tradeexchange_billing_countries) ? $item->tradeexchange_billing_countries : '',
                    'K' => isset($item->tradeexchange_billing_province) ? $item->tradeexchange_billing_province : '',
                    'L' => isset($item->tradeexchange_billing_city) ? $item->tradeexchange_billing_city : '',
                    'M' => isset($item->tradeexchange_billing_postal_code) ? $item->tradeexchange_billing_postal_code : '',
                    'N' => isset($item->ms_bank_id) ? $item->ms_bank_id : '',
                    'O' => isset($item->tradeexchange_bank_account) ? $item->tradeexchange_bank_account : '',
                    "P" => isset($item->tradeexchange_bank_number) ? $item->tradeexchange_bank_number : '',
                    "Q" => isset($item->tradeexchange_npwp) ? $item->tradeexchange_npwp : '',
                    'R' => isset($item->status) ? $item->status : 'Sukses',
                    'S' => isset($item->remark) ? $item->remark : '-'
                ];

                foreach ($listImpor as $column => $value) {
                    $sheetImpor->setCellValueExplicit($column . $rowIndex, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }

                // Increment the row index for the next row of data
                $rowIndex++;
            }

            $startRow = 3;

            $listCategory = '"Penyedia,Pelanggan,Penyedia & Pelanggan"';
            $categoryValidation = $sheetImpor->getCell('C' . $startRow)->getDataValidation();
            $categoryValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $categoryValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $categoryValidation->setShowDropDown(true);
            $categoryValidation->setFormula1($listCategory);

            for ($row = $startRow + 1; $row <= 52; $row++) {
                $cellC = $sheetImpor->getCell('C' . $row);
                $cellC->setDataValidation(clone $categoryValidation);
            }
           
            for ($col = 'A'; $col <= 'S'; $col++) {
                $sheetImpor->getStyle($col . '2')->applyFromArray($boldFontStyle);
                $sheetImpor->getStyle($col . ':' . $col)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            }


            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                
                // Set all columns in the sheet to auto width
                if($sheetName !== 'Panduan Pengguna') {
                    foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }
            
            $writer = new Xls($spreadsheet);

            $filename = $title.'.xls';
            $location = public_path('assets/export/');
            ob_start();
            $writer->save($location.$filename);
            // $xlsData = ob_get_contents();
            ob_end_clean();

            return url('/assets/export/'.$filename);
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

class ChunkReadFilter implements IReadFilter
{
    private $startRow = 0;

    private $endRow = 0;

    /**
     * Set the list of rows that we want to read.
     *
     * @param mixed $startRow
     * @param mixed $chunkSize
     */
    public function setRows($startRow, $chunkSize)
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        //  Only read the heading row, and the rows that are configured in            $this->_startRow and $this->_endRow
        if (($row == 1) || ($row >= $this->startRow && $row <   $this->endRow)) {
            return true;
        }

        return false;
    }
}