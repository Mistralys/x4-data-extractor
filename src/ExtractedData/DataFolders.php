<?php

declare(strict_types=1);

namespace Mistralys\X4\ExtractedData;

use AppUtils\ArrayDataCollection;
use AppUtils\Collections\BaseStringPrimaryCollection;
use AppUtils\FileHelper\FolderInfo;
use AppUtils\FileHelper\JSONFile;
use const Mistralys\X4\X4_GAME_FOLDER;

/**
 * @method DataFolder getByID(string $id)
 * @method DataFolder getDefault()
 * @method DataFolder[] getAll()
 */
class DataFolders extends BaseStringPrimaryCollection
{
    public const string FOLDER_VANILLA = 'vanilla';
    public const string DEFAULT_FOLDER = self::FOLDER_VANILLA;

    private X4GameInfo $gameInfo;

    public function __construct(X4GameInfo $gameInfo)
    {
        $this->gameInfo = $gameInfo;
    }

    public function getGameInfo(): X4GameInfo
    {
        return $this->gameInfo;
    }

    public function getDefaultID(): string
    {
        return self::DEFAULT_FOLDER;
    }

    protected function registerItems(): void
    {
        foreach($this->gameInfo->getData()->getArray(X4GameInfo::KEY_DATA_FOLDERS) as $dataFolder)
        {
            $data = ArrayDataCollection::create($dataFolder);

            $this->registerItem(new DataFolder(
                $this,
                $data->getString(X4GameInfo::KEY_FOLDER_ID),
                $data->getString(X4GameInfo::KEY_FOLDER_LABEL),
                $data->getBool(X4GameInfo::KEY_FOLDER_IS_EXTENSION)
            ));
        }
    }
}
