<?php

namespace OST\LaravelFileManager\Classes;

use Illuminate\Http\UploadedFile;
use OST\LaravelFileManager\Models\DeleteResponse;
use OST\LaravelFileManager\Models\StorageUploadResponse;
use OST\LaravelFileManager\Models\UploadResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use OST\LaravelFileManager\Models\File;

class FileManager
{

    private Request $request;

    private \Illuminate\Http\UploadedFile|array|null $file;

    private ?string $user_id = null ;
    private ?string $file_path = null;
    private int $model_id = 0;
    private ?string $model_name = null;

    private ?string $disk = null;
    private ?string $allowed_extensions = null;
    private ?int $max_file_size_kb = null;
    private string $file_name = 'file';
    private bool $is_required = true;


    public function __construct()
    {
        $this->disk = config('filesystems.default');
    }

    public function setRequest(Request $request ,string|null $file_name = 'file',bool $file_is_required = true): static
    {
        $this->request = $request;
        $this->file = $request->file($file_name);
        $this->file_name = $file_name;
        $this->is_required = $file_is_required;
        return $this;
    }

    /**
     * @param string $user_id
     * @return FileManager
     */
    public function setUserId(string $user_id): static
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @param string $file_path
     * @return FileManager
     */
    public function setFilePath(string $file_path): static
    {
        $this->file_path = $file_path;
        return $this;
    }

    /**
     * @param int $model_id
     * @return FileManager
     */
    public function setModelId(int $model_id): static
    {
        $this->model_id = $model_id;
        return $this;
    }

    /**
     * @param string $model_name
     * @return FileManager
     */
    public function setModelName(string $model_name): static
    {
        $this->model_name = $model_name;
        return $this;
    }

    /**
     * @param string $disk
     * @return FileManager
     */
    public function setDisk(string $disk): static
    {
        $this->disk = $disk;
        return $this;
    }


    /**
     * @param string $allowed_extensions
     * @return FileManager
     */
    public function setAllowedExtensions(string $allowed_extensions): static
    {
        $this->allowed_extensions = $allowed_extensions;
        return $this;
    }

    /**
     * @param int $max_file_size_kb
     * @return FileManager
     */
    public function setMaxFileSizeKB(int $max_file_size_kb): static
    {
        $this->max_file_size_kb = $max_file_size_kb;
        return $this;
    }






    /**
     * upload files
     * @return UploadResponse
     */
    public function uploadFiles():UploadResponse{
        $this->validateFile();
        return  $this->upload();
    }

    /**
     * Upload and Update Files
     * @param array|string $updated_path
     * @return UploadResponse
     */
    public function updateFiles(array|string $updated_path):UploadResponse{
        $this->validateFile();
        $result = $this->upload();
        if ($result->getStatus()){
            $this->deleteFiles($updated_path);
        }
        return $result;
    }


    public function updateByEncryptedUrl(string|array $updated_url):UploadResponse{
        $this->validateFile();
        $paths = FileFunctions::getPathFromEncryptedUrl($updated_url);
        $result = $this->upload();
        if ($result->getStatus()){
            $this->deleteFiles($paths);
        }
        return $result;
    }

    public function deleteByEncryptedUrl(string|array $encrypted_url): DeleteResponse
    {
        $paths = FileFunctions::getPathFromEncryptedUrl($encrypted_url);
        return $this->deleteFiles($paths);
    }


    /**
     * Delete file or multiple file from storage disk
     * @param array|string $deleted_file_path
     * @return DeleteResponse
     */
    public function deleteFiles(array|string $deleted_file_path): DeleteResponse
    {
        $validator = \Validator::make(['user_id'=>$this->user_id],['user_id'=>'required|string']);
        if ($validator->fails()){
            throw new ValidationException($validator);
        }

        if (!is_array($deleted_file_path)) {
            //if one path add in array
            $deleted_file_path = [$deleted_file_path];
        }

        $delete_count = 0;

        //delete from storage
        foreach ($deleted_file_path as $path) {
            if (Storage::disk($this->disk)->exists($path)) {
                Storage::disk($this->disk)->delete($path);
            }
            $delete_count += 1;
        }

        //delete from db
        $db_result = File::deleteFiles($deleted_file_path,$this->user_id);

        if (count($deleted_file_path) == $delete_count){
            $storage_delete = true;
        }else{
            $storage_delete = false;
        }

        return new DeleteResponse($storage_delete,$db_result);

    }




    /**
     * Save Files in Storage and DB
     * @return UploadResponse
     */
    private function upload():UploadResponse{

        //if one file entered and the type not array add file in array
        if (!is_array($this->file)) {
            $files = [$this->file];
        }else{
            $files = $this->file;
        }

        $files_data = [];
        $file_paths = [];
        $files_count = count($files);
        $uploaded_file = 0;
        $order = 1;


        foreach ($files as $file) {

            $upload_result = $this->storageFileUpload($file);


            $files_data[] = File::getFile(
                $this->user_id,
                $this->model_id,
                $this->model_name,
                $upload_result->getFilePath(),
                $upload_result->getFileName(),
                $file->getMimeType(),
                $this->formatSizeUnits($file->getSize()),
                $this->disk,
                $order
            );

            $uploaded_file += 1;
            $order += 1;
            $file_paths[] = $upload_result->getFilePath();

        }

        //save files in db
        File::insertFiles($files_data);


        if ($uploaded_file == $files_count) { //when all files are uploaded
            return new UploadResponse(true,'Files Uploaded Successfully',$file_paths);
        }
        else if ($uploaded_file < $files_count) { //when some files are uploaded
            $un_uploaded_file = $files_count - $uploaded_file;
            $message = 'The uploaded files is: '.$uploaded_file.'and the un uploaded files is:'.$un_uploaded_file;
            return new UploadResponse(true,$message,$file_paths);
        }
        //when not any files are uploaded
        return new UploadResponse(false,'Error: failed upload files',$file_paths);

    }



    /**
     * Customize file name
     *
     * Path Example: '/users/' . 'userid' . '/profile'
     *
     * [YFS] MEAN: YouFreeStorage
     * @param $file
     * @return StorageUploadResponse
     */
    private function storageFileUpload($file): StorageUploadResponse
    {
        $extension = $file->getClientOriginalExtension();
        $file_name = 'YFS' . Str::random(27) . '.' . $extension;
        $file_path = $file->storeAs($this->file_path, $file_name, $this->disk);
        return new StorageUploadResponse($file_name,$file_path);
    }



    private function s3FileUpload($file,$path): void{
        $disk = Storage::disk('s3');
        $disk->put($path, fopen($file, 'r+'));
        Storage::disk('s3')->put($path, file_get_contents($file));
    }



    private function formatSizeUnits($bytes): string
    {
        if ($bytes >= 1099511627776) {
            $bytes = number_format($bytes / 1099511627776, 2) . ' TB';
        } elseif ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    private function validateFile(): void
    {
        // validate file
        if (is_array($this->file)) {
            $this->file_name = $this->file_name . '.*';
        }

        $file_roles = $this->is_required?'required':'';
        if ($this->allowed_extensions) {
            $file_roles = $file_roles . '|mimes:' . $this->allowed_extensions;
        }

        if ($this->max_file_size_kb) {
            $file_roles = $file_roles . '|max:' . $this->max_file_size_kb;
        }

        $this->request->validate([$this->file_name=>$file_roles]);


        // validate local variable

        $data = [
            'user_id'=>$this->user_id,
            'file_path'=>$this->file_path,
            'model_name'=>$this->model_name,
        ];

        $roles['user_id'] = 'required|string';
        $roles['file_path'] = 'required|string';
        $roles['model_name'] = 'required|string';


        $validator = \Validator::make($data, $roles);

        if ($validator->fails()) {
            //$message = collect(json_decode($validator->errors())->file)->implode(' | ');
            throw new ValidationException($validator);
        }

    }

}

