# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.2.3] - 2019-04-24
### Fixed
- Due to a bug at Tikkie, dates may be returned as a epoch timestamp with milliseconds instead of an ISO-8601 formatted string. This has been reported to Tikkie, and while we wait support for those timestamps has been added to the library. 

## [0.2.2] - 2018-10-31
### Changed
- All Guzzle-related exceptions are now turned into a `RequestException`.

## [0.2.1] - 2018-09-27
### Changed
- Trace ID of requests is now included in exception messages. ([#3](https://github.com/jarnovanleeuwen/php-tikkie/pull/3))

## [0.2.0] - 2018-06-20
### Added
- Added a test suite.

### Changed
- PHPTikkie now requires PHP 7.1+

## [0.1.2] - 2018-06-19
### Fixed
- Cast `from` and `to` dates to UTC time zone. ([#1](https://github.com/jarnovanleeuwen/php-tikkie/pull/1))

## [0.1.1] - 2018-03-05
### Fixed
- Check if responses are valid JSON.
- Fix the class name of `ResponseException`.

## [0.1.0] - 2017-12-12
### Added
- Initial implementation of the Tikkie API.
