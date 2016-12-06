<?php namespace MyPlugin\Controllers;

use MyPlugin\Models\Empresa;
use Herbert\Framework\Http;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Capsule\Manager as DB;
use Herbert\Framework\Exceptions\HttpErrorException;
use JasonGrimes\Paginator;
use MyPlugin\Helper;

class EmpresaController {
	
	/**
     * Show the empresa for the given id.
     */
    public function show($id)
    {
        try {
            $empresa = Empresa::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new HttpErrorException(404, "It looks like that empresa doesn't exist.");
        }
		
		$empresa['categoria'] = $empresa->categorias()->first()->titulo;
        return view('@MyPlugin/empresa/single.twig', ['empresa' => $empresa]);
    }
	
	public function all(Http $http)
	{
		try {
            $empresas = Empresa::All();
        } catch (ModelNotFoundException $e) {
            throw new HttpErrorException(404, "It looks like that empresa doesn't exist.");
        }
		// Paginacao
		$page = $http->get('p', 1);
		$totalItems = count($empresas);
		$currentPage = $page;
		$link_paginacao = route_url('MyPlugin::empresasAll');		
		
		$urlPattern = $link_paginacao.'/?p=(:num)';
		$perPage = Helper::get('perPag');
		
		$paginator = new Paginator($totalItems, $perPage, $currentPage, $urlPattern);
		
		$empresas = Empresa::query()
            ->forPage($page, $perPage)
            ->get();
		
		return view('@MyPlugin/empresa/list.twig', ['empresas' => $empresas, 'paginator' => $paginator]);
	}
	
	public function search($empresa, $slug_categoria = null)
	{
		$page = (isset($_GET['p'])) ? $_GET['p'] : 1;
		$perPage = Helper::get('perPag');		
		
		// Se nao buscou por categoria
		if (!empty($empresa) and is_null($slug_categoria)) {			
			$empresasTotal = DB::table('categorias')
            ->join('categoria_empresa', 'categoria_empresa.categoria_id', '=', 'categorias.id')
            ->join('empresas', 'empresas.id', '=', 'categoria_empresa.empresa_id')
            ->where('empresas.nome', 'like', '%' . $empresa . '%')
			->select('empresas.*', 'categorias.titulo')
            ->get();
			
			$empresas = DB::table('categorias')
            ->join('categoria_empresa', 'categoria_empresa.categoria_id', '=', 'categorias.id')
            ->join('empresas', 'empresas.id', '=', 'categoria_empresa.empresa_id')
            ->where('empresas.nome', 'like', '%' . $empresa . '%')
			->select('empresas.*', 'categorias.titulo')
			->forPage($page, $perPage)
            ->get();
			
			$link_paginacao = route_url('MyPlugin::empresasAllSearchEmpresa');
			$link_paginacao = str_replace('{empresa}', $empresa, $link_paginacao);
		
		} else if(!empty($empresa) and $slug_categoria !== NULL){			
			$empresasTotal = DB::table('categorias')
            ->join('categoria_empresa', 'categoria_empresa.categoria_id', '=', 'categorias.id')
            ->join('empresas', 'empresas.id', '=', 'categoria_empresa.empresa_id')
            ->where('empresas.nome', 'like', '%' . $empresa . '%')
			->where('categorias.slug', '=', $slug_categoria)
			->select('empresas.*', 'categorias.titulo')
            ->get();
			
			$empresas = DB::table('categorias')
            ->join('categoria_empresa', 'categoria_empresa.categoria_id', '=', 'categorias.id')
            ->join('empresas', 'empresas.id', '=', 'categoria_empresa.empresa_id')
            ->where('empresas.nome', 'like', '%' . $empresa . '%')
			->where('categorias.slug', '=', $slug_categoria)
			->select('empresas.*', 'categorias.titulo')
			->forPage($page, $perPage)
            ->get();
			
			$link_paginacao = route_url('MyPlugin::empresasAllSearchEmpresaCategory');		
			$link_paginacao = str_replace('{empresa}', $empresa, $link_paginacao);
			$link_paginacao = str_replace('{slug_categoria}', $slug_categoria, $link_paginacao);
		}
		
		// Paginacao		
		$totalItems = count($empresasTotal);
		$currentPage = $page;
		$urlPattern = $link_paginacao.'/?p=(:num)';		
		
		$paginator = new Paginator($totalItems, $perPage, $currentPage, $urlPattern);
		
		return view('@MyPlugin/empresa/list_search.twig', ['empresas' => $empresas, 'paginator' => $paginator]);
	}
	
	public function searchCategoria($slug_categoria)
	{
		$page = (isset($_GET['p'])) ? $_GET['p'] : 1;
		$perPage = Helper::get('perPag');
		
		$empresasTotal = DB::table('categorias')
            ->join('categoria_empresa', 'categoria_empresa.categoria_id', '=', 'categorias.id')
            ->join('empresas', 'empresas.id', '=', 'categoria_empresa.empresa_id')
			->where('categorias.slug', '=', $slug_categoria)
			->select('empresas.*', 'categorias.titulo')
            ->get();
		
		$empresas = DB::table('categorias')
            ->join('categoria_empresa', 'categoria_empresa.categoria_id', '=', 'categorias.id')
            ->join('empresas', 'empresas.id', '=', 'categoria_empresa.empresa_id')
			->where('categorias.slug', '=', $slug_categoria)
			->select('empresas.*', 'categorias.titulo')
			->forPage($page, $perPage)
            ->get();	
			
		// Paginacao
		$link_paginacao = route_url('MyPlugin::empresasAllSearchCategory');
		$link_paginacao = str_replace('{slug_categoria}', $slug_categoria, $link_paginacao);
		
		$totalItems = count($empresasTotal);
		$currentPage = $page;
		$urlPattern = $link_paginacao.'/?p=(:num)';

		$paginator = new Paginator($totalItems, $perPage, $currentPage, $urlPattern);	
			
		return view('@MyPlugin/empresa/list_search.twig', ['empresas' => $empresas, 'paginator' => $paginator]);	
	}

}