<?php

/**
 * Class Catalog_AdminItemController
 */
class Catalog_AdminItemController extends Zend_Controller_Action
{
    /**
     * Init
     */
    public function init()
    {
        $this->view->doctype('XHTML1_STRICT');
    }

    /**
     * Action to handle admin URLs
     */
    public function preDispatch()
    {
        // set admin layout
        // check if user is authenticated
        // if not, redirect to login page
        $url = $this->getRequest()->getRequestUri();
        $this->_helper->layout->setLayout('admin');
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $session = new Zend_Session_Namespace('zf1.auth');
            $session->requestURL = $url;
            $this->_redirect('/admin/login');
        }
    }

    /**
     * Action to display list of catalog items
     * @throws Zend_Config_Exception
     * @throws Zend_Controller_Action_Exception
     */
    public function indexAction()
    {
        // set filters and validators for GET input
        $filters = array(
            'sort' => array('HtmlEntities', 'StripTags', 'StringTrim'),
            'dir' => array('HtmlEntities', 'StripTags', 'StringTrim'),
            'page' => array('HtmlEntities', 'StripTags', 'StringTrim')
        );
        $validators = array(
            'sort' => array(
                'Alpha',
                array('InArray', 'haystack' =>
                    array('RecordID', 'Title', 'Denomination', 'CountryID', 'GradeID', 'Year'))
            ),
            'dir' => array(
                'Alpha', array('InArray', 'haystack' =>
                    array('asc', 'desc'))
            ),
            'page' => array('Int')
        );
        $input = new Zend_Filter_Input($filters, $validators);
        $input->setData($this->getRequest()->getParams());

        // test if input is valid
        // create query and set pager parameters
        if ($input->isValid()) {
            $q = Doctrine_Query::create()
                ->from('Zf1_Model_Item i')
                ->leftJoin('i.Zf1_Model_Grade g')
                ->leftJoin('i.Zf1_Model_Country c')
                ->leftJoin('i.Zf1_Model_Type t')
                ->orderBy(sprintf('%s %s', $input->sort, $input->dir));

            // configure pager
            $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
            $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
            $perPage = $localConfig->admin->itemsPerPage;
            $numPageLinks = $localConfig->admin->numPageLinks;

            // initialize pager
            $pager = new Doctrine_Pager($q, $input->page, $perPage);

            // execute paged query
            $result = $pager->execute(array(), Doctrine::HYDRATE_ARRAY);

            // initialize pager layout
            $pagerRange = new Doctrine_Pager_Range_Sliding(array('chunk' => $numPageLinks), $pager);
            $pagerUrlBase = $this->view->url(array(), 'admin-catalog-index', 1) . "/{%page}/{$input->sort}/{$input->dir}";
            $pagerLayout = new Doctrine_Pager_Layout($pager, $pagerRange, $pagerUrlBase);

            // set page link display template
            $pagerLayout->setTemplate('<a href="{%url}">{%page}</a>');
            $pagerLayout->setSelectedTemplate('<span class="current">{%page}</span>');
            $pagerLayout->setSeparatorTemplate('&nbsp;');

            // set view variables
            $this->view->records = $result;
            $this->view->pages = $pagerLayout->display(null, true);
        } else {
            throw new Zend_Controller_Action_Exception('Invalid input');
        }
    }

    /**
     * Action to delete catalog items
     * @throws Doctrine_Query_Exception
     * @throws Zend_Controller_Action_Exception
     */
    public function deleteAction()
    {
        // set filters and validators for POST input
        $filters = array(
            'ids' => array('HtmlEntities', 'StripTags', 'StringTrim')
        );
        $validators = array(
            'ids' => array('NotEmpty', 'Int')
        );
        $input = new Zend_Filter_Input($filters, $validators);
        $input->setData($this->getRequest()->getParams());

        // test if input is valid
        // read array of record identifiers
        // delete records from database
        if ($input->isValid()) {
            $q = Doctrine_Query::create()
                ->delete('Zf1_Model_Item i')
                ->whereIn('i.RecordID', $input->ids);
            $result = $q->execute();
            $config = $this->getInvokeArg('bootstrap')->getOption('uploads');
            foreach ($input->ids as $id) {
                foreach (glob("{$config['uploadPath']}/{$id}_*") as $file) {
                    unlink($file);
                }
            }
            $this->_helper->getHelper('FlashMessenger')->addMessage('The records were successfully deleted.');
            $this->_redirect('/admin/catalog/item/success');
        } else {
            throw new Zend_Controller_Action_Exception('Invalid input');
        }
    }

    /**
     * Action to modify an individual catalog item
     * @throws Zend_Controller_Action_Exception
     * @throws Zend_Form_Exception
     */
    public function updateAction()
    {
        // load JavaScript and CSS files
        $this->view->headLink()->appendStylesheet('http://yui.yahooapis.com/combo?2.8.0r4/build/calendar/assets/skins/sam/calendar.css');
        $this->view->headScript()->appendFile('/js/form.js');
        $this->view->headScript()->appendFile('http://yui.yahooapis.com/combo?2.8.0r4/build/yahoo-dom-event/yahoo-dom-event.js&2.8.0r4/build/calendar/calendar-min.js');

        // generate input form
        //$form = new Zf1_Form_ItemUpdate;
        $form = new Zf1_Form_ItemUpdate2;
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            // if POST request
            // test if input is valid
            // retrieve current record
            // update values and replace in database
            $postData = $this->getRequest()->getPost();

            /*
            $postData['DisplayUntil'] = sprintf('%04d-%02d-%02d',
                $this->getRequest()->getPost('DisplayUntil_year'),
                $this->getRequest()->getPost('DisplayUntil_month'),
                $this->getRequest()->getPost('DisplayUntil_day')
            );*/

            if ($form->isValid($postData)) {
                $input = $form->getValues();
                $item = Doctrine::getTable('Zf1_Model_Item')->find($input['RecordID']);
                $item->fromArray($input);
                $item->DisplayUntil = ($item->DisplayStatus == 0) ? null : $item->DisplayUntil;
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

                $this->_helper->getHelper('FlashMessenger')->addMessage('The record was successfully updated.');
                $this->_redirect('/admin/catalog/item/success');
            }
        } else {
            // if GET request
            // set filters and validators for GET input
            // test if input is valid
            // retrieve requested record
            // pre-populate form
            $filters = array(
                'id' => array('HtmlEntities', 'StripTags', 'StringTrim')
            );
            $validators = array(
                'id' => array('NotEmpty', 'Int')
            );
            $input = new Zend_Filter_Input($filters, $validators);
            $input->setData($this->getRequest()->getParams());
            if ($input->isValid()) {
                $q = Doctrine_Query::create()
                    ->from('Zf1_Model_Item i')
                    ->leftJoin('i.Zf1_Model_Country c')
                    ->leftJoin('i.Zf1_Model_Grade g')
                    ->leftJoin('i.Zf1_Model_Type t')
                    ->where('i.RecordID = ?', $input->id);
                $result = $q->fetchArray();
                if (count($result) == 1) {
                    /*
                    // perform adjustment for date selection lists
                    $date = $result[0]['DisplayUntil'];
                    $result[0]['DisplayUntil_day'] = date('d', strtotime($date));
                    $result[0]['DisplayUntil_month'] = date('m', strtotime($date));
                    $result[0]['DisplayUntil_year'] = date('Y', strtotime($date));
                    */
                    $this->view->form->populate($result[0]);
                } else {
                    throw new Zend_Controller_Action_Exception('Page not found', 404);
                }
            } else {
                throw new Zend_Controller_Action_Exception('Invalid input');
            }
        }
    }

    /**
     * Action to display an individual catalog item
     * @throws Zend_Config_Exception
     * @throws Zend_Controller_Action_Exception
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
                ->where('i.RecordID = ?', $input->id);
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
     * Success action
     */
    public function successAction()
    {
        if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {
            $this->view->messages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        } else {
            $this->_redirect('/admin/catalog/item/index');
        }
    }

    /**
     * Action to create full-text indices
     */
    public function indexesAction()
    {
        $form = new Zf1_Form_CreateIndexes;
        $this->view->form = $form;
        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($this->getRequest()->getParams())) {
            try {
                $start = microtime(true);
                set_time_limit(0);

                // create and execute query
                $q = Doctrine_Query::create()
                    ->from('Zf1_Model_Item i')
                    ->leftJoin('i.Zf1_Model_Country c')
                    ->leftJoin('i.Zf1_Model_Grade g')
                    ->leftJoin('i.Zf1_Model_Type t')
                    ->where('i.DisplayStatus = 1')
                    ->addWhere('i.DisplayUntil >= CURDATE()');
                $result = $q->fetchArray();

                // get index directory
                $config = $this->getInvokeArg('bootstrap')->getOption('indexes');
                $index = Zend_Search_Lucene::create($config['indexPath']);

                foreach ($result as $r) {
                    // create new document in index
                    $doc = new Zend_Search_Lucene_Document();

                    // index and store fields
                    $doc->addField(Zend_Search_Lucene_Field::Text('Title', $r['Title']));
                    $doc->addField(Zend_Search_Lucene_Field::Text('Country', $r['Zf1_Model_Country']['CountryName']));
                    $doc->addField(Zend_Search_Lucene_Field::Text('Grade', $r['Zf1_Model_Grade']['GradeName']));
                    $doc->addField(Zend_Search_Lucene_Field::Text('Year', $r['Year']));
                    $doc->addField(Zend_Search_Lucene_Field::UnStored('Description', $r['Description']));
                    $doc->addField(Zend_Search_Lucene_Field::UnStored('Denomination', $r['Denomination']));
                    $doc->addField(Zend_Search_Lucene_Field::UnStored('Type', $r['Zf1_Model_Type']['TypeName']));
                    $doc->addField(Zend_Search_Lucene_Field::UnIndexed('SalePriceMin', $r['SalePriceMin']));
                    $doc->addField(Zend_Search_Lucene_Field::UnIndexed('SalePriceMax', $r['SalePriceMax']));
                    $doc->addField(Zend_Search_Lucene_Field::UnIndexed('RecordID', $r['RecordID']));

                    // save result to index
                    $index->addDocument($doc);
                }

                // set number of documents in index
                $count = $index->count();

                $end = microtime(true);
                $time = round($end - $start, 2);
                $this->_helper->getHelper('FlashMessenger')
                    ->addMessage("The index was successfully created with $count documents by $time seconds.");
                $this->_redirect('/admin/catalog/item/success');
            } catch (\Zend_Exception $fault) {
                $error2 = 'faultcode = ' . $fault->getCode() . ', faultstring = ' . $fault->getMessage();
            }
        }
    }

    /**
     * Generates fake data
     * https://github.com/fzaninotto/Faker
     * @throws Exception
     */
    public function generatesAction()
    {
        $form = new Zf1_Form_GeneratesFakeData;
        $this->view->form = $form;

        if ($form->isValid($this->getRequest()->getParams())) {
            $start = microtime(true);
            $input = $form->getValues();
            if (!empty($input['countFaker'])) {
                try {
                    set_time_limit(0);

                    // require the Faker autoloader
                    $libraryBaseDir = SITE_ROOT_DIR . '/library/Faker/src/autoload.php';
                    require_once $libraryBaseDir;

                    // use the factory to create a Faker\Generator instance
                    $faker = Faker\Factory::create();
                    $countFaker = (int)$input['countFaker'];
                    $results = '';
                    for ($i = 0; $i < $countFaker; $i++) {
                        $item = new Zf1_Model_Item();
                        $item->RecordDate = $faker->date('Y-m-d');
                        $item->SellerName = $faker->name;
                        $item->SellerEmail = $faker->email;
                        $item->SellerTel = $faker->phoneNumber;
                        $item->SellerAddress = $faker->address;
                        $item->Title = $faker->text;
                        $item->Year = $faker->year();
                        $item->CountryID = $faker->numberBetween(1, 16);
                        $item->Denomination = $faker->numberBetween(1, 10);
                        $item->TypeID = $faker->numberBetween(1, 5);
                        $item->GradeID = $faker->numberBetween(1, 5);
                        $item->SalePriceMin = $faker->numberBetween(1, 10);
                        $item->SalePriceMax = $faker->numberBetween(100, 1000);
                        $item->Description = $faker->text;
                        $item->DisplayStatus = 1;
                        $item->DisplayUntil = '2020-12-31';
                        $item->save();
                        $id = $item->getIncremented();
                        if ($results == '') {
                            $results .= $id;
                        } else {
                            $results .= ', ' . $id;
                        }
                    }
                    $end = microtime(true);
                    $time = round($end - $start, 2);

                    $this->view->countFaker = $countFaker;
                    $this->view->results = $results;
                    $this->view->time = $time;
                } catch (\Zend_Exception $fault) {
                    $error2 = 'faultcode = ' . $fault->getCode() . ', faultstring = ' . $fault->getMessage();
                }
            }
        }
    }
}
