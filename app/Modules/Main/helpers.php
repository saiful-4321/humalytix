<?php

use App\Modules\Auth\Services\AuthOtpService;
use App\Modules\Main\Services\UserService;
use Carbon\Carbon; 

if (!function_exists('dbToDateTime')) {
    function dbToDateTime($date, $format = "F d, Y h:i:s A") {
        if ($date instanceof \DateTimeInterface) {
            return $date->format($format);
        } elseif (is_string($date)) {
            // Check if the date is in "Y-m-d H:i:s" format
            if (preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/', $date)) {
                return Carbon::createFromFormat("Y-m-d H:i:s", $date)->format($format);
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return Carbon::createFromFormat("Y-m-d", $date)->format($format); 
            }
        }
        return $date; 
    }
}


if (!function_exists('dbToDate')) {
    function dbToDate($date, $format = "F d, Y") {
        return dbToDateTime($date, $format);
    }
}


if (!function_exists('dbToTime')) {
    function dbToTime($date, $format = "h:i:s") {
        return dbToDateTime($date, $format);
    }
}

if (!function_exists('getApiToken')) {
    function getApiToken() {
        $str = implode("", array_merge(range("0","9"), range("A","Z"))); 
        return substr(str_shuffle($str), 0, 4) . '-' .
            substr(str_shuffle($str), 0, 4) . '-' .
            substr(str_shuffle($str), 0, 4) . '-' .
            substr(str_shuffle($str), 0, 5);
    }
}


if (!function_exists('uniqueId')) {
    function uniqueId($prefix = '', $postfix = '') {
        return "{$prefix}". uniqid() . "{$postfix}";
    }
}
 
if (!function_exists('mask')) {
    function mask($str, $pattern = "*") { 
        $len   = strlen($str);
		$limit = round($len/3);
		
		$start  = substr($str, 0, $limit);
		$middle = str_repeat($pattern, $limit);
		$end    = substr($str, ($limit*2), $len);
		 
		return $start.$middle.$end;
    }
}


if (!function_exists('msisdn_11')) {
    function msisdn_11($msisdn = null) {
        return '0' . substr(msisdn($msisdn), -10);
    }
}

if (!function_exists('msisdn')) {
    function msisdn($msisdn = null) {
        $msisdn = trim($msisdn);
        $localMsisdn = preg_match('/(^(\+880|00880|880|0)?(1){1}[3456789]{1}(\d){8})$/', $msisdn);
        if ($localMsisdn) {
            return '880' . substr($msisdn, -10);
        } else {
            return $msisdn;
        }
    }
}

if (!function_exists('msisdn_prefix')) {
    function msisdn_prefix($msisdn) {
        $msisdn = trim($msisdn);
        $localMsisdn = preg_match('/(^(\+880|00880|880|0)?(1){1}[3456789]{1}(\d){8})$/', $msisdn);
        if ($localMsisdn) {
            $msisdn =  '0' . substr($msisdn, -10);
        } 
        return substr($msisdn, 0, 3);
    }
}

// return 3 formatted numbers
if (!function_exists('msisdns')) {
    function msisdns($msisdn = null) {
        $msisdn = trim($msisdn);
        $localMsisdn = preg_match('/(^(\+880|00880|880|0)?(1){1}[3456789]{1}(\d){8})$/', $msisdn);
        if ($localMsisdn) {
            return [
                 substr($msisdn, -10),
                '0' . substr($msisdn, -10),
                '880' . substr($msisdn, -10),
            ];
        } else {
            return is_string($msisdn) ? [$msisdn] : $msisdn;
        }
    }
}


// return all sms processing tables
if (!function_exists('getSmsProcessingTable')) {
    function getSmsProcessingTable($table = null) {
        $tables = [
            'sms_api',
            'sms_bulk'
        ];

        if ($table === null) {
            return $tables;
        }

        $table = strtolower($table);

        if (in_array($table, $tables)) {
            return $table;
        }

        return null;
    }
}


// article read more/less
if (!function_exists('readMoreLess')) {
    function readMoreLess($str, $length = 20) {
        if (strlen($str) > $length) {
            $str = '<p style="white-space:normal;text-overflow: ellipsis;"><span>' . mb_substr($str, 0, $length) . '</span><span class="hidden-text" style="display: none">' . mb_substr($str, $length) . '</span> <a href="#" class="show-more" onclick="showMore(this); return false;">...More</a><a href="#" style="display: none" class="hidden-text" onclick="lessSms(this); return false;">Less</a></p>';
        }
        return $str;
    }
}


// break paragraph 
if (!function_exists('paragraph')) {
    function paragraph($str, $wordBreak = 5) { 
        $words = explode(' ', $str);
        $wordsWithBr = [];
        $wordCount = 0;
    
        foreach ($words as $word) {
            $wordsWithBr[] = $word;
            $wordCount++;
            if ($wordCount % $wordBreak == 0) {
                $wordsWithBr[] = '<br/>';
            }
        }
        return implode(' ', $wordsWithBr);
    }
}
 
// random string
if (!function_exists('getRandomString')) {
    function getRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_[]{}';
        $randomChars = array_map(function() use ($characters) {
            return $characters[rand(0, strlen($characters) - 1)];
        }, range(1, $length));

        return implode('', $randomChars);
    }
}

// random string
if (!function_exists('getUserId')) {
    function getUserId($id = null) {
        return (new AuthOtpService)->getUserId($id);
    }
}

 
if (!function_exists("createOrUpdateUserSession")) {
    function createOrUpdateUserSession($type, $pretentId = null, $guard = 'web') {
        (new UserService)->saveUserSession($type, $pretentId, $guard);
    }
}
 
if (!function_exists("publicAsset")) {
    function publicAsset($path) {
        if (file_exists($path)) {
            if (request()->get('pdf')) {
                return "./{$path}";
            } else {
                return asset($path);
            }
        }
        return "";
    }
}


if(!function_exists('getLogProperties')) {
    function getLogProperties ($data = "", $type = "", $length = 20) {
        if (empty($data)) {
            return "";
        }

        // Convert to array if object
        if (is_object($data)) {
            $data = json_decode(json_encode($data), true);
        }
        
        if (!is_array($data)) {
             return $data;
        }

        if ($type == 'agent') {
            if (isset($data['agent'])) {
                $agent = $data['agent'];
                return "<small style='display: block; width: 100%; min-width: 200px; white-space: normal;'>
                    <div style='margin-bottom: 4px;'><b>IP:</b> " . ($agent['ip'] ?? '-') . "</div>
                    <div style='margin-bottom: 4px; word-break: break-word;'><b>Browser:</b> " . ($agent['user-agent'] ?? '-') . "</div>
                    <div style='word-break: break-all;'><b>URL:</b> " . ($agent['url'] ?? '-') . "</div>
                </small>";
            }
            return '-';
        }

        if ($type == 'data') {
            if (!isset($data['data']) || empty($data['data'])) {
                return '-';
            }
            
            $changes = $data['data'];
            $html = '<table class="table table-sm table-bordered mb-0" style="font-size: 11px;">';
            
            // Check if it's an update (has old/new) or create (flat array)
            $firstKey = array_key_first($changes);
            $isUpdate = is_array($changes[$firstKey]) && (array_key_exists('old', $changes[$firstKey]) || array_key_exists('new', $changes[$firstKey]));

            if ($isUpdate) {
                $html .= '<thead class="bg-light"><tr><th>Field</th><th>Old</th><th>New</th></tr></thead><tbody>';
                foreach ($changes as $field => $change) {
                    $old = is_array($change['old'] ?? '') ? json_encode($change['old']) : ($change['old'] ?? '-');
                    $new = is_array($change['new'] ?? '') ? json_encode($change['new']) : ($change['new'] ?? '-');
                    $html .= "<tr>
                        <td class='fw-bold'>{$field}</td>
                        <td class='text-danger'>{$old}</td>
                        <td class='text-success'>{$new}</td>
                    </tr>";
                }
            } else {
                // Creation or simple log
                $html .= '<thead class="bg-light"><tr><th>Field</th><th>Value</th></tr></thead><tbody>';
                foreach ($changes as $field => $value) {
                    $val = is_array($value) ? json_encode($value) : $value;
                    $html .= "<tr>
                        <td class='fw-bold'>{$field}</td>
                        <td>{$val}</td>
                    </tr>";
                }
            }
            $html .= '</tbody></table>';
            return $html;
        }

        return json_encode($data);
    }
}

if (!function_exists('getLogUser')) {
    function getLogUser($model = null, $id = null) {
        if (empty($id)) {
            return "System";
        }
        
        // Try to find the user
        $user = \App\Models\User::find($id);
        
        if ($user) {
            return $user->name;
        }
        
        return "User #{$id} (Deleted)";
    }
}

