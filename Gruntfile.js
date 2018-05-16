const path = require('path');

module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            options: {
                compress: true,
                sourceMap: true,
                mangle: {
                    except: ['jQuery', 'Parsleyjs']
                }
            },
            front: {
                files: {
                    // Front form
                    'static/dist/js/front/form.min.js': [
                        'static/src/js/front/form/wizard.js',
                        'static/src/js/front/form/condition.js',
                        'static/src/js/front/form.js',
                    ],
                    // Parsley (front form validation)
                    'static/dist/vendor/parsley.min.js': [
                        'static/dist/vendor/parsley/**.js',
                        'static/dist/vendor/parsley/i18n/*.js',
                    ],
                    // jQuery
                    'static/dist/vendor/jquery.min.js': [
                        'static/dist/vendor/jquery.js',
                    ],
                }
            },
            admin: {
                files: {
                    // Admin forms CRUD
                    'static/dist/js/admin/insert_update.min.js': [
                        'static/src/js/admin/insert_update.js',
                    ],
                    // Admin forms enhancer
                    'static/dist/js/admin/enhancer.min.js': [
                        'static/src/js/admin/enhancer.js',
                    ],
                    // Admin forms CRUD select custom script
                    'static/dist/js/admin/field/select.min.js': [
                        'static/src/js/admin/field/select.js',
                    ],
                    // Admin forms CRUD checkbox custom script
                    'static/dist/js/admin/field/checkbox.min.js': [
                        'static/src/js/admin/field/checkbox.js',
                    ],
                    // Admin forms CRUD consent checkbox custom script
                    'static/dist/js/admin/field/consent/checkbox.min.js': [
                        'static/src/js/admin/field/consent/checkbox.js',
                    ],
                }
            },
        },
        copy: {
            front: {
                files: [
                    // Copies jquery from the node module
                    {
                        src: [require.resolve('jquery/dist/jquery.js')],
                        dest: 'static/dist/vendor/jquery.js',
                    },
                    // Copies parsley (whole directory with i18n files) from the node module
                    {
                        expand: true,
                        cwd: path.dirname(require.resolve('parsleyjs')),
                        src: '**',
                        dest: 'static/dist/vendor/parsley/',
                    },
                ],
            },
        },
        cssmin: {
            options: {
                shorthandCompacting: false,
                roundingPrecision: -1,
                sourceMap: true,
            },
            front: {
                files: {
                    'static/dist/css/front/form.min.css' : [
                        'static/dist/css/front/form.css',
                    ],
                    'static/dist/css/front/form-foundation.min.css' : [
                        'static/dist/css/front/form-foundation.css',
                    ],
                    'static/dist/css/front/form-bootstrap.min.css' : [
                        'static/dist/css/front/form-bootstrap.css',
                    ],
                }
            },
            admin: {
                files: {
                    'static/dist/css/admin/form.min.css' : [
                        'static/dist/css/admin/form.css',
                    ],
                }
            },
        },
        sass: {
            front: {
                files: {
                    'static/dist/css/front/form.css' : [
                        'static/src/sass/front/form.scss',
                    ],
                    'static/dist/css/front/form-foundation.css' : [
                        'static/src/sass/front/form-foundation.scss',
                    ],
                    'static/dist/css/front/form-bootstrap.css' : [
                        'static/src/sass/front/form-bootstrap.scss',
                    ],
                }
            },
            admin: {
                files: {
                    'static/dist/css/admin/form.css' : [
                        'static/src/sass/admin/form.scss'
                    ],
                }
            },
        },
        watch: {
            css: {
                files: [
                    'static/src/sass/**/*.scss',
                    'static/src/sass/*.scss'
                ],
                tasks: ['sass', 'cssmin']
            },
            js: {
                files: 'static/src/js/**/*.js',
                tasks: ['uglify']
            },
        }
    });

    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-copy');

    grunt.registerTask('default',['watch']);
    grunt.registerTask('build',['copy', 'sass', 'cssmin', 'uglify']);
    grunt.registerTask('build-front',['copy::front', 'sass:front', 'cssmin:front', 'uglify:front']);
    grunt.registerTask('build-admin',['sass:admin', 'cssmin:admin', 'uglify:admin']);
};
