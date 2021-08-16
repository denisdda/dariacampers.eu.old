<?php

	$doc = $displayData->doc;
	$paramsTpl = $doc->params;
	//if(empty($paramsTpl->get('t4-navigation'))) $paramsTpl->set('t4-navigation','default');
	//$params = json_decode(\T4\Helper\Path::getFileContent('etc/navigation/'.$paramsTpl->get('t4-navigation').'.json'));
	//if(empty($params)) return;
	//if (!$params->navigation_oc_enabled) return;
	$navigation_settings = $paramsTpl->get('navigation-settings');
	if (!$navigation_settings->get('oc_enabled')) return;

	$oc_effect = $navigation_settings->get('oc_effect', 1);
	$oc_pos_name = $navigation_settings->get('oc_pos_name', 'offcanvas');
	$oc_pos_style = $navigation_settings->get('oc_pos_style', 'xhtml');
	$oc_rightside = filter_var($navigation_settings->get('oc_rightside'), FILTER_VALIDATE_BOOLEAN);

	// add css & js
	$doc->addScript(T4\Helper\Path::findInTheme('js/offcanvas.js', true));
	$doc->addStylesheet(T4\Helper\Path::findInTheme('css/offcanvas' . ($oc_rightside ? '-right' : '') . '.css', true));
?>
<div class="t4-offcanvas" data-effect="<?php echo $oc_effect ?>">
	<div class="t4-off-canvas-header">
		<h3><?php echo JText::_('T4_OFF_CANVAS_TITLE') ?></h3>
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	</div>

	<div class="t4-off-canvas-body">
		<jdoc:include type="modules" name="<?php echo $oc_pos_name ?>" style="<?php echo $oc_pos_style ?>" />
	</div>
</div>
