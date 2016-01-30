module.exports = function(grunt) {
    var MODULE_PREFIX = 'e5t';
    var target = null;
    var data = null;

    var release = function ( ) {
        var buildPath = data.outputPath + data.minifiedName + '.concat.js';
        var tasks = [];
        tasks.push( concatFiles( buildPath ) );
        tasks.push( uglifyFiles( buildPath ) );
        var taskName =  releaseReplaceFiles(  );
        if ( taskName ) {
            tasks.push( taskName );
        }
        console.log( MODULE_PREFIX +  target );
        console.log( tasks);
        grunt.task.registerTask(MODULE_PREFIX +  target, tasks );
        grunt.task.run( MODULE_PREFIX + target );
    };
    var restore = function ( ) {
        var tasks = [];
        var taskName =  devReplaceFiles(  );
        if ( taskName ) {
            tasks.push( taskName );
        }
        grunt.task.registerTask(MODULE_PREFIX +  target, tasks );
        grunt.task.run( MODULE_PREFIX + target );
    };
    var concatFiles = function (buildPath) {
        var config = {
            src: data.scripts,
            dest: buildPath
        };
        grunt.config.set( 'concat.' + MODULE_PREFIX + target , config);
        return 'concat:' + MODULE_PREFIX + target;
    }
    var uglifyFiles = function (buildPath) {
        var config = {
            files: {}
        };
        var resultPath = data.outputPath + data.minifiedName;
        config.files[ resultPath ] = buildPath;

        grunt.config.set('uglify.' + MODULE_PREFIX + target , config);

        return 'uglify:' + MODULE_PREFIX + target;
    };
    var getBlockPrefix = function () {
        if ( data['blockTitle'].length > 0) {
            return data['blockTitle'] + ':';
        }
        return '';
    };
    var getReplacementRegex = function () {

        var prefix = data['blockTitle'].length > 0 ? data['blockTitle'] + '\\:' : '';
        var replacementRegex = '\\<\\!\\-\\- ' + prefix + 'PLACE JS HERE\\, PLEASE\\-\\-\\>[\\s\\S0-9]*?\\<\\!\\-\\- THANK YOU FOR JS \\-\\-\\>';
        replacementRegex = new RegExp( replacementRegex );
        console.log( replacementRegex );
        return replacementRegex;
    }
    var releaseReplaceFiles = function () {
        if ( data['wrapResultCode']) {
            var result = data.wrapResultCode.call( null, data.minifiedName );
            var config = {
                src: data.templatePath,
                overwrite: true,
                replacements: [
                    {
                        from: getReplacementRegex(),
                        to: '<!-- '+ getBlockPrefix() +'PLACE JS HERE, PLEASE-->' + result +'<!-- THANK YOU FOR JS -->'
                    }
                ]
            }
            grunt.config.set('replace.' + MODULE_PREFIX + target , config);
            return 'replace:' + MODULE_PREFIX + target;
        }
    };

    var devReplaceFiles = function () {

        if ( data['wrapScript']) {

            var config = {
                src: data.templatePath,
                overwrite: true,
                replacements: [
                    {
                        from: getReplacementRegex(),
                        to: function () {
                            var result = '<!-- ' + getBlockPrefix() + 'PLACE JS HERE, PLEASE-->';
                            var src = '';
                            var matches = [];
                            for (var i = 0; i < data.scripts.length; i++) {
                                result += data.wrapScript.call( null, data.scripts[ i ]);
                            }
                            result += '<!-- THANK YOU FOR JS -->';
                            console.log( result );
                            return result;
                        }
                    }
                ]
            };
            grunt.config.set('replace.' + MODULE_PREFIX + target , config);
            return 'replace:' + MODULE_PREFIX + target;
        }

    }
    grunt.registerMultiTask('e5template', function() {
        var done = this.async();
        target = this.target;
        data = this.data;
        data.blockTitle = data.blockTitle ? data.blockTitle : '';
        if ( data.mode == 'release') {
            release( );
        } else {
            restore( );
        }
        done();
    });
};