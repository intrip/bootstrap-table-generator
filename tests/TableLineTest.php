<?php
/**
 * TableLineTest
 *
 * @author Jacopo Beschi beschi.jacopo@gmail.com
 */

use Mockery as m;

class TableLineTest extends PHPUnit_Framework_TestCase
{
	protected $line;

	public function setUp()
	{
		$this->line = m::mock('Jacopo\Bootstrap3Table\TableLine')->makePartial();
	}

	public function tearDown()
	{
		m::close();
	}

	public function testGetLenghWorks()
	{
		$args = array("first","second");
		$this->line->data = $args;

		$length = $this->line->getLength();
		$this->assertEquals(2, $length);
	}

	public function testGetHtmlSuccess()
	{
		$this->line->shouldReceive('getTagRow')
		->once()
		->andReturn('tag');

		$args = array("first","second");
		$this->line->data = $args;
		$this->line->css_classes = array("class");
		$html = $this->line->getHtml();

$expected_html=<<<STR
\t<tr class="class ">\n\t\t<tag>first</tag>\n\t\t<tag>second</tag>\n
STR;
		$this->assertEquals($expected_html, $html);
	}

	public function testGetHtmlClasses()
	{
		$classes = array(
				"test-class1",
				"test-class2"
			);

		$this->line->css_classes = $classes;
		$expected_html = " class=\"test-class1 test-class2 \"";
		$this->assertEquals($expected_html, $this->line->getHtmlClasses() );
	}

}
