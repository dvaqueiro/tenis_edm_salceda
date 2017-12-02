<?php

namespace Infrastructure\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class FileUploader
{
    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->getTargetDir(), $fileName);

        return $fileName;
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }
}