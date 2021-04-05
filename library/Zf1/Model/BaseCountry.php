<?php

/**
 * Zf1_Model_BaseCountry
 *
 * @property integer $CountryID
 * @property string $CountryName
 */
abstract class Zf1_Model_BaseCountry extends Doctrine_Record
{
    /**
     * setTableDefinition
     */
    public function setTableDefinition()
    {
        $this->setTableName('country');
        $this->hasColumn('CountryID', 'integer', 4, array('type' => 'integer', 'length' => 4, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('CountryName', 'string', 255, array('type' => 'string', 'length' => 255, 'notnull' => true));
    }
}