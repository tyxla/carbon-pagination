<?php
/**
 * @group item
 * @group item_direction_page
 * @group item_direction_page_backward
 * @group item_direction_page
 * @group item_last_page
 */
class CarbonPaginationItemLastPageGetDirectionHtmlTest extends WP_UnitTestCase {

	public function setUp() {
		$mock_methods = array( 'get_last_html' );
		$paginationStub = $this->getMockForAbstractClass('Carbon_Pagination', array(), '', TRUE, TRUE, TRUE, $mock_methods);
		$this->pagination = $paginationStub;

		$params = array($this->pagination);
		$collectionStub = $this->getMock('Carbon_Pagination_Collection', null, $params);
		$this->collection = $collectionStub;

		$params = array($this->collection);
		$itemStub = $this->getMock('Carbon_Pagination_Item_Last_Page', null, $params);
		$this->item = $itemStub;

		$this->html = '<span class="foo">Bar</span>';
		$this->pagination->expects( $this->any() )
			->method( 'get_last_html' )
			->will( $this->returnValue( $this->html ) );
	}

	public function tearDown() {
		unset($this->pagination);
		unset($this->collection);
		unset($this->item);
	}

	/**
	 * @covers Carbon_Pagination_Item_Last_Page::get_direction_html
	 */
	public function testGetDirectionHtml() {
		$this->assertSame( $this->html, $this->item->get_direction_html() );
	}

}