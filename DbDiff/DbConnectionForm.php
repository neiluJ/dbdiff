<?php
namespace DbDiff;

use Fwk\Form\Form;
use Fwk\Form\Elements\Text;
use Fwk\Form\Elements\Password;
use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;
use Fwk\Form\Elements\Submit;
use Fwk\Form\Elements\Select;
use Fwk\Form\Validation\IsInArrayFilter;

class DbConnectionForm extends Form
{
    public function __construct($action = null, $method = self::METHOD_POST, 
        array $options = array()
    ) {
        parent::__construct($action, $method, $options);
        
        $driver = new Select('driver', null, 'pdo_mysql');
        $drivers = array(
            'pdo_mysql'     => 'MySQL',
            'pdo_sqlite'    => 'SQLite',
            'pdo_pgsql'     => 'PostgreSQL',
            'oci8'          => 'Oracle',
            'pdo_sqlsrv'    => 'SQLServer'
        );
        $driver->setOptions($drivers);
        $driver->label('Driver')
               ->sanitizer(new StringSanitizer())
                ->filter(new NotEmptyFilter(), "Vous devez spécifier un driver")
                ->filter(new IsInArrayFilter(array_keys($drivers)), "Driver invalide");
        
        $host = new Text('hostname');
        $host->label('Hostname')
             ->sanitizer(new StringSanitizer())
             ->filter(new NotEmptyFilter(), "Vous devez spécifier un hostname");
        
        $user = new Text('username');
        $user->label('User')
             ->sanitizer(new StringSanitizer())
             ->filter(new NotEmptyFilter(), "Vous devez spécifier un user");
        
        $pass = new Password('passwd');
        $pass->label('Password')
             ->sanitizer(new StringSanitizer());
        
        $dbname = new Text('database');
        $dbname->label('Database')
             ->sanitizer(new StringSanitizer())
             ->filter(new NotEmptyFilter(), "Vous devez spécifier une base");
        
        $this->addAll(array($driver, $host, $user, $pass, $dbname, new Submit()));
    }
}