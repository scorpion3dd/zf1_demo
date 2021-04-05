<?php

/**
 * Class NewsController
 */
class NewsController extends Zend_Controller_Action
{
    /**
     * index action
     */
    public function indexAction()
    {
        try {
            // get Twitter search feed
            $q = 'philately';
            $this->view->q = $q;
            $twitter = new Zend_Service_Twitter();
            $tweets = $twitter->searchTweets($q, array('lang' => 'en', 'rpp' => 8, 'show_user' => true));
            $HttpClient = $twitter->getHttpClient();
            $last_response = $HttpClient->getLastResponse();
            if ($last_response->getStatus() == 200) {
                $this->view->tweets = $tweets;
            }

            // get Google News Atom feed
            $this->view->feeds = array();
            $gnewsFeed = "http://news.google.com/news?hl=en&q=$q&output=atom";
            $this->view->feeds[0] = Zend_Feed_Reader::import($gnewsFeed);

            // get BPMA RSS feed
            $bpmaFeed = "http://k.img.com.ua/rss/ru/all_news2.0.xml";
            $this->view->feeds[1] = Zend_Feed_Reader::import($bpmaFeed);

            // get BPMA RSS feed
            $bpmaFeed = "https://www.rbc.ua/static/rss/newsline.img.rus.rss.xml";
            $this->view->feeds[2] = Zend_Feed_Reader::import($bpmaFeed);
        } catch (Exception $e) {
            $err = $e->getMessage();
        }
    }

    /**
     * index2 action
     */
    public function index2Action()
    {
        try {
            // get Twitter search feed
            $q = 'philately';
            $this->view->q = $q;

            // get cache
            $fileCache = $this->getInvokeArg('bootstrap')
                ->getResource('cachemanager')
                ->getCache('news');

            // read Twitter results from cache if available
            $id = 'twitter';
            if (!($this->view->tweets = $fileCache->load($id))) {
                $twitter = new Zend_Service_Twitter();
                $this->view->tweets = $twitter->searchTweets($q,
                    array('lang' => 'en', 'rpp' => 8, 'show_user' => true));
                $fileCache->save($this->view->tweets, $id);
            }

            // cache feeds
            Zend_Feed_Reader::setCache($fileCache);

            // get Google News Atom feed
            $this->view->feeds = array();

            // get BPMA RSS feed
            $bpmaFeed = "http://k.img.com.ua/rss/ru/all_news2.0.xml";
            $this->view->feeds[1] = Zend_Feed_Reader::import($bpmaFeed);

            // get BPMA RSS feed
            $bpmaFeed = "https://www.rbc.ua/static/rss/newsline.img.rus.rss.xml";
            $this->view->feeds[2] = Zend_Feed_Reader::import($bpmaFeed);
        } catch (Exception $e) {
            $err = $e->getMessage();
        }
    }
}
