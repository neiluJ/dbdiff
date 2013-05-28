<?php
namespace DbDiff;

use Fwk\Form\Form;
use Fwk\Form\Elements\Text;
use Fwk\Form\Elements\Password;
use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;
use Fwk\Form\Elements\Submit;

class DbConnectionForm extends Form
{
    public function __construct($prefix = 'base', array $options = array()) 
    {
        parent::__construct(
            "", 
            Form::METHOD_POST, 
            array_merge(array('prefix' => $prefix), $options)
        );
        
        $host = new Text($prefix .'.hostname');
        $host->label('Hostname')
             ->sanitizer(new StringSanitizer())
             ->filter(new NotEmptyFilter(), "Vous devez spécifier un hostname");
        
        $user = new Text($prefix .'.username');
        $user->label('User')
             ->sanitizer(new StringSanitizer())
             ->filter(new NotEmptyFilter(), "Vous devez spécifier un user");
        
        $pass = new Password($prefix .'.passwd');
        $pass->label('Password')
             ->sanitizer(new StringSanitizer());
        
        $dbname = new Text($prefix .'.database');
        $dbname->label('Database')
             ->sanitizer(new StringSanitizer())
             ->filter(new NotEmptyFilter(), "Vous devez spécifier une base");
        
        $this->addAll(array($host, $user, $pass, $dbname, new Submit()));
    }
}