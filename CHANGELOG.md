# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [1.1.0] - 2017-04-18
### Added
- New configuration option `container_url`. This will help you to retrieve file URLs while using custom CDN domain;
- `SelectelAdapter::getUrl` method to retrieve full URL to given file/directory path. This will allow Laravel to use File URLs just like with `s3` adapter.

## [1.0.1] - 2017-03-11
### Added
- Built-in Service Provider for Laravel Framework;
- Information about Flysystem methods that are not supported by this adapter.

## [1.0.0] - 2017-03-11
First release.

### Added
- Laravel Integration docs.

### Fixed
- Directories are marked as `dir` in content listings;
- Fixed issue with single file retrieving;
- Fixed issue with file/directory sizes detection.

### Removed
- Visibility support.

## [0.9.0] - 2017-03-11
Initial release.

[Unreleased]: https://github.com/ArgentCrusade/flysystem-selectel/compare/1.1.0...HEAD
[1.1.0]: https://github.com/ArgentCrusade/flysystem-selectel/compare/1.0.1...1.1.0
[1.0.1]: https://github.com/ArgentCrusade/flysystem-selectel/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/ArgentCrusade/flysystem-selectel/compare/0.9.0...1.0.0
