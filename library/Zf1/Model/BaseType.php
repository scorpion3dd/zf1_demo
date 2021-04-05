<?php

/**
 * Zf1_Model_BaseType
 *
 * @property integer $TypeID
 * @property string $TypeName
 */
abstract class Zf1_Model_BaseType extends Doctrine_Record
{
    /**
     * setTableDefinition
     */
    public function setTableDefinition()
    {
        $this->setTableName('type');
        $this->hasColumn('TypeID', 'integer', 4, array('type' => 'integer', 'length' => 4, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('TypeName', 'string', 255, array('type' => 'string', 'length' => 255, 'notnull' => true));
    }
}