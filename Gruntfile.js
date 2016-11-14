module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            options: {
                mangle: false,
                compress: false,
                sourceMap: true,
                // mangle: {
                //     except: ['jQuery']
                // }
            },
            front: {
                files: {
                    // Front form
                    'static/dist/js/front/form.min.js': [
                        'static/src/js/front/form.js',
                        'static/src/js/front/form/condition.js',
                    ],
                    // Parsley (front form validation)
                    'static/dist/vendor/parsley.min.js': [
                        'static/src/vendor/parsley/parsley.js',
                        'static/src/vendor/parsley/i18n/fr.js',
                        'static/src/vendor/parsley/i18n/fr.js',
                        'static/src/vendor/parsley/i18n/ja.js',
                        'static/src/vendor/parsley/i18n/fr.js',
                        'static/src/vendor/parsley/i18n/pt.js',
                        'static/src/vendor/parsley/i18n/pt-br.js',
                        'static/src/vendor/parsley/i18n/fr.js'
                    ],
                    // jQuery
                    'static/dist/vendor/jquery.min.js': [
                        'static/src/vendor/jquery/jquery.js',
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
                }
            },
        },
        cssmin: {
            options: {
                shorthandCompacting: false,
                roundingPrecision: -1
            },
            front: {
                files: {
                    'static/dist/css/front/form.min.css' : [
                        'static/dist/css/front/form.css',
                    ],
                    'static/dist/css/front/form-foundation.min.css' : [
                        'static/dist/css/front/form-foundation.css',
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

    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default',['watch']);
    grunt.registerTask('build',['sass', 'cssmin', 'uglify']);
    grunt.registerTask('build-front',['sass:front', 'cssmin:front', 'uglify:front']);
    grunt.registerTask('build-admin',['sass:admin', 'cssmin:admin', 'uglify:admin']);
};
