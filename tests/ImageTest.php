<?php

namespace Yannlo\Tests;

use PHPUnit\Framework\TestCase;
use Yannlo\ImageRessource\Image;
use Yannlo\ImageRessource\Exceptions\ImageException;

class ImageTest extends TestCase
{
    public function setUp(): void
    {
        $this-> data =[
            "name" => "img23_".Image::MEDIUM_SIZE,
            "type" => "image/png",
            "path" => "/public/picture",
            "extension" => "png"
        ];
    }

    public function testCorrectPNGImage(): void
    {
        $img = new Image($this->data);
        $this->assertEquals('img23_medium', $img->name());
        $this->assertEquals('image/png', $img->type());
        $this->assertEquals('/public/picture', $img->path());
        $this->assertEquals('png', $img->extension());
    }

    public function testCorrectJPEGImage(): void
    {
        $data = $this->data;
        $data['extension'] = 'jpg';
        $data['type'] = 'image/jpeg';

        $img = new Image($data);
        $this->assertEquals('img23_medium', $img->name());
        $this->assertEquals('image/jpeg', $img->type());
        $this->assertEquals('/public/picture', $img->path());
        $this->assertEquals('jpg', $img->extension());
    }

    public function testInvalidImageNameWithRandomName(): void
    {
        $data = $this->data;
        $data['name'] = 'zqvvrqvqv';
        $this -> expectException(ImageException::class);
        $img = new Image($data);
    }

    public function testInvalidImageNameWithGoodSignature(): void
    {
        $data = $this->data;
        $data['name'] = 'imgefzez';
        $this -> expectException(ImageException::class);
        $img = new Image($data);
    }

    public function testInvalidNameImageWithBadId(): void
    {
        $data = $this->data;
        $data['name'] = 'img21e_';
        $this -> expectException(ImageException::class);
        $img = new Image($data);

    }

    public function testInvalidNameImageWithBadSize(): void
    {
        $data = $this->data;
        $data['name'] = 'img21_zvzeezv';
        $this -> expectException(ImageException::class);
        $img = new Image($data);
    }

    public function testInvalidTypeImageWithRandomType(): void
    {
        $data = $this->data;
        $data['type'] = 'qrvqeve/plain';
        $this -> expectException(ImageException::class);
        $img = new Image($data);

    }

    public function testInvalidTypeImageWithBadSignature(): void
    {
        $data = $this->data;
        $data['type'] = 'image/plain';
        $this -> expectException(ImageException::class);
        $img = new Image($data);
    }

    public function testInvalidExtensionImage(): void
    {
        $data = $this->data;
        $data['extension'] = 'jpeg';
        $this -> expectException(ImageException::class);
        $img = new Image($data);
    }

    public function testInvalidPathImage(): void
    {
        $data = $this->data;
        $data['path'] = '/publlic/ima';
        $this -> expectException(ImageException::class);
        $img = new Image($data);
    }


    public function testImageURLBuilder(): void
    {
        $data = $this->data;
        $img = new Image($data);
        $url = $img->getImageURL();
        $this -> assertEquals("/public/picture/img23_medium.png", $url);
    }
}