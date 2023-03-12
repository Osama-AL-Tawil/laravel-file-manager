<?php

namespace OST\LaravelFileManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class File extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function getFilePathAttribute($value):string
    {
        return asset($value);
    }

    public function getThumbnailAttribute($value):string
    {
        return asset($value);
    }

    public function getFileTypeAttribute($value):array
    {
        return ['fileExtension'=>$value,'view_type'=>'image'];
    }


//    public function setNameAttribute(string $value)
//    {
//        $value = ucwords($value);
//
//        $this->attributes['name'] = $this->encrypt('name', $value);
//    }

    public static function getFile(
        string $user_id,
        int $model_id,
        string $model_name,
        string $file_path,
        string $file_name,
        string $file_type,
        string $file_size,
        string $disk,
        string $order,

    )
    {
        return ['user_id' => $user_id,
            'model_id' => $model_id,
            'model_name' => $model_name,
            'file_path' => $file_path,
            'file_name' => $file_name,
            'file_type' => $file_type,
            'file_size' => $file_size,
            'disk' => $disk,
            'order' => $order,
            'created_at'=>now(),
            'updated_at'=>now()
        ];
    }

    public static function insertFiles(array $files_data):bool{
        if ($files_data){
            return DB::table('files')->insert($files_data);
        }
        return false;
    }

    public static function deleteFiles(array $files_paths,string $user_id):bool{
        $files_names = [];
        foreach ($files_paths as $path) {
            $files_names[] = basename($path);
        }
        return (bool) File::query()->whereIn('file_name',$files_names)
            ->where('user_id',$user_id)
            ->delete();

    }
}
