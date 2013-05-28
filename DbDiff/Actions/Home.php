<?php
namespace DbDiff\Actions;

use Fwk\Core\Preparable;
use Fwk\Core\Action\Result;
use Fwk\Db\Connection;
use DbDiff\DbConnectionForm;
use DbDiff\ValidConnectionFilter;
use Doctrine\DBAL\Schema\Comparator;

class Home implements Preparable
{
    protected $renderer;
    protected $errors;
    protected $databases;
    protected $diff;
    protected $submitted = false;
    
    public function prepare()
    {
        $this->renderer = new \Fwk\Form\Renderer();
        $this->databases = array(
            0 => array(
                'database' => null,
                'hostname' => null,
                'username' => null,
                'passwd'   => null
            ),
            1 => array(
                'database' => null,
                'hostname' => null,
                'username' => null,
                'passwd'   => null
            )
        );
    }
    
    public function show()
    {
        if ($_SERVER['REQUEST_METHOD'] !== "POST") {
           return Result::SUCCESS;
        }
        
        $this->submitted = true;
        
        if (!isset($_POST['db']) || !is_array($_POST['db'])) {
            return Result::SUCCESS;
        }
        
        $databases = array();
        $errors = array();
        foreach ($_POST['db'] as $idx => $data) {
            $form = new DbConnectionForm();
            $form->submit($data);
            $connection = new Connection(array(
                'dbname'    => $form->database,
                'user'      => $form->username,
                'password'  => $form->passwd,
                'driver'    => 'pdo_mysql',
                'host'      => $form->hostname
            ));
            // support enums
            $connection->getDriver()->getDatabasePlatform()
                    ->registerDoctrineTypeMapping('enum', 'string');
            
            $databases[$idx]['form']        = $form;
            $databases[$idx]['connection']  = $connection;
            
            $form->filter(
                new ValidConnectionFilter($connection), 
                "Impossible de se connecter à la base"
            );
            
            if (!$form->validate()) {
                $errors[$idx] = $form->getErrors();
                continue;
            }
        }
        
        $this->errors       = $errors;
        $this->databases    = $databases;
        
        if (count($errors)) {
            return Result::SUCCESS;
        }
        
        
        $this->computeDiff();
        
        return Result::SUCCESS;
    }
    
    
    protected function computeDiff()
    {
       $base    = array();
       $others  = array();
       foreach ($this->databases as $idx => $db) {
           $connection = $db['connection'];
           if (!$connection instanceof Connection) {
               continue;
           }
           
           $sch = $connection->getDriver()->getSchemaManager();
           $tablesNames = $sch->listTableNames();
           
           $tables = array();
           foreach ($tablesNames as $tableName) {
               $tables[$tableName] = $sch->listTableDetails($tableName);
           }
           
           $this->databases[$idx]['tables'] = $tables;
           $this->databases[$idx]['schema'] = $sch->createSchema();
       }
       
       $dbs = $this->databases;
       $base = array_shift($dbs);
       
       foreach ($dbs as $idx => $db) {
           $comp = Comparator::compareSchemas($base['schema'], $db['schema']);
           $this->databases[$idx]['schema_diff'] = $comp;
           $this->databases[$idx]['sql_diff'] = $comp->toSaveSql($db['connection']->getDriver()->getDatabasePlatform());
           
       }
    }
    
    public function getRenderer()
    {
        return $this->renderer;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function getDatabases()
    {
        return $this->databases;
    }
    
    public function getDiff()
    {
        return $this->diff;
    }
    
    public function getSubmitted()
    {
        return $this->submitted;
    }
}