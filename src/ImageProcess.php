<?php

namespace Yannlo\ImageRessource;

use Psr\Http\Message\UploadedFileInterface;

/**
 * ImageProcess
 * permit to process all images
 */
class ImageProcess
{
    private array $images;
    private \PDO $pdo;
    public function __construct(array $UploadedFiles, \PDO $pdo)
    {
        $this -> pdo = $pdo;

        $count = count($UploadedFiles);
        $allName = $this -> generateName($count);
        foreach ($UploadedFiles as $file) {
            $image = [];
            $image["extension"] = $this -> generateExt($file);
            $image["type"] = $this -> getType($file);
            if ($count > 1) {
                foreach ($allName as $id => $names) {
                    $partImages = [];
                    foreach ($names as $name) {
                        $image["name"] = $name;
                        $partImages[] = new Image($image);
                    }
                    $this -> images[$id] = $partImages;
                }
            } else {
                foreach ($allName as $name) {
                    $image["name"] = $name;
                    $partImages[] = new Image($image);
                }
                $this -> images = $partImages;
            }
        }
    }

    /**
     * generateExt
     *
     * permite to generate all image name
     *
     * @param  UploadedFileInterface $file
     * @return string
     */
    public function generateExt(UploadedFileInterface $file): string
    {
        $name = $file-> getClientFileName();
        return substr($name, strpos($name, '.') + 1);
    }

    /**
     * generateName
     *
     * @return array
     */
    public function generateName(int $count = 1): array
    {

        $request = $this ->pdo -> query("SELECT id FROM images ORDER BY id DESC LIMIT 1");
        $id = (int) $request->fetch()["id"];
        $names = [];
        $sizes = [Image::LITTLE_SIZE,Image::MEDIUM_SIZE,Image::BIG_SIZE];
        if ($count === 1) {
            foreach ($sizes as $size) {
                $names[] = "img" . ($id + 1) . "_" . $size;
            }
            return $names;
        }

        for ($i = 1; $i <= $count; $i++) {
            $namesPart = [];
            foreach ($sizes as $size) {
                $namesPart[] = "img" . ($id + $i) . "_" . $size;
            }
            $names[($id + $i)] = $namesPart;
        }

        return $names;
    }

    /**
     * getType
     *
     * @param  UploadedFileInterface $file
     * @return string
     */
    public function getType(UploadedFileInterface $file): string
    {
         return $file -> getClientMediaType();
    }

    public function getImages(): array
    {
        return $this -> images;
    }
}
