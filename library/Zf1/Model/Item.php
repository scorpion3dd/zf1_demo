<?php

/**
 * Zf1_Model_Item
 */
class Zf1_Model_Item extends Zf1_Model_BaseItem
{
    /**
     * setUp
     */
    public function setUp()
    {
        $this->hasOne('Zf1_Model_Grade', array(
                'local' => 'GradeID',
                'foreign' => 'GradeID'
            )
        );
        $this->hasOne('Zf1_Model_Country', array(
                'local' => 'CountryID',
                'foreign' => 'CountryID'
            )
        );
        $this->hasOne('Zf1_Model_Type', array(
                'local' => 'TypeID',
                'foreign' => 'TypeID'
            )
        );
    }

    /**
     * @param $id
     * @param bool $active
     * @return array
     */
    public function getItem($id, $active = true)
    {
        $q = Doctrine_Query::create()
            ->from('Zf1_Model_Item i')
            ->leftJoin('i.Zf1_Model_Country c')
            ->leftJoin('i.Zf1_Model_Grade g')
            ->leftJoin('i.Zf1_Model_Type t')
            ->where('i.RecordID = ?', $id);
        if ($active) {
            $q->addWhere('i.DisplayStatus = 1')
                ->addWhere('i.DisplayUntil >= CURDATE()');
        }
        return $q->fetchArray();
    }
}