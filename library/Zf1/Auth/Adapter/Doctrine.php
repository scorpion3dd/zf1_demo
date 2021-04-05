<?php
/**
 * Class Zf1_Auth_Adapter_Doctrine
 */
class Zf1_Auth_Adapter_Doctrine implements Zend_Auth_Adapter_Interface
{
    /**
     * array containing authenticated user record
     */
    protected $_resultArray;

    /**
     * Accepts username and password
     * Zf1_Auth_Adapter_Doctrine constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Main authentication method
     * queries database for match to authentication credentials
     * returns Zend_Auth_Result with success/failure code
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $q = Doctrine_Query::create()
            ->from('Zf1_Model_User u')
            ->where('u.Username = ? AND u.Password = CONCAT(\'*\', UPPER(SHA1(UNHEX(SHA1(?)))))',
                array($this->username, $this->password)
            );
        $sql = $q->getSqlQuery();
        $result = $q->fetchArray();
        if (count($result) == 1) {
            $this->_resultArray = $result[0];
            return new Zend_Auth_Result(
                Zend_Auth_Result::SUCCESS, $this->username, array());
        } else {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE, null,
                array('Authentication unsuccessful')
            );
        }
    }

    /**
     * Returns result array representing authenticated user record
     * excludes specified user record fields as needed
     * @param null $excludeFields
     * @return array|false
     */
    public function getResultArray($excludeFields = null)
    {
        if (!$this->_resultArray) {
            return false;
        }

        if ($excludeFields != null) {
            $excludeFields = (array)$excludeFields;
            foreach ($this->_resultArray as $key => $value) {
                if (!in_array($key, $excludeFields)) {
                    $returnArray[$key] = $value;
                }
            }
            return $returnArray;
        } else {
            return $this->_resultArray;
        }
    }
}
