<?php
/*
	Name: Thesis Related Special Box
	Author: Pedro Lima
	Version: 1.0
	Description: Special related by custom post type and custom taxonomy.
	Class: thesis_related_special_box
*/

class thesis_related_special_box extends thesis_box {
	
	protected function translate() {
		$this->title = __('Thesis Related Special Box', 'thesis');
	}
	
	protected function construct() {
		wp_enqueue_style('thesis-related-special-style3post', THESIS_USER_BOXES_URL . "/thesis-related-special-box/css/style.css");
	}


	protected function options() {
		global $thesis;

		$get_post_types = get_post_types('', 'objects');
		$post_types = array();
		foreach ($get_post_types as $name => $pt_obj)
			if (!in_array($name, array('revision', 'nav_menu_item', 'attachment','page','acf')))
				$post_types[$name] = !empty($pt_obj->labels->name) ? esc_html($pt_obj->labels->name) : esc_html($pt_obj->name);
		$loop_post_types = $post_types;

		$options['post_type'] = array( // create the post type option
			'type' => 'select',
			'label' => __('Select Post Type', 'thesis'),
			'options' => $loop_post_types,
			'dependents' => NULL

			);
		return $options;

	}

	public function html() {
		global $thesis;
		global $post;
		// get options
		
		$options = $thesis->api->get_options($this->options(), $this->options);

		$ads .= '<div class="thesis_related">';

			$backup = $post;  // backup the current object
			$found_none = '<h2>No related posts found!</h2>';
			$taxonomy = 'topics';//  e.g. post_tag, category, custom taxonomy
			$param_type = 'topics'; //  e.g. tag__in, category__in, but genre__in will NOT work
			$tax_args=array('orderby' => 'none');
			$tags = wp_get_post_terms( $post->ID , $taxonomy, $tax_args);

			if ($options['post_type'] != 'page'){

			if ($tags) {
			  foreach ($tags as $tag) {
			    $args=array(
			      "$param_type" => $tag->slug,
			      'post__not_in' => array($post->ID),
			      'post_type' => $options['post_type'],
			      'showposts'=>4,
			      'caller_get_posts'=>1
			    );
			    $my_query = null;
			    $my_query = new WP_Query($args);
			    if( $my_query->have_posts() ) {
			      while ($my_query->have_posts()) : $my_query->the_post();
			       if ( has_post_thumbnail() ) { ?>
			<li><div class="relatedthumb"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail(); ?></a></div></li>
			<li><div class="related-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?> </a></div></li>
			<section class="fact-check-home"><?php add_fact_checking()?><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">More Facts</a></section>
			<?php } else { ?>
			<li><div class="relatedthumb"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><img src="<?php echo get_post_meta($post->ID, 'Image',true) ?>" width="100" height="100" alt="<?php the_title_attribute(); ?>" /><?php the_title(); ?></a></div></li>
			<section class="fact-check-home"><?php add_fact_checking()?></section>
			<?php }
			        $found_none = '';
			      endwhile;
			    }
			  }
			}
		}
		else{

			echo " ";

		}

if ($found_none) {
echo $found_none;
}
$post = $backup;  // copy it back
wp_reset_query();

			?>
			
			<div class="clear"></div>

			<?php
			 
		if ( $options['html'] ) {

			$ads .= stripslashes ($options['html']);

		}
		
		$ads .= '</div>';
		echo $ads;}
		}
	?>
