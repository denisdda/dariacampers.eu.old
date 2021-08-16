<?php
/*------------------------------------------------------------------------
# JF_COUDA! - JOOMFREAK.COM JOOMLA 3 TEMPLATE 
# Mar 2016
# ------------------------------------------------------------------------
# COPYRIGHT: (C) 2014 JOOMFREAK.COM / KREATIF GMBH
# LICENSE: Creative Commons Attribution
# AUTHOR: JOOMFREAK.COM
# WEBSITE: http://www.joomfreak.com - http://www.kreatif-multimedia.com
# EMAIL: info@joomfreak.com
-------------------------------------------------------------------------*/

defined('_JEXEC') or die;



$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$lang            = JFactory::getLanguage();
$this->language  = $doc->language;
$this->direction = $doc->direction;

// Getting params from template
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');
$menu      = $app->getMenu();
$active    = $menu->getItem($itemid);
$font 	   = $this->params->get('googleFont');
$latitude           = (float)$this->params->get( 'latitude', '' );
$longitude          = (float)$this->params->get( 'longitude', '' );
$markerdescription  = $this->params->get('markerdescription', '');

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');

$doc->addScript($this->baseurl . '/templates/' . $this->template . '/scripts/js/isotope.pkgd.min.js');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/scripts/js/imagesloaded.min.js');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/scripts/js/owl.carousel.min.js');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/scripts/js/nivo-lightbox.min.js');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/scripts/js/template.js');

// Add Stylesheets
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/bootstrap.css');
$doc->addStyleSheet('https://fonts.googleapis.com/css?family=Roboto:,300,300italic,400,400italic,500,500italic,700,700italic,900,900italic');
if($font == 'OpenSans')
	$doc->addStyleSheet('https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic,700,700italic');
if($font == 'Lato')
	$doc->addStyleSheet('https://fonts.googleapis.com/css?family=Lato:300,300italic,400,400italic,700,700italic,900,900italic');
if($font == 'PTSans')
	$doc->addStyleSheet('https://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic');
if($font == 'SourceSansPro')
	$doc->addStyleSheet('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400italic,600,600italic,700,700italic');
if($font == 'Nobile')
	$doc->addStyleSheet('https://fonts.googleapis.com/css?family=Nobile:400,400italic,700,700italic');
if($font == 'Ubuntu')
	$doc->addStyleSheet('https://fonts.googleapis.com/css?family=Ubuntu:300,300italic,400,400italic,500,500italic,700,700italic');
if($font == 'IstokWeb')
	$doc->addStyleSheet('https://fonts.googleapis.com/css?family=Istok+Web:400,400italic,700,700italic');
if($font == 'Exo2')
	$doc->addStyleSheet('https://fonts.googleapis.com/css?family=Exo+2:400,400italic,500,500italic,600,600italic,700,700italic,800,800italic');

$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/icons-styles.css');
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/owl.carousel.css');
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/nivo-lightbox.css');
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/template.css');

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Adjusting content width
if ($this->countModules('left') && $this->countModules('right')) {
	$span = "col-sm-6";
} elseif ($this->countModules('left') && !$this->countModules('right')) {
	$span = "col-sm-9";
} elseif (!$this->countModules('left') && $this->countModules('right')) {
	$span = "col-sm-9";
} else {
	$span = "col-sm-12";
}

// Logo file or site title param
if ($this->params->get('logoOption') == 1 && $this->params->get('logoFile')) {
	$logo = '<img src="' . JUri::root() . $this->params->get('logoFile') . '" alt="' . $sitename . '" />';
} elseif ($this->params->get('logoOption') == 2 && $this->params->get('logoText')) {
	$logo = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($this->params->get('logoText')) . '</span>';
} else {
	$logo = '<span class="site-title" title="' . $sitename . '">' . $sitename . '</span>';
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<jdoc:include type="head" />
	
	<?php // Font ?>
	<?php if($font == 'OpenSans') : ?>
	<style type="text/css">
		body {
			font-family: 'Open Sans', sans-serif;
		}
	</style>
	<?php endif; ?>
		
	<?php if($font == 'Lato') : ?>
	<style type="text/css">
		body {
			font-family: 'Lato', sans-serif;
		}
	</style>
	<?php endif; ?>
		
	<?php if($font == 'PTSans') : ?>
	<style type="text/css">
		body {
			font-family: 'PT Sans', sans-serif;
		}
	</style>
	<?php endif; ?>
		
	<?php if($font == 'SourceSansPro') : ?>
	<style type="text/css">
		body {
			font-family: 'Source Sans Pro', sans-serif;
		}
	</style>
	<?php endif; ?>
		
	<?php if($font == 'Nobile') : ?>
	<style type="text/css">
		body {
			font-family: 'Nobile', sans-serif;
		}
	</style>
	<?php endif; ?>
		
	<?php if($font == 'Ubuntu') : ?>
	<style type="text/css">
		body {
			font-family: 'Ubuntu', sans-serif;
		}
	</style>
	<?php endif; ?>
		
	<?php if($font == 'IstokWeb') : ?>
	<style type="text/css">
		body {
			font-family: 'Istok Web', sans-serif;
		}
	</style>
	<?php endif; ?>
		
	<?php if($font == 'Exo2') : ?>
	<style type="text/css">
		body {
			font-family: 'Exo 2', sans-serif;
		}
	</style>
	<?php endif; ?>
		
	<?php // Background color ?>
	<?php if ($this->params->get('backgroundColor') == 'light') : ?>
	<style type="text/css">
		body, #content {
			background-color: #f6f6f6;
		}
	</style>
	<?php endif; ?>
	<?php if ($this->params->get('backgroundColor') == 'dark') : ?>
	<style type="text/css">
		body, #content {
			background-color: #121212;
		}
		
		h1, h2, h3, h4, h5, h6 {
			color: #fafafa;
		}
		
		.project-section .moduletable > h3, .featured-section h3, .blog-section h3, .page-header h2, .breadcrumb > .active, .componentheading, h2.itemTitle {
			color: #fafafa;
		}
		
		a {
			color: #f5f5f5;
		}
		
	</style>
	<?php endif; ?>
	
	<?php // Template color ?>
	<?php if ($this->params->get('templateColor')) : ?>
	<style type="text/css">
		#panel, #header .slide span, .btn2, .projectLink a, .services-section [class*=" icon-"]:after, .services-section [class^="icon-"]:after, .portfolio-filter ul li.filter-item.active, .project-section .portfolio-caption, .team-section .team-caption, .footer-subscribe [type="submit"], #back-top, #k2Container .portfolio-caption, div.itemCommentsForm form input#submitCommentButton {
			background: <?php echo $this->params->get('templateColor'); ?>;
		}
		
		#header .slide, .services-section [class*=" icon-"], .services-section [class^="icon-"], .feedback-section .feedback-box:hover, blockquote, .countdown-section {
			border-color: <?php echo $this->params->get('templateColor'); ?>;
		}
		
		.services-section [class*=" icon-"], .services-section [class^="icon-"], .featured-section span[class*=" icon-"], .featured-section span[class^="icon-"], .feedbacks-header h1, .blog-section .moduleItemDateCreated, .top-footer div.k2ItemsBlock ul li .moduleItemDateCreated, .top-footer .menu li:before, div.itemRelated .itemRelCreated, div.catItemHeader span.catItemDateCreated, div.itemHeader span.itemDateCreated, div.itemComments ul.itemCommentsList li span.commentDate {
			color: <?php echo $this->params->get('templateColor'); ?>;
		}
		
		.feedback-section .feedback-box:hover:before {
			border-top-color: <?php echo $this->params->get('templateColor'); ?>;
		}
		
		.owl-controls .owl-prev i:hover, .owl-controls .owl-next i:hover, div.itemRelated .owl-controls .owl-prev i, div.itemRelated .owl-controls .owl-next i {
			background-color: <?php echo $this->params->get('templateColor'); ?>;
			border-color: <?php echo $this->params->get('templateColor'); ?>;
		}
	</style>
	<?php endif; ?>
	
	<?php if ($option == 'com_contact' && $this->params->get('map')) : ?>
	<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/scripts/js/jquery.gmap.min.js"></script>
	<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>

	<script>
	jQuery(document).ready(function(){
		// Map Markers
		var mapMarkers = [{     
			latitude: <?php echo $latitude ?>,
			longitude: <?php echo $longitude ?>,
			popup: true,
			icon: { 
				image: "<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/map_marker.png",
				iconsize: [44,44],
				iconanchor: [12,46],
				infowindowanchor: [12, 0] 
			} 
		}];
		
		// Map Color Scheme - more styles here http://snazzymaps.com/
		var mapStyles = [
			{
				"featureType": "water",
				"stylers": [
					{
						"visibility": "on"
					},
					{
						"color": "#acbcc9"
					}
				]
			},
			{
				"featureType": "landscape",
				"stylers": [
					{
						"color": "#f2e5d4"
					}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "geometry",
				"stylers": [
					{
						"color": "#c5c6c6"
					}
				]
			},
			{
				"featureType": "road.arterial",
				"elementType": "geometry",
				"stylers": [
					{
						"color": "#e4d7c6"
					}
				]
			},
			{
				"featureType": "road.local",
				"elementType": "geometry",
				"stylers": [
					{
						"color": "#fbfaf7"
					}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "geometry",
				"stylers": [
					{
						"color": "#c5dac6"
					}
				]
			},
			{
				"featureType": "administrative",
				"stylers": [
					{
						"visibility": "on"
					},
					{
						"lightness": 33
					}
				]
			},
			{
				"featureType": "road"
			},
			{
				"featureType": "poi.park",
				"elementType": "labels",
				"stylers": [
					{
						"visibility": "on"
					},
					{
						"lightness": 20
					}
				]
			},
			{},
			{
				"featureType": "road",
				"stylers": [
					{
						"lightness": 20
					}
				]
			}
		];
		// Map Initial Location
		var initLatitude = <?php echo $latitude ?>;
		var initLongitude = <?php echo $longitude ?>;

		// Map Extended Settings
		var map = jQuery("#map").gMap({
			controls: {
				panControl: true,
				zoomControl: true,
				mapTypeControl: true,
				scaleControl: true,
				streetViewControl: true,
				overviewMapControl: true
			},
			scrollwheel: false,
			markers: mapMarkers,
			latitude: initLatitude,
			longitude: initLongitude,
			zoom: 12,
			style: mapStyles
		});
	});
	</script>
	<?php endif; ?>
	
	<!--[if lt IE 9]>
		<script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script>
	<![endif]-->
</head>

<body class="site 
	<?php if ($menu->getActive() == $menu->getDefault($lang->getTag())) echo 'home'; ?> 
	<?php echo $menu->getParams( $active->id )->get( 'pageclass_sfx' ); ?>
	<?php echo $option
	. ' view-' . $view
	. ($layout ? ' layout-' . $layout : ' no-layout')
	. ($task ? ' task-' . $task : ' no-task')
	. ($itemid ? ' itemid-' . $itemid : '');
	echo ($this->direction == 'rtl' ? ' rtl' : '');
	?>">

	<div id="page-wrapper">
		<!-- Header -->
		<header id="header">
			<?php if ($this->countModules('top-info') || ($this->params->get('social') && ($this->params->get('twitterLink') != '' || $this->params->get('facebookLink') != '' || $this->params->get('pinterestLink') != '' || $this->params->get('instagramLink') != '' || $this->params->get('dribbbleLink') != ''))) : ?>
			<div id="panel">
				<div class="container">
					<div class="row">
						<div class="col-sm-9">
							<div class="info-left">
								<jdoc:include type="modules" name="top-info" style="none" />
							</div>
						</div>
						<div class="col-sm-3">
							<ul class="info-right reset-list">
								<?php if ($this->params->get('twitterIcon') && $this->params->get('twitterLink') != '') : ?>
								<li class="ico-twitter animation">
									<a href="<?php echo $this->params->get('twitterLink'); ?>" target="_blank"><span class="icon-twitter"></span></a>
								</li>
								<?php endif; ?>	
								<?php if ($this->params->get('facebookIcon') && $this->params->get('facebookLink') != '') : ?>
								<li class="ico-facebook animation">
									<a href="<?php echo $this->params->get('facebookLink'); ?>" target="_blank"><span class="icon-facebook"></span></a>
								</li>
								<?php endif; ?>
								<?php if ($this->params->get('pinterestIcon') && $this->params->get('pinterestLink') != '') : ?>
								<li class="ico-pinterest animation">
									<a href="<?php echo $this->params->get('pinterestLink'); ?>" target="_blank"><span class="icon-pinterest"></span></a>
								</li>
								<?php endif; ?>
								<?php if ($this->params->get('instagramIcon') && $this->params->get('instagramLink') != '') : ?>
								<li class="ico-instagram animation">
									<a href="<?php echo $this->params->get('instagramLink'); ?>" target="_blank"><span class="icon-instagram"></span></a>
								</li>
								<?php endif; ?>
								<?php if ($this->params->get('dribbbleIcon') && $this->params->get('dribbbleLink') != '') : ?>
								<li class="ico-dribbble animation">
									<a href="<?php echo $this->params->get('dribbbleLink'); ?>" target="_blank"><span class="icon-dribbble"></span></a>
								</li>
								<?php endif; ?>								
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="slide">
				<div class="container relative">
					<a class="btn-slide" href="#"><span class="icon-angle-up"></span></a>
				</div>
			</div>
			<?php endif; ?>
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="relative center clearfix">
							<div class="logo-container">
								<h1 id="logo">
									<a href="<?php echo $this->baseurl; ?>/">
										<?php echo $logo; ?>
									</a>
								</h1>
							</div>
							<?php if ($this->countModules('search')) : ?>
							<div class="searchPanel">
								<span class="icon-search3"></span>
								<jdoc:include type="modules" name="search" style="none" />
							</div>
							<?php endif; ?>
							<?php if ($this->countModules('language')) : ?>
							<div class="dropdown languagePanel">
								<span aria-expanded="false" data-toggle="dropdown" id="dropdownMenu1" type="button" class="globe icon-earth"></span>
								<jdoc:include type="modules" name="language" style="none" />
							</div>
							<?php endif; ?>
							<div class="zn-res-menuwrapper">
								<a href="#" class="zn-res-trigger"></a>
							</div>
							<div id="main-menu">
								<jdoc:include type="modules" name="main-menu" style="none" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</header>
		
		<?php if ($this->countModules('slideshow')) : ?>
		<!-- Slideshow -->
		<div id="slideshow">
			<jdoc:include type="modules" name="slideshow" style="none" />
			<?php if ($this->countModules('spotlight')) : ?>
			<div id="swappers">
				<div class="container">
					<div class="row">
						<jdoc:include type="modules" name="spotlight" style="jfxhtmlswappers" />
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		
		<?php if ($this->countModules('slideshow-fullscreen')) : ?>
		<!-- Slideshow -->
		<div id="slideshow-fullscreen">
			<jdoc:include type="modules" name="slideshow-fullscreen" style="none" />
		</div>
		<?php endif; ?>
		
		<?php if ($this->countModules('breadcrumb')) : ?>
		<!-- breadcrumb -->
		<div id="breadcrumb">
			<div class="container">
				<jdoc:include type="modules" name="breadcrumb" style="none" />
			</div>
		</div>
		<?php endif; ?>
		
		<?php if ($option == 'com_contact' && $this->params->get('map')) : ?>
		<div id="map" class="mbottom60"></div><!-- end map -->
		<?php endif; ?>
				
		<!-- Content -->
		<div id="content">				
			<div class="container">
				<div class="row">
					<?php if ($this->countModules('left')) : ?>						
						<div class="col-sm-3">
							<!-- Begin Sidebar -->
							<div class="sidebar">
								<jdoc:include type="modules" name="left" style="xhtml" />
							</div>
							<!-- End Sidebar -->
						</div>						
					<?php endif; ?>
					<div class="<?php echo $span; ?>">
						<!-- Begin Content -->
						<div class="main-content">
							<jdoc:include type="message" />
							<jdoc:include type="component" />
						</div>
						<!-- End Content -->
					</div>
					<?php if ($this->countModules('right')) : ?>
						<div class="col-sm-3">
							<!-- Begin Right Sidebar -->
							<div class="sidebar">
								<jdoc:include type="modules" name="right" style="xhtml" />
							</div>
							<!-- End Right Sidebar -->
						</div>
					<?php endif; ?>
				</div>
			</div>
			
			<?php if ($this->countModules('services-section')) : ?>
			<div class="services-section">
				<div class="container">
					<div class="row center">
						<jdoc:include type="modules" name="services-section" style="jfxhtml" />
					</div>
				</div>
			</div>
			<?php endif; ?>
			
			<?php if ($this->countModules('project-section')) : ?>
			<div class="project-section">
				<jdoc:include type="modules" name="project-section" style="xhtml" />
			</div>
			<?php endif; ?>
			
			<?php if ($this->countModules('produce-section')) : ?>
			<div class="produce-section">
				<div class="container">
					<div class="row">
						<jdoc:include type="modules" name="produce-section" style="jfxhtml" />
					</div>
				</div>
			</div>
			<?php endif; ?>
			
			<?php if ($this->countModules('featured-section')) : ?>
			<div class="featured-section">
				<div class="container">
					<div class="row">
						<jdoc:include type="modules" name="featured-section" style="jfxhtml" />
					</div>
				</div>
			</div>
			<?php endif; ?>
			
			<?php if ($this->countModules('feedback-section') || $this->countModules('feedback-header')) : ?>
			<div class="feedback-section">
				<div class="container">
					<?php if ($this->countModules('feedback-header')) : ?>
					<div class="row center feedbacks-header">
						<jdoc:include type="modules" name="feedback-header" style="jfxhtml" />
					</div>
					<?php endif; ?>
					
					<?php if ($this->countModules('feedback-section')) : ?>
					<div class="row">
						<jdoc:include type="modules" name="feedback-section" style="jfxhtml" />
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
			
			<?php if ($this->countModules('blog-section')) : ?>
			<div class="blog-section">
				<div class="container">
					<jdoc:include type="modules" name="blog-section" style="xhtml" />
				</div>
			</div>
			<?php endif; ?>
			
			<?php if ($this->countModules('team-section')) : ?>
			<div class="team-section">
				<div class="container">
					<div class="relative">
						<jdoc:include type="modules" name="team-section" style="xhtml" />
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
		
		<!-- Footer -->
		<footer class="footer" role="contentinfo">
			<?php if ($this->countModules('footer')) : ?>
			<div class="top-footer">
				<div class="container">
					<div class="row">
						<jdoc:include type="modules" name="footer" style="jfxhtml" />
					</div>
				</div>
			</div>
			<?php endif; ?>
					
			<div class="bottom-footer">
				<div class="container">
					<div class="row">
						<div class="col-sm-12 center">
							<div class="jf">
								<a href="http://www.joomfreak.com" target="_blank" class="jflink">joomfreak</a>
							</div>
							<?php if ($this->params->get('copyright')) : ?>
								<p class="copyright"><?php echo $this->params->get('copyright'); ?> Powered by <a href="http://www.kreatif.it/it/" target="_blank" title="Kreatif">Kreatif</a></p>
							<?php endif; ?>														
						</div>
					</div>
				</div>
			</div>
		</footer>
	</div>
	<div id="back-top">
		<a href="#top"><span class="icon-angle-up"></span></a>
	</div>
	<jdoc:include type="modules" name="debug" style="none" />
</body>
</html>
