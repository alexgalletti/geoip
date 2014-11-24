var gulp       = require('gulp');
var sass       = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var minifycss  = require('gulp-minify-css');
var rename     = require('gulp-rename');
var del        = require('del');

var paths = {
  js: ['./resources/js/*.js'],
  css: ['./resources/css/*.css'],
  sass: ['./resources/scss/*.scss']
};

gulp.task('clean', function(cb) {
  del(['./public/assets/**/*.*'], cb);
});

gulp.task('sass', function() {
  return gulp.src(paths.sass)
    .pipe(sourcemaps.init())
    .pipe(sass())
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('./public/assets/css'));
});

gulp.task('minifycss', function() {
  return gulp.src(paths.css)
    .pipe(minifycss())
    .pipe(rename({extname: '.min.css'}))
    .pipe(gulp.dest('./public/assets/css'));
});

gulp.task('watch', function() {
  gulp.watch(paths.sass, ['sass']);
  gulp.watch(paths.css, ['minifycss']);
});

gulp.task('default', ['clean', 'sass', 'minifycss']);
