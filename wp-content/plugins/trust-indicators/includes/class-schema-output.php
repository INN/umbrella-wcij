<?php
/**
 * Trust Indicators Schema Output.
 *
 * @since   1.0
 * @package Trust_Indicators
 */

/**
 * Trust Indicators Schema Output class.
 *
 * @since 1.0
 */
class Trust_Indicators_Schema_Output {
	/**
	 * Parent plugin class.
	 *
	 * @var    Trust_Indicators
	 * @since  1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  1.0
	 *
	 * @param  Trust_Indicators $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0
	 */
	public function hooks() {
		add_action( 'wp_head', array( $this, 'json_ld_output' ) );
	}

	/**
	 * Output to wp_head.
	 *
	 * @since  1.0
	 * @uses   $this->front_page_output
	 * @uses   $this->author_page_output
	 * @uses   $this->single_page_output
	 */
	public function json_ld_output() {
		global $post;

		/**
		 * By default, schema.org markup for the sitewide indicators will output only on the front page of the site.
		 * To override this, you can use the filter below to pass an array of post IDs, and/or the string 'front_page' where sitewide indicators should appear.
		 *
		 * Example: Output schema tags on post 26 only.
		 * add_filter( 'trust_indicators_sitewide_output_post', 'your_function_name' );
		 * function your_function_name( $var ) {
		 *   return array( 26 );
		 * }
		 *
		 * Example: Output schema tags on the front page and post 26.
		 * add_filter( 'trust_indicators_sitewide_output_post', 'your_function_name' );
		 * function your_function_name( $var ) {
		 *   return array( 'front_page', 26 );
		 * }
		 */
		$output_pages = apply_filters( 'trust_indicators_sitewide_output_post', array( 'front_page' ) );

		// Return if $output_pages was filtered and is no longer an array.
		if ( ! is_array( $output_pages ) ) {
			return;
		}

		// Determine what type of page we're on.
		if (
			( is_front_page() && in_array( 'front_page', $output_pages, true ) ) ||
			in_array( $post->ID, $output_pages, true )
		) {
			$page = 'front';
		} elseif ( is_single() ) {
			$page = 'single';
		} elseif ( is_author() ) {
			$page = 'author';
		} else {
			$page = '';
		}

		$output_fields = array(
			'@context' => 'http://schema.org',
			'@type' => $this->schema_type( $page ),
		);

		switch ( $page ) {
			case 'front':
				$output_fields = array_merge( $output_fields, $this->front_page_output( $post ) );
				break;
			case 'author':
				$output_fields = array_merge( $output_fields, $this->author_page_output( $post ) );
				break;
			case 'single':
				$output_fields = array_merge( $output_fields, $this->single_page_output( $post ) );
				break;
		}

		echo sprintf(
			'<script type="application/ld+json">' . "\n" . '%s' . "\n" . '</script>' . "\n",
			wp_json_encode( $output_fields, JSON_PRETTY_PRINT )
		);
	}

	/**
	 * LD+JSON fields for the front page
	 *
	 * @since 1.0
	 * @param WP_Post $post The global $post.
	 * @return Array a PHP representation of the LD+JSON output for the front page
	 */
	public function front_page_output( $post ) {
		$output_fields = array();
		$output_fields['name'] = apply_filters( 'trust_indicators_org_name', get_bloginfo( 'name' ) );
		$settings_fields = Trust_Indicators_Settings::get_fields();

		$output_fields['contactPoint'] = array();

		foreach ( $settings_fields as $section_key => $section ) {
			foreach ( $section['fields'] as $key => $field ) {

				$value = get_option( $key );
				if ( $value ) {
					if ( isset( $field['schema'] ) && ! empty( $field['schema'] ) ) {
						if ( is_array( $field['schema'] ) ) {
							$output_fields[ $field['schema']['parent'] ][ $field['schema']['key'] ] = $value;
						} else {
							if ( isset( $output_fields[ $field['schema'] ] ) ) {
								if ( is_array( $output_fields[ $field['schema'] ] ) ) {
									$output_fields[ $field['schema'] ][] = get_option( $key );
								} else {
									$output_fields[ $field['schema'] ] = array( $output_fields[ $field['schema'] ], $value );
								}
							} else {
								$output_fields[ $field['schema'] ] = $value;
							}
						}
					}
				}
			}
			if ( 'actionable_feedback' === $section_key ) {
				if ( '' !== $output_fields['contactPoint_feedback'] ) {
					$output_fields['contactPoint'][] = array_merge(
						array(
							'@type' => 'ContactPoint',
							'contactType' => 'Public Engagement',
						),
						$output_fields['contactPoint_feedback']
					);
					unset( $output_fields['contactPoint_feedback'] );
				}
			} elseif ( 'newsroom_contact_info' === $section_key ) {
				if ( '' !== $output_fields['contactPoint_newsroom'] ) {
					$output_fields['contactPoint'][] = array_merge(
						array(
							'@type' => 'ContactPoint',
							'contactType' => 'Newsroom Contact'
						),
						array(
							$output_fields['contactPoint_newsroom']
						)
					);
					unset( $output_fields['contactPoint_newsroom'] );
				}
			}
		}

		return $output_fields;
	}

	/**
	 * LD+JSON fields for an author page
	 *
	 * @since 1.0
	 * @param WP_Post $post The global $post.
	 * @return Array a PHP representation of the LD+JSON output for an author page
	 */
	public function author_page_output( $post ) {
		global $authordata;
		$output_fields = array();

		$output_fields['name'] = get_the_author_meta( 'display_name', $post->post_author );
		$output_fields['workLocation']['@type'] = 'Place';
		$output_fields['contactPoint']['@type'] = 'ContactPoint';
		$output_fields['contactPoint']['contactType'] = 'Journalist';

		$author_bio = get_the_author_meta( 'description', $authordata->ID );
		if ( ! empty( $author_bio ) ) {
			$output_fields['description'] = $author_bio;
		}

		$author_settings = Trust_Indicators_User_Settings::get_fields();

		foreach ( $author_settings as $field_key => $field ) {
			if ( ! isset( $field['schema'] ) || empty( $field['schema'] ) ) {
				continue;
			}

			if ( function_exists( 'get_user_attribute' ) ) {
				$value = get_user_attribute( get_the_author_meta( 'ID', $authordata->ID ), 'trust_indicators_' . $field_key );
			} else {
				//@codingStandardsIgnoreLine
				$value = get_user_meta( get_the_author_meta( 'ID', $authordata->ID ), $field_key, true );
			}

			if ( ! empty( $value ) ) {

				if ( is_array( $field['schema'] ) ) {
					if ( 'areas_of_expertise' === $field_key ) {
						$value_array = array_map( 'trim', explode( PHP_EOL, $value ) );
						foreach ( $value_array as $value_item ) {
							$output_fields[ $field['schema']['parent'] ][] = $value_item;
						}
					} elseif ( 'location_expertise' === $field_key ) {
						$value_array = array_map( 'trim', explode( PHP_EOL, $value ) );
						foreach ( $value_array as $value_item ) {
							$output_fields[ $field['schema']['parent'] ][] = array(
								'@type' => 'Place',
								$field['schema']['key'] => $value_item,
							);
						}
					} elseif ( 'languages_spoken' === $field_key ) {
						$value_array = array_map( 'trim', explode( ',', $value ) );
						foreach ( $value_array as $value_item ) {
							$output_fields[ $field['schema']['parent'] ][] = array(
								'@type' => 'Language',
								$field['schema']['key'] => $value_item,
							);
						}
					} else {
						$output_fields[ $field['schema']['parent'] ][ $field['schema']['key'] ] = $value;
					}
				} else {

					if ( isset( $output_fields[ $field['schema'] ] ) ) {

						if ( is_array( $output_fields[ $field['schema'] ] ) ) {
							$output_fields[ $field['schema'] ][] = $value;
						} else {
							$output_fields[ $field['schema'] ] = array( $output_fields[ $field['schema'] ], $value );
						}
					} else {
						$output_fields[ $field['schema'] ] = $value;
					}
				}
			}
		}
		return $output_fields;
	}

	/**
	 * LD+JSON fields for a single page
	 *
	 * @since 1.0
	 * @param WP_Post $post The global $post.
	 * @uses wp_extract_urls
	 * @return Array a PHP representation of the LD+JSON output for a single page
	 */
	public function single_page_output( $post ) {
		$article_settings = Trust_Indicators_Article_Settings::get_fields();
		$author_settings = Trust_Indicators_User_Settings::get_fields();
		$output_fields = array();

		foreach ( $article_settings as $meta_box_key => $meta_box ) {
			foreach ( $meta_box['fields'] as $field_key => $field ) {
				if ( isset( $field['schema'] ) && ! empty( $field['schema'] ) ) {
					$value = get_post_meta( $post->ID, $field_key, true );
					if ( ! empty( $value ) ) {

						if ( 'citation' === $field['schema'] ) {
							$urls = wp_extract_urls( $value );
							if ( ! empty( $urls ) ) {
								foreach ( $urls as $url ) {
									$output_fields['citation'][] = array(
										'@type' => 'CreativeWork',
										'url' => $url,
									);
								}
							}
						} elseif ( 'correction' === $field['schema'] ) {
							$output_fields[ $field['schema'] ] = array(
								'@type' => 'CorrectionComment',
								'text' => $value,
								// this value for the correction date is approximate,
								// see https://github.com/INN/trust-indicators/pull/83 .
								'datePublished' => get_the_modified_time( 'c' ),
							);
						} else {
							$output_fields[ $field['schema'] ] = $value;
						}
					}
				}
			}
		}

		// publishingPrinciples only appears on posts.
		$publishing_principles = get_option( 'publishing_principles' );
		if ( $publishing_principles ) {
			$output_fields['publishingPrinciples'] = $publishing_principles;
		}

		$post_thumbnail = get_the_post_thumbnail_url( $post );
		if ( $post_thumbnail ) {
			$output_fields['image'] = $post_thumbnail;
		}

		$output_fields['headline'] = $post->post_title;
		$output_fields['datePublished'] = $post->post_date;
		$output_fields['author']['@type'] = 'Person';
		$output_fields['author']['workLocation']['@type'] = 'Place';
		$output_fields['author']['contactPoint']['@type'] = 'ContactPoint';
		$output_fields['author']['contactPoint']['contactType'] = 'Journalist';
		$output_fields['author']['sameAs'][] = get_author_posts_url( $post->post_author );

		/*
		 * Author information
		 */
		$output_fields['author']['name'] = get_the_author_meta( 'display_name', $post->post_author );

		$author_bio = get_the_author_meta( 'description', $post->post_author );
		if ( ! empty( $author_bio ) ) {
			$output_fields['author']['description'] = $author_bio;
		}

		foreach ( $author_settings as $field_key => $field ) {

			// If we have a defined schema for this data:
			if ( isset( $field['schema'] ) && ! empty( $field['schema'] ) ) {

				if ( function_exists( 'get_user_attribute' ) ) {
					$value = get_user_attribute( get_the_author_meta( 'ID', $post->post_author ), 'trust_indicators_' . $field_key );
				} else {
					//@codingStandardsIgnoreLine
					$value = get_user_meta( get_the_author_meta( 'ID', $post->post_author ), $field_key, true );
				}

				// If we have data for this schema:
				if ( ! empty( $value ) ) {

					// If the schema is an array:
					if ( is_array( $field['schema'] ) ) {

						if ( 'areas_of_expertise' === $field_key ) {
							$value_array = array_map( 'trim', explode( PHP_EOL, $value ) );
							foreach ( $value_array as $value_item ) {
								$output_fields['author'][ $field['schema']['parent'] ][] = $value_item;
							}
						} elseif ( 'location_expertise' === $field_key ) {
							$value_array = array_map( 'trim', explode( PHP_EOL, $value ) );
							foreach ( $value_array as $value_item ) {
								$output_fields['author'][ $field['schema']['parent'] ][] = array(
									'@type' => 'Place',
									$field['schema']['key'] => $value_item,
								);
							}
						} elseif ( 'languages_spoken' === $field_key ) {
							$value_array = array_map( 'trim', explode( ',', $value ) );
							foreach ( $value_array as $value_item ) {
								$output_fields[ $field['schema']['parent'] ][] = array(
									'@type' => 'Language',
									$field['schema']['key'] => $value_item,
								);
							}
						} else {
							$output_fields['author'][ $field['schema']['parent'] ][ $field['schema']['key'] ] = $value;
						}

					} else {

						// If the schema is already defined:
						if ( isset( $output_fields['author'][ $field['schema'] ] ) ) {

							// If existing output is an array, add to it. Otherwise, create an array containing the existing value.
							if ( is_array( $output_fields['author'][ $field['schema'] ] ) ) {
								$output_fields['author'][ $field['schema'] ][] = $value;
							} else {
								$output_fields['author'][ $field['schema'] ] = array( $output_fields['author'][ $field['schema'] ], $value );
							}

						} else {
							$output_fields['author'][ $field['schema'] ] = $value;
						}
					}
				}
			}
		}
		return $output_fields;
	}

	/**
	 * Output shema @type variable
	 *
	 * @since 1.0
	 * @param String $page The type of page we're viewing
	 */
	public function schema_type( $page ) {

		if ( 'front' === $page ) {

			return apply_filters( 'trust_indicators_org_type', 'NewsMediaOrganization' );

		} elseif ( 'author' === $page ) {

			return apply_filters( 'trust_indicators_author_type', 'Person' );

		} elseif ( 'single' === $page ) {

			// Get the type of work tax terms associated with this post.
			global $post;
			$type_of_work_terms = wp_get_post_terms( $post->ID, 'type-of-work', array( 'fields' => 'ids' ) );

			$type = array();

			// Check for the schema defined as term meta. If it exists, then add the schema.
			if ( is_array( $type_of_work_terms ) && ! empty( $type_of_work_terms ) ) {
				foreach ( $type_of_work_terms as $type_of_work ) {
					$term_schema = get_term_meta( $type_of_work, 'schema', true );
					if ( $term_schema ) {
						$type[] = $term_schema;
					}
				}
				return $type;
			}
		}
	}
}
