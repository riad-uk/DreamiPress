"use strict";

const gulp = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const concat = require("gulp-concat");
const minify = require("gulp-minify");
const cleanCss = require("gulp-clean-css");
const sourcemaps = require("gulp-sourcemaps");
const browserSync = require("browser-sync").create();
const { exec } = require('child_process');

function globalCss() {
  return gulp
    .src("./src/global.scss")
    .pipe(sass({}).on("error", sass.logError))
    .pipe(gulp.dest("./css"))
    .pipe(browserSync.stream());
}

function jsConcat() {
  return gulp
    .src(["./js/libs.js", "./js/main.js"])
    .pipe(concat("scripts.js"))
    .pipe(gulp.dest("./js"));
}

function packJs() {
  return gulp
    .src(["./js/scripts.js"])
    .pipe(concat("scripts-min.js"))
    .pipe(minify({
      ext: {
        min: '.js'
      },
      noSource: true
    }))
    .pipe(gulp.dest("./js"));
}

function packCss() {
  return gulp
    .src(["./css/global.css"])
    .pipe(sourcemaps.init())
    .pipe(cleanCss())
    .pipe(sourcemaps.write())
    .pipe(gulp.dest("./styles"));
}

function tailwindCss() {
  return new Promise((resolve, reject) => {
    exec('npx @tailwindcss/cli -i ./src/input.css -o ./src/output.css', (error, stdout, stderr) => {
      if (error) {
        console.error(`Error: ${error}`);
        reject(error);
      } else {
        console.log(`Tailwind CSS compiled successfully`);
        resolve();
      }
    });
  });
}

function browserSyncIt() {
  browserSync.init({
    proxy: "https://dreami-wp.web",
    target: "https://dreami-wp.web",
    ws: true,
    notify: false,
  });
  gulp.watch("./sass/*.scss", gulp.series(["globalCss", "packCss"]));
  gulp.watch("**/*.php", gulp.series(["tailwindCss"])).on("change", browserSync.reload);
  gulp
    .watch(
      ["./js/libs.js", "./js/main.js"],
      gulp.series(["jsConcat", "packJs"])
    )
    .on("change", browserSync.reload);
}

exports.browserSyncIt = browserSyncIt;
exports.globalCss = globalCss;
exports.jsConcat = jsConcat;
exports.packJs = packJs;
exports.packCss = packCss;
exports.tailwindCss = tailwindCss;

gulp.task("default", browserSyncIt);
