<?php namespace Jacopo\Bootstrap3Table;
/**
 * Bootstrap 3 Table generator class
 *
 * usage:
 *   $table = new BootstrapTable();
 *   $table->setConfig(array("table-hover"=>true));
 *   $table->setHeader(array("firstCol",...));
 *   $table->addRow(array("cell1","cell2",...), array("class1"));
 *   echo $table; //returns getHtml()
 *
 * @author Jacopo Beschi <beschi.jacopo@gmail.com>
 */

class BootstrapTable
{
  /**
  * Table data rows
  */
  protected $rows;
  /**
  * Table heading
  */
  protected $header;
  /**
  * max lenght of rows insert
  */
  protected $max_length_rows = 0;
  /**
  * Id of the table
  * @var String|null
  */
  public $id = null;
  /**
  * if tables are zebra striped
  */
  public $table_striped = false;
  /**
  * if table have border on sides
  */
  public $table_bordered = false;
  /**
  * if table color on hover
  */
  public $table_hover = false;
  /**
  * if table is show in condensed mode
  */
  public $table_condensed = false;
  /**
  * if table is responsive
  */
  public $table_responsive = false;
  /**
   * Extra custom classes for the table
   * @var array
   */
  protected $table_extra_classes = array();

	public function __construct(array $config = array() )
  {
    if($config)
      $this->setConfig($config);
	}

 /**
  * Set the configuration of the table
  *
  * @param  array $config
  * @throws InvalidArgumentException
  * @return  void
  */
  public function setConfig(array $configs = array() )
  {
    foreach($configs as $config_key => $config)
    {
      switch($config_key)
      {
        case 'table-striped':
          $this->table_striped = $config;
        break;
        case 'table-bordered':
          $this->table_bordered = $config;
        break;
        case 'table-hover':
          $this->table_hover = $config;
        break;
        case 'table-condensed':
          $this->table_condensed = $config;
        break;
        case 'table-responsive':
          $this->table_responsive = $config;
        break;
        case 'id':
          $this->id = $config;
          break;
        default:
          throw new \InvalidArgumentException();
        break;
      }
    }
  }

  /**
   * Set the header of the table
   *
   * @param  array $header_cols columns of the header
   * @return  void
   */
   public function setHeader(array $header_cols){
      $this->header = new TableHeader($header_cols);

      $this->updateMaxRowLength($this->header);
   }

  /**
   * Add a row to the table
   *
   * @param array $row row with data columns
   * @return void
   */
   public function addRows(array $rows, array $classes = array() ){
    $table_row = new TableRow($rows, $classes);
    $this->rows[] = $table_row;

    $this->updateMaxRowLength($table_row);
   }

  /**
   * Update the max lenght of rows in the table
   *
   * @param  $table_line
   * @return  $max_length_rows
   */
  protected function updateMaxRowLength($table_line)
  {
    $length = $table_line->getLength();
    if($length > $this->max_length_rows)
    {
      $this->max_length_rows = $length;
    }

    return $this->max_length_rows;
  }

  /**
   * Fill the rest of the row with empty cells
   *
   * @throws  InvalidArgumentException
   * @return String $html
   */
  protected function fillWithEmptyCells($table_row,$tag_row)
  {
    $html = '';

    if( ! ($table_row instanceof TableLine) )
    {
      throw new \InvalidArgumentException;
    }

    $length_row = $table_row->getLength();
    $diff = $this->max_length_rows - $length_row;

    if( $diff > 0 )
    {
      // add empty cells
      foreach( range(1,$diff) as $key)
      {
        $html.="\t\t\t<{$tag_row}></{$tag_row}>\n";
      }
    }

    $html.="\t\t</tr>\n";

    return $html;
  }

  /**
   * Get the classes in string format
   * separated by a space
   *
   * @return String $classes
   */
  protected function getTableClasses()
  {
    $classes = "";

    if($this->table_striped)
    {
      $classes.= "table-striped ";
    }
    if($this->table_bordered)
    {
      $classes.= "table-bordered ";
    }
    if($this->table_hover)
    {
      $classes.= "table-hover ";
    }
    if($this->table_condensed)
    {
      $classes.= "table-condensed ";
    }

    $classes.= $this->getTableExtraClassesString();

    return $classes;
  }

  /**
   * Return the extra classes as concatenated strings
   *
   * @return String $classes
   */
  protected function getTableExtraClassesString()
  {
    $classes_html = '';

    if( ! empty($this->table_extra_classes) )
    {
      foreach($this->table_extra_classes as $class)
      {
        $classes_html.= "{$class} ";
      }
    }

    return $classes_html;
  }

  /**
  * Add extra classes to the table
  *
  * @param array $classes
  * @return  void
  * @throws  \InvalidArgumentException
  */
  public function setTableExtraClasses($classes = array())
  {
    if( ! empty($classes) )
    {
      // validate classes
      foreach($classes as $class)
      {
        if(str_word_count($class) > 1)
        {
          throw new \InvalidArgumentException;
        }
      }
      $this->table_extra_classes = $classes;
    }
  }

  public function getTableExtraClasses()
  {
    return $this->table_extra_classes;
  }

  /**
  * Return the table as Html
  *
  * @return String $html
  */
  public function getHtml()
  {
    $html = "";

    $table_classes = $this->getTableClasses();

    if($this->table_responsive)
    {
      $html.= "<div class=\"table-responsive\">\n";
    }

    $id_tag = $this->getTagId();

    $html.= "<table {$id_tag} class=\"table {$table_classes}\">\n";

    // table header
    if( isset($this->header) )
    {
        $html.= "\t<thead>\n";
        $html.= $this->header->getHtml();
        $html.= $this->fillWithEmptyCells($this->header, $this->header->getTagRow() );
        $html.= "\t</thead>\n";
    }

    // table data
    if( isset($this->rows))
    {
      $html.= "\t<tbody>\n";
      foreach($this->rows as $row)
      {
        $html.= $row->getHtml();
        $html.= $this->fillWithEmptyCells($row, $row->getTagRow() );
      }
      $html.= "\t</tbody>\n";
    }

    $html.= "</table>\n";

    if($this->table_responsive)
    {
      $html.= "</div>\n";
    }

    return $html;
  }

  /**
   * [getTagId description]
   * @return [type] [description]
   */
  protected function getTagId()
  {
    return ($this->id) ? "id=\"{$this->id}\"" : "";
  }

  public function getHeader()
  {
    return $this->header;
  }

  public function getRows()
  {
    return $this->rows;
  }

  public function __toString()
  {
    return $this->getHtml();
  }

}
