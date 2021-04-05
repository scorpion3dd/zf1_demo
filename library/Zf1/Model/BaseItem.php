<?php

/**
 * Zf1_Model_BaseItem
 *
 * @property integer $RecordID
 * @property date $RecordDate
 * @property string $SellerName
 * @property string $SellerEmail
 * @property string $SellerTel
 * @property string $SellerAddress
 * @property string $Title
 * @property integer $Year
 * @property integer $CountryID
 * @property float $Denomination
 * @property integer $TypeID
 * @property integer $GradeID
 * @property integer $SalePriceMin
 * @property integer $SalePriceMax
 * @property string $Description
 * @property integer $DisplayStatus
 * @property date $DisplayUntil
 */
abstract class Zf1_Model_BaseItem extends Doctrine_Record
{
    /**
     * setTableDefinition
     */
    public function setTableDefinition()
    {
        $this->setTableName('item');
        $this->hasColumn('RecordID', 'integer', 4, array('type' => 'integer', 'length' => 4, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('RecordDate', 'date', null, array('type' => 'date', 'notnull' => true));
        $this->hasColumn('SellerName', 'string', 255, array('type' => 'string', 'length' => 255, 'notnull' => true));
        $this->hasColumn('SellerEmail', 'string', 255, array('type' => 'string', 'length' => 255, 'notnull' => true));
        $this->hasColumn('SellerTel', 'string', 50, array('type' => 'string', 'length' => 50));
        $this->hasColumn('SellerAddress', 'string', null, array('type' => 'string'));
        $this->hasColumn('Title', 'string', 255, array('type' => 'string', 'length' => 255, 'notnull' => true));
        $this->hasColumn('Year', 'integer', null, array('type' => 'integer', 'notnull' => true));
        $this->hasColumn('CountryID', 'integer', 4, array('type' => 'integer', 'length' => 4, 'notnull' => true));
        $this->hasColumn('Denomination', 'float', null, array('type' => 'float', 'notnull' => true));
        $this->hasColumn('TypeID', 'integer', 4, array('type' => 'integer', 'length' => 4, 'notnull' => true));
        $this->hasColumn('GradeID', 'integer', 4, array('type' => 'integer', 'length' => 4, 'notnull' => true));
        $this->hasColumn('SalePriceMin', 'integer', 4, array('type' => 'integer', 'length' => 4, 'notnull' => true));
        $this->hasColumn('SalePriceMax', 'integer', 4, array('type' => 'integer', 'length' => 4, 'notnull' => true));
        $this->hasColumn('Description', 'string', null, array('type' => 'string', 'notnull' => true));
        $this->hasColumn('DisplayStatus', 'integer', 1, array('type' => 'integer', 'length' => 1, 'notnull' => true));
        $this->hasColumn('DisplayUntil', 'date', null, array('type' => 'date'));
    }
}