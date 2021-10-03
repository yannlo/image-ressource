<?php

namespace Yannlo\ImageRessource;

use Psr\Http\Message\UploadedFileInterface;

class ImageProcess
{
    private array $images;
    private \PDO $pdo;
    public function __construct(array $data,\PDO $pdo)
    {
        $this -> pdo = $pdo;
    }

    public function generateExt(UploadedFileInterface $file): string
    {
        $name = $file-> getClientFileName();
        return substr($name, strpos($name, '.') + 1);
    }

    public function generateName(string $name): array
    {
        $request = $this ->pdo -> query("SELECT id FROM images LIMIT 1");
        $id = $request->fetch()["id"];
        $sizes= [Image::LITTLE_SIZE,Image::MEDIUM_SIZE,Image::BIG_SIZE];
        $names =[];
        foreach ($sizes as $size)
        {
            $names[] = "img".($id+1)."_".$size;
        }
        return $names;
    }
}
