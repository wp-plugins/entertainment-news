<?php


include_once('EntertainmentNews_LifeCycle.php');

class EntertainmentNews_Plugin extends EntertainmentNews_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'summerylimit' => array(__('Summery: Number of articles', 'gw'), '2', '3', '4', '5', '6', '7', '8', '9', '10'),
			'summeryicons' => array(__('Summery: Show icons', 'gw'), 'true', 'false'),
			'summerybody' => array(__('Summery: Show summery text', 'gw'), 'true', 'false'),
			'summerydate' => array(__('Summery: Show date', 'gw'), 'true', 'false'),
			'summerylink' => array(__('Summery: Link relationship', 'gw'), 'auto', 'normal', 'nofollow'),
			
			'articlelimit' => array(__('Full Article: Number of articles', 'gw'), '2', '3', '4', '5', '6', '7', '8', '9', '10'),
			'articleicons' => array(__('Full Article: Show icons', 'gw'), 'false', 'true'),
			'articlebody' => array(__('Full Article: Show summery text', 'gw'), 'true', 'false'),
			'articledate' => array(__('Full Article: Show date', 'gw'), 'true', 'false'),
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'Entertainment News';
    }

    protected function getMainPluginFileName() {
        return 'entertainment-news.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));
		
		include_once('EntertainmentNews_widget.php');
		
		add_action( 'widgets_init', function(){
			register_widget( 'gw_news_widget' );
		});

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
        //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        }


        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37


        // Adding scripts & styles to all pages
        // Examples:
        //        wp_enqueue_script('jquery');
        wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));


        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39
		add_shortcode('entertainment-news-summery', array($this, 'shortlist'));
		add_shortcode('entertainment-news-articles', array($this, 'longlist'));


        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

    }
	
	private function create_link($url, $title){
		// The auto link option is to decide if the site linking to gloom wire is relevant
		// If so, it may benefit search engines to crawl our website
		$value = wp_cache_get('value', 'entertainment_news');
		if($value === false){
			$value = 0;
			$site = get_site_url();
			$name = get_bloginfo( 'name'  );
			$desc = get_bloginfo( 'description'  );
			$posts = wp_count_posts();
			$users = count_users();
			if(strlen($site) < 27){ $value += 1; }
			if(strpos($site, 'https') !== false){ $value += 2; }
			if(strpos($site, '.co') !== false){ $value += 1; }
			if(strpos($site, '.com') !== false){ $value += 1; }
			if(strpos($site, '.net') !== false){ $value += 1; }
			if(strpos($site, '.org') !== false){ $value += 1; }
			if(strpos($site, 'news') !== false){ $value += 2; }
			if(strpos($name, 'news') !== false){ $value += 2; }
			if(strpos($desc, 'news') !== false){ $value += 1; }
			if(strpos($desc, 'strange') !== false){ $value += 2; }
			if($posts > 100){ $value += 2; }
			if($users > 10){ $value += 2; }
			if(strpos($site, 'x'.'x'.'x') !== false){ $value -= 3; }
			if(strpos($site, 'po'.'rn') !== false){ $value -= 3; }
			if(strpos($site, 's'.'ex') !== false){ $value -= 3; }
			if(strpos($site, 'tube') !== false){ $value -= 3; }
			if(strpos($desc, 'x'.'x'.'x') !== false){ $value -= 3; }
			if(strpos($desc, 'po'.'rn') !== false){ $value -= 3; }
			if(strpos($desc, 's'.'ex') !== false){ $value -= 3; }
			if(strpos($desc, 'tube') !== false){ $value -= 3; }
			wp_cache_set( 'value', $value, 'entertainment_news', 1296000 );
		}else{
			$value = intval($value);
		}
		if($this->getOption('summerylink') == 'nofollow' || $value <= 0){
			return '<a href="'.$url.'" rel="nofollow">'.$title.'</a>';
		}
		if($value >= 4){
			return '<a href="'.$url.'">'.$title.'</a>';
		}
		if($this->getOption('summerylink') == 'normal'){
			return '<a href="'.$url.'">'.$title.'</a>';
		}
		return '<a href="'.$url.'" rel="nofollow">'.$title.'</a>';
	}
	
	public function shortlist(){
		$json = wp_cache_get('shortfeed', 'entertainment_news');
		if($json === false){
			$json = file_get_contents('http://gloomwire.com/feeds/json/articles/');
			wp_cache_set( 'shortfeed', $json, 'entertainment_news', 21600 );
		}
		$data = json_decode($json);
		$articles = $data->articles;
		$limit = $this->getOption('summerylimit');
		if($limit > $articles){ $limit = count($articles); }
		$output = '';
		for($i = 0; $i < intval($limit); $i++){
			$output .= '<div class="ensummerywrap">';
			if($this->getOption('summerybody') == 'true'){
				$output .= '<div class="summerytitle"><h2>'.$articles[$i]->title.'</h2></div>';
			}else{
				$output .= '<div class="summerytitle"><h2>'.$this->create_link($articles[$i]->link, $articles[$i]->title).'</h2></div>';
			}
			if($this->getOption('summeryicons') == 'true'){
				$output .= '<div class="summeryicons"><img src="http://cdn.gloomwire.com/'.$articles[$i]->thumbnail.'" style="width:100%;"></div>';
			}
			if($this->getOption('summerybody') == 'true'){
				$output .= '<div class="summerybidy">'.html_entity_decode($articles[$i]->body).'</div>';
			}
			if($this->getOption('summerydate') == 'true'){
				$output .= '<div class="summerydate">'.date('M j Y', $articles[$i]->time).'</div>';
			}
			if($this->getOption('summerybody') == 'true'){
				$output .= '<div class="summerybody">'.$this->create_link($articles[$i]->link, 'Read More');
			}
			
			$output .= '</div>';
		}
		return $output;
	}
	
	public function longlist(){
		$json = wp_cache_get('longfeed', 'entertainment_news');
		if($json === false){
			$json = file_get_contents('http://gloomwire.com/feeds/json/full-articles/');
			wp_cache_set( 'longfeed', $json, 'entertainment_news', 21600 );
		}
		$data = json_decode($json);
		$articles = $data->articles;
		$limit = $this->getOption('articlelimit');
		if($limit > $articles){ $limit = count($articles); }
		$output = '';
		for($i = 0; $i < intval($limit); $i++){
			$output .= '<div class="enarticleswrap">';
			if($this->getOption('articlebody') == 'true'){
				$output .= '<div class="articlestitle"><h2>'.$articles[$i]->title.'</h2></div>';
			}else{
				$output .= '<div><h2 class="articlestitle">'.$this->create_link($articles[$i]->link, $articles[$i]->title).'</h2></div>';
			}
			if($this->getOption('articleicons') == 'true'){
				$output .= '<div class="articlesicon"><img src="http://cdn.gloomwire.com/'.$articles[$i]->thumbnail.'" style="width:100%;"></div>';
			}
			if($this->getOption('articlebody') == 'true'){
				$output .= '<div class="articlesbody">'.html_entity_decode($articles[$i]->body).'</div>';
			}
			if($this->getOption('articledate') == 'true'){
				$output .= '<div class="articlesdate">'.date('M j Y', $articles[$i]->time).'</div>';
			}
			$output .= '</div>';
		}
		return $output;
	}


}
