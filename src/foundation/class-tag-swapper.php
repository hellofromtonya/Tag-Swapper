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

	/**
	 * Flags if the content has changed
	 *
	 * @var bool
	 */
	protected $content_changed = false;

	/**
	 * Configuration array
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Instance of the DOMDocument
	 *
	 * @var DOMDocument
	 */
	protected $document;

	/**
	 * Array of valid HTML attributes
	 *
	 * @var array
	 */
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
	 * @param array $config Runtime configuration parameters
	 * @param array $attributes
	 */
	public function __construct( array $config, array $attributes = array() ) {
		$this->config = $config;

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

		$elements = $this->get_html_elements( $html );
		if ( ! $elements ) {
			return false;
		}

		$this->content_changed = false;

		foreach ( $elements as $element ) {
			$search_attribute = $element->getAttribute( $this->config['search_attribute'] );

			if ( ! $this->search_attribute_found( $search_attribute ) ) {
				continue;
			}

			$new_element = $this->build_replacement_element( $element, $this->config );

			$element->parentNode->replaceChild( $new_element, $element );

			$this->content_changed = true;
		}

		return $this->update_html( $html );
	}

	/**
	 * Updates the HTML to finalize the swap.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html
	 *
	 * @return bool|string
	 */
	protected function update_html( $html ) {
		if ( ! $this->content_changed ) {
			return false;
		}

		$html = $this->document->saveHTML();

		$this->document = null;

		return $this->strip_wrappers_from_html( $html );
	}

	/**
	 * Checks if this node has the search attribute and its value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $search_attribute
	 *
	 * @return bool
	 */
	protected function search_attribute_found( $search_attribute ) {
		return $search_attribute && strpos( $search_attribute, $this->config['attribute_value'] ) !== false;
	}

	/**
	 * Loads the DOM Document and fetches the elements by the (old) tag name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html
	 *
	 * @return bool|\DOMNodeList
	 */
	protected function get_html_elements( $html ) {
		$this->document = new DOMDocument();

		if ( $this->config['suppress_errors'] === true ) {
			libxml_use_internal_errors( true );
			@$this->document->loadHTML( $html, LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING );
			libxml_clear_errors();

		} else {
			$this->document->loadHTML( $html, LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING );
		}

		if ( empty( $this->document ) ) {
			return false;
		}

		$elements = $this->document->getElementsByTagName( $this->config['old_tag'] );

		return $elements->length == 0 ? false : $elements;
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
		$ending_tags_offset   = ( strlen( '</body></html>' ) + 1 ) * - 1;
		$starting_tags_offset = strlen( '<html><body>' );

		$html = substr( $html, 0, $ending_tags_offset );

		return substr( $html, $starting_tags_offset );
	}

	/**
	 * Build the replacement element.
	 *
	 * @since 1.0.0
	 *
	 * @param DOMElement $old_element
	 *
	 * @return DOMElement
	 */
	protected function build_replacement_element( DOMElement $old_element ) {
		$new_element = $this->document->createElement( $this->config['new_tag'] );

		foreach ( $this->attributes as $attribute ) {
			if ( $old_element->hasAttribute( $attribute ) ) {
				$new_element->setAttribute( $attribute, $old_element->getAttribute( $attribute ) );
			}
		}

		$new_element->nodeValue = $old_element->nodeValue;

		return $new_element;
	}
}
