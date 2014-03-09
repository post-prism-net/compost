module.exports = function(grunt){

	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

		less: {
		  development: {
		    files: {
		      'css/style.css': 'css/style.less',
		    }
		  }
		},

		autoprefixer: {
		    style: {
		      src: 'css/style.css',
		      dest: 'css/style.css'
		    }
		},

		modernizr: {
		    dist: {
		        'devFile' : 'remote',
		        'outputFile' : 'js/src/modernizr.js',
		        'extra' : {
		            'shiv' : true,
		            'printshiv' : false,
		            'load' : true,
		            'mq' : true,
		            'cssclasses' : true
		        },
		        'uglify' : false,
		        'parseFiles' : true,
		        'files' : {
            		'src': ['js/src/*.js', 'css/**/*.css']
        		},
		        'matchCommunityTests' : false
		    }
		},

		uglify: {
			all: {
				files: {
					'js/all.js': ['js/src/**/*.js']
				}
			}
		},

		imagemin: {
			all: {                        
				files: [{
					expand: true,  
					cwd: 'img/src',
					src: ['**/*.{png,jpeg,jpg,gif}'],
					dest: 'img/'
		     	}]
		    }
	    },

	    svgmin: {                      
	        options: {                 
	            plugins: [
	              { removeViewBox: false },
	              { removeUselessStrokeAndFill: false }
	            ]
	        },
	        dist: {                    
	            files: [{              
	                expand: true,       
	                cwd: 'img/src',     
	                src: ['**/*.svg'],  
	                dest: 'img/'       
	            }]
	        }
	    },

	    svg2png: {
	        all: {
	            files: [{ 
	            	src: ['img/*.svg'], 
	            	dest: 'img/' 
	            }]
	        }
	    },

		watch: {
		    css: {
		        files: ['css/**/*.less'],
		        tasks: ['buildcss']
		    },
		    js: {
		    	files: ['src/js/**/*.js'],
		    	tasks: ['buildjs']
		    }
		}


    });

    grunt.registerTask( 'default', ['build'] );

	grunt.registerTask( 'buildcss',  ['less', 'autoprefixer'] );
	grunt.registerTask( 'buildmodernizr', ['modernizr'] );
	grunt.registerTask( 'buildjs',  ['uglify'] );
	grunt.registerTask( 'buildimages',  ['imagemin', 'svgmin', 'svg2png'] );

	grunt.registerTask( 'build',  ['buildcss', 'buildmodernizr', 'buildjs', 'buildimages'] );
};