module.exports = function(config){
    config.set({

        basePath : '../',

        files : [
            'js/vendors/jquery-1.10.2.min.js',
            'vendors/angular/angular.js',
            'vendors/angular/angular-route.js',
            'vendors/angular/angular-resource.js',
            'vendors/angular/angular-animate.js',
            'vendors/angular/angular-mocks.js',
            '../node_modules/jasmine-jquery/lib/jasmine-jquery.js',
            'js/Password.js',
            'test/unit/**/*.js'
        ],

        autoWatch : true,

        frameworks: ['jasmine'],

        browsers : ['Chrome'],

        plugins : [
            'karma-chrome-launcher',
            'karma-jasmine'
        ],

        junitReporter : {
            outputFile: 'unit.xml',
            suite: 'unit'
        }

    });
};