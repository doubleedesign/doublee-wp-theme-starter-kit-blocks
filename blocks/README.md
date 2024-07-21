# WordPress theme foundation

Block-based parent theme designed for use with my [WordPress starterkit](https://github.com/doubleedesign/doublee-wordpress-starterkit).

## Block folders

- `core` - overrides for output of WordPress core blocks
- `custom` - overrides for output of blocks added via my fork of the Gutenberg plugin
- `doublee` - blocks added in this theme.

General idea is that this theme does not require a build step other than compiling SCSS. Blocks with sufficient
complexity in the editor to require a JavaScript build step are added through my version of the plugin. 

## New block boilerplate

From the theme root, run:

```bash
npx generate-vue-cli component block-name
```

The name is a bit of a misnomer since by default new blocks are not currently Vue components*. But hey, it does the job.

**Note:** At the time of writing, the automatic file renaming for the SCSS file was not working, so just rename the generated `_template-name.scss` to suit your block.

- Edit `block.json` to set an [icon](https://wordpress.github.io/gutenberg/?path=/story/icons-icon--library) and description; modify the allowed block parents, available style names, whether it supports background colours; etc.
- Configure block output in `index.php`.

These files can also be copied into client themes to override things on a per-site basis. They should be placed in `/components/blocks/`.

_*I initially did attempt to do everything with Vue, but ran into problems with other JavaScript-powered elements like forms. For an example of how Vue can be used, see `components/layout/header` in the client theme boilerplate._

## Block customisation boilerplate

The generator can also be used to generate the boilerplate files for customising a core block or a block in my customised Gutenberg plugin:

```bash
npx generate-vue-cli component block-name --type=core
```
```bash
npx generate-vue-cli component block-name --type=custom
```

## Block output utilities 

Utilities for standardising and simplifying output of blocks are located in `inc/class-block-utils.php`. The standard block opening and closing code is already included in the `index.php` boilerplate, but other utilities are also available in this file such as `get_acf_field_for_block`.
