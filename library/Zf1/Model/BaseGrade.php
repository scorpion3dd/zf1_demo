<?php

/**
 * Zf1_Model_BaseGrade
 *
 * @property integer $GradeID
 * @property string $GradeName
 */
abstract class Zf1_Model_BaseGrade extends Doctrine_Record
{
    /**
     * setTableDefinition
     */
    public function setTableDefinition()
    {
        $this->setTableName('grade');
        $this->hasColumn('GradeID', 'integer', 4, array('type' => 'integer', 'length' => 4, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('GradeName', 'string', 255, array('type' => 'string', 'length' => 255, 'notnull' => true));
    }
}