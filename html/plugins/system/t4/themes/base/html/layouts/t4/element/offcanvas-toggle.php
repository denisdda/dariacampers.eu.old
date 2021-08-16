<?php
	$paramsTpl = JFactory::getApplication()->getTemplate(true)->params;

    $navigation_settings = $paramsTpl->get('navigation-settings');
    if (!filter_var($navigation_settings->get('oc_enabled'), FILTER_VALIDATE_BOOLEAN)) return;

	$show_in_lg = filter_var($navigation_settings->get('oc_showlg'), FILTER_VALIDATE_BOOLEAN);

    $breakpoint = $navigation_settings->get('option_breakpoint', 'lg');
    $lgsc = $show_in_lg ? '' : ' d-' . $breakpoint . '-none';
    // add bodyclass
    if ($show_in_lg) {
        \T4\T4::getInstance()->addBodyClass('oc-desktop');
    }
?>
<span class="t4-offcanvas-toggle<?php echo $lgsc ?>"><span class="toggle-bars"></span></span>
