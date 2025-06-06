<?php

declare(strict_types=1);

namespace Mistralys\X4\ExtractedData;

use AppUtils\FileHelper\FolderInfo;
use AppUtils\Interfaces\StringPrimaryRecordInterface;

class DataFolder implements StringPrimaryRecordInterface
{
    private string $id;
    private string $label;
    private bool $isExtension;
    private FolderInfo $folder;

    public function __construct(FolderInfo $folder, string $id, string $label, bool $isExtension = false)
    {
        $this->folder = $folder;
        $this->id = $id;
        $this->label = $label;
        $this->isExtension = $isExtension;
    }

    public function getID(): string
    {
        return $this->id;
    }

    public function getFolder() : FolderInfo
    {
        return $this->folder;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isExtension(): bool
    {
        return $this->isExtension;
    }
}
