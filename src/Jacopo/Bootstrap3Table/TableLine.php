<?php namespace Jacopo\Bootstrap3Table;
/**
 * The generic table line
 *
 * @author Jacopo Beschi <beschi.jacopo@gmail.com>
 */

abstract class TableLine
{
	public $data;
	/**
	 * Custom css classes for the line
	 * @var String
	 */
	public $css_classes = array();
	protected $data_html;

	/**
	 * The string inside the tag < > to rappresent the line eg: th, td
	 * @var String
	 */
	protected $tag_row;

	public function __construct(array $data, array $classes = array() )
	{
		$this->data = $data;
		if(! empty($classes) )
			$this->css_classes = $classes;
	}

	public function getLength()
	{
		return count($this->data);
	}

	public function getHtml()
	{
		$tag = $this->getTagRow();
		$classes = $this->getHtmlClasses();

		$html = "\t<tr{$classes}>\n";

		foreach($this->data as $data)
		{
			$html.="\t\t<{$tag}>$data</{$tag}>\n";
		}

		$this->data_html = $html;

		return $this->data_html;
	}

	/**
	 * Return the tag for the row: only the part inside the < > tag
	 * @return String
	 */
	public function getTagRow()
	{
		return $this->tag_row;
	}

	/**
	 * Return the custom classes as html attribute
	 * @return String
	 */
	protected function getHtmlClasses()
	{
		$classes = '';

		if( ! empty($this->css_classes) )
		{
			// open attribute
			$classes.= " class=\"";
			foreach($this->css_classes as $class)
			{
				$classes.="{$class} ";
			}
			// close attribute
			$classes.= "\"";
		}

		return $classes;
	}

	public function __toString()
	{
		return $this->getHtml();
	}
}