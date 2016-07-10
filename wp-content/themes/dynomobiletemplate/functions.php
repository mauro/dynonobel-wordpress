<?php
add_action( 'wp_enqueue_scripts', 'dynomobiletemplate_scripts' );
/**
 * Enqueue Dashicons style for frontend use
 */
function dynomobiletemplate_scripts() {
	wp_enqueue_style( 'dashicons' );
}

 class CSV_Responsive_Tables {
 	
 	// Init
 	public function __construct() {

 		// Add Shortcode to display the Tables
 		add_shortcode( 'table-from-csv', array($this, 'render_table') );

 		// Enqueue Styles and Scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_and_scripts'), 9999 );
 	}

	// Enqueue Custom Styles and Scripts
	public function enqueue_styles_and_scripts() {
	    wp_enqueue_style(	'csv-responsive-tables',	get_template_directory_uri().'/datatables.min.css'	);
	    wp_enqueue_script(	'datatables',				get_template_directory_uri().'/datatables.min.js'	);
	}

	// Shortcode function
	public function render_table( $atts ) {
		return $this->table_from_csv($atts['file'], $atts['search'], $atts['id']);
	}

	// Helper function to generate a unique HTML/CSS ID for a table if
	// no ID is given through the shortcode.
	private function generate_unique_id() {
		return 'table_'.microtime();
	}

	// Rendering function: gets the file and outputs the necessary HTML and Javascript Code
	// If no file is found returns an error to be rendered in place of the shortcode.
 	private function table_from_csv($file, $search = NULL, $table_id) {
		if ($search == true) $data_filtering = 'data-filtering="true"';
		else $data_filtering = '';
		if (empty($table_id)) $table_id = $this->generate_unique_id();
		$f = fopen(ABSPATH.$file, "r");
		if (!$f) return 'Error: file not found at '.$file;
		$isFirstRow = true;
		$column = 1;
		$columns_to_show_xs = 1;
		$columns_to_show_sm = 3;
		$columns_to_show_md = 5;
		$visibility_toggle = "<div class=\"visibility-toggle\">\n 	<h3>Toggle Columns Visibility</h3>\n 	<ul>\n";
		$table = "<table id=\"".$table_id."\" class=\"supertable display responsive\" width=\"100%\">\n    <thead>\n";
		while (($row = fgetcsv($f)) !== false) {
		        $table .= "      <tr>\n";
		        foreach ($row as $cell) {
		        		if ($isFirstRow) {
		        			if ($column == 1) {
		        				$table .= "        <th class=\"col-".$column."\">" . htmlspecialchars($cell) . "</th>\n";
		        			} else {
		        				$breakpoints = '';
		        				if ($column > $columns_to_show_xs) $breakpoints .= 'xs';
		        				if ($column > $columns_to_show_sm) $breakpoints .= ' sm';
		        				if ($column > $columns_to_show_md) $breakpoints .= ' md';
		        				$table .= "        <th class=\"class=\"col-".$column."\" data-breakpoints=\"".$breakpoints."\">" . htmlspecialchars($cell) . "</th>\n";
		        				$visibility_toggle .= "		<li><a class=\"toggle-vis\" data-column=\"".($column-1)."\">" . htmlspecialchars($cell) . "</a></li>\n";
		        			}
		        		}
		        		else $table .= "        <td class=\"col-".$column."\">" . htmlspecialchars($cell) . "</td>\n";
		        		$column++;
		        }
		        $column = 1;
		        $table .= "      </tr>\n";
		        if ($isFirstRow) {
		        	$table .= "    </thead>\n    <tbody>\n";
		        	$visibility_toggle .= "	</ul>\n</div>\n";
		        	$isFirstRow = false;
		        }
		}
		fclose($f);
		$table .= "    </tbody>\n</table>\n";

		$javascript = "
			<script type=\"text/javascript\">
				jQuery(document).ready(function() {
				    var table = jQuery('#".$table_id."').DataTable( {
				        \"paging\": false,
				        \"responsive\": true,
				        \"colReorder\": true,
				        \"dom\": 'Bfrtip',
				                \"buttons\": [
				                    'colvis'
				                ]
				    } );
				 
				    jQuery('a.toggle-vis').on( 'click', function (e) {
				        e.preventDefault();
				 
				        // Get the column API object
				        var column = table.column( jQuery(this).attr('data-column') );
				 
				        // Toggle the visibility
				        column.visible( ! column.visible() );
				    } );
				} );
			</script>
		";
		return $table.$javascript;
	}
 }

// Invoke our plug-in class
 $csv_responsive_tables = new CSV_Responsive_Tables();