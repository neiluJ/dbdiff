<?php
/**
 * This file is part of DbDiff, a simple database-diff tool.
 *
 * MIT License
 * Copyright (c) 2012-2016 Julien Ballestracci
 */
namespace DbDiff;

use Fwk\Form\Filter;
use Fwk\Db\Connection;
use Fwk\Form\Form;

class ValidConnectionFilter implements Filter
{
    protected $connection;
    
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    public function validate($value = null)
    {
        if (!$value instanceof Form) {
            return false;
        }
        
        try {
            $this->connection->connect();
        } catch(\Exception $e) {
            return false;
        }
        
        return true;
    }
}
