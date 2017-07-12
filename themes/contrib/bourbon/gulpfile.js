var gulp = require('gulp'),
    sass = require('gulp-sass');


var paths = {
	scss: 'scss/*.scss'
};

gulp.task('styles', function () {
	return gulp.src(paths.scss)
		.pipe(sass({
		     includePaths: require('node-neat').includePaths
		}).on('error', sass.logError))
		     .pipe(gulp.dest('css/'));
		});

		gulp.task('default',function(){
		     gulp.watch(paths.scss, ['styles']);
		});

