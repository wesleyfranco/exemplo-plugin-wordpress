<?php namespace MyPlugin\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
	
	protected $fillable = ['titulo'];
	
	protected $primaryKey = 'id';
	
	public function empresas()
    {
        return $this->belongsToMany(Empresa::class);
    }
}