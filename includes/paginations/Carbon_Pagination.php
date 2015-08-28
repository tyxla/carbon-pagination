<?php
/**
 * The Carbon Pagination base class.
 * Contains and manages all of the pagination settings.
 * Abstract, can be extended by all specific pagination types.
 *
 * @abstract
 */
abstract class Carbon_Pagination {
	/**
	 * Wrapper - before.
	 * @var string
	 */
	protected $wrapper_before = '<div class="paging">';

	/**
	 * Wrapper - after.
	 * @var string
	 */
	protected $wrapper_after = '</div>';

	/**
	 * Optional. Can be used if you want to loop through IDs instead of consecutive pages.
	 * If not defined, falls back to an array of all pages from 1 to $total_pages.
	 * @var array
	 */
	protected $pages = array();

	/**
	 * The total number of pages.
	 * If $pages is passed upon initialization, this will be set automatically.
	 * @var int
	 */
	protected $total_pages = 1;

	/**
	 * The current page number.
	 * @var int
	 */
	protected $current_page = 1;

	/**
	 * Whether the previous page link should be displayed.
	 * @var bool
	 */
	protected $enable_prev = true;

	/**
	 * Whether the next page link should be displayed.
	 * @var bool
	 */
	protected $enable_next = true;

	/**
	 * Whether the first page link should be displayed.
	 * @var bool
	 */
	protected $enable_first = false;

	/**
	 * Whether the last page link should be displayed.
	 * @var bool
	 */
	protected $enable_last = false;

	/**
	 * Whether the page number links should be displayed.
	 * @var bool
	 */
	protected $enable_numbers = false;

	/**
	 * Whether the current page text ("Page X of Y") should be displayed.
	 * @var bool
	 */
	protected $enable_current_page_text = false;

	/**
	 * How much page number links should be displayed (on each side of the current page item).
	 * Using 0 means only the current page item will be displayed.
	 * Using -1 means no limit (all page number links will be displayed).
	 * @var int
	 */
	protected $number_limit = -1;

	/**
	 * How much larger page number links should be displayed.
	 * Larger page numbers can be: 10, 20, 30, etc.
	 * Using 0 means none (no larger page number links will be displayed).
	 * @var int
	 */
	protected $large_page_number_limit = 0;

	/**
	 * The interval between larger page number links.
	 * If set to 5, larger page numbers will be 5, 10, 15, 20, etc.
	 * @var int
	 */
	protected $large_page_number_interval = 10;

	/**
	 * The wrapper before the page number links (1, 2, 3, etc).
	 * @var string
	 */
	protected $numbers_wrapper_before = '<ul>';

	/**
	 * The wrapper after the page number links (1, 2, 3, etc).
	 * @var string
	 */
	protected $numbers_wrapper_after = '</ul>';

	/**
	 * The HTML of the previous page link. You can use the following tokens:
	 * - {URL} - the link URL
	 * @var string
	 */
	protected $prev_html = '<a href="{URL}" class="paging-prev"></a>';

	/**
	 * The HTML of the next page link. You can use the following tokens:
	 * - {URL} - the link URL
	 * @var string
	 */
	protected $next_html = '<a href="{URL}" class="paging-next"></a>';

	/**
	 * The HTML of the first page link. You can use the following tokens:
	 * - {URL} - the link URL
	 * @var string
	 */
	protected $first_html = '<a href="{URL}" class="paging-first"></a>';

	/**
	 * The HTML of the last page link. You can use the following tokens:
	 * - {URL} - the link URL
	 * @var string
	 */
	protected $last_html = '<a href="{URL}" class="paging-last"></a>';

	/**
	 * The HTML of the page number link. You can use the following tokens:
	 * - {URL} - the link URL
	 * - {PAGE_NUMBER} - the particular page number
	 * @var string
	 */
	protected $number_html = '<li><a href="{URL}">{PAGE_NUMBER}</a></li>';

	/**
	 * The HTML of the current page number link. You can use the following tokens:
	 * - {URL} - the link URL
	 * - {PAGE_NUMBER} - the particular page number
	 * @var string
	 */
	protected $current_number_html = '<li class="current"><a href="{URL}">{PAGE_NUMBER}</a></li>';

	/**
	 * The HTML of limiter between page number links.
	 * @var string
	 */
	protected $limiter_html = '<li class="paging-spacer">...</li>';

	/**
	 * The current page text HTML. You can use the following tokens:
	 * - {CURRENT_PAGE} - the current page number
	 * - {TOTAL_PAGES} - the total number of pages
	 * @var string
	 */
	protected $current_page_html = '<span class="paging-label">Page {CURRENT_PAGE} of {TOTAL_PAGES}</span>';

	/**
	 * The class name of the pagination collection object.
	 * @var string
	 */
	protected $collection = 'Carbon_Pagination_Collection';

	/**
	 * The class name of the pagination renderer object.
	 * @var string
	 */
	protected $renderer = 'Carbon_Pagination_Renderer';

	/**
	 * The default argument values. Can be declared in the inheriting classes.
	 * Will override the default configuration options in Carbon_Pagination::__construct
	 * but can be overriden by the $args parameter.
	 * @var array
	 */
	public $default_args = array();

	/**
	 * Constructor.
	 * Creates and configures a new pagination with the provided settings.
	 *
	 * @access public
	 * @param array $args Configuration options to modify the pagination settings.
	 */
	public function __construct( $args = array() ) {
		// allow default options to be filtered
		$defaults = apply_filters( 'carbon_pagination_default_options', $this->default_args, $this );

		// parse configuration options
		$args = wp_parse_args( $args, $defaults );

		// set configuration options & constraints
		$this->set( $args );
	}

	/**
	 * Bulk set certain configuration options & constraints.
	 *
	 * @access public
	 * @param array $args Configuration options
	 */
	public function set( $args ) {
		foreach ( $args as $arg_name => $arg_value ) {
			$method = 'set_' . $arg_name;
			if ( method_exists( $this, $method ) ) {
				call_user_func( array( $this, $method ), $arg_value );
			}
		}
	}

	/**
	 * Retrieve the pagination wrapper - before.
	 *
	 * @access public
	 * @return string $wrapper_before The pagination wrapper - before.
	 */
	public function get_wrapper_before() {
		return $this->wrapper_before;
	}

	/**
	 * Modify the pagination wrapper - before.
	 *
	 * @access public
	 * @param string $wrapper_before The new pagination wrapper - before.
	 */
	public function set_wrapper_before( $wrapper_before ) {
		$this->wrapper_before = $wrapper_before;
	}

	/**
	 * Retrieve the pagination wrapper - after.
	 *
	 * @access public
	 * @return string $wrapper_after The pagination wrapper - after.
	 */
	public function get_wrapper_after() {
		return $this->wrapper_after;
	}

	/**
	 * Modify the pagination wrapper - after.
	 *
	 * @access public
	 * @param string $wrapper_after The new pagination wrapper - after.
	 */
	public function set_wrapper_after( $wrapper_after ) {
		$this->wrapper_after = $wrapper_after;
	}

	/**
	 * Retrieve the pages array.
	 *
	 * @access public
	 * @return array $pages The pages array.
	 */
	public function get_pages() {
		return $this->pages;
	}

	/**
	 * Modify the pages array.
	 * Array keys are intentionally reset.
	 *
	 * @access public
	 * @param array $pages The new pages array.
	 */
	public function set_pages( $pages = array() ) {
		if ( ! is_array( $pages ) ) {
			$pages = array( $pages );
		}

		$this->pages = array_values( $pages );
		$this->total_pages = count( $pages );
	}

	/**
	 * Retrieve the current page number.
	 *
	 * @access public
	 * @return int $current_page The current page number.
	 */
	public function get_current_page() {
		return $this->current_page;
	}

	/**
	 * Modify the current page number.
	 *
	 * @access public
	 * @param int $current_page The new current page number.
	 */
	public function set_current_page( $current_page = 1 ) {
		$current_page = intval( $current_page );
		if ( $current_page < 1 ) {
			$current_page = 1;
		}

		$total_pages = $this->get_total_pages();
		if ( $current_page > $total_pages ) {
			$current_page = $total_pages;
		}

		$this->current_page = $current_page;
	}

	/**
	 * Retrieve the total number of pages.
	 *
	 * @access public
	 * @return int $total_pages The total number of pages.
	 */
	public function get_total_pages() {
		return $this->total_pages;
	}

	/**
	 * Modify the total number of pages.
	 *
	 * @access public
	 * @param int $total_pages The new total number of pages.
	 */
	public function set_total_pages( $total_pages ) {
		$total_pages = intval( $total_pages );
		if ( $total_pages < 1 ) {
			$total_pages = 1;
		}

		$this->total_pages = $total_pages;
		$this->pages = range( 1, $total_pages );
	}

	/**
	 * Whether the previous page link should be displayed.
	 *
	 * @access public
	 * @return bool $enable_prev Whether the previous page link should be displayed.
	 */
	public function get_enable_prev() {
		return $this->enable_prev;
	}

	/**
	 * Change whether the previous page link should be displayed.
	 *
	 * @access public
	 * @param bool $enable_prev Whether the previous page link should be displayed.
	 */
	public function set_enable_prev( $enable_prev ) {
		$this->enable_prev = (bool) $enable_prev;
	}

	/**
	 * Whether the next page link should be displayed.
	 *
	 * @access public
	 * @return bool $enable_next Whether the next page link should be displayed.
	 */
	public function get_enable_next() {
		return $this->enable_next;
	}

	/**
	 * Change whether the next page link should be displayed.
	 *
	 * @access public
	 * @param bool $enable_next Whether the next page link should be displayed.
	 */
	public function set_enable_next( $enable_next ) {
		$this->enable_next = (bool) $enable_next;
	}

	/**
	 * Whether the first page link should be displayed.
	 *
	 * @access public
	 * @return bool $enable_first Whether the first page link should be displayed.
	 */
	public function get_enable_first() {
		return $this->enable_first;
	}

	/**
	 * Change whether the first page link should be displayed.
	 *
	 * @access public
	 * @param bool $enable_first Whether the first page link should be displayed.
	 */
	public function set_enable_first( $enable_first ) {
		$this->enable_first = (bool) $enable_first;
	}

	/**
	 * Whether the last page link should be displayed.
	 *
	 * @access public
	 * @return bool $enable_last Whether the last page link should be displayed.
	 */
	public function get_enable_last() {
		return $this->enable_last;
	}

	/**
	 * Change whether the last page link should be displayed.
	 *
	 * @access public
	 * @param bool $enable_last Whether the last page link should be displayed.
	 */
	public function set_enable_last( $enable_last ) {
		$this->enable_last = (bool) $enable_last;
	}

	/**
	 * Whether the page number links should be displayed.
	 *
	 * @access public
	 * @return bool $enable_numbers Whether the page number links should be displayed.
	 */
	public function get_enable_numbers() {
		return $this->enable_numbers;
	}

	/**
	 * Change whether the page number links should be displayed.
	 *
	 * @access public
	 * @param bool $enable_numbers Whether the page number links should be displayed.
	 */
	public function set_enable_numbers( $enable_numbers ) {
		$this->enable_numbers = (bool) $enable_numbers;
	}

	/**
	 * Whether the current page text should be displayed.
	 *
	 * @access public
	 * @return bool $enable_current_page_text Whether the current page text should be displayed.
	 */
	public function get_enable_current_page_text() {
		return $this->enable_current_page_text;
	}

	/**
	 * Change whether the current page text should be displayed.
	 *
	 * @access public
	 * @param bool $enable_current_page_text Whether the current page text should be displayed.
	 */
	public function set_enable_current_page_text( $enable_current_page_text ) {
		$this->enable_current_page_text = (bool) $enable_current_page_text;
	}

	/**
	 * Retrieve the page number links limit.
	 *
	 * @access public
	 * @return int $number_limit The page number links limit.
	 */
	public function get_number_limit() {
		return $this->number_limit;
	}

	/**
	 * Modify the page number links limit.
	 *
	 * @access public
	 * @param int $number_limit The new page number links limit.
	 */
	public function set_number_limit( $number_limit ) {
		$this->number_limit = intval( $number_limit );
	}

	/**
	 * Retrieve the large page number links limit.
	 *
	 * @access public
	 * @return int $large_page_number_limit The large page number links limit.
	 */
	public function get_large_page_number_limit() {
		return $this->large_page_number_limit;
	}

	/**
	 * Modify the large page number links limit.
	 *
	 * @access public
	 * @param int $large_page_number_limit The new large page number links limit.
	 */
	public function set_large_page_number_limit( $large_page_number_limit ) {
		$this->large_page_number_limit = absint( $large_page_number_limit );
	}

	/**
	 * Retrieve the large page number links interval.
	 *
	 * @access public
	 * @return int $large_page_number_interval The large page number links interval.
	 */
	public function get_large_page_number_interval() {
		return $this->large_page_number_interval;
	}

	/**
	 * Modify the large page number links interval.
	 *
	 * @access public
	 * @param int $large_page_number_interval The new large page number links interval.
	 */
	public function set_large_page_number_interval( $large_page_number_interval ) {
		$this->large_page_number_interval = absint( $large_page_number_interval );
	}

	/**
	 * Retrieve the pagination numbers wrapper - before.
	 *
	 * @access public
	 * @return string $numbers_wrapper_before The pagination numbers wrapper - before.
	 */
	public function get_numbers_wrapper_before() {
		return $this->numbers_wrapper_before;
	}

	/**
	 * Modify the pagination numbers wrapper - before.
	 *
	 * @access public
	 * @param string $numbers_wrapper_before The new pagination numbers wrapper - before.
	 */
	public function set_numbers_wrapper_before( $numbers_wrapper_before ) {
		$this->numbers_wrapper_before = $numbers_wrapper_before;
	}

	/**
	 * Retrieve the pagination numbers wrapper - after.
	 *
	 * @access public
	 * @return string $numbers_wrapper_after The pagination numbers wrapper - after.
	 */
	public function get_numbers_wrapper_after() {
		return $this->numbers_wrapper_after;
	}

	/**
	 * Modify the pagination numbers wrapper - after.
	 *
	 * @access public
	 * @param string $numbers_wrapper_after The new pagination numbers wrapper - after.
	 */
	public function set_numbers_wrapper_after( $numbers_wrapper_after ) {
		$this->numbers_wrapper_after = $numbers_wrapper_after;
	}

	/**
	 * Retrieve the previous page link HTML.
	 *
	 * @access public
	 * @return string $prev_html The previous page link HTML.
	 */
	public function get_prev_html() {
		return $this->prev_html;
	}

	/**
	 * Modify the previous page link HTML.
	 *
	 * @access public
	 * @param string $prev_html The new previous page link HTML.
	 */
	public function set_prev_html( $prev_html ) {
		$this->prev_html = $prev_html;
	}

	/**
	 * Retrieve the next page link HTML.
	 *
	 * @access public
	 * @return string $next_html The next page link HTML.
	 */
	public function get_next_html() {
		return $this->next_html;
	}

	/**
	 * Modify the next page link HTML.
	 *
	 * @access public
	 * @param string $next_html The new next page link HTML.
	 */
	public function set_next_html( $next_html ) {
		$this->next_html = $next_html;
	}

	/**
	 * Retrieve the first page link HTML.
	 *
	 * @access public
	 * @return string $first_html The first page link HTML.
	 */
	public function get_first_html() {
		return $this->first_html;
	}

	/**
	 * Modify the first page link HTML.
	 *
	 * @access public
	 * @param string $first_html The new first page link HTML.
	 */
	public function set_first_html( $first_html ) {
		$this->first_html = $first_html;
	}

	/**
	 * Retrieve the last page link HTML.
	 *
	 * @access public
	 * @return string $last_html The last page link HTML.
	 */
	public function get_last_html() {
		return $this->last_html;
	}

	/**
	 * Modify the last page link HTML.
	 *
	 * @access public
	 * @param string $last_html The new last page link HTML.
	 */
	public function set_last_html( $last_html ) {
		$this->last_html = $last_html;
	}

	/**
	 * Retrieve the HTML of a page number link.
	 *
	 * @access public
	 * @return string $number_html The HTML of a page number link.
	 */
	public function get_number_html() {
		return $this->number_html;
	}

	/**
	 * Modify the HTML of a page number link.
	 *
	 * @access public
	 * @param string $number_html The new HTML of a page number link.
	 */
	public function set_number_html( $number_html ) {
		$this->number_html = $number_html;
	}

	/**
	 * Retrieve the HTML of the current page number link.
	 *
	 * @access public
	 * @return string $current_number_html The HTML of the current page number link.
	 */
	public function get_current_number_html() {
		return $this->current_number_html;
	}

	/**
	 * Modify the HTML of the current page number link.
	 *
	 * @access public
	 * @param string $current_number_html The new HTML of the current page number link.
	 */
	public function set_current_number_html( $current_number_html ) {
		$this->current_number_html = $current_number_html;
	}

	/**
	 * Retrieve the HTML of a limiter.
	 *
	 * @access public
	 * @return string $limiter_html The HTML of a limiter.
	 */
	public function get_limiter_html() {
		return $this->limiter_html;
	}

	/**
	 * Modify the HTML of a limiter.
	 *
	 * @access public
	 * @param string $limiter_html The new HTML of a limiter.
	 */
	public function set_limiter_html( $limiter_html ) {
		$this->limiter_html = $limiter_html;
	}

	/**
	 * Retrieve the HTML of the current page text.
	 *
	 * @access public
	 * @return string $current_page_html The HTML of the current page text.
	 */
	public function get_current_page_html() {
		return $this->current_page_html;
	}

	/**
	 * Modify the HTML of the current page text.
	 *
	 * @access public
	 * @param string $current_page_html The new HTML of the current page text.
	 */
	public function set_current_page_html( $current_page_html ) {
		$this->current_page_html = $current_page_html;
	}

	/**
	 * Retrieve the collection object class name.
	 *
	 * @access public
	 * @return string $collection The collection object class name.
	 */
	public function get_collection() {
		return $this->collection;
	}

	/**
	 * Modify the collection object class name.
	 *
	 * @access public
	 * @param string $collection The new collection object class name.
	 */
	public function set_collection( $collection ) {
		$this->collection = $collection;
	}

	/**
	 * Retrieve the renderer object class name.
	 *
	 * @access public
	 * @return string $renderer The renderer object class name.
	 */
	public function get_renderer() {
		return $this->renderer;
	}

	/**
	 * Modify the renderer object class name.
	 *
	 * @access public
	 * @param string $renderer The new renderer object class name.
	 */
	public function set_renderer( $renderer ) {
		$this->renderer = $renderer;
	}

	/**
	 * Render the pagination.
	 *
	 * @access public
	 * @param bool $echo Whether to display or return the output. True will display, false will return.
	 */
	public function render( $echo = true ) {
		$presenter = new Carbon_Pagination_Presenter( $this );
		$output = $presenter->render();

		if ( ! $echo ) {
			return $output;
		}

		echo wp_kses( $output, wp_kses_allowed_html( 'post' ) );
	}

	/**
	 * Get the URL to a certain page.
	 *
	 * @abstract
	 * @access public
	 * @param int $page_number The page number.
	 * @param string $old_url Optional. The URL to add the page number to.
	 * @return string $url The URL to the page number.
	 */
	abstract public function get_page_url( $page_number, $old_url = '' );
}