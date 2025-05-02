<?php
namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use App\Model\Master\WajibPajakUserModel;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\Model\Setting\SettingAnnouncementModel;
use App\User;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengumumanController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        try {
            $karyawan_id = session()->get('karyawan_data')['karyawan_id'];

            // $today = Carbon::today();
            $today = date('Y-m-d');
            
            $announcementSettings = SettingAnnouncementModel::whereDate('announcement_start_date', '<=', $today)
                ->whereDate('announcement_end_date', '>=', $today)
                ->get();
            $result = [];
            $totalUnread = 0;
            foreach ($announcementSettings as $setting) {
                $karyawanIdsArray = json_decode($setting->announcement_karyawan, true);
                $readIdsArray = json_decode($setting->announcement_karyawan_is_read, true);
                $clearIdsArray = json_decode($setting->announcement_karyawan_is_clear, true);
                // Check if $karyawan_id is in the array

                if($setting->announcement_is_all === true || in_array($karyawan_id, $karyawanIdsArray)) {
                    if (in_array(strval($karyawan_id).',0', $clearIdsArray)) {
                        // Add the record to the result array
                        $announcementDescription = strlen($setting->announcement_description) > 185 ?
                            substr($setting->announcement_description, 0, 182) . '...' :
                            $setting->announcement_description;
                        
                        if (in_array(strval($karyawan_id).',0', $readIdsArray)) {
                            $isRead = false;
                            $totalUnread++;
                        } else {
                            $isRead = true;
                        }
    
                        $result[] = [
                            'announcement_id' => $setting->announcement_id,
                            'announcement_title' => $setting->announcement_title,
                            'announcement_start_date' => $setting->announcement_start_date,
                            'announcement_description' => $setting->announcement_description,
                            'announcement_is_read' => $isRead,
                            'announcement_view_description' => $announcementDescription
                        ];
                    }
                }
            }

            usort($result, function ($a, $b) {
                if ($a['announcement_is_read'] === $b['announcement_is_read']) {
                    return 0;
                }
                return ($a['announcement_is_read'] === false) ? -1 : 1;
            });

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memuat data pengumuman.',
                'data' => $result,
                'unread' => $totalUnread
            ], 200);
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
            $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
            
            

            if($request->input('tipe') === 'READ') {
                $announcementSettings = SettingAnnouncementModel::find($request->input('announcement_id'));
                $readIdsArray = json_decode($announcementSettings->announcement_karyawan_is_read, true);

                $target = $karyawan_id . ',0';
                $replacement = $karyawan_id . ',1';

                for ($i = 0; $i < count($readIdsArray); $i++) {
                    if ($readIdsArray[$i] === $target) {
                        $readIdsArray[$i] = $replacement;
                        break;  // Exit the loop after the first replacement
                    }
                }

                $announcementSettings->update([
                    'announcement_karyawan_is_read' => $readIdsArray
                ]);

                
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil merubah data pengumuman.'
                ], 200);
            } else if($request->input('tipe') === 'CLEAR') {
                $announcementSettings = SettingAnnouncementModel::find($request->input('announcement_id'));
                $clearIdsArray = json_decode($announcementSettings->announcement_karyawan_is_clear, true);

                $target = $karyawan_id . ',0';
                $replacement = $karyawan_id . ',1';

                for ($i = 0; $i < count($clearIdsArray); $i++) {
                    if ($clearIdsArray[$i] === $target) {
                        $clearIdsArray[$i] = $replacement;
                        break;  // Exit the loop after the first replacement
                    }
                }

                $announcementSettings->update([
                    'announcement_karyawan_is_clear' => $clearIdsArray
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil menghapus pengumuman.'
                ], 200);
            } else if($request->input('tipe') === 'CLEARALL') {
                // $today = Carbon::today();
                $today = date('Y-m-d');
                $announcementSettings = SettingAnnouncementModel::whereDate('announcement_start_date', '<=', $today)
                    ->whereDate('announcement_end_date', '>=', $today)
                    ->get();

                foreach ($announcementSettings as $setting) {
                    // $karyawanIdsArray = json_decode($setting->announcement_karyawan, true);
                    $readIdsArray = json_decode($setting->announcement_karyawan_is_read, true);
                    $clearIdsArray = json_decode($setting->announcement_karyawan_is_clear, true);
                    // Check if $karyawan_id is in the array

                    if ($clearIdsArray !== null && in_array(strval($karyawan_id).',0', $clearIdsArray)) {
                        // Add the record to the result array
                        $target = $karyawan_id . ',0';
                        $replacement = $karyawan_id . ',1';

                        for ($i = 0; $i < count($clearIdsArray); $i++) {
                            if ($clearIdsArray[$i] === $target) {
                                $clearIdsArray[$i] = $replacement;
                                break;  // Exit the loop after the first replacement
                            }
                        }

                        $setting->update([
                            'announcement_karyawan_is_clear' => $clearIdsArray
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil menghapus pengumuman.'
                ], 200);
            }

        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}