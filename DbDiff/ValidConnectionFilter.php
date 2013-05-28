<?php
namespace DbDiff;

use Fwk\Form\Filter;
use Fwk\Db\Connection;

class ValidConnectionFilter implements Filter
{
    protected $connection;
    
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    public function validate($value = null)
    {
        if (!$value instanceof \Fwk\Form\Form) {
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
