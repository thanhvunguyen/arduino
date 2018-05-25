module.exports = function (grunt) {
    'use strict';

    require('time-grunt')(grunt);
    require('jit-grunt')(grunt, {
        replace: 'grunt-text-replace'
    });

    // Init
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        base: {
            src: 'source',
            pub: 'public_html',
            tmp: ['files', 'csstoc.json']
        },
        clean: {
            tmp: '<%= base.tmp %>',
            pub: '<%= base.pub %>'
        },
        sass: {
            dist: {
                options: {
                    sourcemap: 'file',
                    noCache: true,
                    style: 'expanded'
                },
                files: [{
                    src: 'main.scss',
                    cwd: '<%= base.src %>/assets/scss',
                    dest: '<%= base.pub %>/assets/css',
                    ext: '.css',
                    expand: true
                }]
            }
        },
        postcss: {
            options: {
                map: false,
                processors: [
                    require('autoprefixer')({
                        browsers: ['last 10 versions', 'ie 9'],
                        cascade: false,
                        remove: true
                    })
                ]
            },
            main: {
                src: '<%= base.pub %>/assets/css/main.css',
                dest: '<%= base.pub %>/assets/css/main.css'
            }
        },
        csscomb: {
            options: {
                config: '.csscomb.json'
            },
            files: '<%= base.pub %>/assets/css/main.css'
        },
        cssmin: {
            options: {
                compress: true
            },
            app: {
                src: ['<%= base.pub %>/assets/css/main.css'],
                dest: '<%= base.pub %>/assets/css/main.min.css'
            }
        },
        jshint: {
            options: {
                jshintrc: true,
                force: false
            },
            files: '<%= base.pub %>/assets/js/*.js'
        },
        concat: {
            dist: {
                src: ['bower_components/jquery/dist/jquery.min.js',
                    'bower_components/bootstrap-sass/assets/javascripts/bootstrap.min.js',
                    'bower_components/moment/min/moment-with-locales.min.js',
                'bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
                    'bower_components/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'bower_components/slick-carousel/slick/slick.min.js'],
                dest: '<%= base.pub %>/assets/js/plugins.js',
            }
        },
        watch: {
            options: {
                spawn: false
            },
            sass: {
                files: '<%= base.src %>/assets/scss/**/*.scss',
                tasks: ['sass', 'postcss', 'search', 'replace:css']
            }
        },
        search: {
            imports: {
                files: {
                    src: '<%= base.src %>/assets/scss/*.scss'
                },
                options: {
                    searchString: /@import[ \("']*([^;]+)[;\)"']*/g,
                    logFormat: 'json',
                    logFile: 'csstoc.json'
                }
            }
        },
        replace: {
            css: {
                src: ['<%= base.pub %>/assets/css/main.css'],
                overwrite: true,
                replacements: [{
                    from: '@@toc',
                    to: function () {
                        if (!grunt.file.exists('csstoc.json')) {
                            return '';
                        }

                        var tocFile = grunt.file.readJSON('csstoc.json'), files = tocFile.results, toc = '', i = 1, match;

                        function capitalize(s) {
                            var s = s.toLowerCase().split(' ');
                            for (var i = 0; i < s.length; i++) {
                                s[i] = s[i].split('');
                                s[i][0] = s[i][0].toUpperCase();
                                s[i] = s[i].join('');
                            }
                            return s.join(' ');
                        }

                        for (var file in files) {
                            if (files.hasOwnProperty(file)) {
                                var results = files[file];
                                for (var res in results) {
                                    if (results.hasOwnProperty(res)) {
                                        match = results[res].match;
                                        match = match.replace(/"|'|@import|;|.scss/gi, '').trim();
                                        match = match.replace('-', ' ').trim();
                                        match = match.split('/').pop();
                                        match = capitalize(match);
                                        if (['Variables', 'Mixins'].indexOf(match) === -1) {
                                            if (i < 10) {
                                                toc += '\n  0' + i + '. ' + match;
                                            } else {
                                                toc += '\n  ' + i + '. ' + match;
                                            }
                                            i++;
                                        }
                                    }
                                }
                            }
                        }
                        return toc;
                    }
                },
                    {
                        from: '/*',
                        to: '\n/*'
                    },
                    {
                        from: '\n\n\n/*',
                        to: '\n\n/*'
                    }]
            }
        }
        // bowercopy: {
        //     options: {
        //         srcPrefix: 'bower_components'
        //     },
        //     scripts: {
        //         options: {
        //             destPrefix: 'public/vendors/bower_components'
        //         },
        //         files: {
        //             'jquery/dist/jquery.min.js': 'jquery/dist/jquery.min.js',
        //             'bootstrap/dist/js/bootstrap.min.js': 'bootstrap/dist/js/bootstrap.min.js',
        //             'bootstrap/dist/css/bootstrap.min.css': 'bootstrap/dist/css/bootstrap.min.css',
        //             'bootstrap/dist/fonts': 'bootstrap/dist/fonts',
        //             'switchery/dist/switchery.min.css': 'switchery/dist/switchery.min.css',
        //             'owl.carousel/dist/assets/owl.carousel.min.css': 'owl.carousel/dist/assets/owl.carousel.min.css',
        //             'owl.carousel/dist/assets/owl.theme.default.min.css': 'owl.carousel/dist/assets/owl.theme.default.min.css',
        //             'awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css': 'awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css',
        //             'datatables/media/js/jquery.dataTables.min.js': 'datatables/media/js/jquery.dataTables.min.js',
        //             'datatables/media/css/jquery.dataTables.min.css': 'datatables/media/css/jquery.dataTables.min.css',
        //             'moment/min/moment.min.js': 'moment/min/moment.min.js',
        //             'waypoints/lib/jquery.waypoints.min.js': 'waypoints/lib/jquery.waypoints.min.js',
        //             'jquery.counterup/jquery.counterup.min.js': 'jquery.counterup/jquery.counterup.min.js',
        //             'owl.carousel/dist/owl.carousel.min.js': 'owl.carousel/dist/owl.carousel.min.js',
        //             'switchery/dist/switchery.min.js': 'switchery/dist/switchery.min.js',
        //             'raphael/raphael.min.js': 'raphael/raphael.min.js',
        //             'morris.js/morris.min.js': 'morris.js/morris.min.js',
        //             'morris.js/morris.css': 'morris.js/morris.css',
        //             'jquery-toast-plugin/dist/jquery.toast.min.js': 'jquery-toast-plugin/dist/jquery.toast.min.js',
        //             'bootstrap-select/dist/css/bootstrap-select.min.css': 'bootstrap-select/dist/css/bootstrap-select.min.css',
        //             'bootstrap-select/dist/js/bootstrap-select.min.js': 'bootstrap-select/dist/js/bootstrap-select.min.js',
        //             'bootstrap-validator/dist/validator.min.js': 'bootstrap-validator/dist/validator.min.js',
        //             'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js': 'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
        //             'eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css': 'eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
        //             'datatables.net-buttons/js/buttons.html5.min.js': 'datatables.net-buttons/js/buttons.html5.min.js',
        //             'datatables.net-buttons/js/dataTables.buttons.min.js': 'datatables.net-buttons/js/dataTables.buttons.min.js',
        //             'jquery.steps/demo/css/jquery.steps.css': 'jquery.steps/demo/css/jquery.steps.css',
        //             'moment/min/moment-with-locales.min.js': 'moment/min/moment-with-locales.min.js',
        //         }
        //     },
        //     bourbon:{
        //         options: {
        //             destPrefix: 'source/assets/scss/bower_components'
        //         },
        //         files: {
        //             'bourbon': 'bourbon',
        //         }
        //     }
        // }
    });
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-concat');
    // grunt.loadNpmTasks('grunt-bowercopy');
    // Task
    grunt.registerTask('default', [
        'watch'
    ]);
    grunt.registerTask('build', [
        'sass', 'postcss', 'csscomb',
        'search', 'replace:css', 'cssmin',
        'clean:tmp'
    ]);
    grunt.registerTask('buildjs', ['concat'
    ]);
};