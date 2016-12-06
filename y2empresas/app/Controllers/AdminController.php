<?php namespace MyPlugin\Controllers;

use \MyPlugin\Models\Empresa;
use \MyPlugin\Models\Categoria;
use Herbert\Framework\Http;
use Herbert\Framework\Exceptions\HttpErrorException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Capsule\Manager as DB;
use Herbert\Framework\Notifier;
use Symfony\Component\HttpFoundation\Session\Session;
use \MyPlugin\Helper;

class AdminController {

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
		$listTable = new \listTableEmpresa();
		$listTable->prepare_items();
		$listTable->display();
    }
	
	 public function save(Http $http)
    {
		if ($http->has('nome') and $http->has('localizacao') and $http->has('categoria_id')) {
			$data = [];
			$data['nome'] = $http->get('nome');
			$data['localizacao'] = $http->get('localizacao');
			$data['slug'] = sanitize_title($data['nome']);
			$data['imagem'] = '';
			$data['file'] = '';
			$id = (int) $http->get('id');
			$categoria_id = (int) $http->get('categoria_id');
			// Verifico se ja existe esta empresa na mesma categoria, se for um novo registro
			if ($id == 0 and $this->verifyIsDuplicate($data['nome'], $categoria_id) > 0) {
				$nomeCategoria = Categoria::find($categoria_id);
				$this->notices[] = ['msg'=>'Empresa <strong>'.$data['nome'].'</strong> já se encontra cadastrada para a categoria <strong>'.$nomeCategoria->titulo.'</strong>','type'=>'error'];
				$this->session->set('notice',$this->notices);
				return redirect_response(panel_url('MyPlugin::mainPanel'));
				exit;
			}
			// Se enviou alguma imagem
			if (!empty($_FILES['imagem']['tmp_name'])) {
				$upload = Helper::upload($_FILES);
				// Se retornar um array
				if (is_array($upload)) {
					// Se der erro
					if (isset($upload['error'])) {
						$this->notices[] = ['msg'=>$upload['error'],'type'=>'error'];
						$this->session->set('notice',$this->notices);
						return redirect_response(panel_url('MyPlugin::mainPanel'));
						exit;
					}
					// Pego a imagem enviada
					$data['imagem'] = $upload['url'];
					$data['file'] = $upload['file'];
				} else {
					$this->notices[] = ['msg'=>'Erro ao enviar imagem, tente novamente mais tarde','type'=>'error'];
					$this->session->set('notice',$this->notices);
					return redirect_response(panel_url('MyPlugin::mainPanel'));
					exit;
				}
			}
			
			// Se enviou algum id entao atualizo o registro
			if ($id > 0) {
				$empresa = Empresa::find($id);
				$imagem = $empresa->file;
				$empresa->nome = $data['nome'];
				$empresa->localizacao = $data['localizacao'];
				$empresa->slug = $data['slug'];
				$empresa->imagem = $data['imagem'];
				$empresa->file = $data['file'];
				// Se ja tiver imagem cadastrada e foi enviada uma nova imagem, 
				// deleto a imagem cadastrada do servidor
				if (!empty($imagem) and !empty($data['file'])) {
					unlink($imagem);
				}
				if ($empresa->save()) {					
					// Faco o relacionamento, o primeiro e a categoria_id 
					// e como e o mesmo objeto ja salvo, ja coloca o empresa_id automaticamente
					// o sync tira o relacionamento e depois cadastra novamente
					$empresa->categorias()->sync([$categoria_id]);
					$this->notices[] = ['msg'=>'Empresa <strong>'.$data['nome'].'</strong> atualizada com sucesso','type'=>'success'];
				} else {
					$this->notices[] = ['msg'=>'Falha ao atualizar empresa, tente novamente mais tarde','type'=>'error'];
				}

			} else { // Insiro um novo registro
				$empresa = new Empresa;
				$empresa->nome = $data['nome'];
				$empresa->localizacao = $data['localizacao'];
				$empresa->slug = $data['slug'];
				$empresa->imagem = $data['imagem'];
				$empresa->file = $data['file'];
				if ($empresa->save()) {				
					$categoria_id = (int) $http->get('categoria_id');
					// Faco o relacionamento, o primeiro e a categoria_id 
					// e como e o mesmo objeto ja salvo, ja coloca o empresa_id automaticamente
					// o attach cadastra uma nova linha na tabela de relacionamento
					$empresa->categorias()->attach([$categoria_id]);
					$this->notices[] = ['msg'=>'Empresa <strong>'.$data['nome'].'</strong> cadastrada com sucesso','type'=>'success'];
				} else {
					$this->notices[] = ['msg'=>'Falha ao cadastrar empresa, tente novamente mais tarde','type'=>'error'];
				}
			}	
			$this->session->set('notice',$this->notices);
		}

		return redirect_response(panel_url('MyPlugin::mainPanel'));
    }
	
	public function delete($id)
    {		
		try {          
			$empresa = Empresa::findOrFail($id);
			$nome = $empresa->nome;
			$imagem = $empresa->file;
        } catch (ModelNotFoundException $e) {
            throw new HttpErrorException(404, "Esta empresa não existe.");
        }
		// Se ja tiver imagem cadastrada deleto a imagem do servidor
		if (!empty($imagem)) {
			unlink($imagem);
		}
        if ($empresa->delete()) {
			$this->notices[] = ['msg'=>'Empresa <strong>'.$nome.'</strong> excluída com sucesso','type'=>'success'];
		} else {
			$this->notices[] = ['msg'=>'Falha ao excluir empresa, tente novamente mais tarde','type'=>'error'];
		}
		$this->session->set('notice',$this->notices);
    }
	
	public function novo()
	{
		$categorias = Categoria::orderBy('titulo', 'ASC')->get();
		return view('@MyPlugin/admin/form_empresa.twig',['title'=>'Y2 Empresas - Nova empresa', 'categorias' => $categorias]);
	}
	
	public function edit(Http $http)
	{
		try {          
			$id = (int) $http->get('id');
			$empresa = Empresa::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new HttpErrorException(404, "Esta empresa não existe.");
        }
		// Todas as categorias
		$categorias = Categoria::orderBy('titulo', 'ASC')->get();
		// Categoria que pertence a esta empresa
		$categoria_id_empresa = $empresa->categorias()->first()->id;
		return view('@MyPlugin/admin/form_empresa.twig',['title'=>'Y2 Empresas - Editar empresa ('.$empresa->nome.')', 'categorias' => $categorias, 'empresa' => $empresa, 'categoria_id_empresa' => $categoria_id_empresa]);
	}
	// Verifico se ja existe esta empresa nesta categoria	
	private function verifyIsDuplicate($nomeEmpresa, $idCategoria)
	{
		$empresa = DB::table('categorias')
            ->join('categoria_empresa', 'categoria_empresa.categoria_id', '=', 'categorias.id')
            ->join('empresas', 'empresas.id', '=', 'categoria_empresa.empresa_id')
            ->where('empresas.nome', '=', $nomeEmpresa)
			->where('categorias.id', '=', $idCategoria)
			->select('empresas.*', 'categorias.titulo')
            ->get()->count();
		return $empresa;	
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