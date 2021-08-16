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
$font 	   = $params->get('googleFont');

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Logo file or site title param
if ($params->get('logoOption') == 1 && $params->get('logoFile')) {
	$logo = '<img src="' . JUri::root() . $params->get('logoFile') . '" alt="' . $sitename . '" />';
} elseif ($params->get('logoOption') == 2 && $params->get('logoText')) {
	$logo = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($params->get('logoText')) . '</span>';
} else {
	$logo = '<span class="site-title" title="' . $sitename . '">' . $sitename . '</span>';
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo $this->title; ?> <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
	
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:,300,300italic,400,400italic,500,500italic,700,700italic,900,900italic" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/bootstrap.css" type="text/css" />
	<?php if($font == 'OpenSans') : ?>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic,700,700italic" type="text/css" />
	<?php endif; ?>
	<?php if($font == 'Lato') : ?>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,300italic,400,400italic,700,700italic,900,900italic" type="text/css" />
	<?php endif; ?>
	<?php if($font == 'PTSans') : ?>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic" type="text/css" />
	<?php endif; ?>
	<?php if($font == 'SourceSansPro') : ?>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400italic,600,600italic,700,700italic" type="text/css" />
	<?php endif; ?>
	<?php if($font == 'Nobile') : ?>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nobile:400,400italic,700,700italic" type="text/css" />
	<?php endif; ?>
	<?php if($font == 'Ubuntu') : ?>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Ubuntu:300,300italic,400,400italic,500,500italic,700,700italic" type="text/css" />
	<?php endif; ?>
	<?php if($font == 'IstokWeb') : ?>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Istok+Web:400,400italic,700,700italic" type="text/css" />
	<?php endif; ?>
	<?php if($font == 'Exo2') : ?>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Exo+2:400,400italic,500,500italic,600,600italic,700,700italic,800,800italic" type="text/css" />
	<?php endif; ?>

	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/icons-styles.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/k2.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/template.css" type="text/css" />
	
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
	<?php if ($params->get('backgroundColor') == 'light') : ?>
	<style type="text/css">
		body, #content {
			background-color: #f6f6f6;
		}
	</style>
	<?php endif; ?>
	<?php if ($params->get('backgroundColor') == 'dark') : ?>
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
	<?php if ($params->get('templateColor')) : ?>
	<style type="text/css">
		#panel, #header .slide span, .btn2, .projectLink a, .services-section [class*=" icon-"]:after, .services-section [class^="icon-"]:after, .portfolio-filter ul li.filter-item.active, .project-section .portfolio-caption, .team-section .team-caption, .footer-subscribe [type="submit"], #back-top, #k2Container .portfolio-caption, div.itemCommentsForm form input#submitCommentButton {
			background: <?php echo $params->get('templateColor'); ?>;
		}
		
		#header .slide, .services-section [class*=" icon-"], .services-section [class^="icon-"], .feedback-section .feedback-box:hover, blockquote {
			border-color: <?php echo $params->get('templateColor'); ?>;
		}
		
		.services-section [class*=" icon-"], .services-section [class^="icon-"], .featured-section span[class*=" icon-"], .featured-section span[class^="icon-"], .feedbacks-header h1, .blog-section .moduleItemDateCreated, .top-footer div.k2ItemsBlock ul li .moduleItemDateCreated, .top-footer .menu li:before, div.itemRelated .itemRelCreated, div.catItemHeader span.catItemDateCreated, div.itemHeader span.itemDateCreated, div.itemComments ul.itemCommentsList li span.commentDate {
			color: <?php echo $params->get('templateColor'); ?>;
		}
		
		.feedback-section .feedback-box:hover:before {
			border-top-color: <?php echo $params->get('templateColor'); ?>;
		}
		
		.owl-controls .owl-prev i:hover, .owl-controls .owl-next i:hover, div.itemRelated .owl-controls .owl-prev i, div.itemRelated .owl-controls .owl-next i {
			background-color: <?php echo $params->get('templateColor'); ?>;
			border-color: <?php echo $params->get('templateColor'); ?>;
		}
	</style>
	<?php endif; ?>
	
	<script src="<?php echo JUri::root(true); ?>/media/jui/js/jquery.min.js"></script>
	<script src="<?php echo JUri::root(true); ?>/media/jui/js/bootstrap.min.js"></script>
	<script src="<?php echo $this->baseurl . '/templates/' . $this->template . '/scripts/js/isotope.pkgd.min.js'; ?>"></script>
	<script src="<?php echo $this->baseurl . '/templates/' . $this->template . '/scripts/js/imagesloaded.min.js'; ?>"></script>
	<script src="<?php echo $this->baseurl . '/templates/' . $this->template . '/scripts/js/owl.carousel.min.js'; ?>"></script>
	<script src="<?php echo $this->baseurl . '/templates/' . $this->template . '/scripts/js/nivo-lightbox.min.js'; ?>"></script>
	<script src="<?php echo $this->baseurl . '/templates/' . $this->template . '/scripts/js/template.js'; ?>"></script>

	<!--[if lt IE 9]>
		<script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script>
	<![endif]-->
</head>

<body class="site <?php echo $option
	. ' view-' . $view
	. ($layout ? ' layout-' . $layout : ' no-layout')
	. ($task ? ' task-' . $task : ' no-task')
	. ($itemid ? ' itemid-' . $itemid : '');
?>">

	<div id="page-wrapper">
		<!-- Header -->
		<header id="header">
			<?php if (JModuleHelper::getModule('top-info') || ($params->get('social') && ($params->get('twitterLink') != '' || $params->get('facebookLink') != '' || $params->get('pinterestLink') != '' || $params->get('instagramLink') != '' || $params->get('dribbbleLink') != ''))) : ?>
			<div id="panel">
				<div class="container">
					<div class="row">
						<div class="col-sm-9">
							<div class="info-left">
								<?php echo $doc->getBuffer('modules', 'top-info', array('style' => 'none')); ?>
							</div>
						</div>
						<div class="col-sm-3">
							<ul class="info-right reset-list">
								<?php if ($params->get('twitterIcon') && $params->get('twitterLink') != '') : ?>
								<li class="ico-twitter animation">
									<a href="<?php echo $params->get('twitterLink'); ?>" target="_blank"><span class="icon-twitter"></span></a>
								</li>
								<?php endif; ?>	
								<?php if ($params->get('facebookIcon') && $params->get('facebookLink') != '') : ?>
								<li class="ico-facebook animation">
									<a href="<?php echo $params->get('facebookLink'); ?>" target="_blank"><span class="icon-facebook"></span></a>
								</li>
								<?php endif; ?>
								<?php if ($params->get('pinterestIcon') && $params->get('pinterestLink') != '') : ?>
								<li class="ico-pinterest animation">
									<a href="<?php echo $params->get('pinterestLink'); ?>" target="_blank"><span class="icon-pinterest"></span></a>
								</li>
								<?php endif; ?>
								<?php if ($params->get('instagramIcon') && $params->get('instagramLink') != '') : ?>
								<li class="ico-instagram animation">
									<a href="<?php echo $params->get('instagramLink'); ?>" target="_blank"><span class="icon-instagram"></span></a>
								</li>
								<?php endif; ?>
								<?php if ($params->get('dribbbleIcon') && $params->get('dribbbleLink') != '') : ?>
								<li class="ico-dribbble animation">
									<a href="<?php echo $params->get('dribbbleLink'); ?>" target="_blank"><span class="icon-dribbble"></span></a>
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
							
							<div class="searchPanel">
								<span class="icon-search3"></span>
								<?php echo $doc->getBuffer('modules', 'search', array('style' => 'none')); ?>
							</div>
							
							<div class="dropdown languagePanel">
								<span aria-expanded="false" data-toggle="dropdown" id="dropdownMenu1" type="button" class="globe icon-earth"></span>
								<?php echo $doc->getBuffer('modules', 'language', array('style' => 'none')); ?>
							</div>
							
							<div class="zn-res-menuwrapper">
								<a href="#" class="zn-res-trigger"></a>
							</div>
							<div id="main-menu">
								<?php echo $doc->getBuffer('modules', 'main-menu', array('style' => 'none')); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</header>
		
		<!-- Content -->
		<div id="content">				
			<div class="container">
				<div class="row">					
					<div class="col-sm-12">
						<h2 class="mbottom50 center"><?php echo JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'); ?></h2>
						<img class="mauto mbottom30" alt="" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/404.png">

						<p class="center"><?php echo JText::_('JERROR_LAYOUT_SEARCH_PAGE'); ?></p>
						<div class="mbottom50 center"><?php echo $doc->getBuffer('module', 'search'); ?></div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Footer -->
		<footer class="footer" role="contentinfo">
			<div class="top-footer">
				<div class="container">
					<div class="row">
						<?php echo $doc->getBuffer('modules', 'footer', array('style' => 'jfxhtml')); ?>
					</div>
				</div>
			</div>
					
			<div class="bottom-footer">
				<div class="container">
					<div class="row">
						<div class="col-sm-12 center">
							<div class="jf">
								<a href="http://www.joomfreak.com" target="_blank" class="jflink">joomfreak</a>
							</div>
							<?php if ($params->get('copyright')) : ?>
								<p class="copyright"><?php echo $params->get('copyright'); ?> Powered by <a href="http://www.kreatif.it/" target="_blank">Kreatif</a></p>
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
	
	<?php echo $doc->getBuffer('modules', 'debug', array('style' => 'none')); ?>
</body>
</html>
