var env = "dev";
var pkg = require('./package.json'),
    gulp = require("gulp"),
    pump = require('pump'),
    copy = require('gulp-copy'),
    clean = require("gulp-clean"),
    concat = require("gulp-concat"),
    cssmin = require("gulp-cssmin"),
    header = require("gulp-header"),
    notify = require("gulp-notify"),
    rename = require("gulp-rename"),
    image = require('gulp-image'),
    rev = require("gulp-rev"),
    sourcemaps = require("gulp-sourcemaps"),
    uglify = require("gulp-uglify"),
    util = require("gulp-util"),
    watch = require("gulp-watch"),
    sass = require("gulp-sass"),
    momentjs = require('moment'),
	  autoprefixer = require('gulp-autoprefixer'),
    cssConfig = require("./gulp-config/css.json"),
    jsConfig = require("./gulp-config/js.json");

//Define file banner
var banner = ['/**',
    ' * <%= pkg.name %> - <%= pkg.description %>',
    ' * @version v<%= pkg.version %>',
    ' * generated at : <%= generatedDate %>',
    ' */',
    '\n'].join('\n');

var base = {
    src: "./src/",
    dist: "./public/templates/publico/"
};
var autoprefixerOptions = {
  browsers: ['last 20 versions', '> 5%', 'Firefox ESR']
};
var taskConfig = {
    style: {
        sass: cssConfig.sass_config,
        css: cssConfig.css_config,
        vendors: cssConfig.vendors
    },
    script: {
        vendors: jsConfig.vendors,
        apps: jsConfig.apps
    }
};

/*======= Tasks =======*/
//All clean
gulp.task('dist_clean', function (cb) {
    pump([
        gulp.src(base.dist),
        clean({ force: true }),
        notify("All contents in " + base.dist + " has been cleared!")
    ], cb);
});


/*===== SASS, CSS tasks ====*/
gulp.task('styles_clean', function () {
    return gulp.src(taskConfig.style.css.dist_path + taskConfig.style.css.dist_filename.replace(".css", ".min.css"), { read: false }).pipe(clean());
});
gulp.task('sass_compile', ['styles_clean'], function (cb) {
    pump([
        gulp.src(taskConfig.style.sass.source_path + taskConfig.style.sass.target_less_filename),
		//sourcemaps.init(),
		sass({ outputStyle: 'compressed' }).on('error', sass.logError),
		//sourcemaps.write(),
		autoprefixer(autoprefixerOptions),
        rename(taskConfig.style.sass.compiled_filename),
        notify('Stylesheet minified!'),
        gulp.dest(taskConfig.style.sass.compiled_output)
    ], cb);
});
gulp.task('init_sass', ['sass_compile'], function () {
    return gulp.watch(taskConfig.style.sass.watch_path, ['sass_compile']);
});


/*===== HTML, PHP, htaccess files ====*/
var templateFileExt = 'html,htm,php,htaccess';
gulp.task('html_clean', function (cb) {
    pump([
        gulp.src('**/*.{' + templateFileExt + '}', { cwd: base.dist }),
        clean({ force: true }),
        gulp.dest(base.dist)
    ], cb);
});
gulp.task('html_copy', ['html_clean'], function (cb) {
    pump([
        gulp.src('**/*.{' + templateFileExt + '}', { cwd: base.src }),
        gulp.dest(base.dist)
    ], cb);
});
gulp.task('html_watch', ["html_copy"], function (cb) {
    return gulp.watch(base.src + '**/*.{' + templateFileExt + '}', function (obj) {
        if (obj.type === 'changed') {
            gulp.src(obj.path, { "base": base.src })
                .pipe(gulp.dest(base.dist))
                .pipe(notify('HTML,PHP modified!'));
        }
    });
});


/*===== .htaccess ====*/
gulp.task('htaccess_clean', function (cb) {
    pump([
        gulp.src('.htaccess', { cwd: base.dist }),
        clean({ force: true }),
        gulp.dest(base.dist)
    ], cb);
});
gulp.task('htaccess_copy', ["htaccess_clean"], function (cb) {
    pump([
        gulp.src('.htaccess.' + env, { cwd: base.src }),
        rename('.htaccess'),
        gulp.dest(base.dist)
    ], cb);
});


/*===== Javascript 3rd party libraries ====*/
gulp.task('js_vendors_clean', function (cb) {
    pump([
        gulp.src(taskConfig.script.vendors.target_path + taskConfig.script.vendors.target_filename.replace(".js",".min.js"), { cwd: base.dist }),
        clean({ force: true }),
        gulp.dest(base.dist)
    ], cb);
});
gulp.task('js_vendors_concat', ['js_vendors_clean'], function () {
    var concatVendorList = taskConfig && taskConfig.script.vendors ? (taskConfig.script.vendors.file_list || []) : [];

    return gulp.src(concatVendorList)
        .pipe(concat(taskConfig.script.vendors.target_filename))
        .pipe(gulp.dest(taskConfig.script.vendors.tmp_path));
});
gulp.task('js_vendors_compress', ['js_vendors_concat'], function (cb) {
    pump([
        gulp.src(taskConfig.script.vendors.tmp_path + taskConfig.script.vendors.target_filename),
        uglify(),
        rename({suffix: '.min'}),
        header(banner, {
            pkg: pkg,
            generatedDate: momentjs().format('YYYY-MM-DD HH:mm:ss')
        }),
        gulp.dest(taskConfig.script.vendors.target_path)
    ],
    cb);
});
gulp.task('init_js_vendors', ['js_vendors_compress'], function (cb) {
    pump([
        gulp.src(taskConfig.script.vendors.tmp_path + taskConfig.script.vendors.target_filename, { read: false }),
        clean()
    ], cb);
});


/*===== App Javascript Tasks ====*/
gulp.task('js_apps_clean', function (cb) {
    pump([
        gulp.src(taskConfig.script.apps.target_path + taskConfig.script.apps.target_filename.replace(".js",".min.js"), { cwd: base.dist }),
        clean({ force: true }),
        gulp.dest(base.dist)
    ], cb);
});
gulp.task('js_apps_concat', ['js_apps_clean'], function () {
    var concatAppsList = taskConfig && taskConfig.script.apps ? (taskConfig.script.apps.file_list || []) : [];

    return gulp.src(concatAppsList)
        .pipe(concat(taskConfig.script.apps.target_filename))
        .pipe(gulp.dest(taskConfig.script.apps.tmp_path));
});
gulp.task('js_apps_compress', ['js_apps_concat'], function (cb) {
    pump([
        gulp.src(taskConfig.script.apps.tmp_path + taskConfig.script.apps.target_filename),
        uglify(),
        rename({suffix: '.min'}),
        header(banner, {
            pkg: pkg,
            generatedDate: momentjs().format('YYYY-MM-DD HH:mm:ss')
        }),
        gulp.dest(taskConfig.script.apps.target_path)
    ],
    cb);
});
gulp.task('js_apps_produce', ['js_apps_compress'], function (cb) {
    pump([
        gulp.src(taskConfig.script.apps.tmp_path + taskConfig.script.apps.target_filename, { read: false }),
        clean(),
        notify("JS application minified!")
    ], cb);
});
gulp.task('init_js_apps', ['js_apps_produce'], function () {
    return gulp.watch(taskConfig.script.apps.watch_path, ['js_apps_produce']);
});


/*===== Assets tasks & watcher ====*/
var imageOptions = {
        pngquant: false,
        optipng: false,
        zopflipng: true,
        jpegRecompress: false,
        jpegoptim: true,
        mozjpeg: true,
        gifsicle: true,
        concurrent: 10
    },
    imageFormat = 'jpg,jpeg,png,gif,gif,JPG,JPEG,PNG,GIF',
    assetFormat = 'svg,eot,ttf,woff,woff2,SVG,EOT,TTF,WOFF,WOFF2';

// For images
gulp.task('images_clean', function (cb) {
    pump([
        gulp.src('**/*.{' + imageFormat + '}', { cwd: base.dist }),
        clean({ force: true }),
        gulp.dest(base.dist)
    ], cb);
});
gulp.task('images_copy', ['images_clean'], function (cb) {
    pump([
        gulp.src('**/*.{' + imageFormat + '}', { cwd: base.src }),
        image(imageOptions),
        gulp.dest(base.dist)
    ], cb);
});

//For any other assets like fonts etc.
gulp.task('assets_clean', function (cb) {
    pump([
        gulp.src('**/*.{' + assetFormat + '}', { cwd: base.dist }),
        clean({ force: true }),
        gulp.dest(base.dist)
    ], cb);
});
gulp.task('assets_copy', ['assets_clean'], function (cb) {
    pump([
        gulp.src('**/*.{' + assetFormat + '}', { cwd: base.src }),
        gulp.dest(base.dist)
    ], cb);
});
gulp.task('assets_watch', ["assets_copy"], function (cb) {
    return gulp.watch(base.src + '**/*.{' + [imageFormat, assetFormat].join(',') + '}', function (obj) {
        if (obj.type === 'changed') {
            if (/[.]((jpg)|(jpeg)|(png)|(gif))$/gi.test(obj.path)) {
                gulp.src(obj.path, { "base": base.src })
                    .pipe(image(imageOptions))
                    .pipe(gulp.dest(base.dist));
            } else {
                gulp.src(obj.path, { "base": base.src })
                    .pipe(gulp.dest(base.dist));
            }
        }
    });
});

//gulp.task("default", ["images_copy", "html_watch", "assets_watch", "init_js_vendors", "init_js_apps", "init_sass"]);
gulp.task("default", ["html_watch", "assets_watch", "init_js_vendors", "init_js_apps", "init_sass"]);

gulp.task("clean-dist", ["dist_clean"]);
