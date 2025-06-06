<?php

declare(strict_types=1);

namespace Mistralys\X4\DataExtractor;

use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\FolderInfo;
use DOMDocument;
use Mistralys\X4\UI\Console;

class CatFileFinder
{
    public  const SOURCE_VANILLA = 'vanilla';
    private FolderInfo $gameFolder;

    public function __construct(FolderInfo $gameFolder)
    {
        $this->gameFolder = $gameFolder;
    }

    public function findFiles() : array
    {
        Console::header('Scanning Game Folders');

        $catFiles = $this->getCatFiles($this->gameFolder, self::SOURCE_VANILLA, 'Base game');

        foreach(FolderInfo::factory($this->gameFolder->getPath().'/extensions')->getSubFolders() as $extensionFolder)
        {
            if(!str_contains($extensionFolder->getName(), 'ego_dlc')) {
                continue;
            }

            Console::line1('Scanning extension folder [%s]...', $extensionFolder->getName());

            $dom = new DOMDocument();
            $dom->loadXML($extensionFolder->getSubFile('content.xml')->getContents());
            $label = $dom->getElementsByTagName('content')->item(0)
                ->getAttribute('name');

            array_push($catFiles, ...$this->getCatFiles($extensionFolder, $extensionFolder->getName(), $label));
        }

        return $this->filterFiles($catFiles);
    }

    private function getCatFiles(FolderInfo $folder, string $source, string $label) : array
    {
        $files = $folder->createFileFinder()
            ->includeExtension('cat')
            ->getFiles()
            ->typeANY();

        $result = array();
        foreach($files as $file) {
            $result[] = $file
                ->setRuntimeProperty('source', $source)
                ->setRuntimeProperty('label', $label);
        }

        return $result;
    }

    /**
     * @param FileInfo[] $catFiles
     * @return FileInfo[]
     */
    private function filterFiles(array $catFiles) : array
    {
        $catFiles = array_filter($catFiles, function($file) {
            return !str_contains($file->getBaseName(), '_sig');
        });

        Console::line1('Found %s catalog files.', count($catFiles));

        return $catFiles;
    }
}
