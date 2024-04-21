import gulp from 'gulp';
import sassGlob from 'gulp-sass-glob';
import jsonToScss from '@valtech-commerce/json-to-scss';
import { readFile, writeFile } from 'fs';
import sass from 'gulp-dart-sass';
import sourcemaps from 'gulp-sourcemaps';

// Generate SCSS variables from theme-vars.json file
gulp.task('scss-variables', (done) => {
	readFile(`./theme-vars.json`, 'utf8', async(error, theme) => {
		if (error) {
			console.log(error);
			done();
		}
		const scss = jsonToScss.convert(`${theme}`);
		if (scss) {
			await writeFile('common/scss/_variables.scss', scss, '', () => {
				console.log('theme.json converted to SCSS variables');
				done();
			});
		}
		else {
			console.log('Problem with converting theme.json to SCSS variables');
			done();
		}
	});

});

// Generate the core WordPress theme stylesheet
gulp.task('theme-css', (done) => {
	gulp.src('common/scss/style.scss')
		.pipe(sourcemaps.init())
		.pipe(sassGlob())
		.pipe(sass())
		.pipe(sourcemaps.write())
		.pipe(gulp.dest('./'));
	done();
});

// Subset of core shared styles to also be loaded in the editor
gulp.task('editor-css', (done) => {
	gulp.src('common/scss/editor.scss')
		.pipe(sourcemaps.init())
		.pipe(sassGlob())
		.pipe(sass())
		.pipe(sourcemaps.write())
		.pipe(gulp.dest('./assets/'));
	done();
});

// Admin UI-specific styles
gulp.task('admin-css', (done) => {
	gulp.src('common/scss/admin.scss')
		.pipe(sourcemaps.init())
		.pipe(sassGlob())
		.pipe(sass())
		.pipe(sourcemaps.write())
		.pipe(gulp.dest('./assets/'));
	done();
});

gulp.task('default', function() {
	gulp.watch('theme-vars.json', { ignoreInitial: false }, gulp.series('scss-variables'));
	gulp.watch(
		'**/*.scss', {
			ignoreInitial: false,
			events: ['change'],
		},
		gulp.parallel('theme-css', 'editor-css'),
	);
	gulp.watch('common/scss/admin.scss', { ignoreInitial: false }, gulp.series('admin-css'));
});
