<?php
ob_start();
use MyPlugin\Controllers\CategoriaController;

class listTableCategoria extends WpListTable {
	
	/**
	* Constructor, we override the parent to pass our own arguments
	* We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	*/
	public function __construct() {
		parent::__construct(array(
			'singular'  => 'categoria',     //singular name of the listed records
			'plural'    => 'categorias',    //plural name of the listed records
			'ajax'      => false        //does this table support ajax?
		));
	}
	
	public function prepare_items() {
		global $wpdb;

		/* -- Preparing your query -- */
		$query = "SELECT * FROM {$this->table_prefix}categorias";
		/* -- Search parameters -- */
		$search = !empty($_GET['s']) ? mysql_real_escape_string($_GET["s"]) : '';
		
		if(!empty($search)) {$query.= " WHERE titulo LIKE '%{$search}%'";}
		/* -- Ordering parameters -- */
		$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'id';
		$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : 'DESC';
		if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

		/* -- Pagination parameters -- */
		//Number of elements in your table?
		$total_items = $wpdb->query($query); //return the total number of affected rows
		//Which page is this?
		$paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
		//adjust the query to take pagination into account
		if(!empty($paged)){
			$offset=($paged-1)*$this->per_page;
			$query.=' LIMIT '.(int)$offset.','.(int)$this->per_page;
		}
		// Register the pagination
		$current_page = $this->get_pagenum();
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => ceil($total_items/$this->per_page),
			'per_page' => $this->per_page,
		) );

		// Register the columns
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);		   

		// Bulk action
		$this->process_bulk_action();
		
		// Fetch item
		$this->items = $wpdb->get_results($query, 'ARRAY_A');
	}
	
	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	public function get_columns() {
		return array(
			'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
			'titulo' => __('Título'),
		);
	}

	/**
	 * Decide which columns to activate the sorting functionality on
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	public function get_sortable_columns() {
		return array(
			'titulo' => array('titulo'),
		);
	}
	
	public function column_titulo($item){

		// Mando a paginacao que o usuario estava
		if (!empty($_GET["paged"])) {
			$linkEdit = panel_url('MyPlugin::mainCategoria', ['action'=>'edit', 'id'=> $item['id'], 'paged' => $_GET["paged"]]);
		} else {
			$linkEdit = panel_url('MyPlugin::mainCategoria', ['action'=>'edit', 'id'=> $item['id']]);
		}
		//Build row actions
		$actions = array(
			'edit'      => '<a href="'.$linkEdit.'">Editar</a>',			
		);

		//Return the title contents
		return $item['titulo'].' '.$this->row_actions($actions);
	}	
	
	public function get_bulk_actions() {
		
		$actions = array(
			'delete' => __( 'Deletar', 'your-textdomain' ),
		);
		return $actions;
	}	
	
	public function process_bulk_action() {

		// security check!
        if ( isset( $_REQUEST['_wpnonce'] ) && ! empty( $_REQUEST['_wpnonce'] ) ) {

            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if ( ! wp_verify_nonce( $nonce, $action ) )
                wp_die( 'Nope! Security check failed!' );

        }

        $action = $this->current_action();

        switch ( $action ) {
            case 'delete':				
				$delete = new CategoriaController;
				foreach($_POST['categoria'] as $categoria_id) {					
					$delete->delete($categoria_id);
				}
				wp_safe_redirect($_REQUEST['_wp_http_referer']);				
            break;
        }

        return;
    }
	
	public function display() 
	{
		$linkNewCategoria = panel_url('MyPlugin::mainCategoria', ['action'=>'new']);
		$linkNewEmpresa = panel_url('MyPlugin::mainPanel', ['action'=>'new']);
		?>
		<div class="wrap">
			<h2>Y2 Empresas <a href="<?php echo $linkNewCategoria ?>" class="add-new-h2">Nova categoria</a> <a href="<?php echo $linkNewEmpresa ?>" class="add-new-h2">Nova empresa</a></h2>
			<form method="get">				
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $this->search_box('Pesquisar categorias', 'categoria_id'); ?>
			</form>
			<form method="post">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />				
				<?php parent::display(); ?>				
			</form>
		</div>	
		<?php
	}
}