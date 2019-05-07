<?php

namespace ioliga\Http\Controllers;

use Illuminate\Http\Request;
use ioliga\Models\Estadio;
use ioliga\DataTables\EstadioDataTable;
use Illuminate\Support\Facades\Auth;
use ioliga\Http\Requests\Estadio\RqGuardarEstadio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Estadios extends Controller
{
	public function __construct(Estadio $estadioModel)
    {
        $this->middleware('auth');
    }

    public function index(EstadioDataTable $dataTable)
    {
    	$this->authorize('view',Estadio::class);
		return $dataTable->render('estadios.index');
    	
    }

    public function crear()
    {
    	$this->authorize('create',Estadio::class);
    	return view('estadios.crear');
    }

    public function guardar(RqGuardarEstadio $request)
    {
        $this->authorize('create',Estadio::class);

        $estadio=new Estadio;
        $estadio->nombre=$request->nombre;
        $estadio->direccion=$request->direccion;
        $estadio->telefono=$request->telefono;
        $estadio->usuarioCreado=Auth::id();
        if ($estadio->save()) {
            if ($request->hasFile('foto')) {
                $foto=$estadio->id.'_'.Carbon::now().'.'.$request->foto->extension();
                $path = $request->foto->storeAs('estadios', $foto,'public');
                $estadio->foto=$foto;    
                $estadio->save();
            }
        }
        $request->session()->flash('success','Nuevo estadio creado');
        return redirect()->route('estadios');
        
    }

    public function editar($CodigoEstadio)
    {
    	$estadio = Estadio::find($CodigoEstadio);    	    	
        $this->authorize('update',$estadio);
    	return view('estadios.editar',compact('estadio'));
    }

    public function actualizar(Request $request, $id)
    {

    	$request->validate([
    		'nombre'=>'required|unique:estadio,nombre,'.$id,
    		'direccion'=>'required',
    		'telefono'=>'nullable|digits_between:6,10',
    	]);
    	$idUser=Auth::user()->id;
    	$estadio=Estadio::findOrFail($id);
        $this->authorize('update',$estadio);
    	$estadio->nombre=$request->nombre;
    	$estadio->direccion=$request->direccion;
    	$estadio->telefono=$request->telefono;
    	$estadio->usuarioActualizado=$idUser;
    	$estadio->save();
    	 return redirect('/estadios');
    }


    public function eliminar(Request $request,$idEstadio)
    {
        $estadio=Estadio::findOrFail($idEstadio);
        $this->authorize('delete',$estadio);
        try {
            $estadio->delete();
            $request->session()->flash('success','Estadio eliminado');
        } catch (\Exception $e) {
            $request->session()->flash('info','Estadio no eliminado');
        }
        return redirect()->route('estadios');
    }
}
