<?php

/**
 * Class DoctrineController
 */
class DoctrineController extends Zend_Controller_Action
{
    private $manager;
    private $conn;
    private $request;
    private $params;

    /**
     * @throws Doctrine_Manager_Exception
     */
    public function init()
    {
        $this->manager = Doctrine_Manager::getInstance();
        $this->conn = $this->manager->getConnection('doctrine');

        $this->request = $this->getRequest();
        $this->params = $this->request->getParams();
    }

    /**
     * index action
     */
    public function indexAction()
    {
        // action body
    }

    /**
     * list Databases action
     */
    public function listDatabasesAction()
    {
        $databases = $this->conn->import->listDatabases();

        print_r($databases);
        exit;
    }

    /**
     * @throws Zend_Exception
     */
    public function generateModelsAction()
    {
        $dir = SITE_ROOT_DIR . '/tmp/models';
        if(Doctrine::generateModelsFromDb($dir,
            array('doctrine'),
            array('classPrefix' => Zend_Registry::get('config')->resources->db->square->classPrefix)))
        {
            echo 'generate models OK';
        }
        else
        {
            echo 'NO generate models';
        }
        exit;
    }

    /**
     * all Items action
     */
    public function allItemsAction()
    {
        if($items = Doctrine::getTable('Zf1_Model_Item')->findAll())
        {
            var_dump($items);
        }
        exit;
    }

    /**
     * all Items array action
     */
    public function allItemsArrayAction()
    {
        $Query = Doctrine_Query::create()->from('Zf1_Model_Item');
        if($items = $Query->fetchArray())
        {
            echo $Query->getSqlQuery();
            var_dump($items);
        }
        exit;
    }

    /**
     * item action
     */
    public function itemAction()
    {
        if(isset($this->params['id']) && $this->params['id'] != '')
        {
            $id = (int)trim($this->params['id']);
            if($item = Doctrine::getTable('Zf1_Model_Item')->find($id))
            {
                var_dump($item->getData());
            }
            else
            {
                echo 'NO selected datas';
            }
        }
        exit;
    }

    /**
     * item By Year action
     */
    public function itemByYearAction()
    {
        if(isset($this->params['year']) && $this->params['year'] != '')
        {
            $year = (int)trim($this->params['year']);
            $item = Doctrine_Query::create()
                ->from('Zf1_Model_Item it')
                ->leftJoin('it.Zf1_Model_Country c')
                ->where('it.Year = ?', $year)
                ->orderBy('it.CountryID DESC');
            if($item && count($item) > 0)
            {
                echo $item->getSqlQuery();
                var_dump($item->fetchArray());
            }
            else
            {
                echo 'NO selected datas';
            }
        }
        exit;
    }

    /**
     * add Country action
     */
    public function addCountryAction()
    {
        if(isset($this->params['country_name']) && $this->params['country_name'] != '')
        {
            $country_name = (string)trim($this->params['country_name']);

            $country = new Zf1_Model_Country;
            $country->CountryName = $country_name;
            $country->save();
            $id = $country->getIncremented();
            if(isset($id) && $id > 0)
            {
                var_dump('id = ' . $id);
            }
            else
            {
                echo 'NO added data';
            }
        }
        exit;
    }

    /**
     * update Country action
     */
    public function updateCountryAction()
    {
        if(isset($this->params['country_name']) && $this->params['country_name'] != '' &&
            isset($this->params['id']) && $this->params['id'] != '')
        {
            $country_name = (string)trim($this->params['country_name']);
            $id = (int)trim($this->params['id']);
            if($country = Doctrine::getTable('Zf1_Model_Country')->find($id))
            {
                $country->CountryName = $country_name;
                $country->save();
                echo 'update data OK';
            }
            else
            {
                echo 'NO added data';
            }
        }
        exit;
    }
}

