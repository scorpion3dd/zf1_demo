<?php

/**
 * Class Catalog_ItemController
 */
class Catalog_ItemController extends Zend_Controller_Action
{
    /**
     * Init
     */
    public function init()
    {
        $this->view->doctype('XHTML1_STRICT');
        // initialize context switch helper
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('searchFull', 'xml')
            ->initContext();
    }

    /**
     * Action to display a catalog item
     * @throws Zend_Config_Exception
     * @throws Zend_Controller_Action_Exception
     * @throws Zend_Date_Exception
     * @throws Zend_Exception
     */
    public function displayAction()
    {
        // set filters and validators for GET input
        $filters = array(
            'id' => array('HtmlEntities', 'StripTags', 'StringTrim')
        );
        $validators = array(
            'id' => array('NotEmpty', 'Int')
        );
        $input = new Zend_Filter_Input($filters, $validators);
        $input->setData($this->getRequest()->getParams());

        // test if input is valid
        // retrieve requested record
        // attach to view
        if ($input->isValid()) {
            $q = Doctrine_Query::create()
                ->from('Zf1_Model_Item i')
                ->leftJoin('i.Zf1_Model_Country c')
                ->leftJoin('i.Zf1_Model_Grade g')
                ->leftJoin('i.Zf1_Model_Type t')
                ->where('i.RecordID = ?', $input->id)
                ->addWhere('i.DisplayStatus = 1')
                ->addWhere('i.DisplayUntil >= CURDATE()');
            $sql = $q->getSqlQuery();
            $result = $q->fetchArray();
            if (count($result) == 1) {
                $this->view->item = $result[0];
                $this->view->images = array();
                $config = $this->getInvokeArg('bootstrap')->getOption('uploads');
                foreach (glob("{$config['uploadPath']}/{$this->view->item['RecordID']}_*") as $file) {
                    $this->view->images[] = basename($file);
                }
                $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
                $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
                $this->view->seller = $localConfig->user->displaySellerInfo;
                $registry = Zend_Registry::getInstance();
                $this->view->locale = $registry->get('Zend_Locale');
                $this->view->recordDate = new Zend_Date($result[0]['RecordDate']);
            } else {
                throw new Zend_Controller_Action_Exception('Page not found', 404);
            }
        } else {
            throw new Zend_Controller_Action_Exception('Invalid input');
        }
    }

    /**
     * Action to display a catalog item
     * @throws Zend_Config_Exception
     * @throws Zend_Controller_Action_Exception
     * @throws Zend_Date_Exception
     * @throws Zend_Exception
     */
    public function display2Action()
    {
        // set filters and validators for GET input
        $filters = array(
            'id' => array('HtmlEntities', 'StripTags', 'StringTrim')
        );
        $validators = array(
            'id' => array('NotEmpty', 'Int')
        );
        $input = new Zend_Filter_Input($filters, $validators);
        $input->setData($this->getRequest()->getParams());

        // test if input is valid
        // retrieve requested record from cache or database
        // attach to view
        if ($input->isValid()) {
            $memoryCache = $this->getInvokeArg('bootstrap')
                ->getResource('cachemanager')
                ->getCache('memory');
            if (!($result = $memoryCache->load('public_item_'.$input->id))) {
                $item = new Zf1_Model_Item;
                $result = $item->getItem($input->id, true);
                $memoryCache->save($result, 'public_item_'.$input->id);
            }
            if (count($result) == 1) {
                $this->view->item = $result[0];
                $this->view->images = array();
                $config = $this->getInvokeArg('bootstrap')->getOption('uploads');
                foreach (glob("{$config['uploadPath']}/{$this->view->item['RecordID']}_*") as $file) {
                    $this->view->images[] = basename($file);
                }
                $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
                $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
                $this->view->seller = $localConfig->user->displaySellerInfo;
                $registry = Zend_Registry::getInstance();
                $this->view->locale = $registry->get('Zend_Locale');
                $this->view->recordDate = new Zend_Date($result[0]['RecordDate']);
            } else {
                throw new Zend_Controller_Action_Exception('Page not found', 404);
            }
        } else {
            throw new Zend_Controller_Action_Exception('Invalid input');
        }
    }

    /**
     * @throws Zend_Form_Exception
     */
    public function createAction()
    {
        // generate input form
        $form = new Zf1_Form_ItemCreate;
        $this->view->form = $form;

        // test for valid input
        // if valid, populate model
        // assign default values for some fields
        // save to database
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $item = new Zf1_Model_Item;
                $item->fromArray($form->getValues());
                $item->RecordDate = date('Y-m-d', mktime());
                $item->DisplayStatus = 0;
                $item->DisplayUntil = null;
                $item->save();
                $id = $item->RecordID;

                $config = $this->getInvokeArg('bootstrap')->getOption('uploads');
                $form->images->setDestination($config['uploadPath']);
                $adapter = $form->images->getTransferAdapter();
                for($x=0; $x<$form->images->getMultiFile(); $x++) {
                    $xt = @pathinfo($adapter->getFileName('images_'.$x.'_'), PATHINFO_EXTENSION);
                    $adapter->clearFilters();
                    $adapter->addFilter('Rename', array(
                        'target' => sprintf('%d_%d.%s', $id, ($x+1), $xt),
                        'overwrite' => true
                    ));
                    $adapter->receive('images_'.$x.'_');
                }

                $this->_helper->getHelper('FlashMessenger')->addMessage('Your submission has been accepted as item #' . $id . '. A moderator will review it and, if approved, it will appear on the site within 48 hours.');
                $this->_redirect('/catalog/item/success');
            }
        }
    }

    /**
     * successAction
     */
    public function successAction()
    {
        if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {
            $this->view->messages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        } else {
            $this->_redirect('/');
        }
    }

    /**
     * Action to perform simple search from DB
     * @throws Zend_Form_Exception
     */
    public function searchSimpleAction()
    {
        $start = microtime(true);

        // generate input form
        $form = new Zf1_Form_SearchSimple;
        $this->view->form = $form;

        // get items matching search criteria
        if ($this->_request->isPost() &&
            $form->isValid($this->getRequest()->getParams())
        ) {
            $input = $form->getValues();
            $q = Doctrine_Query::create()->from('Zf1_Model_Item i')
                ->leftJoin('i.Zf1_Model_Country c')
                ->leftJoin('i.Zf1_Model_Grade g')
                ->leftJoin('i.Zf1_Model_Type t')
                ->where('i.DisplayStatus = 1')
                ->addWhere('i.DisplayUntil >= CURDATE()');

            if (!empty($input['Description'])) {
                $q->addWhere('i.Description LIKE ?', '%' . $input['Description'] . '%');
            }

            if (!empty($input['y'])) {
                $q->addWhere('i.Year = ?', $input['y']);
            }

            if (!empty($input['g'])) {
                $q->addWhere('i.GradeID = ?', $input['g']);
            }

            if (!empty($input['p'])) {
                $q->addWhere('? BETWEEN i.SalePriceMin AND i.SalePriceMax', $input['p']);
            }

            $sql = $q->getSqlQuery();
            $results = $q->fetchArray();
            $this->view->results = $results;

            $end = microtime(true);
            $time = $end - $start;
            $this->view->time = $time;
        }
    }

    /**
     * Action to perform full-text search
     * @throws Zend_Form_Exception
     */
    public function searchFullAction()
    {
        $start = microtime(true);

        // generate input form
        $form = new Zf1_Form_SearchFull;
        $this->view->form = $form;

        // get items matching search criteria
        if ($form->isValid($this->getRequest()->getParams())) {
            $input = $form->getValues();
            if (!empty($input['q'])) {
                $config = $this->getInvokeArg('bootstrap')->getOption('indexes');
                try {
                    $index = Zend_Search_Lucene::open($config['indexPath']);
                    $results = $index->find(Zend_Search_Lucene_Search_QueryParser::parse($input['q']));
                    $indexSize = $index->count();
                    $indexCountDocs = $index->numDocs();

                    $end = microtime(true);
                    $time = $end - $start;

                    $this->view->results = $results;
                    $this->view->indexSize = $indexSize;
                    $this->view->indexCountDocs = $indexCountDocs;
                    $this->view->time = $time;
                } catch (\Zend_Exception $fault) {
                    $error2 = 'faultcode = ' . $fault->getCode() . ', faultstring = ' . $fault->getMessage();
                }
            }
        }
    }
}
