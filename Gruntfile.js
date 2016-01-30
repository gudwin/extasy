module.exports = function (grunt) {
    var testsDir = 'tests/';
    var replacementRegex = /\<\!\-\- PLACE JS HERE\, PLEASE\-\-\>[\s\S]+\<\!\-\- THANK YOU FOR JS \-\-\>/;
    var menuScripts = [
        'resources/DashboardMenu/audit.js',
        'resources/DashboardMenu/search.js'
    ];

    var kernelScripts = [
        'resources/js/vendors/jquery-1.10.2.min.js',
        'resources/js/vendors/jquery-migrate-1.2.1.js',
        'resources/js/vendors/bootstrap.js',
        'resources/js/vendors/bootstrap.submenu.js',
        'resources/js/vendors/sprintf.min.js',
        'resources/js/jquery-ui-1.10.3.custom.min.js',
        'resources/js/vendors/datepicker-ru.js',
        'resources/js/controller.js',
        'resources/js/net.js',
        'resources/js/contentloader.js',
        'resources/js/sysutils.js',
        'resources/js/dtree.js',
        'resources/js/vendors/tmpl.js',
        'resources/js/cms/main.js',
        'resources/js/cms/hints.js',
        'resources/js/cms/popup.js',
        'resources/js/cms/message.js',
        'resources/js/cms/editDocument.js',
        'resources/js/administrative/testSuite/index.js'
    ];
    grunt.initConfig({
        phpunit: {
            acl: {
                dir: testsDir + 'acl/'
            },
            api: {
                dir: testsDir + 'Api/'
            },
            audit: {
                dir: testsDir + 'Audit/'
            },

            baseclasses: {
                dir: testsDir + 'kernel/baseclasses/'
            },
            bootstrap: {
                dir: testsDir + 'Bootstrap/'
            },
            columns: {
                dir: testsDir + 'Columns/'
            },

            custom_config: {
                dir: testsDir + 'custom_config/'
            },
            dashboard: {
                dir: testsDir + 'Dashboard/'
            },
            menu: {
                dir: testsDir + 'Menu/'
            },
            models: {
                dir: testsDir + 'Models/'
            },

            schedule: {
                dir: testsDir + 'Schedule/'
            },
            sitemap: {
                dir: testsDir + 'sitemap/'
            },
            system_register: {
                dir: testsDir + 'system_register/'
            },
            staticApplication: {
                dir: testsDir + 'StaticApplication/'
            },


            validators: {
                dir: testsDir + 'Validators/'
            },

            users: {
                dir: testsDir + 'Users/'
            },
            options: {
                bootstrap: testsDir + 'alltests.php',
                colors: true
            }
        },
        e5template : {
            dashboardRelease : {
                templatePath : 'src/kernel/cms/design/plugins/layout.php',
                scriptsBasePath : '../',
                minifiedName : 'extcms.min.js',
                outputPath : 'resources/js/compiled/',
                scripts : kernelScripts,
                mode : 'release',
                wrapResultCode : function ( url ) {
                    var result = '<script type="text/javascript" src="/resources/extasy/js/compiled/' + url +'"></script>';
                    return result;
                }
            },
            dashboardDev : {
                templatePath : 'src/kernel/cms/design/plugins/layout.php',
                scriptsBasePath : '../',
                scripts : kernelScripts,
                mode : 'restore',
                wrapScript : function ( url ) {

                    matches = url.match(/public_html\/(.+)$/);
                    if (matches != null) {
                        src = '/' + matches[1];
                    } else {
                        src = '/resources/extasy/' + url.replace('resources/', '');
                    }
                    return '<script type="text/javascript" src="' + src + '"></script>';
                }
            },
            menuRelease : {
                templatePath : 'src/Dashboard/Views/menu.tpl',
                scriptsBasePath : '../',
                minifiedName : 'dashboard.min.js',
                outputPath : 'resources/DashboardMenu/compiled/',
                scripts : menuScripts,
                mode : 'release',
                wrapResultCode : function ( url ) {
                    var result = '<script type="text/javascript" src="/resources/extasy/DashboardMenu/compiled/' + url +'"></script>';
                    return result;
                }
            },
            menuDev : {
                templatePath : 'src/Dashboard/Views/menu.tpl',
                scriptsBasePath : '../',
                scripts : menuScripts,
                mode : 'restore',
                wrapScript : function ( url ) {
                    matches = url.match(/public_html\/(.+)$/);
                    if (matches != null) {
                        src = '/' + matches[1];
                    } else {
                        src = '/resources/extasy/' + url.replace('resources/', '');
                    }
                    return '<script type="text/javascript" src="' + src + '"></script>';
                }
            }


        },
        concat: {
            options: {
                banner: '// Extasy CMS Dashboard scripts v 4.3' + "\r\n",
                stripBanners: true
            },
            tinymce_ru : {
                banner : '// Compiled langs sources',
                src: 'resources/tiny_mce/plugins/*/langs/ru.js',
                dest : 'resources/tiny_mce/langs/plugins-ru.js'
            }
        },
        replace: {

        },
        uglify: {
            options: {
                mangle: {
                    except: ['jQuery', 'Ext','angular']
                }
            }
        },
        bgShell: {
            testJS: {
                cmd: 'node node_modules/karma/bin/karma start resources/test/karma.conf.js'
            }
        }

    });
    grunt.loadNpmTasks('grunt-phpunit');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-text-replace');
    grunt.loadNpmTasks('grunt-bg-shell');
    grunt.loadTasks('tasks')
    grunt.registerTask('default', ['phpunit']);
    grunt.registerTask('release', ['phpunit', 'e5template:menuRelease']);


};