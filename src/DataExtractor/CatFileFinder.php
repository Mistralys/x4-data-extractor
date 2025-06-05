<?php

declare(strict_types=1);

namespace Mistralys\X4\DataExtractor;

use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\FolderInfo;

class CatFileFinder
{
    private FolderInfo $gameFolder;

    public function __construct(FolderInfo $gameFolder)
    {
        $this->gameFolder = $gameFolder;
    }

    public function findFiles() : array
    {
        echo '- Scanning game folder...'.PHP_EOL;

        $catFiles = $this->gameFolder->createFileFinder()
            ->includeExtension('cat')
            ->getFiles()
            ->typeANY();

        foreach(FolderInfo::factory($this->gameFolder->getPath().'/extensions')->getSubFolders() as $extensionFolder)
        {
            if(!str_contains($extensionFolder->getName(), 'ego_dlc')) {
                continue;
            }

            echo '- Scanning extension folder ['.$extensionFolder->getName().']...'.PHP_EOL;

            array_push($catFiles, ...$extensionFolder->createFileFinder()
                ->includeExtension('cat')
                ->getFiles()
                ->typeANY()
            );
        }

        return $this->filterFiles($catFiles);
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

        echo '- Found '.count($catFiles).' CAT files.'.PHP_EOL;

        return $catFiles;
    }
}
