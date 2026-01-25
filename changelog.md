# X4 Data Extractor - Changelog

## v2.0.0 - Bundled Data (Breaking-L)
- Data: Game and folder information is now bundled in the library.
- Data: Added the `X4GameInfo` class.
- Data: The X4 game version is now available.
- Data: The game version and folders are now bundled with the library.

### Breaking Changes
- Some methods were renamed.
- Use `X4GameInfo` as factory class for data folders.
- Extracted folder path is now optional, via setter method.

## v1.0.1 - Folder Handling
- Batches: Now auto-cleaning existing output folders.
- Batches: Prettier CLI output.

## v1.0.0 - Initial Release
- Initial release with split data folders by extension.
