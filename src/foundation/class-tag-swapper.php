<?php
/**
 * Tag swapper handler - swaps out the specified tag by the attribute and value pair.
 *
 * @package     Tag_Swapper\Foundation
 * @since       1.0.2
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */
namespace Tag_Swapper\Foundation;

use DOMDocument;
use DOMElement;
use DOMNodeList;

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
	 * Tracks the total number of tag swaps
	 *
	 * @var int
	 */
	public $number_of_swaps = 0;

	/**
	 * Limit the swap to only one occurrence
	 * (handy for limiting `h1` tag)
	 *
	 * @var bool
	 */
	protected $limit_to_one_tag_swap = false;

	/**************************
	 * Instantiate & Initialize
	 *************************/

	/**
	 * Instantiate the swapper
	 *
	 * @since 1.0.2
	 *
	 * @param array $config Runtime configuration parameters
	 * @param array $attributes
	 */
	public function __construct( array $config, array $attributes = array() ) {
		$this->config = $config;

		if ( $attributes ) {
			$this->attributes = $attributes;
		}

		if ( $config['new_tag'] == 'h1' ) {
			$this->limit_to_one_tag_swap = true;
		}
	}

	/**
	 * Replace the HTML element by the pattern.
	 *
	 * @since 1.0.2
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
			if ( ! $this->is_ok_to_swap_tag( $element ) ) {
				continue;
			}

			if ( $this->limit_to_one_tag_swap && $this->content_changed ) {
				break;
			}

			$new_element = $this->build_replacement_element( $element );

			$element->parentNode->replaceChild( $new_element, $element );

			$this->update_stats_upon_tag_swap();
		}

		return $this->update_html( $html );
	}

	/**************************
	 * Worker Methods
	 *************************/

	/**
	 * Checks if the tag should occur.  It will not occur if:
	 *
	 *      1. The tag is not the same as the one to be replaced
	 *      2. The attribute value is not what we are looking for
	 *
	 * @since 1.0.2
	 *
	 * @param DOMElement $element
	 *
	 * @return bool
	 */
	protected function is_ok_to_swap_tag( DOMElement $element ) {
		if ( $element->nodeName !== $this->config['old_tag'] ) {
			return false;
		}

		$search_attribute_value = $element->getAttribute( $this->config['search_attribute'] );
		return $this->search_attribute_found( $search_attribute_value );
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
	 * Checks if this node's attribute value is a match.  To make sure we have the exact attribute value
	 * and not just a substring of it (e.g. headline vs. headline1), this method will do the following
	 * checks:
	 *
	 *      1. Does the value contain the one being searched for?
	 *      2. Next we check for an exact match.
	 *
	 * @since 1.0.1
	 *
	 * @param string $search_attribute_value
	 *
	 * @return bool
	 */
	protected function search_attribute_found( $search_attribute_value ) {
		if ( ! $search_attribute_value ) {
			return false;
		}

		$found = strpos( $search_attribute_value, $this->config['attribute_value'] ) !== false;

		if ( ! $found ) {
			return false;
		}

		return $this->search_attribute_value_is_exact_match( $search_attribute_value );
	}

	/**
	 * Checks if the value is an exact match for the one being searched for.  How does it
	 * determine if it's an exact match?
	 *
	 *      1. Separate out the values by words (as an attribute can have multiple values).
	 *      2. Iterate through the values and check that the value and data type are a match.
	 *         If yes, then it found a match; return true.
	 *
	 * @since 1.0.1
	 *
	 * @param string $search_attribute_value
	 *
	 * @return bool
	 */
	protected function search_attribute_value_is_exact_match( $search_attribute_value ) {
		$attribute_values = explode( ' ', trim( $search_attribute_value ) );

		foreach( $attribute_values as $attribute ) {
			if ( $attribute === $this->config['attribute_value'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Loads the DOM Document and fetches the elements by the (old) tag name.
	 *
	 * @since 1.0.2
	 *
	 * @param string $html
	 *
	 * @return bool|DOMNodeList
	 */
	protected function get_html_elements( $html ) {
		$this->document = new DOMDocument();
		$this->document->strictErrorChecking = false;

		if ( $this->config['suppress_errors'] === true ) {
			libxml_use_internal_errors( true );
			@$this->document->loadHTML( $html, LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING );
			libxml_clear_errors();

		} else {
			$this->document->loadHTML( $html, LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING );
		}

		$this->document->normalizeDocument();

		if ( empty( $this->document ) ) {
			return false;
		}

		/**
		 * version 1.0.2 Bug fix - When the HTML is invalid, it will not fetch the tags after the invalid warning
		 * occurs.  This means that any remaining tags within the record's content will not be swapped.  That's not
		 * good, as the site owner or developer does not know s/he has a HTML markup issue or that the swap did not
		 * occur after that point.
		 *
		 * Pulling all of the tag nodes fixes this problem.  However, it causes more processing time, as we have to
		 * loop through all of the nodes instead of just the specific ones.
		 *
		 * TODO-Tonya: We will need to test if this is a problem on HUGE databases, i.e. over 5k records.
		 */
		$elements = $this->document->getElementsByTagName( '*' );
//		$elements = $this->document->getElementsByTagName( $this->config['old_tag'] );

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

	/**
	 * Updates the stats when a tag swap occurs.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	protected function update_stats_upon_tag_swap() {
		$this->content_changed = true;
		$this->number_of_swaps++;
	}
}
