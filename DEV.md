# How to build the CSS and Javascript assets

This project uses Grunt and SASS to bundle the CSS and Javascript files.

## Requirements

* grunt
* grunt-sass
* grunt-contrib-watch
* grunt-contrib-cssmin
* grunt-contrib-uglify

Here is an example of how to install on Linux :

```
aptitude install npm
npm install -g grunt-cli
npm install
```

## Usage

Build the assets :
```
grunt build
```

Watch the assets for auto-build :
```
grunt watch
```
