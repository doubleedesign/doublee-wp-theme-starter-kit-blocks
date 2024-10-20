## Changes in version 2

### Code structure 
- Moved functions that were duplicated in this and the classic theme into a plugin.
- Moved shared template parts that aren't blocks into `components` and co-located their CSS, for use in Storybook and general consistency.
- Co-located block CSS with their PHP and JSON files, for use in Storybook and selective loading.
- Put shared block styles in a standalone `blocks/global.css` file, again for Storybook integration (and what that represents - good separation of concerns).
- Removed favicon field from global options because it's now available natively in the site general settings (it used to be in the customiser, which I disable).
- Added default header and footer components.
- Other general refactoring and renaming of things to better align this theme with my classic starter theme.
- Added some default colours, fonts, etc. to `theme-vars.json` so that this theme works standalone (child themes can still override them).
- Added loading of fonts and Font Awesome from URLs/kit IDs set in the Global Options page, for ease of per-client setup as well as ensuring I stop loading and distributing my accounts' URLs/kit IDs with this theme.

### Dependencies
- Updated some Bootstrap scripts to the latest version.
- Added Vue ESM browser build and Vue SFC Loader for use in the theme.

### Tooling
- Upgraded to Gulp 5.
- Added compilation of co-located CSS for components and blocks.
