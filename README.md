# DbDiff

The simplest databases-diff tool ever: enter two (or more) database credentials and spot differences in one click.
Based on the excellent [Doctrine\DBAL project](http://www.doctrine-project.org/projects/dbal.html).

Requires: 
- PHP 5.3+
- A running xAMP stack (not tested on windows but should work fine)
- (PHP 5.4+ to use the built-in web server)

## Installation 

1. Clone this repository (or download zip) inside a folder www-ready: ```git clone git@github.com:neiluJ/dbdiff``` 
2. Run [Composer](https://getcomposer.org/): ```cd dbdiff && php composer.phar install```
3. Browse http://localhost/~you/dbdiff 

or use the built-in web server:

```
php -S localhost:8888 -t .
``` 
## License

MIT