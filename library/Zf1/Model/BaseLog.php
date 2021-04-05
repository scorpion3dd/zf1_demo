<?php

/**
 * Zf1_Model_BaseLog
 *
 * @property integer $RecordID
 * @property string $LogMessage
 * @property string $LogLevel
 * @property string $LogTime
 * @property string $Stack
 * @property string $Request
 */
abstract class Zf1_Model_BaseLog extends Doctrine_Record
{
    /**
     * setTableDefinition
     */
    public function setTableDefinition()
    {
        $this->setTableName('log');
        $this->hasColumn('RecordID', 'integer', 4, array('type' => 'integer', 'length' => 4, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('LogMessage', 'string', null, array('type' => 'string', 'notnull' => true));
        $this->hasColumn('LogLevel', 'string', 30, array('type' => 'string', 'length' => 30, 'notnull' => true));
        $this->hasColumn('LogTime', 'string', 30, array('type' => 'string', 'length' => 30, 'notnull' => true));
        $this->hasColumn('Stack', 'string', null, array('type' => 'string'));
        $this->hasColumn('Request', 'string', null, array('type' => 'string'));
    }
}