<?php

namespace App\Modules\Main\Utilities;

use App\Modules\Main\Enums\ActiveInactiveEnum;
use App\Modules\Main\Models\Document;
use Illuminate\Support\Facades\Log;
use Exception;

class DocumentService
{
    public static function upload($request, $field = "file", $dir = "")
    {
        try {
            if (!$request->hasFile($field)) {
                return "";
            }

            $file = $request->{$field};
            if (!$file->isValid()) {
                return "";
            }
            
            $fileExt = $file->getClientOriginalExtension();
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = preg_replace("/[^a-zA-Z0-9_\-]/", '_', $fileName);
            $fileName = strtolower(substr($fileName, 0, 10)) . '-' . (auth()->id() ?? null) . '-' . microtime(true);
            $fileName .= '.' . $fileExt;

            $uploadDir = "uploads/{$dir}";
            if (!dir(public_path($uploadDir))) {
                @mkdir(public_path($uploadDir), 0777, true);
            }

            $filePath = "{$uploadDir}/{$fileName}";
            $file->move(public_path($uploadDir), $fileName);
            
            return str_replace("//", "/", $filePath);

        } catch(Exception $e) {
            Log::error("DocumentService@upload - Error - ". $e->getMessage() . ". Line - ". $e->getLine());
            return "";
        }
    }

    public static function saveOrUpdate($request)
    {
        try {
            $request = (object) $request;

            $upload = Document::where(function($query) use ($request) {
                    if ($request->filled('id')) {
                        $query->where('id', $request->id);
                    } else {
                        $query->where('user_id', $request->user_id)
                            ->where('type', $request->type)
                            ->where('title', $request->title);
                    }
                })
                ->firstOrNew();

            // unlink if file exists
            if (file_exists($upload->path)) {
                @unlink($upload->path);
            }


            $upload->user_id  = $request->user_id;
            $upload->type     = $request->type;

            if ($request->filled("title")) {
                $upload->title = $request->title;
            }

            $upload->path      = $request->path; 
            $upload->extension = @pathinfo($upload->path, PATHINFO_EXTENSION);

            $fileSizeInBytes   = @filesize($upload->path);
            $upload->size      = self::convertFileSize($fileSizeInBytes);

            if ($request->filled("ref_table")) {
                $upload->ref_table = $request->ref_table;
            }

            if ($request->filled("ref_id")) {
                $upload->ref_id = $request->ref_id;
            }
 
            $upload->status  = $request->status ?? ActiveInactiveEnum::Active;

            $upload->save();

            return $upload;
            
        } catch(Exception $e) {
            Log::error("DocumentService@saveOrUpdate - Error - ". $e->getMessage() . ". Line - ". $e->getLine());
            return false;
        } 
    }

    public static function delete($id)
    {
        try {
            $upload = Document::find($id);
            return $upload->delete();
        } catch(Exception $e) {
            Log::error("DocumentService@delete - Error - ". $e->getMessage() . ". Line - ". $e->getLine());
            return false;
        } 
    }

    public static function find($id)
    {
        try {
            $document = Document::where("id", $id)->first();
 
            return $document;
        } catch(Exception $e) {
            Log::error("DocumentService@delete - Error - ". $e->getMessage() . ". Line - ". $e->getLine());
            return false;
        }
    }

    public static function getAll($request, $paginate = 10)
    {
        try {

            $request = (object) $request;

            $query = Document::with(["user", "createdBy", "updatedBy"]) 
                ->where(function($query) use ($request) {
                    if (!empty($request->user_id)) {
                        $query->where("user_id", $request->user_id);
                    }
                    if (!empty($request->type)) { 
                        $query->where("type", $request->type);
                    } 
                    if (!empty($request->title)) { 
                        $query->where("title", $request->title);
                    } 
                    if (!empty($request->ref_table)) { 
                        $query->where("ref_table", $request->ref_table);
                    } 
                    if (!empty($request->ref_id)) { 
                        $query->where("ref_id", $request->ref_id);
                    } 
                    if (!empty($request->status)) { 
                        $query->where("status", $request->status);
                    } 
                    if (!empty($request->created_by)) {
                        $query->where("created_by", $request->created_by);
                    }
                    if (!empty($request->updated_by)) {
                        $query->where("updated_by", $request->updated_by);
                    }
                })
                ->orderBy("id", "desc");

                if (!empty($paginate)) {
                    $documents = $query->paginate($request->per_page ?? $paginate);
                } else {
                    $documents = $query->get();
                }
             
            return $documents;

        } catch(Exception $e) {
            Log::error("DocumentService@getAll - Error - ". $e->getMessage() . ". Line - ". $e->getLine());
            return [];
        }
    }

    private static function convertFileSize($sizeInBytes = 1) 
    {
        $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;
        while ($sizeInBytes >= 1024 && $unitIndex < count($units) - 1) {
            $sizeInBytes /= 1024;
            $unitIndex++;
        }
        return number_format($sizeInBytes, 1) . ' ' . $units[$unitIndex];
    }
}
