<?php

declare(strict_types=1);

namespace Mistralys\X4\ExtractedData;

use AppUtils\ArrayDataCollection;
use AppUtils\Collections\BaseStringPrimaryCollection;
use AppUtils\FileHelper\FolderInfo;
use AppUtils\FileHelper\JSONFile;

/**
 * @method DataFolder getByID(string $id)
 * @method DataFolder getDefault()
 * @method DataFolder[] getAll()
 */
class DataFolders extends BaseStringPrimaryCollection
{
    private FolderInfo $extractedDataFolder;

    public function __construct(FolderInfo $extractedDataFolder)
    {
        $this->extractedDataFolder = $extractedDataFolder;
    }

    private static ?DataFolders $instance = null;

    public static function create(FolderInfo $extractedDataFolder): DataFolders
    {
        if(self::$instance === null) {
            self::$instance = new self($extractedDataFolder);
        }

        return self::$instance;
    }

    public function getDefaultID(): string
    {
        return 'vanilla';
    }

    protected function registerItems(): void
    {
        foreach($this->extractedDataFolder->getSubFolders() as $dataFolder)
        {
            $infoFile = $dataFolder->getSubFile('info.json');
            if(!$infoFile instanceof JSONFile) {
                continue;
            }

            $data = ArrayDataCollection::create($infoFile->getData());

            $this->registerItem(new DataFolder(
                $dataFolder,
                $data->getString('id'),
                $data->getString('label'),
                $data->getBool('isExtension')
            ));
        }
    }
}
