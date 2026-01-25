<?php

declare(strict_types=1);

namespace Mistralys\X4\ExtractedData;

use AppUtils\ArrayDataCollection;
use AppUtils\FileHelper\FolderInfo;
use AppUtils\FileHelper\JSONFile;
use AppUtils\Microtime;

class X4GameInfo
{
    public const string FILE_GAME_INFO = 'game-info.json';
    public const string KEY_GAME_VERSION = 'gameVersion';
    public const string KEY_DATE_GENERATED = 'dateGenerated';
    public const string KEY_DATA_FOLDERS = 'dataFolders';
    public const string KEY_FOLDER_IS_EXTENSION = 'isExtension';
    public const string KEY_FOLDER_LABEL = 'label';
    public const string KEY_FOLDER_ID = 'id';

    private ?ArrayDataCollection $data = null;
    private ?FolderInfo $extractedDataFolder;
    private string $infoFilePath;

    private function __construct()
    {
        $this->infoFilePath = __DIR__.'/../../data/game-info.json';
    }

    public function setExtractedDataFolder(FolderInfo $extractedDataFolder) : self
    {
        $this->extractedDataFolder = $extractedDataFolder;
        return $this;
    }

    public function getExtractedDataFolder(): ?FolderInfo
    {
        return $this->extractedDataFolder;
    }

    public function setInfoFilePath(string $filePath) : self
    {
        $this->infoFilePath = $filePath;
        $this->data = null;
        return $this;
    }

    /**
     * Gets the date and time when the extracted data was generated.
     * @return Microtime
     */
    public function getDate() : Microtime
    {
        $date = $this->getData()->getMicrotime(self::KEY_DATE_GENERATED);
        if($date !== null) {
            return $date;
        }

        return Microtime::createFromString('1975-02-07 18:00:00');
    }

    public function getInfoFile()
    {
        return JSONFile::factory($this->infoFilePath)
            ->setPrettyPrint(true)
            ->setTrailingNewline(true)
            ->setEscapeSlashes(false);
    }

    private static ?X4GameInfo $instance = null;

    public static function create(): X4GameInfo
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getFolderCollection() : DataFolders
    {
        return new DataFolders($this);
    }

    public function getGameVersion() : string
    {
        return $this->getData()->getString(self::KEY_GAME_VERSION);
    }

    public function getData() : ArrayDataCollection
    {
        if(isset($this->data)) {
            return $this->data;
        }

        $this->data = ArrayDataCollection::create();

        $file = $this->getInfoFile();

        if($file->exists()) {
            $this->data->setKeys($file->getData());
        }

        return $this->data;
    }
}
