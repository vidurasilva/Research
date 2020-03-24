<?php

namespace AdminBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 09/09/16
 * Time: 08:48
 */
class FileUploader
{
    protected $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->targetDir, $fileName);

        return $fileName;
    }
}