<?php 
if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

abstract class WpListTable extends WP_List_Table {
	
	protected $per_page = 5;
	
	protected $table_prefix;
	
	public function __construct($options, $per_page = NULL) {
		global $status, $page, $wpdb;
		
		//Set parent defaults
		parent::__construct($options);
		
		if ($per_page) {
			$this->per_page = $per_page;
		}
		
		$this->table_prefix = $wpdb->prefix;
	}

	public function column_default($item, $column_name){

		return $item[$column_name];
	}
	
	public function column_cb($item){
		return '<input type="checkbox" name="'.$this->_args['singular'].'[]" value="'.$item['id'].'" />';
	}
	// Tem que implementar este metodo na classe concreta
	public function prepare_items(){}
}