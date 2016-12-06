<?php namespace MyPlugin\Widgets;

use \MyPlugin\Models\Categoria;

class Y2EmpresasWidget extends \WP_Widget 
{

    /**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'y2empresas_widget', // Base ID
			__( 'Y2 Empresas', 'text_domain' ), // Name
			array( 'description' => __( 'Pesquisar empresas', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		$categorias = Categoria::orderBy('titulo', 'ASC')->get();
		$urlSite = get_site_url();
		?>
		<form name="y2empresas_widget_pesquisa" action="" onSubmit="return enviar_pesquisa('<?php echo $urlSite ?>')">
			<input class="form-control" type="text" id="y2empresas_nome" name="y2empresas_nome" placeholder="<?php echo __( 'Nome', 'text_domain' ) ?>" />
			<br/>
			<select class="form-control" id="y2empresas_categoria" name="y2empresas_categoria">
				<option value=""><?php echo __( 'Escolha a categoria', 'text_domain' ) ?></option>
				<?php foreach($categorias as $cat): ?>
					<option value="<?php echo $cat->slug; ?>"><?php echo $cat->titulo; ?></option>
				<?php endforeach; ?>
			</select>
			<br/>
			<input type="submit" name="btn" value="<?php echo __( 'Pesquisar', 'text_domain' ) ?>" />
		</form>		
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Pesquisar empresas', 'text_domain' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

}