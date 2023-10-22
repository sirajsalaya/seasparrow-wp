<?php
	/*	
	*	Goodlayers Item For Page Builder
	*/

	add_action('plugins_loaded', 'tourmaster_add_pb_element_tour_category');
	if( !function_exists('tourmaster_add_pb_element_tour_category') ){
		function tourmaster_add_pb_element_tour_category(){

			if( class_exists('gdlr_core_page_builder_element') ){
				gdlr_core_page_builder_element::add_element('tour_category', 'tourmaster_pb_element_tour_category'); 
			}
			
		}
	}
	
	if( !class_exists('tourmaster_pb_element_tour_category') ){
		class tourmaster_pb_element_tour_category{
			
			// get the element settings
			static function get_settings(){
				return array(
					'icon' => 'fa-plane',
					'title' => esc_html__('Tour Category', 'tourmaster')
				);
			}

			// list all custom taxonomy
			static function get_tax_option_list(){
				
				$ret = array();

				$tax_fields = array( 'tour_tag' => esc_html__('Tag', 'tourmaster') );
				$tax_fields = $tax_fields + tourmaster_get_custom_tax_list();
				foreach( $tax_fields as $tax_field => $tax_title ){
					$ret[$tax_field] = array(
						'title' => $tax_title,
						'type' => 'multi-combobox',
						'options' => tourmaster_get_term_list($tax_field),
						'condition' => array( 'filter-type' => $tax_field ),
						'description' => esc_html__('You can use Ctrl/Command button to select multiple items or remove the selected item. Leave this field blank to select all items in the list.', 'tourmaster'),
					);
				}

				return $ret;
			}

			// return the element options
			static function get_options(){
				return apply_filters('tourmaster_tour_item_options', array(					
					'general' => array(
						'title' => esc_html__('General', 'tourmaster'),
						'options' => array(
							'filter-type' => array(
								'title' => esc_html__('Filter Type', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'tour_category' => esc_html__('Tour Category', 'tourmaster'),
									'tour_tag' => esc_html__('Tour Tag', 'tourmaster'),
								) + tourmaster_get_custom_tax_list()
							),
							'category' => array(
								'title' => esc_html__('Category', 'tourmaster'),
								'type' => 'multi-combobox',
								'options' => tourmaster_get_term_list('tour_category'),
								'condition' => array( 'filter-type' => 'tour_category' ),
								'description' => esc_html__('You can use Ctrl/Command button to select multiple items or remove the selected item. Leave this field blank to select all items in the list.', 'tourmaster'),
							),
						) + self::get_tax_option_list() + array(
							'num-fetch' => array(
								'title' => esc_html__('Num Fetch', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'number',
								'default' => 9,
								'description' => esc_html__('The number of posts showing on the blog item', 'tourmaster')
							),
							'orderby' => array(
								'title' => esc_html__('Order By', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'name' => esc_html__('Name', 'tourmaster'), 
									'slug' => esc_html__('Slug', 'tourmaster'), 
									'term_id' => esc_html__('Term ID', 'tourmaster'), 
								)
							),
							'order' => array(
								'title' => esc_html__('Order', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'desc'=>esc_html__('Descending Order', 'tourmaster'), 
									'asc'=> esc_html__('Ascending Order', 'tourmaster'), 
								)
							),
						),
					),
					'settings' => array(
						'title' => esc_html('Style', 'tourmaster'),
						'options' => array(

							'style' => array(
								'title' => esc_html__('Tour Category Style', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'grid' => esc_html__('Grid Style', 'tourmaster'),
									'grid-2' => esc_html__('Grid 2 Style', 'tourmaster'),
									'grid-3' => esc_html__('Grid 3 Style', 'tourmaster'),
									'widget' => esc_html__('Widget Style', 'tourmaster'),
									'grid-4' => esc_html__('Grid 4 Style', 'tourmaster'),
									'grid-5' => esc_html__('Grid 5 Style', 'tourmaster'),
								),
								'default' => 20,
							),
							'column-size' => array(
								'title' => esc_html__('Column Size', 'tourmaster'),
								'type' => 'combobox',
								'options' => array( 60=>1, 30=>2, 20=>3, 15=>4, 12=>5 ),
								'default' => 20,
								'condition' => array('style' => array('grid', 'grid-2', 'grid-3', 'grid-5', 'widget'))
							),
							'thumbnail-size' => array(
								'title' => esc_html__('Thumbnail Size', 'tourmaster'),
								'type' => 'combobox',
								'options' => 'thumbnail-size'
							),
							'excerpt' => array(
								'title' => esc_html__('Excerpt Type', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'specify-number' => esc_html__('Specify Number', 'tourmaster'),
									'none' => esc_html__('Disable Exceprt', 'tourmaster'),
								),
								'condition' => array( 'style' => 'grid-3' ),
								'default' => 'specify-number',
							),
							'excerpt-number' => array(
								'title' => esc_html__('Excerpt Number', 'tourmaster'),
								'type' => 'text',
								'default' => 55,
								'condition' => array( 'style' => 'grid-3', 'excerpt' => 'specify-number' )
							),

							'with-feature' => array(
								'title' => esc_html__('With Feature', 'tourmaster'),
								'type' => 'checkbox',
								'default' => 'disable',
								'condition' => array( 'style' => 'grid-3' )
							),
							'feature-thumbnail-size' => array(
								'title' => esc_html__('Feature Thumbnail Size', 'tourmaster'),
								'type' => 'combobox',
								'options' => 'thumbnail-size',
								'condition' => array( 'style' => 'grid-3', 'with-feature' => 'enable' )
							),
							'feature-excerpt' => array(
								'title' => esc_html__('Feature Excerpt Type', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'specify-number' => esc_html__('Specify Number', 'tourmaster'),
									'none' => esc_html__('Disable Exceprt', 'tourmaster'),
								),
								'condition' => array( 'style' => 'grid-3' ),
								'default' => 'specify-number',
							),
							'feature-excerpt-number' => array(
								'title' => esc_html__('Feature Excerpt Number', 'tourmaster'),
								'type' => 'text',
								'default' => 55,
								'condition' => array( 'style' => 'grid-3', 'excerpt' => 'specify-number' )
							),
							
							'layout' => array(
								'title' => esc_html__('Layout', 'tourmaster'),
								'type' => 'combobox',
								'options' => array( 
									'fitrows' => esc_html__('Fit Rows', 'tourmaster'),
									'carousel' => esc_html__('Carousel', 'tourmaster'),
								),
								'default' => 'fitrows',
								'condition' => array('style' => array('grid-5'))
							),
							'carousel-scrolling-item-amount' => array(
								'title' => esc_html__('Carousel Scrolling Item Amount', 'goodlayers-core'),
								'type' => 'text',
								'default' => '1',
								'condition' => array('style' => array('grid-5'), 'layout' => 'carousel' )
							),
							'carousel-autoslide' => array(
								'title' => esc_html__('Autoslide Carousel', 'tourmaster'),
								'type' => 'checkbox',
								'default' => 'enable',
								'condition' => array('style' => array('grid-5'), 'layout' => 'carousel' )
							),
							'carousel-navigation' => array(
								'title' => esc_html__('Carousel Navigation', 'tourmaster'),
								'type' => 'combobox',
								'options' => (function_exists('gdlr_core_get_flexslider_navigation_types')? gdlr_core_get_flexslider_navigation_types(): array()),
								'default' => 'navigation',
								'condition' => array('style' => array('grid-5'), 'layout' => 'carousel' )
							),
							'carousel-navigation-align' => (function_exists('gdlr_core_get_flexslider_navigation_align')? gdlr_core_get_flexslider_navigation_align(): array()),
							'carousel-navigation-left-icon' => (function_exists('gdlr_core_get_flexslider_navigation_left_icon')? gdlr_core_get_flexslider_navigation_left_icon(): array()),
							'carousel-navigation-right-icon' => (function_exists('gdlr_core_get_flexslider_navigation_right_icon')? gdlr_core_get_flexslider_navigation_right_icon(): array()),
							'carousel-navigation-icon-color' => (function_exists('gdlr_core_get_flexslider_navigation_icon_color')? gdlr_core_get_flexslider_navigation_icon_color(): array()),
							'carousel-navigation-icon-bg' => (function_exists('gdlr_core_get_flexslider_navigation_icon_background')? gdlr_core_get_flexslider_navigation_icon_background(): array()),
							'carousel-navigation-icon-padding' => (function_exists('gdlr_core_get_flexslider_navigation_icon_padding')? gdlr_core_get_flexslider_navigation_icon_padding(): array()),
							'carousel-navigation-icon-radius' => (function_exists('gdlr_core_get_flexslider_navigation_icon_radius')? gdlr_core_get_flexslider_navigation_icon_radius(): array()),
							'carousel-navigation-size' => (function_exists('gdlr_core_get_flexslider_navigation_icon_size')? gdlr_core_get_flexslider_navigation_icon_size(): array()),
							'carousel-navigation-margin' => (function_exists('gdlr_core_get_flexslider_navigation_margin')? gdlr_core_get_flexslider_navigation_margin(): array()),
							'carousel-navigation-side-margin' => (function_exists('gdlr_core_get_flexslider_navigation_side_margin')? gdlr_core_get_flexslider_navigation_side_margin(): array()),
							'carousel-navigation-icon-margin' => (function_exists('gdlr_core_get_flexslider_navigation_icon_margin')? gdlr_core_get_flexslider_navigation_icon_margin(): array()),
							'carousel-bullet-style' => array(
								'title' => esc_html__('Carousel Bullet Style', 'tourmaster'),
								'type' => 'radioimage',
								'options' => (function_exists('gdlr_core_get_flexslider_bullet_itypes')? gdlr_core_get_flexslider_bullet_itypes(): array()),
								'condition' => array( 'layout' => 'carousel', 'carousel-navigation' => array('bullet','both') ),
								'wrapper-class' => 'gdlr-core-fullsize'
							),
							'carousel-bullet-top-margin' => array(
								'title' => esc_html__('Carousel Bullet Top Margin', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
								'condition' => array( 'layout' => 'carousel', 'carousel-navigation' => array('bullet','both') )
							),

						),
					),
					'typography' => array(
						'title' => esc_html__('Typography', 'tourmaster'),
						'options' => array(
							'title-font-size' => array(
								'title' => esc_html__('Title Font Size', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
							'title-font-weight' => array(
								'title' => esc_html__('Title Font Weight', 'tourmaster'),
								'type' => 'text',
								'description' => esc_html__('Eg. lighter, bold, normal, 300, 400, 600, 700, 800', 'tourmaster')
							),
							'title-letter-spacing' => array(
								'title' => esc_html__('Title Letter Spacing', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
							'title-text-transform' => array(
								'title' => esc_html__('Title Text Transform', 'tourmaster'),
								'type' => 'combobox',
								'data-type' => 'text',
								'options' => array(
									'' => esc_html__('Default', 'tourmaster'),
									'uppercase' => esc_html__('Uppercase', 'tourmaster'),
									'lowercase' => esc_html__('Lowercase', 'tourmaster'),
									'capitalize' => esc_html__('Capitalize', 'tourmaster'),
									'none' => esc_html__('None', 'tourmaster'),
								),
							),
							'read-more-font-size' => array(
								'title' => esc_html__('Read More Font Size', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
							'read-more-font-weight' => array(
								'title' => esc_html__('Read More Font Weight', 'tourmaster'),
								'type' => 'text',
								'description' => esc_html__('Eg. lighter, bold, normal, 300, 400, 600, 700, 800', 'tourmaster')
							),
							'read-more-letter-spacing' => array(
								'title' => esc_html__('Read More Letter Spacing', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
							'read-more-text-transform' => array(
								'title' => esc_html__('Read More Text Transform', 'tourmaster'),
								'type' => 'combobox',
								'data-type' => 'text',
								'options' => array(
									'' => esc_html__('Default', 'tourmaster'),
									'uppercase' => esc_html__('Uppercase', 'tourmaster'),
									'lowercase' => esc_html__('Lowercase', 'tourmaster'),
									'capitalize' => esc_html__('Capitalize', 'tourmaster'),
									'none' => esc_html__('None', 'tourmaster'),
								),
							)
						)
					),
					'spacing' => array(
						'title' => esc_html('Spacing', 'tourmaster'),
						'options' => array(
							'border-radius' => array(
								'title' => esc_html__('Frame/Thumbnail Border Radius', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
							'padding-bottom' => array(
								'title' => esc_html__('Padding Bottom ( Item )', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
								'default' => '30px'
							),
						)
					),
					'item-title' => array(
						'title' => esc_html('Item Title', 'tourmaster'),
						'options' => array(
							'title-align' => array(
								'title' => esc_html__('Title Align', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'left' => esc_html__('Left', 'tourmaster'),
									'center' => esc_html__('Center', 'tourmaster'),
								),
								'default' => 'left',
							),
							'title' => array(
								'title' => esc_html__('Title', 'tourmaster'),
								'type' => 'text',
							),
							'caption' => array(
								'title' => esc_html__('Caption', 'tourmaster'),
								'type' => 'textarea',
							),
							'read-more-text' => array(
								'title' => esc_html__('Read More Text', 'tourmaster'),
								'type' => 'text',
								'default' => esc_html__('Read More', 'tourmaster'),
								'condition' => array( 'title-align' => 'left' )
							),
							'read-more-link' => array(
								'title' => esc_html__('Read More Link', 'tourmaster'),
								'type' => 'text',
								'condition' => array( 'title-align' => 'left' )
							),
							'read-more-target' => array(
								'title' => esc_html__('Read More Target', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'_self' => esc_html__('Current Screen', 'tourmaster'),
									'_blank' => esc_html__('New Window', 'tourmaster'),
								),
								'condition' => array( 'title-align' => 'left' )
							),
							'title-size' => array(
								'title' => esc_html__('Title Size', 'tourmaster'),
								'type' => 'fontslider',
								'default' => '41px'
							),
							'caption-size' => array(
								'title' => esc_html__('Caption Size', 'tourmaster'),
								'type' => 'fontslider',
								'default' => '16px'
							),
							'read-more-size' => array(
								'title' => esc_html__('Read More Size', 'tourmaster'),
								'type' => 'fontslider',
								'default' => '14px',
								'condition' => array( 'title-align' => 'left' )
							),
							'title-color' => array(
								'title' => esc_html__('Title Color', 'tourmaster'),
								'type' => 'colorpicker'
							),
							'caption-color' => array(
								'title' => esc_html__('Caption Color', 'tourmaster'),
								'type' => 'colorpicker'
							),
							'read-more-color' => array(
								'title' => esc_html__('Read More Color', 'tourmaster'),
								'type' => 'colorpicker',
								'condition' => array( 'title-align' => 'left' )
							),
							'read-more-divider-color' => array(
								'title' => esc_html__('Read More Divider Color', 'tourmaster'),
								'type' => 'colorpicker',
								'condition' => array( 'title-align' => 'left' )
							),
						)
					)
				));
			}

			// get the preview for page builder
			static function get_preview( $settings = array() ){
				$content  = self::get_content($settings, true);
				$id = mt_rand(0, 9999);
				
				ob_start();
?><script type="text/javascript" id="tourmaster-preview-tour-category-<?php echo esc_attr($id); ?>" >
if( document.readyState == 'complete' ){
	jQuery(document).ready(function(){
		var tour_preview = jQuery('#tourmaster-preview-tour-category-<?php echo esc_attr($id); ?>').parent();
		tour_preview.gdlr_core_lightbox().gdlr_core_flexslider().gdlr_core_isotope().gdlr_core_fluid_video();
	});
}else{
	jQuery(window).load(function(){
		setTimeout(function(){
			var tour_preview = jQuery('#tourmaster-preview-tour-category-<?php echo esc_attr($id); ?>').parent();
			tour_preview.gdlr_core_lightbox().gdlr_core_flexslider().gdlr_core_isotope().gdlr_core_fluid_video();
		}, 300);
	});
}
</script><?php	
				$content .= ob_get_contents();
				ob_end_clean();
				
				return $content;
			}			
			
			// get the content from settings
			static function get_content( $settings = array() ){
				
				// default variable
				if( empty($settings) ){
					$settings = array( 'category' => '' );
				}

				$settings['thumbnail-size'] = empty($settings['thumbnail-size'])? 'full': $settings['thumbnail-size'];

				// start printing item
				$title_settings = $settings;

				$extra_class  = '';
				if( !empty($settings['style']) && $settings['style'] == 'widget' ){
					$extra_class .= 'tourmaster-item-pdlr';
				}else if( !empty($settings['style']) && $settings['style'] == 'grid-5' && $settings['layout'] == 'carousel' ){
					$extra_class .= 'tourmaster-item-pdlr';
					$title_settings['pdlr'] = false;
				}

				$ret  = '<div class="tourmaster-tour-category clearfix ' . esc_attr($extra_class) . '" ';
				if( !empty($settings['padding-bottom']) && $settings['padding-bottom'] != '30px' ){
					$ret .= tourmaster_esc_style(array('padding-bottom'=>$settings['padding-bottom']));
				}
				if( !empty($settings['id']) ){
					$ret .= ' id="' . esc_attr($settings['id']) . '" ';
				}
				$ret .= ' >';

				// print title
				if( function_exists('gdlr_core_block_item_title') ){
					$ret .= gdlr_core_block_item_title($title_settings);
				}
				
				// query
				$args = array(
					'orderby' => empty($settings['orderby'])? 'name': $settings['orderby'],
					'order' => empty($settings['order'])? 'asc': $settings['order'],
					'number' => empty($settings['num-fetch'])? 0: $settings['num-fetch'],
					'hide_empty' => false
				);
				if( empty($settings['filter-type']) || $settings['filter-type'] == 'tour_category' ){
					$args['taxonomy'] = 'tour_category';

					if( !empty($settings['category']) ){
						if( !is_array($settings['category']) ){
							$settings['category'] = array_map('trim', explode(',', $settings['category']));
						}
						$args['slug'] = $settings['category'];
					}
				}else{
					$args['taxonomy'] = $settings['filter-type'];

					if( !empty($settings[$settings['filter-type']]) ){
						if( !is_array($settings[$settings['filter-type']]) ){
							$settings[$settings['filter-type']] = array_map('trim', explode(',', $settings[$settings['filter-type']]));
						}
						$args['slug'] = $settings[$settings['filter-type']];
					}
				}

				$categories = get_terms($args);

				// print 
				if( !empty($categories) && !is_wp_error($categories) ){
					if( empty($settings['style']) || $settings['style'] == 'grid' ){

						$ret .= self::get_category_grid($categories, $settings, $args['taxonomy']);

					}else if( $settings['style'] == 'grid-2' ){

						$ret .= self::get_category_grid2($categories, $settings, $args['taxonomy']);

					}else if( $settings['style'] == 'grid-3' ){

						$ret .= self::get_category_grid3($categories, $settings, $args['taxonomy']);

					}else if( $settings['style'] == 'widget' ){

						$ret .= self::get_category_widget($categories, $settings, $args['taxonomy']);

					}else if( $settings['style'] == 'grid-4' ){

						$ret .= self::get_category_grid4($categories, $settings, $args['taxonomy']);

					}else if( $settings['style'] == 'grid-5' ){

						$ret .= self::get_category_grid5($categories, $settings, $args['taxonomy']);

					}
				}

				$ret .= '</div>'; // tourmaster-tour-category-item
				
				return $ret;
			}			
			
			static function get_category_grid( $categories, $settings, $taxonomy ){

				$column_sum = 0;
				$column_size = empty($settings['column-size'])? 20: $settings['column-size'];

				$ret = '';

				foreach( $categories as $category ){
					$additional_class  = ' tourmaster-item-pdlr tourmaster-item-mgb';
					if( !empty($column_size) ){
						$additional_class .= ' tourmaster-column-' . $column_size;
					}

					if( $column_sum == 0 || $column_sum + intval($column_size) > 60 ){
						$column_sum = intval($column_size);
						$additional_class .= ' tourmaster-column-first';
					}else{
						$column_sum += intval($column_size);
					}

					$thumbnail = get_term_meta($category->term_id, 'thumbnail', true);
					if( !empty($thumbnail) ){
						$additional_class .= ' tourmaster-with-thumbnail';
					}
					
					$ret .= '<div class="tourmaster-tour-category-grid tourmaster-item-list ' . esc_attr($additional_class) . '" >';
					$ret .= '<div class="tourmaster-tour-category-item-wrap" ' . tourmaster_esc_style(array(
						'border-radius' => empty($settings['border-radius'])? '': $settings['border-radius']
					)) . ' >';
					if( !empty($thumbnail) ){
						$ret .= '<div class="tourmaster-tour-category-thumbnail tourmaster-media-image" >';
						$ret .= tourmaster_get_image($thumbnail, $settings['thumbnail-size']);
						$ret .= '</div>';
						$ret .= '<div class="tourmaster-tour-category-overlay" ></div>';
						$ret .= '<div class="tourmaster-tour-category-overlay-front" ></div>';
					}
					
					$ret .= '<div class="tourmaster-tour-category-head" >';
					$ret .= '<div class="tourmaster-tour-category-head-display clearfix" >';
					$ret .= '<h3 class="tourmaster-tour-category-title" ' . tourmaster_esc_style(array(
						'font-size' => empty($settings['title-font-size'])? '': $settings['title-font-size'],
						'font-weight' => empty($settings['title-font-weight'])? '': $settings['title-font-weight'],
						'letter-spacing' => empty($settings['title-letter-spacing'])? '': $settings['title-letter-spacing'],
						'text-transform' => empty($settings['title-text-transform'])? '': $settings['title-text-transform'],
					)) . ' >';
					$ret .= '<i class="icon_pin_alt" ></i>';
					$ret .= $category->name;
					$ret .= '</h3>';
					$ret .= '<div class="tourmaster-tour-category-count" >';
					if( $category->count <= 1 ){
						$ret .= sprintf(esc_html__('%d tour', 'tourmaster'), $category->count);
					}else{
						$ret .= sprintf(esc_html__('%d tours', 'tourmaster'), $category->count);
					}
					$ret .= '</div>'; // tourmaster-tour-category-count
					$ret .= '</div>'; // tourmaster-tour-category-head-display

					$ret .= '<div class="tourmaster-tour-category-head-animate" >';
					$term_link = get_term_link($category->term_id, $taxonomy);
					if( !is_wp_error($term_link) ){
						$ret .= '<a class="tourmaster-tour-category-head-link" href="' . esc_url($term_link) . '" ' . tourmaster_esc_style(array(
							'font-size' => empty($settings['read-more-font-size'])? '': $settings['read-more-font-size'],
							'font-weight' => empty($settings['read-more-font-weight'])? '': $settings['read-more-font-weight'],
							'letter-spacing' => empty($settings['read-more-letter-spacing'])? '': $settings['read-more-letter-spacing'],
							'text-transform' => empty($settings['read-more-text-transform'])? '': $settings['read-more-text-transform'],
						)) . ' >';
						$ret .= esc_html__('View all tours', 'tourmaster');
						$ret .= '</a>';
					}
					$ret .= '<div class="tourmaster-tour-category-head-divider" ></div>';
					$ret .= '</div>'; // tourmaster-tour-category-head-animate
					$ret .= '</div>'; // tourmaster-tour-category-head
					$ret .= '</div>'; // tourmaster-tour-category-item-wrap
					$ret .= '</div>'; // tourmaster-tour-category-grid
				}

				return $ret;
			}

			static function get_category_grid2( $categories, $settings, $taxonomy ){

				$column_sum = 0;
				$column_size = empty($settings['column-size'])? 20: $settings['column-size'];

				$ret = '';

				foreach( $categories as $category ){
					$additional_class  = ' tourmaster-item-pdlr tourmaster-item-mgb';
					if( !empty($column_size) ){
						$additional_class .= ' tourmaster-column-' . $column_size;
					}

					if( $column_sum == 0 || $column_sum + intval($column_size) > 60 ){
						$column_sum = intval($column_size);
						$additional_class .= ' tourmaster-column-first';
					}else{
						$column_sum += intval($column_size);
					}

					$thumbnail = get_term_meta($category->term_id, 'thumbnail', true);
					if( !empty($thumbnail) ){
						$additional_class .= ' tourmaster-with-thumbnail';
					}
					
					$ret .= '<div class="tourmaster-tour-category-grid-2 tourmaster-item-list ' . esc_attr($additional_class) . '" >';
					$ret .= '<div class="tourmaster-tour-category-item-wrap" ' . tourmaster_esc_style(array(
						'border-radius' => empty($settings['border-radius'])? '': $settings['border-radius']
					)) . ' >';
					if( !empty($thumbnail) ){
						$ret .= '<div class="tourmaster-tour-category-thumbnail tourmaster-media-image" >';
						$ret .= tourmaster_get_image($thumbnail, $settings['thumbnail-size']);
						$ret .= '</div>';
						$ret .= '<div class="tourmaster-tour-category-overlay" ></div>';
						$ret .= '<div class="tourmaster-tour-category-overlay-front" ></div>';
					}
					
					$ret .= '<div class="tourmaster-tour-category-head" >';
					$ret .= '<div class="tourmaster-tour-category-head-display clearfix" >';
					$ret .= '<h3 class="tourmaster-tour-category-title" ' . tourmaster_esc_style(array(
						'font-size' => empty($settings['title-font-size'])? '': $settings['title-font-size'],
						'font-weight' => empty($settings['title-font-weight'])? '': $settings['title-font-weight'],
						'letter-spacing' => empty($settings['title-letter-spacing'])? '': $settings['title-letter-spacing'],
						'text-transform' => empty($settings['title-text-transform'])? '': $settings['title-text-transform'],
					)) . ' >';
					$ret .= $category->name;
					$ret .= '</h3>';
					$ret .= '</div>'; // tourmaster-tour-category-head-display

					$ret .= '<div class="tourmaster-tour-category-head-animate" >';
					$ret .= '<a class="tourmaster-tour-category-head-link" href="' . esc_url(get_term_link($category->term_id, $taxonomy)) . '" ' . tourmaster_esc_style(array(
						'font-size' => empty($settings['read-more-font-size'])? '': $settings['read-more-font-size'],
						'font-weight' => empty($settings['read-more-font-weight'])? '': $settings['read-more-font-weight'],
						'letter-spacing' => empty($settings['read-more-letter-spacing'])? '': $settings['read-more-letter-spacing'],
						'text-transform' => empty($settings['read-more-text-transform'])? '': $settings['read-more-text-transform'],
					)) . ' >';
					$ret .= esc_html__('View all tours', 'tourmaster');
					$ret .= '</a>';
					$ret .= '</div>'; // tourmaster-tour-category-head-animate
					$ret .= '</div>'; // tourmaster-tour-category-head
					
					$ret .= '<div class="tourmaster-tour-category-head-divider" ></div>';
					
					$ret .= '</div>'; // tourmaster-tour-category-item-wrap
					$ret .= '</div>'; // tourmaster-tour-category-grid
				}

				return $ret;
			}

			static function get_category_grid3( $categories, $settings, $taxonomy ){

				$column_sum = 0;
				$column_size = empty($settings['column-size'])? 20: $settings['column-size'];
				
				if( !empty($settings['with-feature']) && $settings['with-feature'] == 'enable' ){
					if( $column_size != 60 ){
						$column_size_feature = ($column_size * 2);
					}
				}
				
				$ret = '';

				foreach( $categories as $category ){
					if( !empty($column_size_feature) ){
						$c_size = $column_size_feature;
						$thumbnail_size = empty($settings['feature-thumbnail-size'])? 'full': $settings['feature-thumbnail-size'];
						$excerpt = empty($settings['feature-excerpt'])? '': $settings['feature-excerpt'];
						$excerpt_number = empty($settings['feature-excerpt-number'])? '': $settings['feature-excerpt-number'];
						$column_size_feature = 0;
					}else{
						$c_size = $column_size;
						$thumbnail_size = $settings['thumbnail-size'];
						$excerpt = empty($settings['excerpt'])? '': $settings['excerpt'];
						$excerpt_number = empty($settings['excerpt-number'])? '': $settings['excerpt-number'];
					}

					$additional_class  = ' tourmaster-item-pdlr tourmaster-item-mgb';
					if( !empty($c_size) ){
						$additional_class .= ' tourmaster-column-' . $c_size;
					}

					if( $column_sum == 0 || $column_sum + intval($c_size) > 60 ){
						$column_sum = intval($c_size);
						$additional_class .= ' tourmaster-column-first';
					}else{
						$column_sum += intval($c_size);
					}

					$thumbnail = get_term_meta($category->term_id, 'thumbnail', true);
					if( !empty($thumbnail) ){
						$additional_class .= ' tourmaster-with-thumbnail';
					}
					
					$ret .= '<div class="tourmaster-tour-category-grid-3 tourmaster-item-list ' . esc_attr($additional_class) . '" >';
					$ret .= '<div class="tourmaster-tour-category-item-wrap" ' . tourmaster_esc_style(array(
						'border-radius' => empty($settings['border-radius'])? '': $settings['border-radius']
					)) . ' >';
					if( !empty($thumbnail) ){
						$ret .= '<div class="tourmaster-tour-category-thumbnail tourmaster-media-image" >';
						$ret .= tourmaster_get_image($thumbnail, $thumbnail_size);
						$ret .= '</div>';
						$ret .= '<div class="tourmaster-tour-category-count" >';
						if( $category->count <= 1 ){
							$ret .= sprintf(esc_html__('%d tour', 'tourmaster'), $category->count);
						}else{
							$ret .= sprintf(esc_html__('%d tours', 'tourmaster'), $category->count);
						}
						$ret .= '</div>'; // tourmaster-tour-category-count
						$ret .= '<div class="tourmaster-tour-category-overlay" ></div>';
						$ret .= '<div class="tourmaster-tour-category-overlay-front" ></div>';
					}
					
					$ret .= '<div class="tourmaster-tour-category-head" >';
					$ret .= '<div class="tourmaster-tour-category-head-display clearfix" >';
					$ret .= '<h3 class="tourmaster-tour-category-title" ' . tourmaster_esc_style(array(
						'font-size' => empty($settings['title-font-size'])? '': $settings['title-font-size'],
						'font-weight' => empty($settings['title-font-weight'])? '': $settings['title-font-weight'],
						'letter-spacing' => empty($settings['title-letter-spacing'])? '': $settings['title-letter-spacing'],
						'text-transform' => empty($settings['title-text-transform'])? '': $settings['title-text-transform'],
					)) . ' >';
					$ret .= $category->name;
					$ret .= '</h3>';
					$ret .= '</div>'; // tourmaster-tour-category-head-display

					$ret .= '<div class="tourmaster-tour-category-head-animate" >';
					if( $excerpt == 'specify-number' ){
						if( !empty($excerpt_number) ){
							$ret .= '<div class="tourmaster-tour-category-description" >';
							$ret .= wp_trim_words($category->description, $excerpt_number);
							$ret .= '</div>';
						}
					}

					$ret .= '<a class="tourmaster-tour-category-head-link" href="' . esc_url(get_term_link($category->term_id, $taxonomy)) . '" ' . tourmaster_esc_style(array(
						'font-size' => empty($settings['read-more-font-size'])? '': $settings['read-more-font-size'],
						'font-weight' => empty($settings['read-more-font-weight'])? '': $settings['read-more-font-weight'],
						'letter-spacing' => empty($settings['read-more-letter-spacing'])? '': $settings['read-more-letter-spacing'],
						'text-transform' => empty($settings['read-more-text-transform'])? '': $settings['read-more-text-transform'],
					)) . ' >';
					$ret .= esc_html__('View all tours', 'tourmaster');
					$ret .= '</a>';
					$ret .= '</div>'; // tourmaster-tour-category-head-animate
					$ret .= '</div>'; // tourmaster-tour-category-head
					
					$ret .= '</div>'; // tourmaster-tour-category-item-wrap
					$ret .= '</div>'; // tourmaster-tour-category-grid
				}

				return $ret;
			}

			static function get_category_grid4( $categories, $settings, $taxonomy ){

				$ret = '';

				$count = 0;
				foreach( $categories as $category ){ $count++;

					if( $count % 6 == 1 ){
						$ret .= '<div class="gdlr-core-column-20 gdlr-core-column-first" >';
					}else if( $count % 6 == 3 ){
						$ret .= '<div class="gdlr-core-column-40" >';
					}else if( $count % 6 == 4 ){
						$ret .= '<div class="gdlr-core-column-40 gdlr-core-column-first" >';
					}else if( $count % 6 == 5 ){
						$ret .= '<div class="gdlr-core-column-20" >';
					}

					$thumbnail_size = $settings['thumbnail-size'];
					$additional_class  = ' tourmaster-item-pdlr tourmaster-item-mgb';

					$thumbnail = get_term_meta($category->term_id, 'thumbnail', true);
					if( !empty($thumbnail) ){
						$additional_class .= ' tourmaster-with-thumbnail';
					}
					
					$ret .= '<div class="tourmaster-tour-category-grid-4 tourmaster-item-list ' . esc_attr($additional_class) . '" >';
					$ret .= '<div class="tourmaster-tour-category-item-wrap" ' . tourmaster_esc_style(array(
						'border-radius' => empty($settings['border-radius'])? '': $settings['border-radius']
					)) . ' >';
					if( !empty($thumbnail) ){
						$ret .= '<div class="tourmaster-tour-category-thumbnail tourmaster-media-image" >';
						$ret .= '<a href="' . esc_url(get_term_link($category->term_id, $taxonomy)) . '" >';
						$ret .= tourmaster_get_image($thumbnail, $thumbnail_size);
						$ret .= '</a>';
						$ret .= '</div>';

						$ret .= '<div class="tourmaster-tour-category-overlay" ></div>';
						$ret .= '<div class="tourmaster-tour-category-overlay-front" ></div>';
					}
					
					$ret .= '<div class="tourmaster-tour-category-head" >';
					$ret .= '<h3 class="tourmaster-tour-category-title" ' . tourmaster_esc_style(array(
						'font-size' => empty($settings['title-font-size'])? '': $settings['title-font-size'],
						'font-weight' => empty($settings['title-font-weight'])? '': $settings['title-font-weight'],
						'letter-spacing' => empty($settings['title-letter-spacing'])? '': $settings['title-letter-spacing'],
						'text-transform' => empty($settings['title-text-transform'])? '': $settings['title-text-transform'],
					)) . ' >';
					$ret .= $category->name;
					$ret .= '</h3>';
					$ret .= '</div>'; // tourmaster-tour-category-head
					
					$ret .= '</div>'; // tourmaster-tour-category-item-wrap
					$ret .= '</div>'; // tourmaster-tour-category-grid

					if( in_array($count % 6, array(0, 2, 3, 4)) ){
						$ret .= '</div>';
					}
				}

				if( in_array($count % 6, array(1, 5)) ){
					$ret .= '</div>';
				}

				return $ret;
			}

			static function get_category_grid5( $categories, $settings, $taxonomy ){

				if( !empty($settings['layout']) && $settings['layout'] == 'carousel' ){
					return self::get_category_grid5_carousel($categories, $settings, $taxonomy);
				}

				$column_sum = 0;
				$column_size = empty($settings['column-size'])? 20: $settings['column-size'];

				$ret = '';

				foreach( $categories as $category ){
					$additional_class  = ' tourmaster-item-pdlr tourmaster-item-mgb';
					if( !empty($column_size) ){
						$additional_class .= ' tourmaster-column-' . $column_size;
					}

					if( $column_sum == 0 || $column_sum + intval($column_size) > 60 ){
						$column_sum = intval($column_size);
						$additional_class .= ' tourmaster-column-first';
					}else{
						$column_sum += intval($column_size);
					}

					$thumbnail = get_term_meta($category->term_id, 'thumbnail', true);
					if( !empty($thumbnail) ){
						$additional_class .= ' tourmaster-with-thumbnail';
					}
					
					$ret .= '<div class="tourmaster-tour-category-grid-5 tourmaster-item-list ' . esc_attr($additional_class) . '" >';
					$ret .= '<div class="tourmaster-tour-category-item-wrap" ' . tourmaster_esc_style(array(
						'border-radius' => empty($settings['border-radius'])? '': $settings['border-radius']
					)) . ' >';
					if( !empty($thumbnail) ){
						$ret .= '<div class="tourmaster-tour-category-thumbnail tourmaster-media-image" >';
						$ret .= tourmaster_get_image($thumbnail, $settings['thumbnail-size']);
						$ret .= '</div>';
						$ret .= '<div class="tourmaster-tour-category-overlay" ></div>';
						$ret .= '<div class="tourmaster-tour-category-overlay-front" ></div>';
					}
					
					$ret .= '<div class="tourmaster-tour-category-head" >';
					$ret .= '<div class="tourmaster-tour-category-head-display clearfix" >';
					$ret .= '<h3 class="tourmaster-tour-category-title" ' . tourmaster_esc_style(array(
						'font-size' => empty($settings['title-font-size'])? '': $settings['title-font-size'],
						'font-weight' => empty($settings['title-font-weight'])? '': $settings['title-font-weight'],
						'letter-spacing' => empty($settings['title-letter-spacing'])? '': $settings['title-letter-spacing'],
						'text-transform' => empty($settings['title-text-transform'])? '': $settings['title-text-transform'],
					)) . ' >';
					$ret .= '<a class="tourmaster-tour-category-head-link" href="' . esc_url(get_term_link($category->term_id, $taxonomy)) . '" >';
					$ret .= $category->name;
					$ret .= '</a>';
					$ret .= '</h3>';
					$ret .= '</div>'; // tourmaster-tour-category-head-display

					$ret .= '<div class="tourmaster-tour-category-head-animate" >';
					$ret .= '<a class="tourmaster-tour-category-head-link" href="' . esc_url(get_term_link($category->term_id, $taxonomy)) . '" ' . tourmaster_esc_style(array(
						'font-size' => empty($settings['read-more-font-size'])? '': $settings['read-more-font-size'],
						'font-weight' => empty($settings['read-more-font-weight'])? '': $settings['read-more-font-weight'],
						'letter-spacing' => empty($settings['read-more-letter-spacing'])? '': $settings['read-more-letter-spacing'],
						'text-transform' => empty($settings['read-more-text-transform'])? '': $settings['read-more-text-transform'],
					)) . ' >';
					if( $category->count <= 1 ){
						$ret .= sprintf(esc_html__('%d tour', 'tourmaster'), $category->count);
					}else{
						$ret .= sprintf(esc_html__('%d tours', 'tourmaster'), $category->count);
					}
					$ret .= '</a>';
					$ret .= '</div>'; // tourmaster-tour-category-count
					$ret .= '</div>'; // tourmaster-tour-category-head
					
					$ret .= '</div>'; // tourmaster-tour-category-item-wrap
					$ret .= '</div>'; // tourmaster-tour-category-grid
				}

				return $ret;
			}

			static function get_flex_atts($settings){

				$settings['column-size'] = empty($settings['column-size'])? 20: $settings['column-size'];

				$flex_atts = array(
					'carousel' => true,
					'column' => 60 / intval($settings['column-size']),
					'move' => empty($settings['carousel-scrolling-item-amount'])? '': $settings['carousel-scrolling-item-amount'],
					'navigation' => empty($settings['carousel-navigation'])? 'navigation': $settings['carousel-navigation'],
					'navigation-align' => empty($settings['carousel-navigation-align'])? '': $settings['carousel-navigation-align'],
					'navigation-size' => empty($settings['carousel-navigation-size'])? '': $settings['carousel-navigation-size'],
					'navigation-icon-color' => empty($settings['carousel-navigation-icon-color'])? '': $settings['carousel-navigation-icon-color'],
					'navigation-icon-background' => empty($settings['carousel-navigation-icon-bg'])? '': $settings['carousel-navigation-icon-bg'],
					'navigation-icon-padding' => empty($settings['carousel-navigation-icon-padding'])? '': $settings['carousel-navigation-icon-padding'],
					'navigation-icon-radius' => empty($settings['carousel-navigation-icon-radius'])? '': $settings['carousel-navigation-icon-radius'],
					'navigation-margin' => empty($settings['carousel-navigation-margin'])? '': $settings['carousel-navigation-margin'],
					'navigation-side-margin' => empty($settings['carousel-navigation-side-margin'])? '': $settings['carousel-navigation-side-margin'],
					'navigation-icon-margin' => empty($settings['carousel-navigation-icon-margin'])? '': $settings['carousel-navigation-icon-margin'],
					'navigation-left-icon' => empty($settings['carousel-navigation-left-icon'])? '': $settings['carousel-navigation-left-icon'],
					'navigation-right-icon' => empty($settings['carousel-navigation-right-icon'])? '': $settings['carousel-navigation-right-icon'],
					'bullet-style' => empty($settings['carousel-bullet-style'])? '': $settings['carousel-bullet-style'],
					'controls-top-margin' => empty($settings['carousel-bullet-top-margin'])? '': $settings['carousel-bullet-top-margin'],
					'nav-parent' => 'tourmaster-tour-item',
					'disable-autoslide' => (empty($settings['carousel-autoslide']) || $settings['carousel-autoslide'] == 'enable')? '': true,
					'mglr' => true,
				);

				if( in_array($flex_atts['navigation'], array('navigation', 'both')) ){
					$flex_atts['vcenter-nav'] = true;
					$flex_atts['additional-class'] = 'tourmaster-nav-style-rect';
				}else if( $flex_atts['navigation'] == 'navigation-outer' && empty($flex_atts['navigation-left-icon']) && empty($flex_atts['navigation-right-icon']) ){
					$flex_atts['navigation-old'] = true;
				}

				return $flex_atts;
			}

			static function get_category_grid5_carousel( $categories, $settings, $taxonomy ){

				$slides = array();
				$flex_atts = self::get_flex_atts($settings);

				foreach( $categories as $category ){
					$additional_class  = '';

					$thumbnail = get_term_meta($category->term_id, 'thumbnail', true);
					if( !empty($thumbnail) ){
						$additional_class .= ' tourmaster-with-thumbnail';
					}
					
					$ret  = '<div class="tourmaster-tour-category-grid-5 tourmaster-item-list ' . esc_attr($additional_class) . '" >';
					$ret .= '<div class="tourmaster-tour-category-item-wrap" ' . tourmaster_esc_style(array(
						'border-radius' => empty($settings['border-radius'])? '': $settings['border-radius']
					)) . ' >';
					if( !empty($thumbnail) ){
						$ret .= '<div class="tourmaster-tour-category-thumbnail tourmaster-media-image" >';
						$ret .= tourmaster_get_image($thumbnail, $settings['thumbnail-size']);
						$ret .= '</div>';
						$ret .= '<div class="tourmaster-tour-category-overlay" ></div>';
						$ret .= '<div class="tourmaster-tour-category-overlay-front" ></div>';
					}
					
					$ret .= '<div class="tourmaster-tour-category-head" >';
					$ret .= '<div class="tourmaster-tour-category-head-display clearfix" >';
					$ret .= '<h3 class="tourmaster-tour-category-title" ' . tourmaster_esc_style(array(
						'font-size' => empty($settings['title-font-size'])? '': $settings['title-font-size'],
						'font-weight' => empty($settings['title-font-weight'])? '': $settings['title-font-weight'],
						'letter-spacing' => empty($settings['title-letter-spacing'])? '': $settings['title-letter-spacing'],
						'text-transform' => empty($settings['title-text-transform'])? '': $settings['title-text-transform'],
					)) . ' >';
					$ret .= '<a class="tourmaster-tour-category-head-link" href="' . esc_url(get_term_link($category->term_id, $taxonomy)) . '" >';
					$ret .= $category->name;
					$ret .= '</a>';
					$ret .= '</h3>';
					$ret .= '</div>'; // tourmaster-tour-category-head-display

					$ret .= '<div class="tourmaster-tour-category-head-animate" >';
					$ret .= '<a class="tourmaster-tour-category-head-link" href="' . esc_url(get_term_link($category->term_id, $taxonomy)) . '" ' . tourmaster_esc_style(array(
						'font-size' => empty($settings['read-more-font-size'])? '': $settings['read-more-font-size'],
						'font-weight' => empty($settings['read-more-font-weight'])? '': $settings['read-more-font-weight'],
						'letter-spacing' => empty($settings['read-more-letter-spacing'])? '': $settings['read-more-letter-spacing'],
						'text-transform' => empty($settings['read-more-text-transform'])? '': $settings['read-more-text-transform'],
					)) . ' >';
					if( $category->count <= 1 ){
						$ret .= sprintf(esc_html__('%d tour', 'tourmaster'), $category->count);
					}else{
						$ret .= sprintf(esc_html__('%d tours', 'tourmaster'), $category->count);
					}
					$ret .= '</a>';
					$ret .= '</div>'; // tourmaster-tour-category-count
					$ret .= '</div>'; // tourmaster-tour-category-head
					
					$ret .= '</div>'; // tourmaster-tour-category-item-wrap
					$ret .= '</div>'; // tourmaster-tour-category-grid

					$slides[] = $ret;
				}

				return tourmaster_get_flexslider($slides, $flex_atts);
			}

			static function get_category_widget( $categories, $settings, $taxonomy ){

				$column_sum = 0;
				$column_size = empty($settings['column-size'])? 20: $settings['column-size'];

				$ret  = '<div class="tourmaster-tour-category-widget-holder clearfix" >';
				foreach( $categories as $category ){
					$additional_class  = '';
					if( !empty($column_size) ){
						$additional_class .= ' tourmaster-column-' . $column_size;
					}

					if( $column_sum == 0 || $column_sum + intval($column_size) > 60 ){
						$column_sum = intval($column_size);
						$additional_class .= ' tourmaster-column-first';
					}else{
						$column_sum += intval($column_size);
					}

					$thumbnail = get_term_meta($category->term_id, 'thumbnail', true);
					if( !empty($thumbnail) ){
						$additional_class .= ' tourmaster-with-thumbnail';
					}
					
					$ret .= '<div class="tourmaster-tour-category-widget tourmaster-item-list ' . esc_attr($additional_class) . '" ' . tourmaster_esc_style(array(
						'border-radius' => empty($settings['border-radius'])? '': $settings['border-radius']
					)) . ' >';
					if( !empty($thumbnail) ){
						$ret .= '<div class="tourmaster-tour-category-thumbnail tourmaster-media-image"  >';
						$ret .= tourmaster_get_image($thumbnail, $settings['thumbnail-size']);
						$ret .= '</div>';
						$ret .= '<div class="tourmaster-tour-category-overlay" ></div>';
					}
					
					$ret .= '<div class="tourmaster-tour-category-head" >';
					$ret .= '<div class="tourmaster-tour-category-head-table" >';
					$ret .= '<h3 class="tourmaster-tour-category-title" ' . tourmaster_esc_style(array(
						'font-size' => empty($settings['title-font-size'])? '': $settings['title-font-size'],
						'font-weight' => empty($settings['title-font-weight'])? '': $settings['title-font-weight'],
						'letter-spacing' => empty($settings['title-letter-spacing'])? '': $settings['title-letter-spacing'],
						'text-transform' => empty($settings['title-text-transform'])? '': $settings['title-text-transform'],
					)) . ' >';
					$ret .= '<a class="tourmaster-tour-category-head-link" href="' . esc_url(get_term_link($category->term_id, $taxonomy)) . '" >';
					$ret .= $category->name;
					$ret .= '</a>';
					$ret .= '</h3>';
					$ret .= '</div>'; // tourmaster-tour-category-head-table
					$ret .= '</div>'; // tourmaster-tour-category-head
					$ret .= '</div>'; // tourmaster-tour-category-widget
				}
				$ret .= '</div>'; // tourmaster-tour-category-widget-holder

				return $ret;
			}

		} // tourmaster_pb_element_tour
	} // class_exists	

	add_shortcode('tourmaster_tour_category', 'tourmaster_tour_category_shortcode');
	if( !function_exists('tourmaster_tour_category_shortcode') ){
		function tourmaster_tour_category_shortcode($atts){
			$atts = wp_parse_args($atts, array());
			$atts['column-size'] = empty($atts['column-size'])? 60: 60 / intval($atts['column-size']); 
			
			$ret  = '<div class="tourmaster-tour-category-shortcode clearfix tourmaster-item-rvpdlr" >';
			$ret .= tourmaster_pb_element_tour_category::get_content($atts);
			$ret .= '</div>';

			return $ret;
		}
	}