<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;
use Joomla\Registry\Registry as JRegistry;

class Css {
	public static function render($tplcss, $data) {
		$tplcss = str_replace('{', "{\n", $tplcss);
		$tplcss = str_replace('}', "\n}", $tplcss);
		$arr = explode("\n", $tplcss);

		$params = is_array($data) ? new JRegistry($data) : $data;

		$result = [];
		foreach ($arr as $i => $s) {
			if (!trim($s)) continue;

			$s = self::renderLine($s, $params);
			if ($s) $result[] = $s;
		}
		$css = implode("\n", $result);

		// remove empty rules
		$css = preg_replace('/[^{}]*\{\s*\}/mu', '', $css);

		return $css;
	}


	public static function renderTheme($tpl, $root, $data) {
		$params = is_array($data) ? new JRegistry($data) : $data;
		// parse to get variable map
		$vars = [];
		self::getVars($tpl, $params, $vars);
		self::getVars($root, $params, $vars);

		// parse tpl and replace value
		$replace = [];
		if (preg_match_all('/var\(([^\)]+)\)/mi', $tpl, $matches)) {

			foreach ($matches[1] as $i => $name) {
				if (!empty($vars[$name])) {
					$replace[$matches[0][$i]] = $vars[$name];
				} else {
					$replace[$matches[0][$i]] = Color::getInstance()->getColor(ltrim($name, '-'));
				}
				// $replace[$matches[0][$i]] = !empty($vars[$name]) ? $vars[$name] : '';
			}
			return str_replace(array_keys($replace), array_values($replace), $tpl);
		}
		return $tpl;
	}


	public static function getVars($tpl, $params, &$output) {

		if (preg_match_all('/^\s*(\-\-[a-z0-9][^\:]*)\:\s*([^;]+);/mi', $tpl, $matches)) {

			foreach ($matches[1] as $i => $name) {
				$val = self::renderLine($matches[2][$i], $params);
				$output[$name] = $val;
			}
		}
	}

	public static function renderLine($s, $params) {

		if (!preg_match_all('/__([0-9a-z_]+)/i', $s, $matches)) {
			return $s;
		}

		$search = [];
		$replace = [];
		foreach ($matches[1] as $j => $name) {
			$val = $params->get($name);
			if ($val) {
				if(preg_match('/(\s*)color/',$val) || preg_match('/gray.+(.*)/',$val)){
					if($params->get('styles_'.(str_replace(' ','_',$val)))) $val = $params->get('styles_'.(str_replace(' ','_',$val)));
					if($params->get('styles_brand_'.(str_replace(' color','',$val)))) $val = $params->get('styles_brand_'.(str_replace(' color','',$val)));
				}
				if (preg_match('/url\(' . preg_quote($matches[0][$j]) . '\)/', $s)) $val = self::sefUrl($val);
				$search[] = $matches[0][$j];
				$replace[] = $val;
			}
		}
		if (count($search)) {
			$s = str_replace($search, $replace, $s);
			return $s;
		}

		return '';
	}


	protected static function sefUrl($url) {
		if (preg_match('/^(https?\:|\/)/', $url)) return $url;
		return \JUri::root(true) . '/' . $url;
	}

}
