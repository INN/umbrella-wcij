# Changelog
All notable changes to this project will be documented in this file.

This format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [1.2.0]

### Added
- The "Help Us Report" type of work term is now included in the list of default type-of-work terms, providing the AskPublicNewsArticle type of work for schema.org JSON markup. Sites already using the plugin will have this term automatically generated during the update from 1.2.

### Changed
- Provides formatting guidelines for adding citation trust indicators to an article: [#64](https://github.com/INN/trust-indicators/issues/64)
- Authors' known languages are now output in the `knowsLanguage` schema entry as individual entries of `@type` [Language](https://schema.org/Language), instead of as a single string. [#88](https://github.com/INN/trust-indicators/pull/88)

### Fixed

- URLs contained in the citation metadata that are not part of HTML links will now be output as https://schema.org/citation, in addition to URLs that are part of HTML links: [#84](https://github.com/INN/trust-indicators/issues/84)

## [1.1.0]

### Changed

- For sites using [the WordPress.com VIP `*_user_attributes` functions](https://vip.wordpress.com/documentation/user_meta-vs-user_attributes/), user meta keys are prefixed with `'trust_indicators_'`. This limits potential conflicts with other user meta keys in the WordPress.com VIP multisite, as described in [#53](https://github.com/INN/trust-indicators/issues/53). A future release of this plugin will migrate unprefixed user meta to this prefixed format, to simplify code.
- Renames the `'create_type_of_work_terms'` action to `'trust_indicators_init'`: [#56](https://github.com/INN/trust-indicators/pull/56)

### Added

- The plugin version number is now included in the options table, with the option name `'trust_indicators_version'`: [#56](https://github.com/INN/trust-indicators/pull/56)

### Fixed

- Plugin is now approved for WordPress.com VIP usage.
- Functions that run on the activation hook can now run on the first page load after the plugin's activation: [#56](https://github.com/INN/trust-indicators/pull/56)

## [v0.1-beta]

Initial plugin implementation of trust indicators and other plugin features.

[1.2.0]: https://github.com/INN/trust-indicators/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/INN/trust-indicators/compare/v0.1-beta...v1.1.0
[v0.1-beta]: https://github.com/INN/trust-indicators/compare/c01f3a7cdb52552eff08be7da5b1a23ec1e21f38...v0.1-beta
