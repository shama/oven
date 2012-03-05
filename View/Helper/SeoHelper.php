<?php
/**
 * Seo Helper
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2011 Kyle Robinson Young
 *
 */
class SeoHelper extends AppHelper {
    public $helpers = array('Html');
    public $_settings = array(
        '*' => array(
            'title' => '',
            'Keywords' => '',
            'Description' => '',
        ),
    );
    public $_view = null;
    
    /**
     * BEFORE RENDER
     */
    function beforeRender() {
        $this->_view =& ClassRegistry::getObject('view');
    }
    
    /**
     * META
     * @param str $name
     * @param str $content
     * @param str $page
     * @return bool
     */
    function meta($name=null, $content=null, $page=null) {
        if (!isset($page)) {
            $page = $this->_view->here;
        }
        $this->_settings = Set::merge(
            $this->_settings,
            array($page => array($name => $content))
        );
        return true;
    }
    
    /**
     * TITLE
     * @param str $content
     * @param str $page
     * @return bool
     */
    function title($title=null, $page=null) {
        return $this->meta('title', $title, $page);
    }
    
    /**
     * RENDER
     * @return str
     */
    function render() {
        $out = '';
        $all = $this->_settings['*'];
        $page = (!empty($this->_settings[$this->_view->here])) ? $this->_settings[$this->_view->here] : array();
        $meta = Set::merge($all, $page);
        foreach ($meta as $name => $content) {
            switch (strtolower($name)) {
                case 'title':
                    $out .= $this->Html->tag('title', $content) . "\n";
                    break;
                default:
                    $out .= $this->Html->meta(compact('name', 'content')) . "\n";
                    break;
            }
        }
        return $this->output($out);
    }
}