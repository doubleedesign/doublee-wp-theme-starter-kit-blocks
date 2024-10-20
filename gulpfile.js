import { readFile, writeFile } from 'fs';
import path from 'path';
import gulp from 'gulp';
import sassGlob from 'gulp-sass-glob';
import jsonToScss from '@valtech-commerce/json-to-scss';
import sass from 'gulp-dart-sass';
import sourcemaps from 'gulp-sourcemaps';
import header from 'gulp-header';

// Generate SCSS variables from theme-vars.json file
function variables(done) {
	readFile(`./theme-vars.json`, 'utf8', async (error, theme) => {
		if (error) {
			console.log(error);
			done();
		} else {
			const scss = jsonToScss.convert(`${theme}`);
			if (scss) {
				await writeFile('common/scss/_variables.scss', scss, '', () => {
					console.log('theme-vars.json converted to SCSS variables');
					done();
				});
			} else {
				console.log('Problem with converting theme-vars.json to SCSS variables');
				done();
			}
		}
	});
}

// Bundle up all theme styles to be served on the front-end
function theme() {
	return gulp.src('common/scss/style.scss')
		.pipe(sourcemaps.init())
		.pipe(sassGlob())
		.pipe(sass().on('error', sass.logError))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest('./'));
}

// Compile individual component styles for use in Storybook
function components() {
	return gulp.src('components/**/*.scss', { base: 'components' })
		// .on('data', function(file) {
		// 	console.log('Found file:', path.relative('modules', file.path));
		// })
		.pipe(sourcemaps.init())
		.pipe(header('@import "../common";')) // Prepend core SCSS to each file
		.pipe(sass().on('error', sass.logError))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest(file => {
			const relativePath = path.relative('components', file.base);
			return path.join('components', relativePath);
		}))
}

// Shared custom styles for all blocks
function block_styles_global() {
	return gulp.src('blocks/global.scss')
		.pipe(sourcemaps.init())
		.pipe(header('@import "./common";')) // Prepend core SCSS imports
		.pipe(sass().on('error', sass.logError))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest('blocks'));
}

// CSS overrides for native blocks
function block_styles_core() {
	return gulp.src('blocks/core/core.scss', { base: 'blocks' })
		.pipe(sourcemaps.init())
		.pipe(sassGlob())
		.pipe(header('@import "../common";')) // Prepend core SCSS imports
		.pipe(sass().on('error', sass.logError))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest('blocks'));
}

// Compile individual block styles
function block_styles() {
	return gulp.src(['blocks/**/*.scss', '!blocks/global.scss', '!blocks/core/core.scss'], { base: 'modules' })
		// .on('data', function(file) {
		// 	console.log('Found file:', path.relative('modules', file.path));
		// })
		.pipe(sourcemaps.init())
		.pipe(header('@import "../../common";')) // Prepend core SCSS imports to each file
		.pipe(sass().on('error', sass.logError))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest(file => {
			const relativePath = path.relative('blocks', file.base);
			return path.join('blocks', relativePath);
		}))
}

// Subset of core shared styles to also be loaded in the editor
function editor() {
	return gulp.src('common/scss/styles-editor.scss')
		.pipe(sourcemaps.init())
		.pipe(sass().on('error', sass.logError))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest('./'));
}

// Compile admin CSS customisations
function admin() {
	return gulp.src('common/scss/styles-admin.scss')
		.pipe(sass().on('error', sass.logError))
		.pipe(gulp.dest('./'));
}


function watchFiles() {
	const options = { events: ['change', 'add', 'unlink'], ignoreInitial: false};

	// Recompile everything if the theme variables change
	gulp.watch('theme-vars.json', options, gulp.series(variables, components, block_styles, theme, editor, admin));

	// Compile the whole-theme stylesheet and editor styles when anything other than _variables.scss changes
	gulp.watch(['common/scss/**/*.scss', 'components/**/*.scss', 'blocks/**/*/scss', '!**/_variables.scss'], options, gulp.parallel(theme, editor));

	// General UI components
	gulp.watch('components/**/*.scss', options, components);

	// Block SCSS
	gulp.watch('blocks/**/*.scss', options, gulp.parallel(block_styles_global, block_styles, block_styles_core));

	// Admin and editor styles
	gulp.watch('common/scss/styles-admin.scss', options, gulp.parallel(admin));
	gulp.watch('common/scss/styles-editor.scss', options, gulp.parallel(editor));
}

function blocks(cb) {
	gulp.parallel(block_styles_global, block_styles, block_styles_core)(cb);
}

export {
	variables,
	theme,
	components,
	blocks,
	editor,
	admin
};

export default watchFiles;
