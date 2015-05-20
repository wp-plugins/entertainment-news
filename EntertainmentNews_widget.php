<?php

class gw_news_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'entertainmentnewswidget', // Base ID
			__( 'Entertainment News', 'text_domain' ), // Name
			array( 'description' => __( 'Fun and strange entertainment news from Gloom Wire.', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		$json = wp_cache_get('shortfeed', 'entertainment_news');
		if($json === false){
			$json = file_get_contents('http://gloomwire.com/feeds/json/articles/');
			wp_cache_set( 'shortfeed', $json, 'entertainment_news', 21600 );
		}
		$data = json_decode($json);
		$articles = $data->articles;
		$limit = $instance['limit'];
		if($limit > $articles){ $limit = count($articles); }
		$output = '';
		for($i = 0; $i < intval($limit); $i++){
			$output .= '<div class="enwidgetwrap">';
			$output .= '<div class="enwidgettitle"><h2>'.$this->create_link($articles[$i]->link, $articles[$i]->title, $instance).'</h2></div>';
			if($instance['icons'] == 'true'){
				$output .= '<div class="enwidgeticons"><img src="http://cdn.gloomwire.com/'.$articles[$i]->thumbnail.'" style="width:100%;"></div>';
			}
			if($instance['summery'] == 'true'){
				$output .= '<div class="enwidgetbody">'.html_entity_decode($articles[$i]->body).'</div>';
			}
			if($instance['date'] == 'true'){
				$output .= '<div class="enwidgetdate">'.date('M j Y', $articles[$i]->time).'</div>';
			}
			
			$output .= '</div>';
		}
		echo $output;
		echo $args['after_widget'];
	}
	
	private function create_link($url, $title, $instance){
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
			if(strpos($site, 'xxx') !== false){ $value -= 3; }
			if(strpos($site, 'porn') !== false){ $value -= 3; }
			if(strpos($site, 'sex') !== false){ $value -= 3; }
			if(strpos($site, 'tube') !== false){ $value -= 3; }
			if(strpos($desc, 'xxx') !== false){ $value -= 3; }
			if(strpos($desc, 'porn') !== false){ $value -= 3; }
			if(strpos($desc, 'sex') !== false){ $value -= 3; }
			if(strpos($desc, 'tube') !== false){ $value -= 3; }
			wp_cache_set( 'value', $value, 'entertainment_news', 1296000 );
		}else{
			$value = intval($value);
		}
		if($instance['link'] == 'nofollow' || $value <= 0){
			return '<a href="'.$url.'" rel="nofollow">'.$title.'</a>';
		}
		if($value >= 4){
			return '<a href="'.$url.'">'.$title.'</a>';
		}
		if($instance['link'] == 'normal'){
			return '<a href="'.$url.'">'.$title.'</a>';
		}
		return '<a href="'.$url.'" rel="nofollow">'.$title.'</a>';
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Entertainment News', 'text_domain' );
		$limit = ! empty( $instance['limit'] ) ? $instance['limit'] : 2;
		$icons = ! empty( $instance['icons'] ) ? $instance['icons'] : __( 'true', 'text_domain' );
		$summery = ! empty( $instance['summery'] ) ? $instance['summery'] : __( 'true', 'text_domain' );
		$date = ! empty( $instance['date'] ) ? $instance['date'] : __( 'true', 'text_domain' );
		$link = ! empty( $instance['link'] ) ? $instance['link'] : __( 'auto', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Number of articles :' ); ?></label> 
		 <select id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>">
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
			<option value="10">10</option>
		</select>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'icons' ); ?>"><?php _e( 'Show icons :' ); ?></label> 
		 <select id="<?php echo $this->get_field_id( 'icons' ); ?>" name="<?php echo $this->get_field_name( 'icons' ); ?>">
			<option value="true">True</option>
			<option value="false">False</option>
		</select>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'summery' ); ?>"><?php _e( 'Show summery :' ); ?></label> 
		 <select id="<?php echo $this->get_field_id( 'summery' ); ?>" name="<?php echo $this->get_field_name( 'summery' ); ?>">
			<option value="true">True</option>
			<option value="false">False</option>
		</select>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e( 'Show date :' ); ?></label> 
		 <select id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>">
			<option value="true">True</option>
			<option value="false">False</option>
		</select>
		</p>
		
		<label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e( 'Link reationship :' ); ?></label> 
		 <select id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
			<option value="auto">Auto</option>
			<option value="normal">Normal</option>
			<option value="nofollow">Nofollow</option>
		</select>
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '2';
		$instance['icons'] = ( ! empty( $new_instance['icons'] ) ) ? strip_tags( $new_instance['icons'] ) : 'true';
		$instance['summery'] = ( ! empty( $new_instance['summery'] ) ) ? strip_tags( $new_instance['summery'] ) : 'true';
		$instance['date'] = ( ! empty( $new_instance['date'] ) ) ? strip_tags( $new_instance['date'] ) : 'true';
		$instance['link'] = ( ! empty( $new_instance['link'] ) ) ? strip_tags( $new_instance['link'] ) : 'auto';
		return $instance;
	}

} // class Foo_Widget
?>