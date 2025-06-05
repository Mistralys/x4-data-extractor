# X4 Game Files Extractor

X4 helper: Batch file generator to extract X4 game files with the XRCatTool.

Analyzes a local X4 installation to detect all `.cat` files for the base game 
and installed DLCs, and generates a batch file to extract them all to the 
configured output folder.

## Requirements

- PHP 8.2 or higher.
- [Composer](https://getcomposer.org/).
- [X Catalog Tool][] (requires a forum account).

## Usage

1. Clone this repository.
2. Copy `dev-config.php.dist` to `dev-config.php`.
3. Edit `dev-config.php` to set the required paths.
4. Run `composer install` to install the dependencies.
5. Run `composer build-extractor` to generate the batch file.
6. Run the generated batch file to extract the game files.

[X Catalog Tool]: https://www.egosoft.com/download/x4/bonus_en.php?download=598
