# Overview
```php
This package deals with the operations that occur on files,
such as uploading, updating, fetching, or deleting a file.
```
### Installation
```php
composer required ost/laravel-file-manager:dev-master
```
#### Publish Package
```php
php artisan vendor:publish --provider="OST\LaravelFileManager\LaravelFileManagerServiceProvider"
```
### Notes
```php
1- The default disk is [public]
2- You can change disk from .env by change FILESYSTEM_DISK=public

```

## Usage

## Upload File
You can upload one or more file
```php
        FileManager::setRequest($request)
            ->setUserId(1)
            ->setModelName(User::class)
            ->setFilePath('/user/1/images')
            ->uploadFile();
```

## Update File
#### Update File By Url
You can update the files by passing the files that you want to upload in the request and passing the urls of the files that you want to update to be deleted from storage
```php
        FileManager::setRequest($request)
            ->setUserId(1)
            ->setModelName(User::class)
            ->setFilePath('/user/1/images')
            ->updateFileByUrl(['url1','url2']);
```
#### Update File By Path
You can update the files by passing the files that you want to upload in the request and passing the paths of the files that you want to update to be deleted from storage
```php
        FileManager::setRequest($request)
            ->setUserId(1)
            ->setModelName(User::class)
            ->setFilePath('/user/1/images')
            ->updateFileByPath(['path1','path2']);
```

## Delete File
#### Delete File by Url
```php
        FileManager::deleteFileByUrl(['url1','url2'],user_id);
```
#### Delete File by File Path
You can pass file paths directly
```php
        FileManager::deleteFileByPath(['path1','path2'],user_id);
```


## Customisation
#### Set max file size
```php
        FileManager::setRequest($request)
            ->setMaxFileSizeKB(8000)
           
```
#### Set disk 
```php
        FileManager::setRequest($request)
            ->setDisk(disk_name)
           
```
#### Set Request File Key 
default value for => file_key: 'file'
```php
       FileManager::setRequest(request: $request,file_key: 'file',file_is_required: true)          
```
#### Make File Upload or update is optional
default value for => file_is_required: true change to false
```php
       FileManager::setRequest(request: $request,file_key: 'file',file_is_required: false)          
```


## Get Url For File
You can get file url and mime type or only url for file by passing file path
```php
 FileManager::getUrl('user/1/images/YFSCBjbOCRQ7At7J7uX4cihDcZkf7j.png',true);
```
The return result =>
{
"url": "http://127.0.0.1:8000/storage/user/1/images/YFSCBjbOCRQ7At7J7uX4cihDcZkf7j.png",
"type": "png"
}


## Encrypt File path
You can encrypt file path but you must to create new disk in filesystem because public disk not with encrypted url
To enable this feature 
###### config/laravel_file_manager.php 
```php
    'encrypted_url'=>true
```
### Create New Disk
###### config/filesystems.php 
```php
   'disks' => [

  'disk_name' => [
            'driver' => 'local',
            'root' => storage_path('app/disk_name'),
            'url' => env('APP_URL').'/disk_name',
        ],
        
    ]

```
###### .env
```php
FILESYSTEM_DISK=disk_name
```
When get File Url ,the returned url like this:<br/>
{
"url": "http://127.0.0.1:8000/disk_name/eyJpdiI6IktKVlRTOENwUnJ5a3VTOG5CNzJsYVE9PSIsInZhbHVlIjoiY25wbEZlYUsxeEhNUXdhWnBSZHgwNlhwRzk1UDJXY0MyTyt4R1NqQjdDS1owRk4vTFdqQWFQU0d3U2h2Z1FBK0Y0TVZCazBRWFNpR2xwOTlpMHBwS2c9PSIsIm1hYyI6IjdjMDgwYTUzOTcxYjMyNWQ2Y2UwNTI5MDI5NmQ0ZjA5YTA0YzU2NjgxMjAxZGZmN2I2YjU5YTMzMjRiMWRkNjMiLCJ0YWciOiIifQ==",
"type": "png"
}

## Advanced
#### You can receive requests for get and customizing files through
###### config/laravel_file_manager.php 
Change value from true to false
```php
    'get_file_route'=>false
```
###### routes/web.php 
Add Route in routes/web to receive request
```php
     Route::get('/storage/' . '{path}', function ($path) {
     return \OST\LaravelFileManager\FileManager::getFileByRoute($path,$disk,false)
     });
```
