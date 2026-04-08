# Changelog
All notable changes to this project will be documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [v2.1.0] - 2026-04-08
### Changed
- Finalize the Laravel 13 support line on `2.1.x`
- Raise the package baseline to PHP 8.4, Illuminate 13, Testbench 11, and PHPUnit 12.5
- Add package metadata for repository homepage and support links

### Fixed
- Restore legacy snake_case API forwarding such as `get_option()` and `set_option()`
- Add a direct Composer dependency on `secretwebmaster/wncms-translatable` because the `Option` model uses its translation trait at runtime
- Make the test bootstrap work both in the standalone package repository and when the package is exercised from the WNCMS monorepo

### Added
- Add basic feature coverage for scoped options, grouped options, repeatable rows, nested JSON access, batch replacement, and legacy method aliases

### Notes
- Translation support currently resolves through `secretwebmaster/wncms-translatable`. Until that package publishes a dedicated Laravel 13 tag, Composer may resolve its compatible `dev-main` alias during installation.

---

## [v2.0.2] - 2025-12-08
### Fixed
- Correct the migration filename extension

## [v2.0.1] - 2025-12-08
### Fixed
- Improve dynamic index cleanup and table prefix handling in the v2 migration

## [v2.0.0] - 2025-12-08
### Added
- Rewrite the package around structured options with `scope`, `group`, and `sort`
- Add JSON-style nested option access and repeatable option rows
- Add translation-aware option values through `HasTranslations`
