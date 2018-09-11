# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [1.0.19][] - 2018-08-21

### Fixed
- Enable WSDL cache to prevent it from being loaded everytime.
- Add validation for deliveryMethod.

### Changed
- Change temando report cron group to default to be consistent with Balance's repo.

## [1.0.18][] - 2018-06-29

### Added
- Set up a cron to send an email with lists of Temando shipment and storepickup every days at 2 AM

## [1.0.17][] - 2018-06-18

### Fixed
- Create upgrade schema to modify column type from text to mediumtext for consignment and label documents.

## [1.0.16][] - 2018-05-04

### Added
- Add last 4 CC number and TT staff name on pickslip.
- Add additional column - Customer Name for both shipment and pickup grids.
- Add default sorting in UiComponent xml.

### Fixed
- Remove default sorting order from collection initSelect function to fix current sorting issue.
- Fix bug for checkout message.

## [1.0.15][] - 2018-01-25

### Changed
- Revert message changes on checkout page.
- Add missing break in loop statement.
- Define different constants for message on product page.

## [1.0.14][] - 2018-01-10

### Changed
- Create and start to log temando exceptions in a new temando.log
- Make sure files are synchronized in temando gitlab and balance bitbucket 

## [1.0.13][] - 2017-12-21

### Changed
- Update stock level messages for both product and checkout page
- Update import inventory script

## [1.0.12][] - 2017-11-13

### Added
- Add stock level message on product page

## [1.0.11][] - 2017-10-27

### Added
- Add ACL resource and permission check for cancel actions of both shipment and pickups
- Add popup confirmation message when pressed Cancel button for both shipment and pickups
- Update pickslip to add customer email, brand and part no

[1.0.19]: https://src.temando.io/magento-v2/total-tools-m2/compare/1.0.18...1.0.19
[1.0.18]: https://src.temando.io/magento-v2/total-tools-m2/compare/1.0.17...1.0.18
[1.0.17]: https://src.temando.io/magento-v2/total-tools-m2/compare/1.0.16...1.0.17
[1.0.16]: https://src.temando.io/magento-v2/total-tools-m2/compare/1.0.15...1.0.16
[1.0.15]: https://src.temando.io/magento-v2/total-tools-m2/compare/1.0.14...1.0.15
[1.0.14]: https://src.temando.io/magento-v2/total-tools-m2/compare/1.0.13...1.0.14
[1.0.13]: https://src.temando.io/magento-v2/total-tools-m2/compare/1.0.12...1.0.13
[1.0.12]: https://src.temando.io/magento-v2/total-tools-m2/compare/1.0.11...1.0.12
[1.0.11]: https://src.temando.io/magento-v2/total-tools-m2/tree/1.0.11