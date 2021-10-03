<?php

namespace Yannlo\Tests;

use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\UploadedFile;
use PHPUnit\Framework\TestCase;
use Yannlo\ImageRessource\ImageProcess;

class ImageProcessTest extends TestCase
{
    public function setUp(): void
    {
        $this -> pdo  = new \PDO("sqlite::memory");

        $this -> pdo -> query("CREATE TABLE IF NOT EXISTS images (id INT PRIMARY KEY, type VARCHAR(20) , extension VARCHAR(3) ) ");
        $type = 'image/png' ;
        $ext = "png";
        for ($i=1; $i <= 10; $i++) {
            $request = $this -> pdo -> prepare("INSERT INTO images (id, type, extension) VALUES(:id, :type, :ext)");
            $request ->execute([
                "id" => $i,
                "type" => $type,
                "ext" => $ext
            ]);
        }
        $this->request = new ServerRequest("POST", "/");
        
        $image1 = new UploadedFile(
            "/tmp/phpf3er5",
            328453,
            0,
            "image inconnu.png",
            "image/png"
        );
        $image2 = new UploadedFile(
            "/tmp/phpf3er5",
            2000000,
            0,
            "image_conu2.png",
            "image/png"
        );
        $uploadFiles =[
            "img1"=>$image1,
            // "img2"=>$image2
        ];
        $this->request -> withUploadedFiles($uploadFiles);

    }


    public function testGenerateNewName()
    {
        $request = $this->request;
        $imageProcess = new ImageProcess([],$this->pdo);
        $names = $imageProcess->generateName($request->getUploadedFiles()[0]);
        $this -> assertSame(["img11_little","img11_medium","img11_big"],$names);
    }

    public function testGetExtension()
    {
        $request = $this->request;
        $imageProcess = new ImageProcess([],$this->pdo);
        $ext = $imageProcess->generateExt($request->getUploadedFiles()[0]);
        $this -> assertEquals("png",$ext);
    }

}