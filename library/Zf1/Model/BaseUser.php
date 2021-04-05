<?php

/**
 * Zf1_Model_BaseUser
 *
 * @property integer $RecordID
 * @property string $Username
 * @property string $Password
 */
abstract class Zf1_Model_BaseUser extends Doctrine_Record
{
    /**
     * setTableDefinition
     */
    public function setTableDefinition()
    {
        $this->setTableName('user');
        $this->hasColumn('RecordID', 'integer', 4, array('type' => 'integer', 'length' => 4, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('Username', 'string', 10, array('type' => 'string', 'length' => 10, 'notnull' => true));
        $this->hasColumn('Password', 'string', null, array('type' => 'string', 'notnull' => true));
    }
}