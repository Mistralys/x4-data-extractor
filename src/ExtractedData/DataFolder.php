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
    private DataFolders $collection;

    public function __construct(DataFolders $collection, string $id, string $label, bool $isExtension = false)
    {
        $this->collection = $collection;
        $this->id = $id;
        $this->label = $label;
        $this->isExtension = $isExtension;
    }

    public function getID(): string
    {
        return $this->id;
    }

    public function getPath() : FolderInfo
    {
        return FolderInfo::factory(sprintf(
            '%s/%s',
            $this->collection->getGameInfo()->getExtractedDataFolder(),
            $this->getID()
        ));
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
