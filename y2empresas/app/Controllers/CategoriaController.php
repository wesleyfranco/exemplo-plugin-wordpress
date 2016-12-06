<?php namespace MyPlugin\Controllers;

use \MyPlugin\Models\Categoria;
use Herbert\Framework\Http;
use Herbert\Framework\Exceptions\HttpErrorException;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Herbert\Framework\Notifier;
use Symfony\Component\HttpFoundation\Session\Session;

class CategoriaController {

	private $session;
	
	private $notices;
		
	public function __construct()
	{
		$this->session = new Session();
		$this->notices = [];
	}
	
	/**
     * Show the post for the given id.
     */
    public function index()
    {    
		// Imprime as notices
		$this->notice();
		$listTable = new \listTableCategoria();
		$listTable->prepare_items();
		$listTable->display();
    }
	
	 public function save(Http $http)
    {
        if ($http->has('titulo')) {
            $id = (int) $http->get('id');
			// Verifico se ja existe esta categoria cadastrada no sistema
			if ($this->verifyIsDuplicate($http->get('titulo')) > 0) {
				$nomeCategoria = $http->get('titulo');
				$this->notices[] = ['msg'=>'Categoria <strong>'.$nomeCategoria.'</strong> já se encontra cadastrada','type'=>'error'];
				$this->session->set('notice',$this->notices);
				return redirect_response(panel_url('MyPlugin::mainCategoria'));
				exit;
			}
			// Se enviou algum id entao atualizo o registro
			if ($id > 0) {
				$categoria = Categoria::find($id);
				$categoria->titulo = $http->get('titulo');
				$categoria->slug = sanitize_title($http->get('titulo'));	
				if ($categoria->save()) {
					$this->notices[] = ['msg'=>'Categoria <strong>'.$categoria->titulo.'</strong> atualizada com sucesso','type'=>'success'];
				} else {
					$this->notices[] = ['msg'=>'Falha ao atualizar categoria, tente novamente mais tarde','type'=>'error'];
				}
			} else { // Insiro um novo registro
				$categoria = new Categoria;
				$categoria->titulo = $http->get('titulo');
				$categoria->slug = sanitize_title($http->get('titulo'));				
				if ($categoria->save()) {
					$this->notices[] = ['msg'=>'Categoria <strong>'.$categoria->titulo.'</strong> cadastrada com sucesso','type'=>'success'];
				} else {
					$this->notices[] = ['msg'=>'Falha ao cadastrar categoria, tente novamente mais tarde','type'=>'error'];
				}
			}
			$this->session->set('notice',$this->notices);
		}

		return redirect_response(panel_url('MyPlugin::mainCategoria'));
    }
	
	public function delete($id)
    {		
		try {          
			$categoria = Categoria::findOrFail($id);
			$titulo = $categoria->titulo;
        } catch (ModelNotFoundException $e) {
            throw new HttpErrorException(404, "Esta categoria não existe.");
        }
		// Se tiver empresas vinculadas a esta categoria eu delo as empresas
		if ($categoria->empresas()->count()) {
			$categoria->empresas()->delete();
		}
		// Deleto a categoria
		if ($categoria->delete()) {
			$this->notices[] = ['msg'=>'Categoria <strong>'.$titulo.'</strong> excluída com sucesso','type'=>'success'];
		} else {
			$this->notices[] = ['msg'=>'Falha ao excluir categoria, tente novamente mais tarde','type'=>'error'];
		}
		$this->session->set('notice',$this->notices);		
    }
	
	public function novo()
	{
		return view('@MyPlugin/admin/form_categoria.twig',['title'=>'Y2 Empresas - Nova categoria']);
	}

	public function edit(Http $http)
	{
		try {          
			$id = (int) $http->get('id');
			$categoria = Categoria::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new HttpErrorException(404, "Esta categoria não existe.");
        }
		return view('@MyPlugin/admin/form_categoria.twig',['title'=>'Y2 Empresas - Editar categoria ('.$categoria->titulo.')', 'categoria' => $categoria]);
	}
	
	// Verifico se ja existe esta categoria cadastrada
	private function verifyIsDuplicate($nomeCategoria)
	{
		$categoria = DB::table('categorias')         
            ->where('titulo', '=', $nomeCategoria)
            ->get()->count();
		return $categoria;	
	}	
	
	// Metodo para imprimir as notices
	private function notice()
	{
		$data = $this->session->get('notice');
		if (count($data)) {
			foreach ($data as $sessao) {
				$tipo 	= $sessao['type'];
				$msg 	= $sessao['msg'];
				Notifier::$tipo($msg);
			}	
		}
		$this->session->clear();
	}
}