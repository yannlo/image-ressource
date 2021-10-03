<?php

namespace Yannlo\Tests;

use PHPUnit\Framework\TestCase;
use Yannlo\ImageRessource\Image;
use GuzzleHttp\Psr7\UploadedFile;
use GuzzleHttp\Psr7\ServerRequest;
use Yannlo\ImageRessource\ImageProcess;

class ImageProcessTest extends TestCase
{
    public function setUp(): void
    {
        $this -> pdo  = new \PDO("sqlite::memory:");

        $this -> pdo -> query("CREATE TABLE IF NOT EXISTS images (id INT PRIMARY KEY, type VARCHAR(20) , extension VARCHAR(3) ) ");
        $type = 'image/png' ;
        $ext = "png";
        for ($i=1; $i <=10; $i++) {
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
            "/tmp/phprver5",
            2000000,
            0,
            "image_conu2.png",
            "image/png"
        );
        $uploadFiles =[
            "img1"=>$image1,
            "img2"=>$image2
        ];
        $this->request = $this->request -> withUploadedFiles($uploadFiles);

    }


    public function testGenerateNewName()
    {
        $imageProcess = new ImageProcess([],$this->pdo);
        $names = $imageProcess->generateName(1);
        $this -> assertSame(["img11_little","img11_medium","img11_big"],$names);
    }

    public function testGenerateMoreNewName()
    {
        $imageProcess = new ImageProcess([],$this->pdo);
        $names = $imageProcess->generateName(2);
        $result[11] = ["img11_little","img11_medium","img11_big"];
        $result[12] = ["img12_little","img12_medium","img12_big"];
        $this -> assertSame($result,$names);
    }

    public function testGetExtension()
    {
        $request = $this->request;
        $imageProcess = new ImageProcess([],$this->pdo);
        
        $ext = $imageProcess->generateExt($request->getUploadedFiles()['img1']);
        $this -> assertEquals("png",$ext);
    }

    public function testGetType()
    {
        $request = $this->request;
        $imageProcess = new ImageProcess([],$this->pdo);
        $ext = $imageProcess->getType($request->getUploadedFiles()['img1']);
        $this -> assertEquals("image/png",$ext);
    }

    public function testGenerateImage()
    {
        $request = $this->request;
        $files = $request->getUploadedFiles();
        unset($files['img2']);
        $imageProcess = new ImageProcess($files,$this->pdo); 
        $result=[
            new Image(["name"=>"img11_little","type"=>"image/png","extension"=>"png"]),
            new Image(["name"=>"img11_medium","type"=>"image/png","extension"=>"png"]),
            new Image(["name"=>"img11_big","type"=>"image/png","extension"=>"png"])
        ];
        $this ->assertEquals($result,$imageProcess->getImages());
    }

    public function testGenerateManyImages()
    {
        $request = $this->request;
        $imageProcess = new ImageProcess($request->getUploadedFiles(),$this->pdo); 
        $result[11]=[
            new Image(["name"=>"img11_little","type"=>"image/png","extension"=>"png"]),
            new Image(["name"=>"img11_medium","type"=>"image/png","extension"=>"png"]),
            new Image(["name"=>"img11_big","type"=>"image/png","extension"=>"png"])
        ];
        $result[12]=[
            new Image(["name"=>"img12_little","type"=>"image/png","extension"=>"png"]),
            new Image(["name"=>"img12_medium","type"=>"image/png","extension"=>"png"]),
            new Image(["name"=>"img12_big","type"=>"image/png","extension"=>"png"])
        ];
        $this ->assertEquals($result,$imageProcess->getImages());
    }

}