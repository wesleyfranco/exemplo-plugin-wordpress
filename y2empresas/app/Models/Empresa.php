<?php namespace MyPlugin\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model {
	
	protected $table = 'empresas';
	
	protected $fillable = ['nome','localizacao','imagem','slug'];

    protected $primaryKey = 'id';
	
	public function categorias()
	{
		return $this->belongsToMany(Categoria::class);
	}
}