<?php

/**
 * Class Zf1_Log_Writer_Doctrine
 */
class Zf1_Log_Writer_Doctrine extends Zend_Log_Writer_Abstract
{
    /**
     * Zf1_Log_Writer_Doctrine constructor. Accepts model name and column map
     * @param $modelName
     * @param $columnMap
     */
    public function __construct($modelName, $columnMap)
    {
        $this->_modelName = $modelName;
        $this->_columnMap = $columnMap;
    }

    /**
     * Stub function to deny formatter coupling
     * @param Zend_Log_Formatter_Interface $formatter
     * @return void|Zf1_Log_Writer_Doctrine
     * @throws Zend_Log_Exception
     */
    public function setFormatter($formatter)
    {
        require_once 'Zend/Log/Exception.php';
        throw new Zend_Log_Exception(get_class() . ' does not support formatting');
    }

    /**
     * Main log write method
     * maps database fields to log message fields
     * saves log messages as database records using model methods
     * @param array $message
     */
    protected function _write($message)
    {
        $data = array();
        foreach ($this->_columnMap as $messageField => $modelField) {
            $data[$modelField] = $message[$messageField];
        }
        $model = new $this->_modelName();
        $model->fromArray($data);
        $model->save();
    }
    /**
     * @param array|Zend_Config $config
     * @return Zf1_Log_Writer_Doctrine
     * @throws Zend_Log_Exception
     */
    static public function factory($config)
    {
        return new self(self::_parseConfig($config));
    }
}
