<?php

declare(strict_types=1);

namespace Mistralys\X4\DataExtractor;

use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\FolderInfo;
use const Mistralys\X4\X4_CATALOG_TOOL_BINARY;
use const Mistralys\X4\X4_EXTRACTED_CAT_FILES_FOLDER;
use const Mistralys\X4\X4_GAME_FOLDER;

class BuildTools
{
    private const BATCH_TEMPLATE = <<<'BATCH'
@echo off

%s %s

pause

BATCH;

    public static function build() : void
    {
        self::init();

        $arguments = array();

        foreach(self::getCatFiles() as $catFile)  {
            $arguments[] = '-in "'.$catFile->getPath().'"';
        }

        $arguments[] = '-out "'.X4_EXTRACTED_CAT_FILES_FOLDER.'"';

        $outputFile = FileInfo::factory(__DIR__.'/../../unpack.bat');

        $outputFile->delete();

        echo '- Saving to batch file ['.$outputFile->getName().'].'.PHP_EOL;

        FileInfo::factory($outputFile)
            ->putContents(sprintf(
                self::BATCH_TEMPLATE,
                X4_CATALOG_TOOL_BINARY,
                implode(' ', $arguments)
            ));
    }

    /**
     * @return FileInfo[]
     */
    private static function getCatFiles() : array
    {
        return (new CatFileFinder(FolderInfo::factory(X4_GAME_FOLDER)))
            ->findFiles();
    }

    private static function init() : void
    {
        if(!file_exists(__DIR__.'/../../vendor/autoload.php')) {
            die('Please run "composer install" first (see README.md).');
        }

        require_once __DIR__.'/../../vendor/autoload.php';

        if(!file_exists(__DIR__.'/../../dev-config.php')) {
            die('Please create the configuration file first (see README.md).');
        }

        require_once __DIR__.'/../../dev-config.php';
    }
}
