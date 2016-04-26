<?php
/**
 * This file is part of DbDiff, a simple database-diff tool.
 *
 * MIT License
 * Copyright (c) 2012-2016 Julien Ballestracci
 */
namespace DbDiff;

use Doctrine\DBAL\Schema\Comparator;
use Fwk\Core\Action\Controller as BaseController;
use Fwk\Core\Preparable;
use Fwk\Db\Connection;

class Controller extends BaseController implements Preparable
{
    protected $errors;
    protected $databases;
    protected $diff;
    protected $submitted = false;
    
    public function prepare()
    {
        $this->databases = array(
            0 => array(
                'form' => array(
                    'database' => null,
                    'hostname' => null,
                    'username' => null,
                    'passwd'   => null,
                    'driver'   => 'pdo_mysql'
                )
            )
        );
    }
    
    public function show()
    {
        if ($_SERVER['REQUEST_METHOD'] !== "POST") {
            return $this->render();
        }
        
        $this->submitted = true;
        
        if (!isset($_POST['db']) || !is_array($_POST['db'])) {
            return $this->render();
        }
        
        if (count($_POST['db']) < 2) {
            $this->errors = array('Vous devez renseigner au moins deux bases pour comparer (gnii)');
            return $this->render();
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
                'driver'    => $form->driver,
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
            return $this->render();
        }
        
        
        $this->computeDiff();
        
        return $this->render();
    }
    
    
    protected function computeDiff()
    {
       $base = null;
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

            if ($base === null) {
                $base = $this->databases[$idx]['schema'];
                continue;
            }

           $comp = Comparator::compareSchemas($base, $this->databases[$idx]['schema']);
           $this->databases[$idx]['schema_diff'] = $comp;
           $this->databases[$idx]['sql_diff'] = $comp->toSaveSql($connection->getDriver()->getDatabasePlatform());
       }
    }

    protected function render()
    {
        return $this->getServices()->get('twig')->render('home.twig', array(
            'errors'     => $this->errors,
            'databases'  => $this->databases,
            'diff'       => $this->diff,
            'submitted'  => $this->submitted
        ));
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
