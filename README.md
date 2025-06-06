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

## Accessing Extracted Files

### Structure 

To be able to recognize whether files belong to the base game or a DLC,
a folder is created for the base game and each DLC separately. 
This is important for modding, as changing files belonging to a DLC 
requires using a matching folder structure in mods.

### Information Files

An `info.json` file is generated in each folder, which provides some basic 
information on the source of the files. The helper class `DataFolders` 
can be used to access this information.

This is the JSON generated for the Boron DLC, for example:

```json
{
  "id": "ego_dlc_boron",
  "label": "Kingdom End",
  "isExtension": true
}
```



[X Catalog Tool]: https://www.egosoft.com/download/x4/bonus_en.php?download=598
