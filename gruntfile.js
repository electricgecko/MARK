module.exports = function (grunt) {

  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    concat: {
    	options: {
    	  separator: '\n',
    	},
    	dist: {
    	  src: 'src/mark.js',
    	  dest: 'dist/j/mark.min.js'
    	}
		},

    uglify: {
      build: {
        src: 'dist/j/mark.min.js',
        dest: 'dist/j/mark.min.js'
      }
    },

    compass: {
		  dist: {
		    options: {
        	banner: '/*\nM A R K\n*/\n',
		      sassDir: 'src/',
		      cssDir: 'dist/c/',
		      specify: 'src/*.scss',
		      outputStyle: 'compressed'
		    }
		  }
		},

		watch: {
		  css: {
		    files: 'src/*.scss',
		    tasks: ['compass'],
		    options: {
		      livereload: true,
		    },
		  },
		  js: {
		    files: 'src/fx.js',
		    tasks: ['concat', 'uglify'],
		    options: {
		      livereload: true,
		    },
		  },
		}
  });

	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-concat');
 	grunt.loadNpmTasks('grunt-contrib-uglify');
 	grunt.loadNpmTasks('grunt-contrib-compass');

	grunt.registerTask('default', ['concat','uglify','compass']);

};
