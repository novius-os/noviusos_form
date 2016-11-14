# How to build the CSS and Javascript assets

This project uses Grunt and SASS to bundle the CSS and Javascript files.

## Requirements

* grunt
* grunt-contrib-sass
* grunt-contrib-watch

Here is an example of how to install on Linux :

```
apt-get install npm
apt-get install ruby
gem install ruby
npm install -g grunt-cli
npm init
npm install grunt --save-dev
npm install grunt-contrib-sass --save-dev
npm install grunt-contrib-watch --save-dev
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


