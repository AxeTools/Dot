# AxeTools/Dot Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog]
and this project adheres to [Semantic Versioning].

## 1.0.0

### Added

* Initial release of the library 
* `Dot::set()` for setting values at a given key location
* `Dot::get()` for getting value at a given key location
* `Dot::has()` for testing existence of a value at a give key location
* `Dot::flatten()` for flattening a multi-dimension array to a single dimension
* `Dot::append()` for adding/creating array lists at a given key location
* `Dot::delete()` for unsetting data at a given key location
* `Dot::count()` for getting array counts at a given key location
* `DotFunctions.php` has been added and included in the autoload to make wrapper function available globally
  * `dotGet()` has been added to wrap `Dot::Get()` static method
  * `dotSet()` has been added to wrap `Dot::Set()` static method
  * `dotHas()` has been added to wrap `Dot::Has()` static method
  * `dotCount()` has been added to wrap `Dot::Count()` static method
  * `dotDelete()` has been added to wrap `Dot::Delete()` static method
  * `dotAppend()` has been added to wrap `Dot::Append()` static method
  * `dotFlatten()` has been added to wrap `Dot::Flatten()` static method

[Keep a Changelog]:http://keepachangelog.com/en/1.1.0/
[Semantic Versioning]:http://semver.org/spec/v2.0.0.html