<?php  
/*
 * Plugin Name: Custom Recent Posts
 * Plugin URI:  http://ideachipper.com/
 * Description: Выводит недавние записи указанного типа
 * Version: 1.0.1
 * Author: Ideachipper.com
 * Author URI:  http://ideachipper.com/
 * License: GPL2 

    Copyright 2017  Alexandr Kharchenko

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License,
    version 2, as published by the Free Software Foundation. 

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details. 

*/

// Register Custom Recent Posts widget
add_action( 'widgets_init', 'init_AT_recent_posts' );
function init_AT_recent_posts() { return register_widget('AT_recent_posts'); }

class AT_recent_posts extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		
		parent::__construct(
			'AT_recent_posts',		// Base ID
			'Custom Recent Posts',		// Name
			array(
				'classname'		=>	'AT_recent_posts',
				'description'	=>	__('Выводит недавние записи указанного типа', 'framework')
			)
		);
		$this->register_at_scripts();

	} // end constructor
		function register_at_scripts(){
			wp_enqueue_style( 'custom-recent-posts', plugins_url( 'custom-recent-posts.css' , __FILE__ ) );
		}
	/**
	* This is our Widget
	**/
	function widget( $args, $instance ) {
		global $post;
		extract($args);
		
		// Widget options
		$title 	 = apply_filters('widget_title', $instance['title'] ); // Title		
		$AT 	 = $instance['types']; // Post type(s) 		
	    $types   = explode(',', $AT); // Let's turn this into an array we can work with.
		$number	 = $instance['number']; // Number of posts to show
		
        
		
	    
			
		$atq = new WP_Query([
				'post_type' => $types,
				'showposts' => $number,
				'post__not_in' => [$post->ID]
		]); 
		
		 //get_template_part( 'template-parts/recent-item');
		 include(locate_template($instance['tpl_path']));
		
		
		
	}

	/** Widget control update */
	function update( $new_instance, $old_instance ) {
		$instance    = $old_instance;
		
		//Let's turn that array into something the Wordpress database can store
		$types       = implode(',', (array)$new_instance['types']);

		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['types']  = $types;
		$instance['number'] = strip_tags( $new_instance['number'] );
		$instance['tpl_path'] = strip_tags( $new_instance['tpl_path'] );
		return $instance;
	}
	
	/**
	* Widget settings
	**/
	function form( $instance ) {	
	
		    // instance exist? if not set defaults
		    if ( $instance ) {
				$title  = $instance['title'];
		        $types  = $instance['types'];
		        $number = $instance['number'];
		        $tpl_path = $instance['tpl_path'];
		    } else {
			    //Defaults value
				$title  = '';
		        $types  = 'post';
		        $number = '5';
				$tpl_path = 'template-parts/recent-item.php';
		    }
			
			//Let's turn $types into an array
			$types = explode(',', $types);
			
			//Count number of post types for select box sizing
			$at_types = get_post_types( array( 'public' => true ), 'names' );
			foreach ($at_types as $AT ) {
			   $at_ar[] = $AT;
			}
			$i = count($at_ar);
			if($i > 10) { $i = 10;}

			// The widget form
			?>
			<p>
			<label for="<?php echo $this->get_field_id('tpl_path'); ?>"><?php echo __( 'Пусть к шаблону:' ); ?></label>
			<input id="<?php echo $this->get_field_id('tpl_path'); ?>" name="<?php echo $this->get_field_name('tpl_path'); ?>" type="text" value="<?php echo $tpl_path; ?>" class="widefat" />
			</p>
			<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __( 'Title:' ); ?></label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" class="widefat" />
			</p>
			<p>
			<label for="<?php echo $this->get_field_id('types'); ?>"><?php echo __( 'Select post type(s):' ); ?></label>
			<select name="<?php echo $this->get_field_name('types'); ?>[]" id="<?php echo $this->get_field_id('types'); ?>" class="widefat" style="height: auto;" size="<?php echo $i ?>" multiple>
				<?php 
				$args = array( 'public' => true );
				$post_types = get_post_types( $args, 'objects' );
				//print_r('<pre>'.(__FILE__).':'.(__LINE__).'<hr />'.print_r( $post_types , true).'</pre>');
				foreach ($post_types as $k => $post_type ) { ?>
					<option value="<?php echo $k; ?>" <?php if( in_array($k, $types)) { echo 'selected="selected"'; } ?>><?php echo $post_type->labels->singular_name;?></option>
				<?php }	?>
			</select>
			</p>
			<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php echo __( 'Number of posts to show:' ); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</p>
	<?php 
	}

} 

?>
