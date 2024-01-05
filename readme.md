# Introduction

This is a plugin which adds a simple shortcode to display a dynamic
year, for use in copyright statements.

Its most basic usage is [year], for the current year, but it will expand
into a date range if the 'from' year is before the current year.

# Usage

Install the plugin and activate it.

Set "from" to apply a range, after "from" year has passed.

// assuming current year is 2024
[year] = 2024

// assuming current year is 2024
[year from="2024"] = 2024

// assuming current year is 2024
[year from="1983"] = 1983-2024

# Download

Download and contribute issues at:

https://github.com/rtpHarry/CurrentYearShortcode-WordPress

# Changelog

1.3.0 - 5th Jan 2024

- Update year examples to 2024

  1.2.0 - 29th May 2023

- Update year examples to 2023

  1.1.0 - 19th February 2022

- Added licence
- Updated plugin meta
- Added readme file
- Initial public release

  1.0.0 - 25th August 2021

- Internal release

# Licence

This plugin is licenced under GPL 3, and is free to use on personal and
commercial projects.

# Author

Built by Matthew Harris of runthings.dev, copyright 2022-2024.

https://runthings.dev/
