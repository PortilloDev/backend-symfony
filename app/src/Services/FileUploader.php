<?php
namespace App\Services;

use League\Flysystem\FilesystemOperator;


class FileUploader 
{
    public function __construct( 
        private FilesystemOperator $defaultStorage
    ) 
    {

    }


    public function uploadBase64File(string $img) :string
    {
        $extension = explode('/', mime_content_type($img))[1];
        $data = explode(',', $img);
        $filename = sprintf('%s.%s', uniqid('book_', true), $extension);
        $this->defaultStorage->write($filename, base64_decode($data[1]));
        return $filename;
    }
}