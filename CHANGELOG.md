## v0.11.2 (2025-04-02)
* Fixed attributeList iteration

## v0.11.1 (2025-04-02)
* Added attribute key normalizer

## v0.11.0 (2025-04-02)
* Support named arguments for attributes

## v0.10.7 (2025-03-24)
* Fixed PHPStan interface issues

## v0.10.6 (2025-02-20)
* Upgraded Coercion dependency

## v0.10.5 (2025-02-17)
* Removed option Dictionary / Tree key type
* Replaced Tree type template references

## v0.10.4 (2025-02-16)
* Moved ArrayAccess to implementations only

## v0.10.3 (2025-02-16)
* Added input type override to AttributeContainer

## v0.10.2 (2025-02-15)
* Fixed fromDelimitedString() return type

## v0.10.1 (2025-02-15)
* Fixed nested type propagation in PHPStan

## v0.10.0 (2025-02-14)
* Restructured interfaces
* Simplified class structure
* Upgraded PHPStan to v2
* Tidied boolean logic
* Fixed Exceptional syntax
* Added PHP8.4 to CI workflow
* Made PHP8.4 minimum version

## v0.9.3 (2025-02-07)
* Fixed missed implicit nullable arguments

## v0.9.2 (2025-02-07)
* Fixed implicit nullable arguments
* Fixed PHP issues
* Added @phpstan-require-implements constraints

## v0.9.1 (2024-08-21)
* Updated Lucid dependency

## v0.9.0 (2024-08-21)
* Converted consts to protected PascalCaes
* Made PHP8.1 minimum version

## v0.8.8 (2023-11-08)
* Check for empty string in Tree::fromDelimitedString()

## v0.8.7 (2023-11-06)
* Parse empty delimited string params as true

## v0.8.6 (2023-11-01)
* Fixed numeric keys in array delimited sets

## v0.8.5 (2023-11-01)
* Fixed rawurlencode() of int in Tree

## v0.8.4 (2023-10-27)
* Avoid affecting node list in offsetExists in Tree

## v0.8.3 (2023-10-18)
* Updated Lucid dependency

## v0.8.2 (2023-09-26)
* Converted phpstan doc comments to generic

## v0.8.1 (2022-11-23)
* Ensure all Collection interfaces are Traversable
* Migrated to use effigy in CI workflow
* Fixed PHP8.1 testing
* Updated composer check script

## v0.8.0 (2022-09-08)
* Replaced Gadgets Sanitizer with Lucid
* Updated CI environment

## v0.7.3 (2022-08-24)
* Applied static returns to interfaces

## v0.7.2 (2022-08-24)
* Fixed propagate() interface compatibility

## v0.7.1 (2022-08-23)
* Added concrete types to all members

## v0.7.0 (2022-08-22)
* Removed PHP7 compatibility
* Updated ECS to v11
* Updated PHPUnit to v9

## v0.6.2 (2022-03-09)
* Transitioned from Travis to GHA
* Updated PHPStan and ECS dependencies

## v0.6.1 (2021-04-07)
* Updated for max PHPStan conformance

## v0.6.0 (2021-03-18)
* Enabled PHP8 testing

## v0.5.7 (2020-10-06)
* Switched to Fluidity for Then dependency
* Applied full PSR12 standards
* Added PSR12 check to Travis build

## v0.5.6 (2020-10-02)
* Removed Glitch dependency

## v0.5.5 (2020-09-30)
* Switched to Exceptional for exception generation

## v0.5.4 (2020-09-24)
* Updated Composer dependency handling

## v0.5.3 (2019-10-15)
* Numerous fixes and updates for full PHPStan max scan

## v0.5.2 (2019-10-11)
* Updated to Gadgets Then interface

## v0.5.1 (2019-10-04)
* Removed Ds dependency

## v0.5.0 (2019-09-10)
* Added initial codebase (ported from Df)
