<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

abstract class baformsHelper 
{
    public static function getFormsLanguage()
    {
        $language = JFactory::getLanguage();
        $paths = $language->getPaths('com_baforms');
        $result = array();
        foreach ($paths as $key => $value) {
            if (JFile::exists($key)) {
                $contents = JFile::read($key);
                $contents = str_replace('_QQ_', '"\""', $contents);
                $data = parse_ini_string($contents);
                foreach ($data as $ind => $value) {
                    $result[$ind] = JText::_($ind);
                }
            }
        }
        $data = 'var formsLanguage = '.json_encode($result).';';

        return $data;
    }

    public static function getAcymailingLists()
    {
        $html = '';
        $checkAcymailing = self::checkAcymailing();
        if (!empty($checkAcymailing)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('listid, name ')
                ->from('#__acymailing_list');
            $db->setQuery($query);
            $list = $db->loadObjectList();
            foreach ($list as $value) {
                $html .= '<option value="'.$value->listid.'">'.$value->name.'</option>';
            }
        }

        return $html;
    }

    public static function getAcymLists()
    {
        $html = '';
        $checkAcymailing = self::checkAcym();
        if (!empty($checkAcymailing)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id, name')
                ->from('#__acym_list');
            $db->setQuery($query);
            $list = $db->loadObjectList();
            foreach ($list as $value) {
                $html .= '<option value="'.$value->id.'">'.$value->name.'</option>';
            }
        }

        return $html;
    }

    public static function checkFormsState()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('`key`')
            ->from('#__baforms_api')
            ->where('service = '.$db->quote('balbooa'));
        $db->setQuery($query);
        $balbooa = $db->loadResult();
        if (empty($balbooa)) {
            $obj = new stdClass();
            $obj->key = $balbooa = '{}';
            $obj->service = 'balbooa';
            $db->insertObject('#__baforms_api', $obj);
            $obj = new stdClass();
            $obj->key = $balbooa = '{}';
            $obj->service = 'balbooa_activation';
            $db->insertObject('#__baforms_api', $obj);
        }

        return $balbooa;
    }

    public static function checkFormsActivation()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('`key`')
            ->from('#__baforms_api')
            ->where('service = '.$db->quote('balbooa_activation'));
        $db->setQuery($query);
        $balbooa = $db->loadResult();

        return $balbooa;
    }

    public static function setAppLicense($data)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_api')
            ->where('service = '.$db->quote('balbooa'));
        $db->setQuery($query);
        $balbooa = $db->loadObject();
        $balbooa->key = json_decode($balbooa->key);
        $balbooa->key->data = $data;
        $balbooa->key = json_encode($balbooa->key);
        $db->updateObject('#__baforms_api', $balbooa, 'id');
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_api')
            ->where('service = '.$db->quote('balbooa_activation'));
        $db->setQuery($query);
        $balbooa = $db->loadObject();
        $balbooa->key = '{"data":"active"}';
        $db->updateObject('#__baforms_api', $balbooa, 'id');
    }

    public static function checkAcymailing()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('element = '.$db->quote('com_acymailing'));
        $db->setQuery($query);
        $id = $db->loadResult();

        return $id;
    }

    public static function checkAcym()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('element = '.$db->quote('com_acym'));
        $db->setQuery($query);
        $id = $db->loadResult();

        return $id;
    }

    public static function aboutUs()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("manifest_cache");
        $query->from("#__extensions");
        $query->where("type=" .$db->quote('component'))
            ->where('element=' .$db->quote('com_baforms'));
        $db->setQuery($query);
        $cache = $db->loadResult();
        $about = json_decode($cache);
        $xml = simplexml_load_file(JPATH_ROOT.'/administrator/components/com_baforms/baforms.xml');
        $about->tag = (string)$xml->tag;

        return $about;
    }

    public static function defaultCheckboxes($name, $form)
    {
        $input = $form->getField($name);
        $test = $form->getValue($name);
        if ($test == null) {
            $class = !empty($input->class) ? ' class="' . $input->class . '"' : '';
            $value = !empty($input->default) ? $input->default : '1';
            $checked = $input->checked || !empty($value) ? ' checked' : '';
            return '<input type="checkbox" name="' . $input->name . '" id="' . $input->id . '" value="'
            . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"' . $class . $checked . ' />';
        } else {
            return $form->getInput($name);
        }
    }
    
    public static function drawFieldEditor($items)
    {
        $str = '<tr><th class="form-title"><a href="#" data-shortcode="[submission ID]">';
        $str .= JText::_('SUBMISSION_ID').'</a></th><td></td></tr>';
        $str .= '<tr><th class="form-title"><a href="#" data-shortcode="[page title]">';
        $str .= JText::_('PAGE_TITLE').'</a></th><td></td></tr>';
        $str .= '<tr><th class="form-title"><a href="#" data-shortcode="[page URL]">';
        $str .= JText::_('PAGE_URL').'</a></th><td></td></tr>';
        foreach ($items as $item) {
            $settings = explode('_-_', $item->settings);
            if ($settings[0] != 'button') {
                $type = $settings[2];
                if ($type != 'image' && $type != 'htmltext' && $type != 'map' && $type !='terms'){
                    $settings = explode(';', $settings[3]);
                    if (!isset($settings[2])) {
                        $settings[2] = '';
                    }
                    $name = self::checkItems($settings[0], $type, $settings[2]);
                    $str .= '<tr><th class="form-title"><a href="#" data-shortcode="[field ID='.$item->id.']">'.$name;
                    $str .= '</a></th><td>'. $item->id. '</td></tr>';
                }
            }
        }
        return $str;
    }

    public static function checkItems($item, $type, $place)
    {
        if ($item != '') {
            return $item;
        } else {
            if ($type == 'textarea') {
                if ($place != '') {
                    return $place;
                } else {
                    return 'Textarea';
                }
            }
            if ($type == 'textInput') {
                if ($place != '') {
                    return $place;
                } else {
                    return 'TextInput';
                }
            }
            if ($type == 'chekInline') {
                return 'ChekInline';
            }
            if ($type == 'checkMultiple') {
                return 'CheckMultiple';
            }
            if ($type == 'radioInline') {
                return 'RadioInline';
            }
            if ($type == 'radioMultiple') {
                return 'RadioMultiple';
            }
            if ($type == 'dropdown') {
                return 'Dropdown';
            }
            if ($type == 'selectMultiple') {
                return 'SelectMultiple';
            }
            if ($type == 'date') {
                return 'Date';
            }
            if ($type == 'slider') {
                return 'Slider';
            }
            if ($type == 'email') {
                if ($place != '') {
                    return $place;
                } else {
                    return 'Email';
                }
            }
            if ($type == 'address') {
                if ($place != '') {
                    return $place;
                } else {
                    return 'Address';
                }
            }
        }
    }
    
    public static function getContentsCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        
        return $data;
    }
    
    public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
            JText::_('Forms'),
			'index.php?option=com_baforms&view=forms',
			$vName == 'forms'
		);
        JHtmlSidebar::addEntry(
            JText::_('Submissions'),
			'index.php?option=com_baforms&view=submissions',
			$vName == 'Submissions'
		);
    }
}