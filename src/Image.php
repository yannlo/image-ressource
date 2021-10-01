<?php

namespace Yannlo\ImageRessource;

use Yannlo\ImageRessource\Exceptions\ImageException;

class Image
{
    private string $name;
    private string $type;
    private string $path;
    private string $extension;

    public const BIG_SIZE = 'big';
    public const MEDIUM_SIZE = 'medium';
    public const LITTLE_SIZE = 'little';
    public const PNG = ['type' => 'png','extension' => 'png'];
    public const JPEG = ['type' => 'jpeg','extension' => 'jpg'];


    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }

    private function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    // GETTERS

    public function name(): string
    {
        return $this->name;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function extension(): string
    {
        return $this->extension;
    }

    // SETTERS

    public function setName(string $name): void
    {
        if (!(bool)preg_match('/^img[0-9]+_[a-z]+/', $name)) {
            throw new ImageException();
            return;
        }

        $endName = substr($name, strpos($name, '_') + 1);

        $sizes = [
            self::BIG_SIZE,
            self::LITTLE_SIZE,
            self::MEDIUM_SIZE
        ];
        if (!in_array($endName, $sizes)) {
            throw new ImageException();
            return;
        }

        $this->name = $name;
    }

    public function setType(string $type): void
    {
        if (substr($type, 0, strpos($type, '/')) !== "image") {
            throw new ImageException();
            return;
        }

        $endType = substr($type, strpos($type, '/') + 1);

        if (!in_array($endType, [self::JPEG["type"],self::PNG["type"]])) {
            throw new ImageException();
            return;
        }

        $this->type = $type;
    }

    public function setPath(string $path): void
    {
        if (!(bool)preg_match('/^\/public\/picture/', $path)) {
            throw new ImageException();
            return;
        }

        $this->path = $path;
    }

    public function setExtension(string $extension): void
    {

        if (!in_array($extension, [self::JPEG["extension"],self::PNG["extension"]])) {
            throw new ImageException();
            return;
        }

        $this->extension = $extension;
    }


    public function getImageURL(): string
    {
        $url = $this->path . DIRECTORY_SEPARATOR . $this-> name . "." . $this -> extension;
        return $url;
    }
}
