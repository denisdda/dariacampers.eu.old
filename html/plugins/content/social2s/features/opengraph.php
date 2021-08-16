<?php
	/**************************************/
	/**************** OPENGRAPH ************/
	/**************************************/

class s2s_og {

	public static $s2s_obj;

	public static $s2s_og;

	public static $s2s_article;

	public function __construct()
    {
    	self::$s2s_obj = PlgContentSocial2s::$s2s_obj;
    	self::$s2s_og = (object) array();
    }

    static function init($s2s_obj, $row){

    	self::$s2s_article = $row;
    	$context = self::$s2s_obj->context;
    	$params = self::$s2s_obj->params;

    	//prepare data
    	self::$s2s_og->title = '';
    	self::$s2s_og->description = '';
    	self::$s2s_og->metadescription = '';

    	//check
    	//self::s2sOg_check();

    	if(!self::s2sOg_check()) return;

    	//prepare
    	self::ogPrepareDefault();

    	self::ogPrepareArticle();

    	self::ogPrepareJevents();

    	self::ogPrepareUniversal();


    	//appling
    	self::ogPrepareDesc();

    	//TODO
    	//self::killMeta();
    	
    	self::ogWriteMeta();

    	self::tcardWriteMeta();




    }

    public static function s2sOg_check(){
    	$params = self::$s2s_obj->params;
    	$context = self::$s2s_obj->context;
    	$method = self::$s2s_obj->method;

 		PlgContentSocial2s::updateS2sOgDebug('OG Check method', $method,'danger', 'check');

 		//universal
 		if($method == 'ons2sUniversal'){
 			return true;
 		}



    	//check module from in_ex.php
    	$ismodule = self::$s2s_obj->ismodule;

    	if($ismodule){
    		PlgContentSocial2s::updateS2sOgDebug('OG Check context', 'NOT passed: IS A MODULE','danger', 'check');
    		return false;
    	}

    	//CONTEXT check
		if(		$context == 'com_k2.item' 
			||  $context == 'com_content.article'
		//	||  $context == 'com_content.featured'
			||  $context == 'com_virtuemart.productdetails' 
			||  $context == 'com_jevents' 
			||  $context == 'com_jevents.' 

		){
			PlgContentSocial2s::updateS2sOgDebug('Check context', 'Passed: '.$context,'success', 'check');
		}else{
			//OTHERS default data
			if($params->get('opengraph_default_data','0')){
				PlgContentSocial2s::updateS2sOgDebug('OG Check context', 'Passed: Default data','success', 'check');

			}else{
				PlgContentSocial2s::updateS2sOgDebug('OG Check context', 'NOT passed: '.$context,'danger', 'check');
				return false;
			}
		}

    	$s2s_og = $params->get('opengraph', 1);
    	$s2s_og_k2 = $params->get('s2s_og_k2', 0);
    	$twitter_cards = $params->get('twitter_cards', 0);
		

		//K2 check
		if($context == 'com_k2.item' && $s2s_og_k2==1){
			return false;
		}

		//PARAM check ON
		if(!$s2s_og && !$twitter_cards){
			PlgContentSocial2s::updateS2sOgDebug('Check ON', 'OpenGraph deactivated','success', 'check');
			return false;
		}

		return true;

    }

    /**
     * [ogPrepareDefault description]
     * @return [type] [description]
     */
	public static function ogPrepareDefault(){

		$context = self::$s2s_obj->context;

		//CONTEXT check
		if(		$context == 'com_k2.itemlist' 
			||  $context == 'com_content.category'
			||  $context == 'com_content.featured'
			||  $context == 'com_virtuemart.category'

		){

			$params = self::$s2s_obj->params;

			if(!$params->get('opengraph_default_data','0')) return;

			$app = JFactory::getApplication();

			//TITLE
			if($params->get('opengraph_default_data','0') == '1'){
				$menu = $app->getMenu();
				self::$s2s_og->title = $menu->getActive()->title;
				PlgContentSocial2s::updateS2sOgDebug('Og data default', 'Default title menu: '.self::$s2s_og->title ,'danger', 'share-alt');
			}else{
				self::$s2s_og->title = $app->getCfg('sitename');
				PlgContentSocial2s::updateS2sOgDebug('Og data default', 'Default title sitename: '.self::$s2s_og->title ,'danger', 'share-alt');
			}
		}else{
			return;
		}




	}

	//ARTICLE
	//K2
    /**
     * [ogPrepareArticle com_content.article]
     * @return [type] [description]
     */
	public static function ogPrepareArticle(){



		//ARTICLE + K2 item
		//
		$s2s_obj = self::$s2s_obj;
		
		$valida = false;
		if($s2s_obj->context == 'com_content.article' || $s2s_obj->context == 'com_k2.item' || $s2s_obj->context == 'com_virtuemart.productdetails') $valida = true;

		if(!$valida) return;


		//modify title (if were necessary)
		self::$s2s_og->title = $s2s_obj->title;

		//modify description
		self::$s2s_og->description = $s2s_obj->description.'...';

		//images
		self::$s2s_og->images = self::$s2s_obj->images;

		//url
		self::$s2s_og->absolute_url = self::$s2s_obj->absolute_url;
		self::$s2s_og->url = self::$s2s_obj->url;

		PlgContentSocial2s::updateS2sOgDebug('Og Article-item', 'Title: '.self::$s2s_og->title ,'success', 'share-alt');
		PlgContentSocial2s::updateS2sOgDebug('Og Article-item', 'Description: '.self::$s2s_og->description ,'success', 'share-alt');


		$implosion = implode("<br>",self::$s2s_og->images);
		PlgContentSocial2s::updateS2sOgDebug('Og Article-item', 'Images: '.$implosion ,'success', 'share-alt');

	}



	/**
	 * [ogPrepareJevents prepare Jevent]
	 * @return [type] [meta]
	 */
	public static function ogPrepareJevents(){

		$s2s_obj = self::$s2s_obj;
		$context = self::$s2s_obj->context;

		//CONTEXT check
		if(	$context == 'com_jevents' ||  $context == 'com_jevents.'){

			//modify title (if were necessary)
			self::$s2s_og->title = $s2s_obj->title;
			self::$s2s_og->description = $s2s_obj->description.'...';
			self::$s2s_og->images = self::$s2s_obj->images;
			self::$s2s_og->absolute_url = self::$s2s_obj->absolute_url;
			self::$s2s_og->url = self::$s2s_obj->url;

			PlgContentSocial2s::updateS2sOgDebug('Og jevents-tit', 'Title: '.self::$s2s_og->title ,'success', 'share-alt');
			PlgContentSocial2s::updateS2sOgDebug('Og jevents-desc', 'Description: '.self::$s2s_og->description ,'success', 'share-alt');

			if(self::$s2s_og->images){
				$implosion = implode("<br>",self::$s2s_og->images);
				PlgContentSocial2s::updateS2sOgDebug('Og jevents-img', 'Images: '.$implosion ,'success', 'share-alt');
			}

		}else{
			return;
		}

	}



	/**
	 * [ogPrepareUniversal prepare UNIVERSAL]
	 * @return [type] [meta]
	 */
	public static function ogPrepareUniversal(){

		$s2s_obj = self::$s2s_obj;

		//check universal
		if(self::$s2s_obj->method != 'ons2sUniversal') return;
		
		//PlgContentSocial2s::updateS2sOgDebug('ons2sUniversal', 'Images: '.$implosion ,'success', 'share-alt');

		//var_dump($s2s_obj);

		

		//modify title (if were necessary)
		if(self::$s2s_og->title == '') self::$s2s_og->title = $s2s_obj->title;
		//modify description
		if(self::$s2s_og->description == '') self::$s2s_og->description = $s2s_obj->description.'...';
		//url
		if(!isset(self::$s2s_og->absolute_url)){
			self::$s2s_og->absolute_url = $s2s_obj->absolute_url;
		}else{
			if(self::$s2s_og->absolute_url == '') self::$s2s_og->absolute_url = $s2s_obj->absolute_url;
		}
		//modify images
		//if(self::$s2s_og->images == '') self::$s2s_og->images = self::$s2s_obj->images;



/*
		//modify title (if were necessary)
		self::$s2s_og->title = $s2s_obj->title;

		//modify description
		self::$s2s_og->description = $s2s_obj->description.'...';

		//images
		self::$s2s_og->images = self::$s2s_obj->images;

		//url
		self::$s2s_og->absolute_url = self::$s2s_obj->absolute_url;
		self::$s2s_og->url = self::$s2s_obj->url;

*/




	}




	/**
	 * [ogPrepareMeta prepare META]
	 * @return [type] [meta]
	 */
	public static function ogPrepareDesc(){
		$params = self::$s2s_obj->params;

		if(!$params->get('opengraph_metadescription','0')) return;

		//always
		if($params->get('opengraph_metadescription','0') == '1'){
			self::$s2s_og->description = self::$s2s_obj->metadescription;
			PlgContentSocial2s::updateS2sOgDebug('OG Use meta description', ' Always: '.self::$s2s_obj->metadescription ,'danger', 'share-alt');
		}else{

			//last chance
			if(self::$s2s_og->description == ''){
				self::$s2s_og->description = self::$s2s_og->metadescription;
				PlgContentSocial2s::updateS2sOgDebug('OG Use meta description', 'Last chance: '.self::$s2s_og->description ,'danger', 'share-alt');
			}
		}
	}


	/**
	 * [og_images_size returns size ]
	 * @return [type] [meta]
	 */
	private static function og_images_meta($img,$type='og'){
	
		//TO DO external function
		//detect external url
		if (substr($img, 0, 7) == "http://"){
			$final_img_to_size = $img;
			$final_img = $img;
		}elseif (substr($img, 0, 8) == "https://"){
			$final_img_to_size = $img;
			$final_img = $img;
		}else{
			$final_img_to_size = JPATH_SITE.'/'.$img;
			$final_img =  JURI::base().$img;
		}


		//check 
		$encoded_img = urlencode($final_img);
		$og_img_size_tag = '';

		if($type=='og'){
			if($img != ""){ 
					//image
					$og_img_size_tag .= '<meta property="og:image" content="'.$final_img.'">';

					//size
					$image_w = getimagesize($final_img_to_size);
					$og_img_size_tag .= '<meta property="og:image:width" content="'.$image_w[0].'" />';
					$og_img_size_tag .= '<meta property="og:image:height" content="'.$image_w[1].'" />';
			}

			PlgContentSocial2s::updateS2sOgDebug('Og Final image','<img src="'.$final_img.'" width="80px"/>  '.$final_img.'<br/> width: '.$image_w[0].' <br/> height: '.$image_w[1],'success', 'share-alt');
		}

		if($type=='tcard'){	

			if($img != ""){ 
					//image
					$og_img_size_tag .= '<meta property="twitter:image" content="'.$final_img.'">';
			}
			PlgContentSocial2s::updateS2sOgDebug('Twitter card Final image','<img src="'.$final_img.'" width="80px"/>  '.$final_img,'success', 'share-alt');
		}


		return $og_img_size_tag;
	}

	public static function killMeta(){

		/*TODO*/

	}


	/**
	 * [ogWriteMeta create META]
	 * @return [type] [meta]
	 */
	public static function ogWriteMeta(){
		$params = self::$s2s_obj->params;
		$s2s_obj = self::$s2s_obj;
		$s2s_og = self::$s2s_og;


		$s2s_og_on = $params->get('opengraph', 1);
		if(!$s2s_og_on){
			PlgContentSocial2s::updateS2sOgDebug('Check OG on','Deactivated: og meta won´t be written','warning', 'share-alt');
			return;
		}

		//title
		$opengraph    = '<meta property="og:title" content="'.htmlspecialchars($s2s_og->title).'">';

		//description
		$opengraph .= '<meta property="og:description" content="'.htmlspecialchars($s2s_og->description).'">';

		//images
		if(self::$s2s_obj->images){
			foreach (self::$s2s_obj->images as $key => $image) {
				$opengraph .= self::og_images_meta($image);
			}
		}


		//sitename
		$config = JFactory::getConfig();
		$opengraph .= '<meta property="og:site_name" content="'.$config->get('sitename').'" />';

		//url
        $opengraph .= '<meta property="og:url"  content="'. $s2s_og->absolute_url.'" />';

        //type
        if($s2s_obj->context == 'com_virtuemart.productdetails'){
        	//VIRTUEMART
        	$opengraph .= '<meta property="og:type"  content="product" />';	
        }else{
        	//WEBSITE
        	$opengraph .= '<meta property="og:type"  content="website" />';
        }
		

		PlgContentSocial2s::updateS2sOgDebug('Og metas',htmlentities($opengraph) ,'success', 'share-alt');

		//app_id
		$opengraph .= '<meta property="fb:app_id" content="514279921989553" />';

		$document = JFactory::getDocument();
		$document->addCustomTag($opengraph);

	}


	/**
	 * [tcardWriteMeta create META]
	 * @return [type] [meta]
	 */
	public static function tcardWriteMeta(){
		$params = self::$s2s_obj->params;
		$s2s_og = self::$s2s_og;

		//CHECK ON
		$t_cards_on = $params->get('twitter_cards', 0);
		if(!$t_cards_on){
			PlgContentSocial2s::updateS2sOgDebug('Check Twitter card on','Deactivated: og meta won´t be written','warning', 'share-alt');
			return;
		}

		//USER CHECK
		$twitter_user = $params->get('twitter_user', '');
		if($twitter_user == ""){
			PlgContentSocial2s::updateS2sOgDebug('Check Twitter user','Empty: og meta won´t be written','warning', 'share-alt');
			return;
		}
		//quito la @
		if (strpos($twitter_user,'@') !== false) {
		    $twitter_user = ltrim($twitter_user, '@');
		}

		//WRITE TWITTER CARD
		$tw_cards='';
		if($params->get('twitter_cards_summary', 0)){
			$tw_cards    = '<meta property="twitter:card" content="summary_large_image"/>';
		}else{
			$tw_cards    = '<meta property="twitter:card" content="summary"/>';
		}

		//user
		$tw_cards    .= '<meta property="twitter:site" content="@'.$twitter_user.'"/>';

		//title
		$tw_cards    .= '<meta property="twitter:title" content="'.htmlspecialchars($s2s_og->title).'">';

		//description
		$tw_cards .= '<meta property="twitter:description" content="'.htmlspecialchars($s2s_og->description).'">';


		//images just first
		if(isset(self::$s2s_obj->images[0])){
			if(self::$s2s_obj->images[0] != ''){
				$tw_cards .= self::og_images_meta(self::$s2s_obj->images[0], 'tcard');
			}
		}

		PlgContentSocial2s::updateS2sOgDebug('Twitter card metas',htmlentities($tw_cards) ,'success', 'share-alt');
		
		$document = JFactory::getDocument();
		$document->addCustomTag($tw_cards);
	}



	//RESTO DE FUNCIONES








}



?>