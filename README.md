# X4 Game Files Extractor

X4 helper: Batch file generator to extract X4 game files with the XRCatTool.

Analyzes a local X4 installation to detect all `.cat` files for the base game 
and installed DLCs, and generates batch files to extract them to the configured 
output folder.

## Requirements

- PHP 8.2 or higher.
- [Composer](https://getcomposer.org/).
- [X Catalog Tool][] (requires a forum account).

## Extracting Game Files

1. Clone this repository.
2. Copy `dev-config.php.dist` to `dev-config.php`.
3. Edit `dev-config.php` to set the required paths.
4. Run `composer install` to install the dependencies.
5. Run `composer build-batches` to generate the batch files.
6. Open the `batches` folder in the repository root. 
7. Run any of the generated batch files to extract the game files.

> Use the `unpack-all.bat` file to extract all files at once.

### Delete Output folder first

To ensure a clean extraction, delete the output folder
first. You can use this command in PowerShell:

```ps
Remove-Item -Recurse -Force output
```

> NOTE: This will run for a while without any progress indication.

## Accessing Extracted Files

### Structure 

To be able to recognize whether files belong to the base game or a DLC,
a folder is created for the base game and each DLC separately. 
This is important for modding, as changing files belonging to a DLC 
requires using a matching folder structure in those mods.

### Programmatic Access

When used as a library (installed via Composer), the `X4GameInfo` 
class can be used to easily access information on the game and
the available extensions.

```php
use \Mistralys\X4\ExtractedData\X4GameInfo;

$gameInfo = X4GameInfo::create();

// The game version, e.g. `8.0.0.0`
$version = $gameInfo->getGameVersion();

$dataFolders = $gameInfo->getFolderCollection();

// Show a list of all data folders
foreach($dataFolders->getAll() as $folder)
{
    echo $folder->getLabel().PHP_EOL;
}
```

### Locally Extracted Folders

When working with a local copy of extracted folders (with
this tool), set the path to the folder to access the full 
data folder paths to get at the files contained within.

```php
use \Mistralys\X4\ExtractedData\X4GameInfo;

$folders = X4GameInfo::create()
    ->setExtractedDataFolder('/path/to/output/folder')
    ->getFolderCollection();

foreach($folders->getAll() as $folder) {
    $path = $folder->getPath();
}
```

### Manual Access 

An `info.json` file is generated in each folder, which provides some basic
information on the source of the files. This is the JSON generated for the 
Boron DLC, for example:

```json
{
  "id": "ego_dlc_boron",
  "label": "Kingdom End",
  "isExtension": true
}
```

[X Catalog Tool]: https://www.egosoft.com/download/x4/bonus_en.php?download=598
