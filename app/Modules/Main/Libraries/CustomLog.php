<?php


namespace App\Modules\Main\Libraries;

use Carbon\Carbon; 

class CustomLog
{

    /**
     * @param $log
     */
    private static function write($log, $subFolder = 'others')
    {
        try {
            if (config('common.custom_log.enabled')) {
                $folder_name = rtrim(config('common.custom_log.path'), '/') . '/' . date('Ym') . "/" . $subFolder . "/";

                if (!file_exists($folder_name)) {
                    mkdir($folder_name, 0775, true);
                }

                $date = Carbon::now()->format("YmdH");
                $filename = $folder_name . $date . ".log";
                $logtime = Carbon::now()->format("H:i:s");
                $logwritedata = "[" . $logtime . " ] " . $log . "\r\n";
                $fh = fopen($filename, 'a');
                fwrite($fh, $logwritedata);
                fclose($fh);
            }
        } catch (\Exception $exception) {
            //
        }
    }

    /**
     * @param $message
     */
    public static function info($log, $subfolder = 'others')
    {
        $log = "INFO:: " . $log;
        self::write($log, $subfolder);
    }

    /**
     * @param $message
     */
    public static function error($log, $subfolder = 'others')
    {
        $log = "ERROR:: " . $log;
        self::write($log, $subfolder);
    }
}
