<?php

namespace Infrastructure\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class FileUploader
{
    private $fotoDir;
    private $publicRootDir;

    public function __construct($publicRootDir, $fotoDir)
    {
        $this->publicRootDir = $publicRootDir;
        $this->fotoDir = $fotoDir;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->getPublicRootDir().$this->getFotoDir(), $fileName);

        return $this->getFotoDir() . $fileName;
    }

    public function deleteFile($filename)
    {
        $ok = false;
        if(file_exists($this->getPublicRootDir().$filename)){
            $ok = unlink($this->getPublicRootDir().$filename);
        }

        return $ok;
    }

    function getFotoDir()
    {
        return $this->fotoDir;
    }

    function getPublicRootDir()
    {
        return $this->publicRootDir;
    }
}