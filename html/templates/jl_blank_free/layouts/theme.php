<?php
/**
* @package   yoo_master2
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get theme configuration
include($this['path']->path('layouts:theme.config.php'));

?>
<!DOCTYPE HTML>
<html lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>"  data-config='<?php echo $this['config']->get('body_config','{}'); ?>'>

<head>
<?php echo $this['template']->render('head'); ?>
</head>

<body class="<?php echo $this['config']->get('body_classes'); ?>">
     <div class="uk-container uk-container-center">

    		<?php if ($this['widgets']->count('toolbar-l + toolbar-r')) : ?>
    		<div class="tm-toolbar uk-clearfix uk-hidden-small">

    			<?php if ($this['widgets']->count('toolbar-l')) : ?>
    			<div class="uk-float-left"><?php echo $this['widgets']->render('toolbar-l'); ?></div>
    			<?php endif; ?>

    			<?php if ($this['widgets']->count('toolbar-r')) : ?>
    			<div class="uk-float-right"><?php echo $this['widgets']->render('toolbar-r'); ?></div>
    			<?php endif; ?>

    		</div>
    		<?php endif; ?>

    		<?php if ($this['widgets']->count('logo + menu + search')) : ?>
    		<div class="tm-headerbar uk-clearfix">
    			<?php if ($this['widgets']->count('logo')) : ?>
    			<a class="tm-logo uk-hidden-small" href="<?php echo $this['config']->get('site_url'); ?>"><?php echo $this['widgets']->render('logo'); ?></a>
    			<?php endif; ?>
    			<?php if ($this['widgets']->count('logo-small')) : ?>
    			<div class="uk-navbar-content uk-visible-small"><a class="tm-logo-small" href="<?php echo $this['config']->get('site_url'); ?>"><?php echo $this['widgets']->render('logo-small'); ?></a></div>
    			<?php endif; ?>
    		    <nav class="tm-navbar uk-navbar uk-float-right">

    			<?php if ($this['widgets']->count('menu')) : ?>
    			<?php echo $this['widgets']->render('menu'); ?>
    			<?php endif; ?>

    			<?php if ($this['widgets']->count('offcanvas')) : ?>
    			<a href="#offcanvas" class="uk-navbar-toggle uk-hidden-large" data-uk-offcanvas></a>
    			<?php endif; ?>
    		</nav>
    		</div>
    		<?php endif; ?>

        <?php if ($this['widgets']->count('slideshow')) : ?>
        <section id="tm-slideshow" class="<?php echo $grid_classes['slideshow']; echo $display_classes['slideshow']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
            <?php echo $this['widgets']->render('slideshow', array('layout'=>$this['config']->get('grid.slideshow.layout'))); ?>
        </section>
        <?php endif; ?>

        <?php if ($this['widgets']->count('top-a')) : ?>
        <section id="tm-top-a" class="<?php echo $grid_classes['top-a']; echo $display_classes['top-a']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
            <?php echo $this['widgets']->render('top-a', array('layout'=>$this['config']->get('grid.top-a.layout'))); ?>
        </section>
        <?php endif; ?>

        <?php if ($this['widgets']->count('top-b')) : ?>
        <section id="tm-top-b" class="<?php echo $grid_classes['top-b']; echo $display_classes['top-b']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
            <?php echo $this['widgets']->render('top-b', array('layout'=>$this['config']->get('grid.top-b.layout'))); ?>
        </section>
        <?php endif; ?>

        <?php if ($this['widgets']->count('top-c')) : ?>
        <section id="tm-top-c" class="<?php echo $grid_classes['top-c']; echo $display_classes['top-c']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
            <?php echo $this['widgets']->render('top-c', array('layout'=>$this['config']->get('grid.top-c.layout'))); ?>
        </section>
        <?php endif; ?>

        <?php if ($this['widgets']->count('top-d')) : ?>
        <section id="tm-top-d" class="<?php echo $grid_classes['top-d']; echo $display_classes['top-d']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
            <?php echo $this['widgets']->render('top-d', array('layout'=>$this['config']->get('grid.top-d.layout'))); ?>
        </section>
        <?php endif; ?>

        <?php if ($this['widgets']->count('main-top + main-bottom + sidebar-a + sidebar-b') || $this['config']->get('system_output', true)) : ?>
        <div id="tm-middle" class="tm-middle uk-grid" data-uk-grid-match data-uk-grid-margin>

            <?php if ($this['widgets']->count('main-top + main-bottom') || $this['config']->get('system_output', true)) : ?>
            <div class="<?php echo $columns['main']['class'] ?>">

                <?php if ($this['widgets']->count('main-top')) : ?>
                <section id="tm-main-top" class="<?php echo $grid_classes['main-top']; echo $display_classes['main-top']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin><?php echo $this['widgets']->render('main-top', array('layout'=>$this['config']->get('grid.main-top.layout'))); ?></section>
                <?php endif; ?>

                <?php if ($this['config']->get('system_output', true)) : ?>
                <main id="tm-content" class="tm-content">

                    <?php if ($this['widgets']->count('breadcrumbs')) : ?>
                    <?php echo $this['widgets']->render('breadcrumbs'); ?>
                    <?php endif; ?>

                    <?php echo $this['template']->render('content'); ?>

                </main>
                <?php endif; ?>

                <?php if ($this['widgets']->count('main-bottom')) : ?>
                <section id="tm-main-bottom" class="<?php echo $grid_classes['main-bottom']; echo $display_classes['main-bottom']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin><?php echo $this['widgets']->render('main-bottom', array('layout'=>$this['config']->get('grid.main-bottom.layout'))); ?></section>
                <?php endif; ?>

            </div>
            <?php endif; ?>

            <?php foreach($columns as $name => &$column) : ?>
            <?php if ($name != 'main' && $this['widgets']->count($name)) : ?>
            <aside class="<?php echo $column['class'] ?>"><?php echo $this['widgets']->render($name) ?></aside>
            <?php endif ?>
            <?php endforeach ?>

        </div>
        <?php endif; ?>

        <?php if ($this['widgets']->count('bottom-a')) : ?>
        <section id="tm-bottom-a" class="<?php echo $grid_classes['bottom-a']; echo $display_classes['bottom-a']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
            <?php echo $this['widgets']->render('bottom-a', array('layout'=>$this['config']->get('grid.bottom-a.layout'))); ?>
        </section>
        <?php endif; ?>

        <?php if ($this['widgets']->count('bottom-b')) : ?>
        <section id="tm-bottom-b" class="<?php echo $grid_classes['bottom-b']; echo $display_classes['bottom-b']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
            <?php echo $this['widgets']->render('bottom-b', array('layout'=>$this['config']->get('grid.bottom-b.layout'))); ?>
        </section>
        <?php endif; ?>

        <?php if ($this['widgets']->count('bottom-c')) : ?>
        <section id="tm-bottom-c" class="<?php echo $grid_classes['bottom-c']; echo $display_classes['bottom-c']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
            <?php echo $this['widgets']->render('bottom-c', array('layout'=>$this['config']->get('grid.bottom-c.layout'))); ?>
        </section>
        <?php endif; ?>

        <?php if ($this['widgets']->count('bottom-d')) : ?>
        <section id="tm-bottom-d" class="<?php echo $grid_classes['bottom-d']; echo $display_classes['bottom-d']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
            <?php echo $this['widgets']->render('bottom-d', array('layout'=>$this['config']->get('grid.bottom-d.layout'))); ?>
        </section>
        <?php endif; ?>
    </div>
	<div id="tm-footer">
	     <div class="uk-container uk-container-center">
        <?php if ($this['widgets']->count('footer + debug') || $this['config']->get('warp_branding', true) || $this['config']->get('totop_scroller', true)) : ?>
        <footer id="tm-footer" class="tm-footer<?php echo $this['config']->get('footer_margin') ? ' tm-footer-margin-top' : '' ; ?>">
            <div class="uk-panel uk-margin">
                <?php if ($this['config']->get('totop_scroller', true)) : ?>
                <a class="tm-totop-scroller uk-link-reset" data-uk-smooth-scroll href="#"></a>
                <?php endif; ?>

                <?php
                    echo $this['widgets']->render('footer');
                    $this->output('warp_branding');
                    echo $this['widgets']->render('debug');
                ?>

            </div>
        </footer>
        <?php endif; ?>
    <?php echo $this->render('footer'); ?>
	</div>
	</div>
    <?php if ($this['widgets']->count('offcanvas')) : ?>
    <div id="offcanvas" class="uk-offcanvas">
        <div class="uk-offcanvas-bar uk-offcanvas-bar-flip"><?php echo $this['widgets']->render('offcanvas'); ?></div>
    </div>
    <?php endif; ?>

</body>
</html>
