<?php

declare(strict_types=1);

namespace Mistralys\X4\DataExtractor;

use AppUtils\FileHelper;
use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\FolderInfo;
use AppUtils\FileHelper\JSONFile;
use Mistralys\X4\ExtractedData\DataFolders;
use Mistralys\X4\UI\Console;
use const Mistralys\X4\X4_CATALOG_TOOL_BINARY;
use const Mistralys\X4\X4_EXTRACTED_CAT_FILES_FOLDER;
use const Mistralys\X4\X4_GAME_FOLDER;

class BuildTools
{
    private const BATCH_TEMPLATE = <<<'BATCH'
@echo off

%s

pause

BATCH;

    public static function build() : void
    {
        self::init();

        $all = array();
        foreach(self::getCatFiles() as $source => $def)
        {
            $arguments = array();

            foreach($def['files'] as $catFile)  {
                $arguments[] = '-in "'.$catFile->getPath().'"';
            }

            $outputFolder = X4_EXTRACTED_CAT_FILES_FOLDER.'/'.$source;
            FileHelper::createFolder($outputFolder);

            JSONFile::factory($outputFolder.'/info.json')
                ->setPrettyPrint(true)
                ->setTrailingNewline(true)
                ->putData(array(
                    'id' => $source,
                    'label' => $def['label'],
                    'isExtension' => $source !== CatFileFinder::SOURCE_VANILLA,
                ));

            $arguments[] = '-out "'.$outputFolder.'"';
            $command = X4_CATALOG_TOOL_BINARY.' '.implode(' ', $arguments);

            $all[] = $command;

            $outputFile = FileInfo::factory(__DIR__.'/../../batch/unpack-'.$source.'.bat');
            $outputFile->delete();

            Console::line1('Saving to batch file [%s].', $outputFile->getName());

            FileInfo::factory($outputFile)
                ->putContents(sprintf(
                    self::BATCH_TEMPLATE,
                    $command
                ));
        }

        $outputFile = FileInfo::factory(__DIR__.'/../../batch/unpack-all.bat');
        $outputFile->delete();

        FileInfo::factory($outputFile)
            ->putContents(sprintf(
                self::BATCH_TEMPLATE,
                implode(PHP_EOL, $all)
            ));
    }

    /**
     * @return array<string,array{files:FileInfo[], label:string}>
     */
    private static function getCatFiles() : array
    {
        $result = array();

        foreach((new CatFileFinder(FolderInfo::factory(X4_GAME_FOLDER)))->findFiles() as $file) {
            $source = $file->getRuntimeProperty('source');
            if(!isset($result[$source])) {
                $result[$source] = array(
                    'label' => $file->getRuntimeProperty('label'),
                    'files' => array(),
                );
            }

            $result[$source]['files'][] = $file;
        }

        return $result;
    }

    public static function listDataFolders() : void
    {
        self::init();

        Console::header('Available Data Folders');

        $folders = DataFolders::create(FolderInfo::factory(X4_EXTRACTED_CAT_FILES_FOLDER));
        foreach($folders->getAll() as $folder) {
            Console::line1($folder->getID().' - '.$folder->getLabel());
        }
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
