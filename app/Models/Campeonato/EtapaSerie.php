<?php

namespace ioliga\Models\Campeonato;

use Illuminate\Database\Eloquent\Model;
use ioliga\Models\Campeonato\Etapa;
use ioliga\Models\Campeonato\Fecha;
use ioliga\Models\Campeonato\GeneroSerie;
use ioliga\Models\Campeonato\Partido;

class EtapaSerie extends Model
{
   protected $table='etapaSerie';

    public function etapa()
	{
	    return $this->hasOne(Etapa::class,'id','etapa_id');
	}
	public function fechas()
	{
		return $this->hasMany(Fecha::class, 'etapaSerie_id');
	}
	public function generoSerie()
	{
	    return $this->hasOne(GeneroSerie::class,'id','generoSerie_id');
	}

	public function fechasOrdenas()
	{
		return $this->hasMany(Fecha::class, 'etapaSerie_id')->orderby('fechaInicio');
	}

	/*buscar etapas partidos*/
	public function buscarPartidoRepetidos()
	{
		 return $this->hasManyThrough(
            Partido::class,
            Fecha::class,
            'etapaSerie_id', // Foreign key on users table...
            'fecha_id', // Foreign key on posts table...
            'id', // Local key on countries table...
            'id' // Local key on users table...
        );
	}
}
