<?php
/**
 * jllike
 *
 * @version 4.0.0
 * @author Vadim Kunicin (vadim@joomline.ru), Arkadiy (a.sedelnikov@gmail.com)
 * @copyright (C) 2010-2019 by Vadim Kunicin (http://www.joomline.ru)
 * @license GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die ;
use Joomla\Registry\Registry as JRegistry;
require_once JPATH_ROOT.'/plugins/content/jllike/helper.php';

class ElementJlLikeZooEl extends Element implements iSubmittable {

	public function hasValue($params = array())
    {
		return (bool) $this->get('value', $this->config->get('default', 1));
	}


	public function render($params = array())
    {
		if (!$this->get('value', $this->config->get('default', 1)))
        {
            return '';
        }

        $ssl = (JFactory::getConfig()->get('force_ssl') == 2) ? 1 : -1;
        $item_route = JRoute::_($this->app->route->item($this->_item, false), true, $ssl);
        JFactory::getLanguage()->load('plg_content_jllike'. JPATH_ROOT.'/plugins/content/jllike');
        $plugin = JPluginHelper::getPlugin('content', 'jllike');
        $plgParams = new JRegistry($plugin->params);

        $parent_contayner = $this->config->get('parent_contayner', '');
        if(!empty($parent_contayner))
        {
            $plgParams->set('parent_contayner', $parent_contayner);
        }
        $helper = PlgJLLikeHelper::getInstance($plgParams);
        $helper->loadScriptAndStyle(0);

        $intro = $text = $image = '';

        $field = $this->config->get('intro_field', '');
        if($field != '')
        {
            $element = $this->_item->getElement($field);
            $intro = $element->get('value');
            if(empty($intro))
            {
                $intro = $element->data();
                $intro = $intro[0]["value"];
            }
        }

        $field = $this->config->get('text_field', '');
        if($field != '')
        {
            $element = $this->_item->getElement($field);
            $text = $element->get('value');
            if(empty($text))
            {
                $text = $element->data();
                $text = $text[0]["value"];
            }
        }

		$image = '';

        if($this->config->get('img_source', 'field') == 'field')
        {
            $field = $this->config->get('img_field', '');
            if(!empty($field)){
                $image = $this->_item->getElement($field)->get('file');

                if(!empty($image)){
                    $params = $this->app->data->create($params);
                    $file  	= $this->app->zoo->resizeImage(JPATH_ROOT.'/'.$image, $params->get('width', 0), $params->get('height', 0));
                    $image   = JURI::root() . $this->app->path->relative($file);
                }
            }
        }
        else{
            $image = PlgJLLikeHelper::extractImageFromText($intro, $text);
        }

        $metadesc = $this->_item->getParams()->get('metadata.description');

        $text = $helper->getShareText($metadesc, $intro, $text);
        $shares = $helper->ShowIN($this->_item->id, $item_route, $this->_item->name, $image, $text, $plgParams->get('enable_opengraph',1));
		return $shares;
	}

	public function edit()
    {
		return $this->app->html->_('select.booleanlist', $this->getControlName('value'), '', $this->get('value', $this->config->get('default', 1)));
	}


	public function renderSubmission($params = array())
    {
        return $this->edit();
	}


	public function validateSubmission($value, $params)
    {
		return array('value' => (bool) $value->get('value'));
	}
}
