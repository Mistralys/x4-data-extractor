<?php

declare(strict_types=1);

namespace Mistralys\X4\DataExtractor;

use AppUtils\FileHelper;
use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\FolderInfo;
use AppUtils\FileHelper\JSONFile;
use AppUtils\Microtime;
use Mistralys\X4\ExtractedData\DataFolders;
use Mistralys\X4\ExtractedData\Console;
use Mistralys\X4\ExtractedData\X4GameInfo;
use const Mistralys\X4\X4_CATALOG_TOOL_BINARY;
use const Mistralys\X4\X4_EXTRACTED_CAT_FILES_FOLDER;
use const Mistralys\X4\X4_GAME_FOLDER;

class BuildTools
{
    private const string BATCH_TEMPLATE = <<<'BATCH'
@echo off

%s

pause

BATCH;

    private static array $data = array();

    public static function build() : void
    {
        self::init();

        self::$data[X4GameInfo::KEY_GAME_VERSION] = '';
        self::$data[X4GameInfo::KEY_DATE_GENERATED] = Microtime::createNow()->getISODate(true);
        self::$data[X4GameInfo::KEY_DATA_FOLDERS] = array();

        $catFiles = self::getCatFiles();

        self::generateBatchFiles($catFiles);
        self::generateInfoFiles($catFiles);
        self::extractGameInfo();

        X4GameInfo::create()->getInfoFile()->putData(self::$data);
    }

    /**
     * @param CatFile[] $catFiles
     * @return void
     */
    private static function generateBatchFiles(array $catFiles) : void
    {
        Console::header('Generating batch files');

        $all = array();
        foreach($catFiles as $def)
        {
            Console::line1($def->getLabel());

            $arguments = array();

            foreach($def->getFiles() as $catFile)  {
                $arguments[] = '-in "'.$catFile->getPath().'"';
            }

            $outputFolder = X4_EXTRACTED_CAT_FILES_FOLDER.'/'.$def->getName();

            $arguments[] = '-out "'.$outputFolder.'"';
            $commands = [];
            $commands[] = 'echo -----------------------------------------------------';
            $commands[] = sprintf('echo EXTRACT: %s', $def->getLabel());
            $commands[] = 'echo -----------------------------------------------------';
            $commands[] = 'echo.';

            // Delete the folder if it exists for a clean extraction
            $commands[] = sprintf('if exist "%1$s" rmdir /S /Q "%1$s"', $outputFolder);

            // Ensure the output folder exists (if not exists just as a failsafe)
            $commands[] = sprintf('if not exist "%1$s" mkdir "%1$s"', $outputFolder);

            // Extract command
            $commands[] = X4_CATALOG_TOOL_BINARY.' '.implode(' ', $arguments);
            $commands[] = 'echo.';

            array_push($all, ...$commands);

            $outputFile = FileInfo::factory(__DIR__.'/../../batch/unpack-'.$def->getName().'.bat');
            $outputFile->delete();

            Console::line2('Saving to batch file [%s].', $outputFile->getName());

            FileInfo::factory($outputFile)
                ->putContents(sprintf(
                    self::BATCH_TEMPLATE,
                    implode(PHP_EOL, $commands)
                ));

            Console::nl();
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
     * @param CatFile[] $catFiles
     * @return void
     */
    private static function generateInfoFiles(array $catFiles) : void
    {
        Console::header('Generating info files');

        foreach($catFiles as $def)
        {
            $outputFolder = X4_EXTRACTED_CAT_FILES_FOLDER.'/'.$def->getName();

            Console::line1($def->getLabel());

            $data = array(
                X4GameInfo::KEY_FOLDER_ID => $def->getName(),
                X4GameInfo::KEY_FOLDER_LABEL => $def->getLabel(),
                X4GameInfo::KEY_FOLDER_IS_EXTENSION => $def->getName() !== CatFileFinder::SOURCE_VANILLA,
            );

            self::$data[X4GameInfo::KEY_DATA_FOLDERS][] = $data;

            JSONFile::factory($outputFolder.'/info.json')
                ->setPrettyPrint(true)
                ->setTrailingNewline(true)
                ->putData($data);
        }

        Console::nl();
    }

    private static function extractGameInfo() : void
    {
        Console::header('Extracting game info');

        $version = self::getX4Version();

        self::resolveGameVersionFile()
            ->putData(array(
                X4GameInfo::KEY_GAME_VERSION => $version
            ));

        self::$data[X4GameInfo::KEY_GAME_VERSION] = $version;

        Console::nl();
    }

    private static function resolveGameVersionFile() :JSONFile
    {
        return JSONFile::factory(X4_EXTRACTED_CAT_FILES_FOLDER.'/'. X4GameInfo::FILE_GAME_INFO)
            ->setPrettyPrint(true)
            ->setTrailingNewline(true)
            ->setEscapeSlashes(false);
    }

    /**
     * @return CatFile[]
     */
    private static function getCatFiles() : array
    {
        $result = array();

        foreach((new CatFileFinder(FolderInfo::factory(X4_GAME_FOLDER)))->findFiles() as $file) {
            $source = $file->getRuntimeProperty('source');
            if(!isset($result[$source])) {
                $result[$source] = new CatFile(
                    $source,
                    (string)$file->getRuntimeProperty('label')
                );
            }

            $result[$source]->addFile($file);
        }

        return array_values($result);
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


    /**
     * Gets the version of X4: Foundations from its executable.
     * @return string|null Version string (e.g., "7.10.0.0") or null on failure
     */
    public static function getX4Version(): ?string
    {
        self::init();

        $path = X4_GAME_FOLDER.'/X4.exe';

        if(!file_exists($path)) {
            Console::line1('X4 executable not found.');
            Console::line1('Expected path: %s', $path);
            return null;
        }

        // PowerShell command to extract the ProductVersion specifically
        $command = 'powershell -command "(Get-Item \'' . $path . '\').VersionInfo.ProductVersion"';

        // Execute and trim whitespace/newlines
        $version = shell_exec($command);

        $result = $version ? trim($version) : null;

        Console::line1('Detected X4 version: %s', $result ?? 'N/A');

        return $result;
    }
}
