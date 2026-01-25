<?php

declare(strict_types=1);

namespace Mistralys\X4\DataExtractor;

use AppUtils\FileHelper\FileInfo;

class CatFile
{
    /**
     * @var FileInfo[]
     */
    private array $files = array();
    private string $label;
    private string $name;

    public function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addFile(FileInfo $filePath) : void
    {
        $this->files[] = $filePath;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return FileInfo[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
