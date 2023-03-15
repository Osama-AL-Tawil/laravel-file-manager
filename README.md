# Overview
```php
This package deals with the operations that occur on files, such as uploading, updating, fetching, or deleting a file.
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

#### Upload File
You can upload one or more file
```php
        FileManager::setRequest($request)
            ->setUserId(1)
            ->setModelName(User::class)
            ->setFilePath('/user/1/images')
            ->uploadFile();
```

#### Update File
You can upload file and delete old files or updated files by passing updated file urls 
```php
        FileManager::setRequest($request)
            ->setUserId(1)
            ->setModelName(User::class)
            ->setFilePath('/user/1/images')
            ->updateFileByUrl(['url1','url2']);
```

#### Delete File by Url
```php
        FileManager::deleteFileByUrl(['url1','url2'],user_id);
```
#### Delete File by File Path
You can pass file paths directly 
```php
        FileManager::deleteFileByPath(['path1','path2'],user_id);
```

#### Get File Url
You can get file url and mime type or only url for file 
```php
 FileManager::getUrl('user/1/images/YFSCBjbOCRQ7At7J7uX4cihDcZkf7j.png',true);
```
The return result =>
{
"url": "http://127.0.0.1:8000/storage/user/1/images/YFSCBjbOCRQ7At7J7uX4cihDcZkf7j.png",
"type": "png"
}
