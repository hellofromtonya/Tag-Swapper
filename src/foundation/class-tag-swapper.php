<?php
/**
 * Tag swapper handler - swaps out the specified tag by the attribute and value pair.
 *
 * @package     Tag_Swapper\Foundation
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */
namespace Tag_Swapper\Foundation;

use DOMDocument;
use DOMElement;

class Tag_Swapper {

	protected $config = array();

	protected $default_config = array(
		'old_tag'          => '',
		'new_tag'          => '',
		'search_attribute' => 'class',
		'attribute_value'  => '',
	);

	protected $attributes = array(
		'id',
		'class',
		'style',
	);

	/**
	 * Instantiate the swapper
	 *
	 * @since 1.0.0
	 *
	 * @param array $config
	 * @param array $attributes
	 */
	public function __construct( array $config, array $attributes = array() ) {
		$this->config = wp_parse_args( $config, $this->default_config );

		if ( $attributes ) {
			$this->attributes = $attributes;
		}
	}

	/**
	 * Replace the HTML element by the pattern.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html
	 *
	 * @return string
	 */
	public function swap( $html ) {

		$wrapped_html = '<body>' . $html . '</body>';

		$document = new DOMDocument();
		$document->loadHTML( $wrapped_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );

		$elements = $document->getElementsByTagName( $this->config['old_tag'] );
		if ( $elements->length == 0 ) {
			return $html;
		}

		foreach ( $elements as $element ) {
			$search_attribute = $element->getAttribute( $this->config['search_attribute'] );

			if ( strpos( $search_attribute, $this->config['attribute_value'] ) === false ) {
				continue;
			}

			$new_element = $this->build_replacement_element( $document, $element, $this->config );

			$element->parentNode->replaceChild( $new_element, $element );
		}

		$html = $document->saveHTML();

		return $this->strip_wrappers_from_html( $html );
	}

	/**
	 * Strip out the wrapper (needed for proper swapping of the tag nodes).
	 *
	 * @since 1.0.0
	 *
	 * @param string $html
	 *
	 * @return string
	 */
	protected function strip_wrappers_from_html( $html ) {
		$html = substr( $html, 0, - 8 );

		return substr( $html, 6 );
	}

	/**
	 * Build the replacement element.
	 *
	 * @since 1.0.0
	 *
	 * @param DOMDocument $document
	 * @param DOMElement $old_element
	 *
	 * @return DOMElement
	 */
	protected function build_replacement_element( DOMDocument $document, DOMElement $old_element ) {

		$new_element = $document->createElement( $this->config['new_tag'] );

		foreach ( $this->attributes as $attribute ) {
			if ( $old_element->hasAttribute( $attribute ) ) {
				$new_element->setAttribute( $attribute, $old_element->getAttribute( $attribute ) );
			}
		}

		$new_element->nodeValue = $old_element->nodeValue;

		return $new_element;
	}
}
