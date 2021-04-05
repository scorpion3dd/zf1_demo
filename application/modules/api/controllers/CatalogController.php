<?php

/**
 * Class Api_CatalogController
 */
class Api_CatalogController extends Zend_Rest_Controller
{
    const API_CATALOG = '/api/catalog';

    /** @var string */
    private string $apiBaseUrl;

    /**
     * Disable layouts and rendering
     */
    public function init()
    {
        $configs = $this->getInvokeArg('bootstrap')->getOption('api');
        $this->apiBaseUrl = $configs->baseUrl . self::API_CATALOG;
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);
    }

    /**
     * @throws Zend_Feed_Exception
     */
    public function indexAction()
    {
        // get records from database
        $q = Doctrine_Query::create()
            ->from('Zf1_Model_Item i')
            ->leftJoin('i.Zf1_Model_Country c')
            ->leftJoin('i.Zf1_Model_Grade g')
            ->leftJoin('i.Zf1_Model_Type t')
            ->addWhere('i.DisplayStatus = 1')
            ->limit(100);
        $result = $q->fetchArray();

        // set feed elements
        $output = array(
            'title' => 'Catalog records',
            'link' => $this->apiBaseUrl,
            'author' => 'Zf1 API/1.0',
            'charset' => 'UTF-8',
            'entries' => array()
        );

        // set entry elements
        foreach ($result as $r) {
            $output['entries'][] = array(
                'title' => $r['Title'] . ' - ' . $r['Year'],
                'link' => $this->apiBaseUrl . '/' . $r['RecordID'],
                'description' => $r['Description'],
                'lastUpdate' => strtotime($r['RecordDate']),
                'zf1:title' => $r['Title']
            );
        }

        // import array into atom feed
        // send to client
        $feed = Zend_Feed::importArray($output, 'atom');
        $feed->send();
        exit;
    }

    /**
     * Forward to indexAction
     */
    public function listAction()
    {
        return $this->_forward('index');
    }

    /**
     * @throws Zend_Controller_Response_Exception
     * @throws Zend_Feed_Exception
     */
    public function getAction()
    {
        // get entry record from database
        $id = $this->_getParam('id');
        $q = Doctrine_Query::create()
            ->from('Zf1_Model_Item i')
            ->leftJoin('i.Zf1_Model_Country c')
            ->leftJoin('i.Zf1_Model_Grade g')
            ->leftJoin('i.Zf1_Model_Type t')
            ->where('i.RecordID = ?', $id)
            ->addWhere('i.DisplayStatus = 1');
        $result = $q->fetchArray();

        // if record available
        // set entry elements
        if (count($result) == 1) {
            // set feed elements
            $output = array(
                'title' => 'Catalog record for item ID: ' . $id,
                'link' => $this->apiBaseUrl . '/' . $id,
                'author' => 'Zf1 App/1.0',
                'charset' => 'UTF-8',
                'entries' => array()
            );

            $output['entries'][0] = array(
                'title' => $result[0]['Title'] . ' - ' . $result[0]['Year'],
                'link' => $this->apiBaseUrl . '/' . $id,
                'description' => $result[0]['Description'],
                'lastUpdate' => strtotime($result[0]['RecordDate'])
            );

            // import array into atom feed
            $feed = Zend_Feed::importArray($output, 'atom');
            Zend_Feed::registerNamespace('zf1', 'http://zf1.demo');

            // set custom namespaced elements
            $feed->rewind();
            $entry = $feed->current();
            if ($entry) {
                $entry->{'zf1:id'} = $result[0]['RecordID'];
                $entry->{'zf1:title'} = $result[0]['Title'];
                $entry->{'zf1:year'} = $result[0]['Year'];
                $entry->{'zf1:grade'} = $result[0]['Zf1_Model_Grade']['GradeName'];
                $entry->{'zf1:description'} = $result[0]['Description'];
                $entry->{'zf1:country'} = $result[0]['Zf1_Model_Country']['CountryName'];
                $entry->{'zf1:price'} = null;
                $entry->{'zf1:price'}->{'zf1:min'} = $result[0]['SalePriceMin'];
                $entry->{'zf1:price'}->{'zf1:max'} = $result[0]['SalePriceMax'];
            }

            // output to client
            $feed->send();
        } else {
            $this->getResponse()->setHttpResponseCode(404);
            echo 'Invalid record identifier';
        }
        exit;
    }

    /**
     * @throws Zend_Controller_Response_Exception
     */
    public function postAction()
    {
        $this->getResponse()->setHttpResponseCode(500);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form = new Zf1_Form_ItemAPI;
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                try {
                    // read POST parameters and save to database
                    $item = new Zf1_Model_Item;
                    $item->fromArray($form->getValues());
                    $item->RecordDate = date('Y-m-d', time());
                    $item->DisplayStatus = 0;
                    $item->DisplayUntil = null;
                    $item->save();
                    $id = $item->RecordID;

                    // set response code to 201
                    // send ID of newly-created record
                    $this->getResponse()->setHttpResponseCode(201);
                    $this->getResponse()->setHeader('Location', $this->apiBaseUrl . '/' . $id);
                    $bodies = "id = $id";
                    echo $this->apiBaseUrl . '/' . $id;
                } catch (\Zend_Exception $fault) {
                    $bodies = 'faultcode = ' . $fault->getCode() . ', faultstring = ' . $fault->getMessage();
                }
            } else {
                $bodies = implode(',', $form->getErrorMessages());
            }
        }
        $this->getResponse()->appendBody($bodies);
        exit;
    }

    /**
     * @throws Zend_Controller_Response_Exception
     * @throws Zend_Form_Exception
     */
    public function putAction()
    {
        $this->getResponse()->setHttpResponseCode(500);
        $id = $this->_getParam('id');
        $q = Doctrine_Query::create()
            ->from('Zf1_Model_Item i')
            ->leftJoin('i.Zf1_Model_Country c')
            ->leftJoin('i.Zf1_Model_Grade g')
            ->leftJoin('i.Zf1_Model_Type t')
            ->where('i.RecordID = ?', $id)
            ->addWhere('i.DisplayStatus = 1');
        $item = $q->fetchOne();
        if (isset($item) && get_class($item) == Zf1_Model_Item::class) {
            $request = $this->getRequest();
            if ($request->isPut()) {
                $putData = $this->getRequest()->getParams();
                $data = [];
                $data['Year'] = $putData['Year'];
                $filters = array(
                    'Year' => 'StringTrim'
                );
                $validators = array(
                    'Year' => array(
                        'Digits',
                        new Zend_Validate_Int(),
                        array('Between', 1900, 2100)
                    )
                );
                $input = new Zend_Filter_Input($filters, $validators, $data);
                if ($input->isValid()) {
                    try {
                        $item->Year = $input->getEscaped('Year');
                        $item->save();
                        $id = $item->RecordID;
                        $this->getResponse()->setHttpResponseCode(201);
                        $this->getResponse()->setHeader('Location', $this->apiBaseUrl . '/' . $id);
                        $bodies = "id = $id";
                        echo $this->apiBaseUrl . '/' . $id;
                    } catch (\Zend_Exception $fault) {
                        $bodies = 'faultcode = ' . $fault->getCode() . ', faultstring = ' . $fault->getMessage();
                    }
                } else {
                    $bodies = implode(',', $input->getMessages());
                }
            }
        } else {
            $this->getResponse()->setHttpResponseCode(404);
            $bodies = 'Invalid record identifier';
        }
        $this->getResponse()->appendBody($bodies);
        exit;
    }

    /**
     * @throws Zend_Controller_Response_Exception
     */
    public function deleteAction()
    {
        $this->getResponse()->setHttpResponseCode(500);
        $id = $this->_getParam('id');
        $q = Doctrine_Query::create()
            ->from('Zf1_Model_Item i')
            ->where('i.RecordID = ?', $id);
        $item = $q->fetchOne();
        if (isset($item) && get_class($item) == Zf1_Model_Item::class) {
            $request = $this->getRequest();
            if ($request->isDelete()) {
                $item->delete();
                $bodies = "deleted id = $id";
                $this->getResponse()->setHttpResponseCode(200);
                $this->getResponse()->appendBody($bodies);
            }
        } else {
            $this->getResponse()->setHttpResponseCode(404);
            $bodies = 'Invalid record identifier';
        }
        $this->getResponse()->appendBody($bodies);
        exit;
    }

    /**
     * @throws Zend_Controller_Response_Exception
     */
    public function headAction()
    {
        $id = $this->_getParam('id');
        $q = Doctrine_Query::create()
            ->from('Zf1_Model_Item i')
            ->where('i.RecordID = ?', $id);
        $item = $q->fetchOne();
        if (isset($item) && get_class($item) == Zf1_Model_Item::class) {
            $this->getResponse()->setHttpResponseCode(200);
        } else {
            $this->getResponse()->setHttpResponseCode(404);
        }
        exit;
    }
}
