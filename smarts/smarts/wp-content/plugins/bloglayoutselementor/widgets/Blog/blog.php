<?php
namespace ElementorBlogLayouts\Widgets;

use \Elementor\Controls_Manager as Controls_Manager;
use \Elementor\Frontend;
use \Elementor\Group_Control_Border as Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow as Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography as Group_Control_Typography;
use \Elementor\Utils as Utils;
use \Elementor\Widget_Base as Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Blog Layouts
 *
 * Elementor widget for team vision
 *
 * @since 1.0.0
 */
class Blog_Layouts extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'blog-layouts';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Blog Layouts', 'elementor-blog-layouts' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'elementor-blog-layouts' ];
	}

	/**
	 * Get post type categories.
	 */
	private function grid_get_all_post_type_categories( $post_type ) {
		$options = array();

		if ( $post_type == 'post' ) {
			$taxonomy = 'category';
		} else {
			$taxonomy = $post_type;
		}

		if ( ! empty( $taxonomy ) ) {
			// Get categories for post type.
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				)
			);
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( isset( $term ) ) {
						if ( isset( $term->slug ) && isset( $term->name ) ) {
							$options[ $term->slug ] = $term->name;
						}
					}
				}
			}
		}

		return $options;
	}

	/**
	 * Get post type categories.
	 */
	private function grid_get_all_custom_post_types() {
		$options = array();

		$args = array( '_builtin' => false );
		$post_types = get_post_types( $args, 'objects' ); 

		foreach ( $post_types as $post_type ) {
			if ( isset( $post_type ) ) {
					$options[ $post_type->name ] = $post_type->label;
			}
		}

		return $options;
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'elementor-blog-layouts' ),
			]
		);

		$this->add_control(
			'blog_style',
			[
				'label' => esc_html__( 'Style', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style1',
				'options' => [
					'style1' => esc_html__('Style 1', 'elementor-blog-layouts' ),
					'style2' => esc_html__('Style 2', 'elementor-blog-layouts' ),
					'style3' => esc_html__('Style 3', 'elementor-blog-layouts' ),
					'style4' => esc_html__('Style 4', 'elementor-blog-layouts' ),
					'style5' => esc_html__('Style 5', 'elementor-blog-layouts' ),
					'style6' => esc_html__('Style 6', 'elementor-blog-layouts' ),
				]
			]
		);

		$this->add_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::SELECT,
				'default' => '2',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3'
				]
			]
		);
		
		$this->end_controls_section();

  		$this->start_controls_section(
  			'section_query',
  			[
  				'label' => esc_html__( 'QUERY', 'essential-addons-elementor' )
  			]
		);

		$this->add_control(
			'source',
			[
				'label' => esc_html__( 'Source', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'wp_posts',
				'options' => [
					'wp_posts' 				=> esc_html__( 'Wordpress Posts', 'essential-addons-elementor' ),
					'post_type' 	=> esc_html__( 'Custom Posts Type', 'essential-addons-elementor' )
				]
			]
		);

		$this->add_control(
			'posts_source',
			[
				'label' => esc_html__( 'All Posts/Sticky posts', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'all_posts',
				'options' => [ 
					'all_posts' 			=> esc_html__( 'All Posts', 'essential-addons-elementor' ),
					'onlystickyposts'	=> esc_html__( 'Only Sticky Posts', 'essential-addons-elementor' )
				],
				'condition'	=> [
					'source'	=> 'wp_posts'
				]
			]
		);

		$this->add_control(
			'posts_type',
			[
				'label' => esc_html__( 'Select Post Type Source', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->grid_get_all_custom_post_types(),
				'condition'	=> [
					'source'	=> 'post_type'
				]
			]
		);

		$this->add_control(
			'categories',
			[
				'label' => esc_html__( 'Categories', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->grid_get_all_post_type_categories('post'),
				'condition'	=> [
					'source'	=> 'wp_posts'
				]				
			]
		);

		$this->add_control(
			'order',
			[
				'label' => esc_html__( 'Order', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [
					'DESC'	=> 'DESC',
					'ASC' 	=> 'ASC'					
				]
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => esc_html__( 'Order By', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'date'			=> 'Date',
					'ID' 			=> 'ID',					
					'author' 		=> 'Author',					
					'title' 		=> 'Title',					
					'name' 			=> 'Name',
					'modified'		=> 'Modified',
					'parent' 		=> 'Parent',					
					'rand' 			=> 'Rand',					
					'comment_count' => 'Comments Count',					
					'none' 			=> 'None'						
				]
			]
		);



		$this->add_control(
			'pagination',
			[
				'label' => esc_html__( 'Pagination', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'no',
				'options' => [
					'no'	=> 'No',
					'yes' 	=> 'Yes',
					'load-more' => 'Yes with Load More'
				]
			]
		);

		$this->add_control(
			'num_posts',
			[
				'label' => esc_html__( 'Number Posts', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::TEXT,
				'default' => '10',
				'condition'	=> [
					'pagination'	=> 'no'
				]
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'label' => esc_html__( 'Pagination Type', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'numeric',
				'options' => [
					'numeric'	=> 'Numeric',
					'normal' 	=> 'Normal'					
				],
				'condition'	=> [
					'pagination'	=> 'yes'
				]				
			]
		);

		$this->add_control(
			'num_posts_page',
			[
				'label' => esc_html__( 'Number Posts For Page', 'elementor-blog-layouts' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => '10',
				'condition'	=> [
					'pagination'	=> array('yes','load-more')
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_animation',
			[
				'label' => esc_html__( 'Animations', 'elementor-blog-layouts' )
			]
		);
		
		$this->add_control(
			'addon_animate',
			[
				'label' => esc_html__( 'Animate', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'off',
				'options' => [
					'off'	=> 'Off',
					'on' 	=> 'On'					
				]
			]
		);		

		$this->add_control(
			'effect',
			[
				'label' => esc_html__( 'Animate Effects', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade-in',
				'options' => [
							'fade-in'			=> esc_html__( 'Fade In', 'elementor-blog-layouts' ),
							'fade-in-up' 		=> esc_html__( 'fade in up', 'elementor-blog-layouts' ),					
							'fade-in-down' 		=> esc_html__( 'fade in down', 'elementor-blog-layouts' ),					
							'fade-in-left' 		=> esc_html__( 'fade in Left', 'elementor-blog-layouts' ),					
							'fade-in-right' 	=> esc_html__( 'fade in Right', 'elementor-blog-layouts' ),					
							'fade-out'			=> esc_html__( 'Fade In', 'elementor-blog-layouts' ),
							'fade-out-up' 		=> esc_html__( 'Fade Out up', 'elementor-blog-layouts' ),					
							'fade-out-down' 	=> esc_html__( 'Fade Out down', 'elementor-blog-layouts' ),					
							'fade-out-left' 	=> esc_html__( 'Fade Out Left', 'elementor-blog-layouts' ),					
							'fade-out-right' 	=> esc_html__( 'Fade Out Right', 'elementor-blog-layouts' ),
							'bounce-in'			=> esc_html__( 'Bounce In', 'elementor-blog-layouts' ),
							'bounce-in-up' 		=> esc_html__( 'Bounce in up', 'elementor-blog-layouts' ),					
							'bounce-in-down' 	=> esc_html__( 'Bounce in down', 'elementor-blog-layouts' ),					
							'bounce-in-left' 	=> esc_html__( 'Bounce in Left', 'elementor-blog-layouts' ),					
							'bounce-in-right' 	=> esc_html__( 'Bounce in Right', 'elementor-blog-layouts' ),					
							'bounce-out'		=> esc_html__( 'Bounce In', 'elementor-blog-layouts' ),
							'bounce-out-up' 	=> esc_html__( 'Bounce Out up', 'elementor-blog-layouts' ),					
							'bounce-out-down' 	=> esc_html__( 'Bounce Out down', 'elementor-blog-layouts' ),					
							'bounce-out-left' 	=> esc_html__( 'Bounce Out Left', 'elementor-blog-layouts' ),					
							'bounce-out-right' 	=> esc_html__( 'Bounce Out Right', 'elementor-blog-layouts' ),	
							'zoom-in'			=> esc_html__( 'Zoom In', 'elementor-blog-layouts' ),
							'zoom-in-up' 		=> esc_html__( 'Zoom in up', 'elementor-blog-layouts' ),					
							'zoom-in-down' 		=> esc_html__( 'Zoom in down', 'elementor-blog-layouts' ),					
							'zoom-in-left' 		=> esc_html__( 'Zoom in Left', 'elementor-blog-layouts' ),					
							'zoom-in-right' 	=> esc_html__( 'Zoom in Right', 'elementor-blog-layouts' ),					
							'zoom-out'			=> esc_html__( 'Zoom In', 'elementor-blog-layouts' ),
							'zoom-out-up' 		=> esc_html__( 'Zoom Out up', 'elementor-blog-layouts' ),					
							'zoom-out-down' 	=> esc_html__( 'Zoom Out down', 'elementor-blog-layouts' ),					
							'zoom-out-left' 	=> esc_html__( 'Zoom Out Left', 'elementor-blog-layouts' ),					
							'zoom-out-right' 	=> esc_html__( 'Zoom Out Right', 'elementor-blog-layouts' ),
							'flash' 			=> esc_html__( 'Flash', 'elementor-blog-layouts' ),
							'strobe'			=> esc_html__( 'Strobe', 'elementor-blog-layouts' ),
							'shake-x'			=> esc_html__( 'Shake X', 'elementor-blog-layouts' ),
							'shake-y'			=> esc_html__( 'Shake Y', 'elementor-blog-layouts' ),
							'bounce' 			=> esc_html__( 'Bounce', 'elementor-blog-layouts' ),
							'tada'				=> esc_html__( 'Tada', 'elementor-blog-layouts' ),
							'rubber-band'		=> esc_html__( 'Rubber Band', 'elementor-blog-layouts' ),
							'swing' 			=> esc_html__( 'Swing', 'elementor-blog-layouts' ),
							'spin'				=> esc_html__( 'Spin', 'elementor-blog-layouts' ),
							'spin-reverse'		=> esc_html__( 'Spin Reverse', 'elementor-blog-layouts' ),
							'slingshot'			=> esc_html__( 'Slingshot', 'elementor-blog-layouts' ),
							'slingshot-reverse'	=> esc_html__( 'Slingshot Reverse', 'elementor-blog-layouts' ),
							'wobble'			=> esc_html__( 'Wobble', 'elementor-blog-layouts' ),
							'pulse' 			=> esc_html__( 'Pulse', 'elementor-blog-layouts' ),
							'pulsate'			=> esc_html__( 'Pulsate', 'elementor-blog-layouts' ),
							'heartbeat'			=> esc_html__( 'Heartbeat', 'elementor-blog-layouts' ),
							'panic' 			=> esc_html__( 'Panic', 'elementor-blog-layouts' )				
				],
				'condition'	=> [
					'addon_animate'	=> 'on'
				]
			]
		);			

		$this->add_control(
			'delay',
			[
				'label' => esc_html__( 'Animate Delay (ms)', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::TEXT,
				'default' => '1000',
				'condition'	=> [
					'addon_animate'	=> 'on'
				]
			]
		);	
		
		$this->end_controls_section();


		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'elementor-blog-layouts' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'custom_style',
			[
				'label' => esc_html__( 'Custom Style', 'elementor-blog-layouts' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'off',
				'options' => [
					'off'	=> 'Off',
					'on' 	=> 'On'					
				]
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'elementor-blog-layouts' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#333333',
				'condition'	=> [
					'custom_style'	=> 'on'
				]
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor-blog-layouts' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#747474',
				'condition'	=> [
					'custom_style'	=> 'on'
				]
			]
		);
		
		$this->add_control(
			'date_color',
			[
				'label' => esc_html__( 'Date Color', 'elementor-blog-layouts' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#AAAAAA',
				'condition'	=> [
					'custom_style'	=> 'on'
				]
			]
		);

		$this->add_control(
			'comment_color',
			[
				'label' => esc_html__( 'Comment Color', 'elementor-blog-layouts' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'condition'	=> [
					'custom_style'	=> 'on'
				]
			]
		);

		$this->add_control(
			'bg_comment_color',
			[
				'label' => esc_html__( 'Background Comment Color', 'elementor-blog-layouts' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000000',
				'condition'	=> [
					'custom_style'	=> 'on'
				]
			]
		);

		$this->add_control(
			'link_color',
			[
				'label' => esc_html__( 'Link Color', 'elementor-blog-layouts' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#c9564c',
				'condition'	=> [
					'custom_style'	=> 'on'
				]
			]
		);

		$this->add_control(
			'link_h_color',
			[
				'label' => esc_html__( 'Link Hover Color', 'elementor-blog-layouts' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#e7685d',
				'condition'	=> [
					'custom_style'	=> 'on'
				]
			]
		);

		$this->end_controls_section();
		
	}

	 
	 /**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		static $instance = 0;
		$instance++;		
		$settings = $this->get_settings_for_display();
	
        $blog_style				= esc_html($settings['blog_style']);
        $columns				= esc_html($settings['columns']);
		
		// Query
		$source					= esc_html($settings['source']);
		$posts_source			= esc_html($settings['posts_source']);
		$posts_type				= esc_html($settings['posts_type']);
		$categories				= '';
		if(!empty($settings['categories'])) {
			$num_cat = count($settings['categories']);
			$i = 1;
			foreach ( $settings['categories'] as $element ) {
				$categories .= $element;
				if($i != $num_cat) {
					$categories .= ',';
				}
				$i++;
			}		
		}		
		$categories_post_type	= '';
		$pagination				= esc_html($settings['pagination']);
		$pagination_type		= esc_html($settings['pagination_type']);
		$num_posts_page			= esc_html($settings['num_posts_page']);
		$num_posts				= esc_html($settings['num_posts']);	
		$orderby				= esc_html($settings['orderby']);
		$order					= esc_html($settings['order']);
					
		// Style
        $custom_style			= esc_html($settings['custom_style']);
        $title_color			= esc_html($settings['title_color']);
        $text_color				= esc_html($settings['text_color']);
        $date_color				= esc_html($settings['date_color']);
        $comment_color			= esc_html($settings['comment_color']);
        $bg_comment_color		= esc_html($settings['bg_comment_color']);
        $link_color				= esc_html($settings['link_color']);
        $link_h_color			= esc_html($settings['link_h_color']);
		
		// Animations
		$addon_animate			= esc_html($settings['addon_animate']);
		$effect					= esc_html($settings['effect']);
		$delay					= esc_html($settings['delay']);
		
		wp_enqueue_style( 'fonts-vc' );
		wp_enqueue_style( 'animations' );

		$container_class = $class_load_more = $class_item_load_more = $css_title = $css_text = $css_link = $css_date = $css_bg_comment = $css_comment = $css_comment_link = $js_class = $data_value = $css_current_pag_num = $css_bg_category_link = $css_post_bg_color = '';
		$css_bg_comment = 'style="--bg-comment-color-var:#009688;"';
		if($columns == '1') : $columns_class = 'col-xs-12'; $container_class = 'bloglayouts-bp-vc-blogs-1-col'; endif;
		if($columns == '2') : $columns_class = 'col-xs-6'; $container_class = 'bloglayouts-bp-vc-blogs-2-col'; endif;
		if($columns == '3') : $columns_class = 'col-xs-4'; $container_class = 'bloglayouts-bp-vc-blogs-3-col'; endif;		
		if(empty($num_posts)) : $num_posts = 6; endif;		
		
		$post_id = get_the_id(); 
		// PAGINATION		
		if($pagination == 'yes') {
			if ( get_query_var('paged') ) {
				$paged = get_query_var('paged');				
			} elseif ( get_query_var('page') ) {			
				$paged = get_query_var('page');			
			} else {			
				$paged = 1;			
			}
		}
		// #PAGINATION		
		
		// Load More
		if($pagination == 'load-more') : 
			$class_load_more = 'bloglayouts-bp-load-more-'.$blog_style.''; 
			$class_item_load_more = 'bloglayouts-bp-item-load-more-'.$blog_style.'';
		endif;
		// #Load More
		
		// LOOP QUERY
		$query = bloglayouts_query( $source,
							$posts_source, 
							$posts_type, 
							$categories,
							$categories_post_type, 
							$order, 
							$orderby, 
							$pagination, 
							$pagination_type,
							$num_posts, 
							$num_posts_page );	
		
		if($custom_style == 'on') :
			$css_title = 'style="color:'.esc_html($title_color).'" onMouseOver="this.style.color = \''.esc_html($link_h_color).'\';" onMouseLeave="this.style.color = \''.esc_html($title_color).'\';"';
			$css_text = 'style="color:'.esc_html($text_color).'"';
			$css_date = 'style="color:'.esc_html($date_color).'"';
			$css_bg_comment = 'style="background-color:'.esc_html($bg_comment_color).';--bg-comment-color-var:'.esc_html($bg_comment_color).';"';
			$css_comment = 'style="color:'.esc_html($comment_color).'"';			
			$css_comment_link = 'style="color:'.esc_html($comment_color).'"';			
			$css_link = 'style="color:'.esc_html($link_color).'" onMouseOver="this.style.color = \''.esc_html($link_h_color).'\';" onMouseLeave="this.style.color = \''.esc_html($link_color).'\';"';			
			$css_current_pag_num = 'style="color:'.esc_html($link_h_color).'"';
			$css_bg_category_link = 'style="color:'.esc_html($category_color).';background:'.esc_html($bg_category_color).'" onMouseOver="this.style.color = \''.esc_html($bg_category_color).'\';this.style.backgroundColor = \''.esc_html($category_color).'\';" onMouseLeave="this.style.color = \''.esc_html($category_color).'\';this.style.backgroundColor = \''.esc_html($bg_category_color).'\';"';
			$css_post_bg_color = 'style="background:'.esc_html($post_bg_color).'"';			
		endif;

		echo '<div class="clearfix"></div>';
		
		$count = 0;
		
		echo '<div class="bloglayouts bloglayouts-bp-vc-element-blogs '.esc_html($class_load_more).' bloglayouts-bp-blogs-'.$blog_style.' '.esc_html($container_class).' bloglayouts-bp-vc-element-blogs-'.esc_html($instance).' element-no-padding">';
		
		echo '<div class="bloglayouts-bp-vc-element-blogs-article-container">';
		
		// Start Query Loop
		$loop = new \WP_Query($query);		

		// Load More
		if($pagination == 'load-more') : 
		
			$readtext 		= esc_html__('Read More','elementor-blog-layouts');
			$loading 		= esc_html__('Loading posts...','elementor-blog-layouts');
			$nomoreposts 	= esc_html__('No more posts to load.','elementor-blog-layouts');		
		
			wp_enqueue_script(
				'bloglayouts-bp-load-blogs',
				BLE_URL . 'assets/js/bloglayouts.js',
				array('jquery'),
				'1.0',
				true
			);		
					
			$max = $loop->max_num_pages;
			$paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;
			
			// Add some parameters for the JS.
			wp_localize_script(
				'bloglayouts-bp-load-blogs',
				'fnwp_',
				array(
					'startPage' 	=> $paged,
					'maxPages' 		=> $max,
					'nextLink' 		=> next_posts($max, false),
					'readtext'		=> $readtext,
					'loading'		=> $loading,
					'nomoreposts'	=> $nomoreposts,
					'cssLink'		=> $css_link,
					'style'			=> $blog_style
				)
			);
		endif;
		// #Load More


		
		if($loop) :
			while ( $loop->have_posts() ) : $loop->the_post();
		
				$id_post = get_the_id();
				$link = get_permalink(); 
				if($count & 1) : $class_odd = "vc-element-post-even"; else : $class_odd = "vc-element-post-odd"; endif;

				/*************************** STYLE 1 ************************/
				if($blog_style == 'style1') {
					echo '<article class="item-blogs first-element-blogs '.esc_html($columns_class).' '.esc_html($class_item_load_more).' '.esc_html($class_odd).bloglayouts_animate_class($addon_animate,$effect,$delay).'>';
							
					# Col 1
					if($columns == 1) :
							
						echo '<div class="article-info">';
							echo '<div class="article-info-top">';								
								echo '<div class="article-category">'.bloglayouts_get_category($source,$posts_type,$css_link).'</div>';
								echo '<h3 class="article-title"><a href="'.esc_url($link).'" '.$css_title.'>'.get_the_title().'</a></h3>';									
								echo '<div class="article-info-bottom">';	
									echo '<div class="article-author">'.bloglayouts_get_author($css_link).'</div>';
									echo '<div class="article-separator">-</div>';
									echo '<div class="article-date" '.$css_date.'>'.get_the_date().'</div>';
									echo '<div class="clearfix"></div>';	
								echo '</div>';										
							echo '</div>';											
						echo '</div>';
								
						echo '<div class="article-image">';
							echo bloglayouts_get_blogs_thumb($columns,$post_id);
							echo bloglayouts_check_post_format();
							echo '<div class="article-comment comment-cloud" '.$css_bg_comment.'>'.bloglayouts_get_only_num_comments($css_comment,$css_comment_link).'</div>';
						echo '</div>';
						echo '<div class="article-info">';
							echo '<p class="article-excerpt" '.$css_text.'>' . bloglayouts_get_blogs_excerpt(300,'on',$css_link) . '</p>';						
							echo '<div class="clearfix"></div>';	
						echo '</div>';
						
					elseif($columns == 2) :
							
						echo '<div class="article-image">';
							echo bloglayouts_get_blogs_thumb($columns,$post_id);
							echo bloglayouts_check_post_format();
							echo '<div class="article-comment comment-cloud" '.$css_bg_comment.'>'.bloglayouts_get_only_num_comments($css_comment,$css_comment_link).'</div>';
						echo '</div>';

						echo '<div class="article-info-container"><div class="article-info">';
							echo '<div class="article-info-top">';								
								echo '<div class="article-category">'.bloglayouts_get_category($source,$posts_type,$css_link).'</div>';
								echo '<h3 class="article-title"><a href="'.esc_url($link).'" '.$css_title.'>'.get_the_title().'</a></h3>';									
								echo '<div class="article-info-bottom">';	
									echo '<div class="article-author">'.bloglayouts_get_author($css_link).'</div>';
									echo '<div class="article-separator">-</div>';
									echo '<div class="article-date" '.$css_date.'>'.get_the_date().'</div>';
									echo '<div class="clearfix"></div>';	
								echo '</div>';										
							echo '</div>';											
						echo '</div>';

						echo '<div class="article-info">';
								echo '<p class="article-excerpt" '.$css_text.'>' . bloglayouts_get_blogs_excerpt(150,'off') . '</p>';						
								echo '<div class="clearfix"></div>';	
						echo '</div></div>';							
							
					else :
							
						echo '<div class="article-image">';
							echo bloglayouts_get_blogs_thumb($columns,$post_id);
							echo bloglayouts_check_post_format();
							echo '<div class="article-comment comment-cloud" '.$css_bg_comment.'>'.bloglayouts_get_only_num_comments($css_comment,$css_comment_link).'</div>';
						echo '</div>';

						echo '<div class="article-info">';
							echo '<div class="article-info-top">';								
								echo '<div class="article-category">'.bloglayouts_get_category($source,$posts_type,$css_link).'</div>';
								echo '<h3 class="article-title"><a href="'.esc_url($link).'" '.$css_title.'>'.get_the_title().'</a></h3>';									
								echo '<div class="article-info-bottom">';	
									echo '<div class="article-author">'.bloglayouts_get_author($css_link).'</div>';
									echo '<div class="article-separator">-</div>';
									echo '<div class="article-date" '.$css_date.'>'.get_the_date().'</div>';
									echo '<div class="clearfix"></div>';	
								echo '</div>';										
							echo '</div>';											
						echo '</div>';					
							
					endif;
							
					echo '</article>';				
					
					$count_clear = $count + 1;
					if(($count_clear % $columns) == 0) : echo '<div class="clearfix"></div>'; endif;
				} 
				
				
				/*************************** STYLE 2 ************************/
				if($blog_style == 'style2') {
					echo '<article class="item-blogs first-element-blogs '.esc_html($columns_class).' '.esc_html($class_item_load_more).' '.esc_html($class_odd).bloglayouts_animate_class($addon_animate,$effect,$delay).'>';;

								echo '<div class="article-image">';
									echo bloglayouts_get_blogs_thumb($columns,$post_id);
									echo bloglayouts_check_post_format();								
								echo '</div>';					
						
								echo '<div class="article-info">';
									echo '<div class="article-info-top">';								
										echo '<div class="article-category">'.bloglayouts_get_category($source,$posts_type,$css_link).'</div>';
										echo '<div class="article-separator">-</div>';
										echo '<div class="article-date" '.$css_date.'>'.get_the_date().'</div>';
										echo '<div class="article-separator">-</div>';
										echo '<div class="article-author">'.bloglayouts_get_author($css_link).'</div>';
										echo '<div class="article-comment comment-cloud" '.$css_bg_comment.'>'.bloglayouts_get_only_num_comments($css_comment,$css_comment_link).'</div>';
										echo '<h3 class="article-title"><a href="'.esc_url($link).'" '.$css_title.'>'.get_the_title().'</a></h3>';
									echo '</div>';
									if($columns != 3) :
										echo '<div class="article-info-bottom">';
											if($columns == 1) : 
												echo '<p class="article-excerpt" '.$css_text.'>' . bloglayouts_get_blogs_excerpt(300,'off') . '</p>';										
											else :
												echo '<p class="article-excerpt" '.$css_text.'>' . bloglayouts_get_blogs_excerpt(150,'off') . '</p>';
											endif;
											echo '<div class="clearfix"></div>';										
										echo '</div>';
									endif;
									echo '<div class="footer-posts">';							
										echo bloglayouts_post_social_share($css_link);
									echo '</div>';
								echo '</div>';
							
					echo '</article>';				
					
					$count_clear = $count + 1;
					if(($count_clear % $columns) == 0) : echo '<div class="clearfix"></div>'; endif;										
				}
				
				
				/*************************** STYLE 3 ************************/
				if($blog_style == 'style3') {
					echo '<article class="item-blogs first-element-blogs '.esc_html($columns_class).' '.esc_html($class_item_load_more).' '.esc_html($class_odd).bloglayouts_animate_class($addon_animate,$effect,$delay).'>';;

						echo '<div class="article-image col-xs-6">';
							echo bloglayouts_get_thumb('bloglayouts-large');
							echo bloglayouts_check_post_format();
							echo '<div class="article-category">'.bloglayouts_get_category($source,$posts_type,$css_bg_category_link).'</div>';
						echo '</div>';					
						
						echo '<div class="article-info col-xs-6">';
							echo '<h3 class="article-title"><a href="'.esc_url($link).'" '.$css_title.'>'.get_the_title().'</a></h3>';								
							echo '<div class="article-info-top">';								
								echo '<div class="article-author">'.bloglayouts_get_author($css_link).'</div>';
								echo '<div class="article-separator">-</div>';
								echo '<div class="article-date" '.$css_date.'>'.get_the_date().'</div>';
							echo '</div>';																	
							echo '<div class="article-info-bottom">';
								echo '<p class="article-excerpt" '.$css_text.'>' . bloglayouts_get_blogs_excerpt(300,'off') . '</p>';
								echo '<div class="clearfix"></div>';										
							echo '</div>';
							echo bloglayouts_post_social_share($css_link);
						echo '</div>';
							
					echo '</article>';				
					
					$count_clear = $count + 1;
					if(($count_clear % $columns) == 0) : echo '<div class="clearfix"></div>'; endif;										
				}

				/*************************** STYLE 4 ************************/
				if($blog_style == 'style4') {
					echo '<article class="item-blogs first-element-blogs '.esc_html($columns_class).' '.esc_html($class_item_load_more).' '.esc_html($class_odd).bloglayouts_animate_class($addon_animate,$effect,$delay).'>';;

						echo '<div class="article-image">';
							echo bloglayouts_get_blogs_thumb($columns,$post_id);
							echo bloglayouts_check_post_format();								
						echo '</div>';					
						
						echo '<div class="article-info">';
							echo '<div class="article-info-top" '.$css_post_bg_color.'>';								
								echo '<div class="article-category">'.bloglayouts_get_category($source,$posts_type,$css_bg_category_link).'</div>';
								echo '<h3 class="article-title"><a href="'.esc_url($link).'" '.$css_title.'>'.get_the_title().'</a></h3>';								
								echo '<div class="article-author">'.bloglayouts_get_author($css_link).'</div>';
								echo '<div class="article-separator">-</div>';
								echo '<div class="article-date" '.$css_date.'>'.get_the_date().'</div>';						
							echo '</div>';
						echo '</div>';
							
					echo '</article>';				
					
					$count_clear = $count + 1;
					if(($count_clear % $columns) == 0) : echo '<div class="clearfix"></div>'; endif;										
				}

				/*************************** STYLE 5 ************************/
				if($blog_style == 'style5') {
					echo '<article class="item-blogs first-element-blogs '.esc_html($columns_class).' '.esc_html($class_item_load_more).' '.esc_html($class_odd).bloglayouts_animate_class($addon_animate,$effect,$delay).'>';;

						echo '<div class="article-image">';
							echo bloglayouts_get_thumb('bloglayouts-blog-medium-vertical');
							echo bloglayouts_check_post_format();	
							echo '<div class="article-info">';
								echo '<div class="article-info-top">';								
									echo '<div class="article-category">'.bloglayouts_get_category($source,$posts_type,$css_bg_category_link).'</div>';
									echo '<h3 class="article-title"><a href="'.esc_url($link).'" '.$css_title.'>'.get_the_title().'</a></h3>';														
								echo '</div>';
							echo '</div>';
							echo '<div class="item-blogs-pattern"></div>';
						echo '</div>';
							
					echo '</article>';				
					
					$count_clear = $count + 1;
					if(($count_clear % $columns) == 0) : echo '<div class="clearfix"></div>'; endif;										
				}

				/*************************** STYLE 6 ************************/
				if($blog_style == 'style6') {
					echo '<article class="item-blogs first-element-blogs '.esc_html($columns_class).' '.esc_html($class_item_load_more).' '.esc_html($class_odd).bloglayouts_animate_class($addon_animate,$effect,$delay).'>';;

						echo '<div class="article-image">';
							echo bloglayouts_get_blogs_thumb($columns,$post_id);
							echo bloglayouts_check_post_format();	
						echo '</div>';				
						echo '<div class="article-info">';
							echo '<div class="article-info-top">';						
								echo '<h3 class="article-title"><a href="'.esc_url($link).'" '.$css_title.'>'.get_the_title().'</a></h3>';
								if($columns == 1) :
									echo '<p class="article-excerpt" '.$css_text.'>' . bloglayouts_get_blogs_excerpt(280,'off') . '</p>';
								elseif($columns == 2)  :
									echo '<p class="article-excerpt" '.$css_text.'>' . bloglayouts_get_blogs_excerpt(140,'off') . '</p>';
								elseif($columns == 3) :
									echo '<p class="article-excerpt" '.$css_text.'>' . bloglayouts_get_blogs_excerpt(70,'off') . '</p>';
								endif;
								echo '<div class="bloglayouts-clear"></div>';	
							echo '</div>';
							echo '<div class="article-info-bottom">';
								echo '<div class="article-category">'.bloglayouts_get_category($source,$posts_type,$css_link).'</div>';	
								echo '<div class="article-separator">-</div>';							
								echo '<div class="article-author">'.bloglayouts_get_author($css_link).'</div>';
								echo '<div class="article-separator">-</div>';
								echo '<div class="article-date" '.$css_date.'>'.get_the_date().'</div>';						
							echo '</div>';
						echo '</div>';
							
					echo '</article>';				
					
					$count_clear = $count + 1;
					if(($count_clear % $columns) == 0) : echo '<div class="clearfix"></div>'; endif;										
				}				
				
			$count++;
			endwhile;
		endif;	
		
		echo '</div><div class="clearfix"></div>';
		
		/**********************************************************************/
		/****************************** PAGINATION ****************************/
		/**********************************************************************/ 
		if($pagination == 'yes') {
				echo '<div class="clearfix"></div><div class="bloglayouts-bp-blogs-display-'.esc_html($instance).' bloglayouts-bp-vc-pagination">';
				if($pagination_type == 'numeric') {
					echo bloglayouts_posts_numeric_pagination($pages = '', $range = 2,$loop,$paged,$css_current_pag_num,$css_link);
				} else {
					echo '<div class="bloglayouts-bp-pagination-normal">';
						echo get_next_posts_link( '<span '.$css_link.'>Older blogs</span>', $loop->max_num_pages );
						echo get_previous_posts_link( '<span '.$css_link.'>Newer blogs</span>' );
					echo '</div>';
				}
				echo '<div class="clearfix"></div></div>';
		}
		/**********************************************************************/
		/***************************** #PAGINATION ****************************/
		/**********************************************************************/ 
		wp_reset_query();
		echo '</div>';
		

		
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _content_template() {}
}
