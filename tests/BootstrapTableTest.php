<?php
/**
 * BootstrapTableTest
 *
 * @author  Jacopo Beschi beschi.jacopo@gmail.com
 */

use Mockery as m;
use Jacopo\Bootstrap3Table\BootstrapTable;
use Jacopo\Bootstrap3Table\TableRow;
use Jacopo\Bootstrap3Table\TableHeader;

class BootstrapTableTest extends PHPUnit_Framework_TestCase
{
	protected $table;

	public function setUp()
	{
		$this->table = m::mock('Jacopo\Bootstrap3Table\BootstrapTable')->makePartial();
	}

	public function tearDown()
	{
		m::close();
	}

	public function testCanIstantiate()
	{
		$table = new BootstrapTable();
	}

	public function testSetConfigSuccess()
	{
		$config = array(
				"table-striped" => true,
				"table-bordered" => true,
				"table-hover" => false,
				"table-condensed" => false,
				"table-responsive" => true
			);

		$this->table->setConfig($config);
		$this->assertEquals(true, $this->table->table_striped);
		$this->assertEquals(true, $this->table->table_bordered);
		$this->assertEquals(false, $this->table->table_hover);
		$this->assertEquals(false, $this->table->table_condensed);
		$this->assertEquals(true, $this->table->table_responsive);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetConfigThrowsInvalidArgumentException()
	{
		$config = array("invalid"=>true);

		$this->table->setConfig($config);
	}

	public function testSetHeaderSuccess()
	{
		$header = array("first","second");
		$expected_header = new TableHeader($header);
		$this->table->setHeader($header);

		$this->assertEquals($expected_header, $this->table->getHeader() );
	}

	public function testAddRowSuccess()
	{
		$row1 = array("one","two");
		$expected_row1 = new TableRow($row1);
		$row2 = array("one","two");
		$custom_classes = array("class");
		$expected_row2 = new TableRow($row2, $custom_classes);
		$expected_array_rows = array(
				$expected_row1,
				$expected_row2
			);
		$this->table->addRows($row1);
		$this->table->addRows($row2,$custom_classes);
		$this->assertEquals($expected_array_rows, $this->table->getRows() );
	}

	public function testUpdateMaxRowLength()
	{
		$row1 = new TableRow( array("one","two") );
		$expected_length = 2;

		$length = $this->table->updateMaxRowLength($row1);
		$this->assertEquals($expected_length,$length);

		$length = $this->table->updateMaxRowLength($row1);
		$this->assertEquals($expected_length,$length);

		$row2 = new TableRow( array("one","two","three") );
		$expected_length = 3;

		$length = $this->table->updateMaxRowLength($row2);
		$this->assertEquals($expected_length,$length);
	}

	public function testFillWithEmpyCells()
	{
		$row_fill_array = array("one");
		$this->table->addRows($row_fill_array);
		$row_fill = new TableRow($row_fill_array);

		$expected_filler= "\t</tr>\n";
		$this->assertEquals($expected_filler, $this->table->fillWithEmptyCells($row_fill,"tag") );

		$row_max_array = array("one","two","three","four");
		$this->table->setHeader($row_max_array);

		$row_two_array = array("one","two");
		$row_two = new TableRow($row_two_array);
		$this->table->addRows($row_two_array);

		$expected_filler = "\t\t<tag></tag>\n";
		$expected_filler.= "\t\t<tag></tag>\n";
		$expected_filler.= "\t</tr>\n";

		$this->assertEquals($expected_filler, $this->table->fillWithEmptyCells($row_two,"tag") );
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testFillWithEmpyCellsThrowsInvalidArgumentException()
	{
		$array = array("notValidArgument");

		$this->table->fillWithEmptyCells($array,"tag");
	}

	public function testGetTableClasses()
	{
		$expected_classes = "";
		$this->assertEquals($expected_classes, $this->table->getTableClasses() );

		$config = array(
				"table-striped" => true,
				"table-bordered" => true,
				"table-hover" => true,
				"table-condensed" => true
			);
		$extra_classes = array("table-extra-class");
		$this->table->setTableExtraClasses($extra_classes);
		$expected_classes = "table-striped table-bordered table-hover table-condensed table-extra-class ";
		$this->table->setConfig($config);

		$this->assertEquals($expected_classes, $this->table->getTableClasses() );
	}

	public function testGetTagId()
	{
		$id = $this->table->getTagId();
		$this->assertEquals("",$id);

		$this->table->id="id";

		$id = $this->table->getTagId();
		$this->assertEquals("id=\"id\"",$id);
	}

	public function testGetHtml()
	{
		// test responsive
		$header = array("first","second");
		$row1 = array("one","two");
		$row2 = array("oneOnly");
		$this->table->setConfig(array("table-responsive"=>true,"table-hover"=>true));
		$this->table->setHeader($header);
		$this->table->addRows($row1);
		$class_row2 = array("row2-class");
		$this->table->addRows($row2, $class_row2);
		$this->table->id="id1";
		$extra_classes = array("test-extra-class");
		$this->table->setTableExtraClasses($extra_classes);

		$expected_html = "<div class=\"table-responsive\">\n";
		$expected_html.= "<table id=\"id1\" class=\"table table-hover test-extra-class \">\n";
		$expected_html.= "\t<tr>\n";
		$expected_html.= "\t\t<th>first</th>\n";
		$expected_html.= "\t\t<th>second</th>\n";
		$expected_html.= "\t</tr>\n";
		$expected_html.= "\t<tr>\n";
		$expected_html.= "\t\t<td>one</td>\n";
		$expected_html.= "\t\t<td>two</td>\n";
		$expected_html.= "\t</tr>\n";
		$expected_html.= "\t<tr class=\"row2-class \">\n";
		$expected_html.= "\t\t<td>oneOnly</td>\n";
		$expected_html.= "\t\t<td></td>\n";
		$expected_html.= "\t</tr>\n";
		$expected_html.= "</table>\n";
		$expected_html.= "</div>\n";

		$this->assertEquals($expected_html,$this->table->getHtml() );

		// test no responsive
		$header = array("first","second");
		$row1 = array("one","two","three");
		$row2 = array("oneOnly");
		$this->table->setConfig(array("table-hover"=>true,"table-striped"=>true));
		$this->table->setHeader($header);
		$this->table->addRows($row1);
		$this->table->addRows($row2);

		$expected_html= "<table  class=\"table table-hover table-striped \">\n";
		$expected_html.= "\t<tr>\n";
		$expected_html.= "\t\t<th>first</th>\n";
		$expected_html.= "\t\t<th>second</th>\n";
		$expected_html.= "\t\t<th></th>\n";
		$expected_html.= "\t</tr>\n";
		$expected_html.= "\t<tr>\n";
		$expected_html.= "\t\t<td>one</td>\n";
		$expected_html.= "\t\t<td>two</td>\n";
		$expected_html.= "\t\t<td>three</td>\n";
		$expected_html.= "\t</tr>\n";
		$expected_html.= "\t<tr>\n";
		$expected_html.= "\t\t<td>oneOnly</td>\n";
		$expected_html.= "\t\t<td></td>\n";
		$expected_html.= "\t\t<td></td>\n";
		$expected_html.= "\t</tr>\n";
		$expected_html.= "</table>\n";
		$expected_html.= "</div>\n";

	}

	public function testSetTableExtraClassesSuccess()
	{
		$classes = array(
				"first",
				"second"
			);

		$this->table->setTableExtraClasses($classes);
		$this->assertEquals($classes, $this->table->getTableExtraClasses() );
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetTableExtraClassesThrowsInvalidArgumentException()
	{
		$classes = array(
				"first invalid argument",
			);

		$this->table->setTableExtraClasses($classes);
	}

	public function testGetTableExtraClassesString()
	{
		$classes = array(
			"first",
			"second"
		);

		$this->table->setTableExtraClasses($classes);
		$expected_html = "first second ";
		$this->assertEquals($expected_html, $this->table->getTableExtraClassesString() );
	}
}
