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
## Usage

### Upload File
You can upload one or more file
```php
        FileManager::setRequest($request)
            ->setUserId(1)
            ->setModelName(User::class)
            ->setFilePath('/user/1/images')
            ->uploadFile();
```

### Update File
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

### Delete File by Url
```php
        FileManager::deleteFileByUrl(['url1','url2'],user_id);
```
### Delete File by File Path
You can pass file paths directly 
```php
        FileManager::deleteFileByPath(['path1','path2'],user_id);
```

### Get Url For File
You can get file url and mime type or only url for file by passing file path
```php
 FileManager::getUrl('user/1/images/YFSCBjbOCRQ7At7J7uX4cihDcZkf7j.png',true);
```
The return result =>
{
"url": "http://127.0.0.1:8000/storage/user/1/images/YFSCBjbOCRQ7At7J7uX4cihDcZkf7j.png",
"type": "png"
}

#### You can receive requests for get and customizing files through
###### laravel_file_manager.php (config)
Change value from true to false
```php
    'get_file_route'=>false
```
###### web.php (routes)
Add Route in routes/web to receive request
```php
     Route::get('/storage/' . '{path}', function ($path) {
     return \OST\LaravelFileManager\FileManager::getFileByRoute($path,$disk,false)
     });
```
