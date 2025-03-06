"use strict";

const gulp = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const concat = require("gulp-concat");
const minify = require("gulp-minify");
const cleanCss = require("gulp-clean-css");
const sourcemaps = require("gulp-sourcemaps");
const browserSync = require("browser-sync").create();
const { exec } = require("child_process");

// Compile Global SCSS
function globalCss() {
  return gulp
    .src("./src/global.scss")
    .pipe(sass().on("error", sass.logError))
    .pipe(gulp.dest("./css"))
    .pipe(browserSync.stream());
}

// Concatenate JS files
function jsConcat() {
  return gulp
    .src(["./js/libs.js", "./js/main.js"])
    .pipe(concat("scripts.js"))
    .pipe(gulp.dest("./js"))
    .pipe(browserSync.stream());
}

// Minify JavaScript
function packJs() {
  return gulp
    .src(["./js/scripts.js"])
    .pipe(concat("scripts-min.js"))
    .pipe(
      minify({
        ext: {
          min: ".js",
        },
        noSource: true,
      })
    )
    .pipe(gulp.dest("./js"))
    .pipe(browserSync.stream());
}

// Minify CSS
function packCss() {
  return gulp
    .src(["./css/global.css"])
    .pipe(sourcemaps.init())
    .pipe(cleanCss())
    .pipe(sourcemaps.write())
    .pipe(gulp.dest("./styles"))
    .pipe(browserSync.stream());
}

// Compile Tailwind CSS
function tailwindCss(done) {
  exec(
    "npx tailwindcss -i ./src/input.css -o ./styles/output.min.css --minify",
    (error, stdout, stderr) => {
      if (error) {
        console.error(`Tailwind Error: ${error}`);
        done(error);
      } else {
        console.log(`Tailwind CSS compiled successfully`);
        browserSync.reload();
        done();
      }
    }
  );
}

// Live reload with BrowserSync
function browserSyncIt() {
  browserSync.init({
    proxy: "https://dreami-wp.web",
    notify: false,
  });

  // Watch SCSS files
  gulp.watch("./src/**/*.scss", gulp.series(globalCss, packCss));

  // Watch JS files
  gulp.watch(["./js/libs.js", "./js/main.js"], gulp.series(jsConcat, packJs));

  // Watch PHP files (triggers Tailwind rebuild)
  gulp.watch("**/*.php", gulp.series(tailwindCss));

  // Reload on any changes
  gulp
    .watch(["./css/**/*.css", "./js/**/*.js"])
    .on("change", browserSync.reload);
}

exports.browserSyncIt = browserSyncIt;
exports.globalCss = globalCss;
exports.jsConcat = jsConcat;
exports.packJs = packJs;
exports.packCss = packCss;
exports.tailwindCss = tailwindCss;

// Default Gulp Task
gulp.task("default", browserSyncIt);
