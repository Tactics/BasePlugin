<?php

Misc::use_helper('Tag');

/**
 *  This class generates a table...
 *
 *
 *
 */
class myTable
{
	/**
	 * Information about each column and its header
	 */
	private $columnInfo;
	
	/**
	 * Target div for table update function
	 */
	private $sortTarget = "";

	/**
	 * Uri of remote table update function
	 */
	private $sortUri = "";
	
	/**
	 * Field by which the table is ordered, if any
	 */
	private $sortField = "";

	/**
	 * Direction in which the table is ordered by $sortField (asc or desc)
	 */
	private $sortOrder = "";

	/**
	 * Whether to show the table header
	 */ 
	private $showHeader = true;
	
	private $rowCount = 0;
	private $colCount = 0;

	private $rowDataHtml = "";


	/**
	 * Create a table object with given columns and options
	 *
	 * $columnInfo: array of arrays, in each subarray:
	 *  - name
	 *  - text
	 *  - link
	 *  - width
	 * 	- align
	 *  - headeralign
	 * 
	 * $options
	 *  - noheader
	 *  - sorturi
	 *  - sorttarget
	 *  - sortfield
	 *  - sortorder			: ASC or DESC
	 */
	function __construct($columnInfo, $options = array()) {
	  // input checks
		if (!is_array($columnInfo)) 
			throw new sfViewException("parameter columnInfo of type array expected");
		if (!is_array($options)) 
			throw new sfViewException("parameter options of type array expected");
		
		// column data
		$this->columnInfo = $columnInfo;	  
		$this->colCount = count($columnInfo);

		// options		
		$this->sortUri= _get_option($options, "sorturi", "");
		$this->sortTarget = _get_option($options, "sorttarget", "");
		$this->sortField = _get_option($options, "sortfield", "");
		$this->sortOrder = _get_option($options, "sortorder", "");
		$this->showHeader = ! (_get_option($options, "noheader", false));
		$this->style = _get_option($options, "style", "");
		$this->class = _get_option($options, "class", "");
		$this->tableAttributes = _get_option($options, "tableAttributes", array());


	}
	
	/**
	 *
	 */
	function __toString()
  {
	  return $this->getHtml();
	}
	
	/**
	 * Return the HTML code for the current table
	 */
	function getHtml() {
	  $html  =  "\n<!-- Begin myTable generated code -->\n";
	  $html .=  "\n<!-- myTable: HEADER -->\n";
	  $html .= $this->_getHeaderHtml();
	  $html .=  "\n<!-- myTable: ROWS -->\n";
		$html .= $this->getRowHtml();
	  $html .=  "\n<!-- myTable: FOOTER -->\n";
		$html .= $this->_getFooterHtml();
	  $html .=  "\n<!-- myTable: SLIDER -->\n";
		//$html .= $this->_getSliderHtml();
	  $html .=  "\n<!-- myTable: SCRIPTS -->\n";
		$html .= $this->_getScripts();
		$html .= "\n<!-- End myTable generated code -->\n";
			
		return $html;
	}
	
	/**
	 * Return only row html
	 */
	function getRowHtml()
	{
	  if ($this->rowCount == 0) {
		  $html = "    <td colspan='$this->colCount'>Geen resultaten</td>\n";
		}
		else $html = $this->rowDataHtml;
		
		return $html;
	}
	
	
	private function _getHeaderCell($rownumber, $cell)
	{
	  $html = "";
	  
	  // Opening td tag with options
	  $html .= "    <td ";

		$html .= " id='headercell$rownumber'";

	  if ( isset($cell["headeralign"]) )
			$html .= " align=\"" . $cell["headeralign"] . "\"";
	  else if ( isset($cell["align"]) )
			$html .= " align=\"" . $cell["align"] . "\"";
			
	  if ( isset($cell["width"]) )
			$html .= " width=" . $cell["width"] . " style='" . $cell["width"] . "px;'";

	  if ( isset($cell["name"]) && $this->sortField == $cell["name"])
			$html .= " class='gesorteerd'";

		$html .= " onmouseover='jQuery(this).addClass(\"mouseover\")'";
		$html .= " onmouseout='jQuery(this).removeClass(\"mouseover\")'";

    
		if (isset($cell["sortable"]) && $cell["sortable"] && isset($cell["name"]) && $cell["name"] != "")
    {
		  if ($this->sortTarget)
      {
  			$html .= " onclick=\"" . tt_remote_function(array('update' => $this->sortTarget, 'url' => $this->sortUri . (strpos($this->sortUri, '?') ? '&' : '?') . "orderby=" . $cell["name"])) . "\"";
  		}
  		else
      {
  			$html .= " onclick=\"document.location = '" . url_for($this->sortUri . "&orderby=" . $cell["name"]) . "';\"";
      }
		}
		$html .= ">\n";

		// Cell content
    $title = isset($cell['title']) ? $cell['title'] : htmlentities(html_entity_decode(htmlspecialchars(strip_tags($cell["text"]))));
		$html .="<div id='headercelldiv$rownumber' style='overflow:hidden' title=\"" . $title . "\">\n";

		$html .= $cell["text"];

		// If applicable: sort order icon
		if (isset($cell["name"]) && $this->sortField == $cell["name"]) {
			if ( strtoupper($this->sortOrder) == "ASC" ) {
			  $html .= "&nbsp;" . image_tag("/ttBase/images/sorteer_asc.gif");
			}
			if ( strtoupper($this->sortOrder) == "DESC" ) {
			  $html .= "&nbsp;" . image_tag("/ttBase/images/sorteer_desc.gif");
			}
		}

		// Close td tag
		
		$html .= "</div>\n";
		$html .= "</td>\n";	  
		
		return $html;
	}
	
	/**
	 * Generate header HTML
	 */
	private function _getHeaderHtml() {
	  // Start table
		$html = "<table class=\"grid {$this->class}\" style=\"{$this->style}\"";

    // Insert user defined attributes for the td tag
    foreach($this->tableAttributes as $attribute => $value)
    {
      $html .= " " . $attribute . '="' . addslashes($value) . '"';        
    }	
        
    $html .= ">\n";
		
		// Create <col> tags
		foreach($this->columnInfo as $cell) {
		  $html .= "<col " . ((isset($cell["name"]) && $this->sortField == $cell["name"]) ? "class='gesorteerd'" : "") . "/>";
		}
		
		if ($this->showHeader) {
			// Column header
			$html .= "  <thead>\n";
	
			$html .= "  <tr>\n";
		
			for($r = 0; $r < count($this->columnInfo); $r++) {
			  $html .= $this->_getHeaderCell($r, $this->columnInfo[$r]);
			}
	
			// End of header, start of tbody	
			$html .= "  </tr>\n";
			$html .= "  </thead>\n";
		}
		
		$html .= "  <tbody>\n";
		return $html;	  
	}

	/**
	 * Add a row to the current table
	 *
	 * options are:
	 *   - static: a static row is not hovered on mouseover, and has different
	 *             backgroundcolor (eg for agregate information or action links)
	 *   - align:  overrides the default alignment
	 *   - style:  add style info to the row
	 */
	function addRow($rowData, $options = array()) {
	  
	  $this->rowCount++;
	  
	  $rowalign = _get_option($options, "align", false);
	  
	  // Start the row, indicate even/oneven
	  $html = "  <tr class=\"";
	  if ( _get_option($options, "static", false)) {
			$html .= "static";
		}
		else {
			$html .= (($this->rowCount % 2) == 0) ? "even" : "oneven";
		}

		$html .= " " . _get_option($options, "rowClass", "");
				
		$html .= "\"";
		
		$style = _get_option($options, "style", "");
		
	  if ( $style != "") {
	    $html .= " style=\"$style\"";
	  }

    // Insert user defined attributes for the tr tag
    foreach(_get_option($options, "trAttributes", array()) as $attribute => $value)
    {
      $html .= " " . $attribute . '="' . addslashes($value) . '"';        
    }												
			
		$html .= ">\n";
	  
	  // Draw each cell in the row
		for($i = 0; $i < $this->colCount; $i++) {
		  
		  // Opening td tag with options
  		$html .= "    <td";
			
			$html .= " id='cell_" . $this->rowCount . "_$i'";

  		if ($rowalign) {
			  $html .= " align=\"$rowalign\"";
			}
		  else if ( isset($this->columnInfo[$i]["align"])) {
			  $html .= " align=\"" . $this->columnInfo[$i]["align"] . "\"";
			}

			if ( is_array($rowData[$i]) && isset($rowData[$i]["style"]) ) {
  			  $html .= " style=\"" . $rowData[$i]["style"] . "\"";
			}

      // Insert user defined attributes for the td tag
			if (is_array($rowData[$i]) && isset($rowData[$i]['tdAttributes']))
			{
        foreach($rowData[$i]['tdAttributes'] as $attribute => $value)
        {
          $html .= " " . $attribute . '="' . addslashes($value) . '"';        
        }												
			}

			$html .= ">";
			
			
			$cellContent = '';
			
			$html .="<div style='overflow:hidden' ";
			
			// Cell content or empty
			if ( ! is_array($rowData[$i]) ){
			  $cellContent = $rowData[$i];
			  $html .= " title=\"" . htmlentities(html_entity_decode(strip_tags($cellContent)));
        $html .= '"';
			}
			else if ( is_array($rowData[$i]) && isset($rowData[$i]["content"]) ) {
				$cellContent = $rowData[$i]["content"];
				$html .= " title=\"" . htmlentities(html_entity_decode(strip_tags($cellContent)));
        $html .= '"';
			}
			else {
			  $cellContent = "&nbsp;";
			}
      
      // Insert user defined attributes for the td tag
			if (is_array($rowData[$i]) && isset($rowData[$i]['divAttributes']))
			{
        foreach($rowData[$i]['divAttributes'] as $attribute => $value)
        {
          $html .= " " . $attribute . '="' . addslashes($value) . '"';        
        }												
			}
			
			$html .= ">";
			
			$html .= $cellContent;
			
			// End of cell
			$html .= "</div>";
			$html .= "    </td>\n";
		}
	
		// End of row
		$html .= "  </tr>\n";
		$this->rowDataHtml .= $html;
	}

	/**
	 * Generate footer html
	 */
	private function _getFooterHtml() {
	  // End of table body and table
		$html = "  </tbody>\n";
		$html .= "</table>\n";
		return $html;	  
	}
  
  private function _getScripts() {
    $html = "";
	  return $html;
	}
}


