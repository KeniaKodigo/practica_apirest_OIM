<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clientes;
use App\Models\Cursos;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CursosController extends Controller
{
    public function index(Request $request){
    	$token = $request->header('Authorization');
    	$clientes = Clientes::all(); //select * from clientes
    	$json = array(); 	
    	foreach ($clientes as $key => $value) {
    		if("Basic ".base64_encode($value["id_cliente"].":".$value["llave_secreta"]) == $token){
    			// $cursos = Cursos::all();
    			if(isset($_GET["page"])){
                    //Cursos::select("")
    				$cursos = DB::table('cursos') //query builder, orm
	    			->join('clientes', 'cursos.id_creador', '=', 'clientes.id')
	    			->select('cursos.id', 'cursos.titulo', 'cursos.descripcion', 'cursos.instructor', 'cursos.imagen', 'cursos.id_creador', 'clientes.nombre', 'clientes.apellido')
	    			->paginate(10);
    			}else{
    				$cursos = DB::table('cursos')
	    			->join('clientes', 'cursos.id_creador', '=', 'clientes.id')
	    			->select('cursos.id', 'cursos.titulo', 'cursos.descripcion', 'cursos.instructor', 'cursos.imagen', 'cursos.id_creador', 'clientes.nombre', 'clientes.apellido')
	    			->get(); //retorno la consulta
    			}
		    	if(!empty($cursos)){
			    	$json = array(
			    		"status"=>200,
			    		"total_registros"=>count($cursos),
			    		"detalles"=>$cursos
			    		
			    	);
			    	return json_encode($json, true);
			    }else{
			    	$json = array(
			    		"status"=>200,
			    		"total_registros"=>0,
			    		"detalles"=>"No hay ningun curso registrado"
			    		
					);
			    }
    		}else{
    			$json = array(
			    		"status"=>404,
			    		"detalles"=>"No esta autorizado para recibir los registros"
			    		
			    	);
    		}
    	}
    	return json_encode($json, true);
    }

    public function store(Request $request){
    	$token = $request->header('Authorization');
    	$clientes = Clientes::all();
    	$json = array();
    	foreach ($clientes as $key => $value) {
    		
    		if("Basic ".base64_encode($value["id_cliente"].":".$value["llave_secreta"]) == $token){
				//Recoger datos
    			$datos = array( "titulo"=>$request->input("titulo"),
    				   			"descripcion"=>$request->input("descripcion"),
    				   			"instructor"=>$request->input("instructor"),
    				   			"imagen"=>$request->input("imagen"),
    				   			"precio"=>$request->input("precio"));
    			if(!empty($datos)){
    				//Validar datos
			    	$validator = Validator::make($datos, [
			            'titulo' => 'required|string|max:255|unique:cursos',
			            'descripcion' => 'required|string|max:255|unique:cursos',
			            'instructor' => 'required|string|max:255',
			            'imagen' => 'required|string|max:255|unique:cursos',
			            'precio' => 'required|numeric',
			        ]);
			        //Si falla la validación
			        if ($validator->fails()) {
			        	$errors = $validator->errors();
			        	$json = array(
			    		
			    			"status"=>404,
			    			"detalle"=>$errors
				    	);
				    	return json_encode($json, true);
			        }else{
			        	$cursos = new Cursos();
			        	$cursos->titulo = $datos["titulo"];
			        	$cursos->descripcion = $datos["descripcion"];
			        	$cursos->instructor = $datos["instructor"];
			        	$cursos->imagen = $datos["imagen"];
			        	$cursos->precio = $datos["precio"];
			        	$cursos->id_creador = $value["id"]; //14
			        	$cursos->save(); //INSER INTO cursos() values ()
		        		$json = array(
				    		"status"=>200,
				    		"detalle"=>"Registro exitoso, su curso ha sido guardado"
				    		
				    	);
				    	return json_encode($json, true);		       
			        }
    			}else{
    				$json = array(
			    		"status"=>404,
			    		"detalle"=>"Los registros no pueden estar vacíos"
			    	);
    			}
    		}
    	}
    	return json_encode($json, true); //convierto el arreglo en json
    }

    public function show($id, Request $request){
    	$token = $request->header('Authorization');
    	$clientes = Clientes::all();
    	$json = array();
    	foreach ($clientes as $key => $value) {
    		
    		if("Basic ".base64_encode($value["id_cliente"].":".$value["llave_secreta"]) == $token){
    			$curso = Cursos::where("id", $id)->get(); //select * from cursos where id = $id
		    	if(!empty($curso)){
			    	$json = array(
			    		"status"=>200,
			    		"detalles"=>$curso
			    		
			    	);
			    }else{
			    	$json = array(
			    		"status"=>200,
			    		"detalles"=>"No hay ningún curso registrado"
			    		
			    	);
			    }
    		
    		}else{
    			$json = array(
			    		"status"=>404,
			    		"detalles"=>"No está autorizado para recibir los registros"
			    		
			    	);
    		}		
    	}
    	return json_encode($json, true);
    }


    public function update($id, Request $request){

    	$token = $request->header('Authorization');
    	$clientes = Clientes::all();
    	$json = array();

    	foreach ($clientes as $key => $value) {
    		
    		if("Basic ".base64_encode($value["id_cliente"].":".$value["llave_secreta"]) == $token){
				//Recoger datos
    			$datos = array( "titulo"=>$request->input("titulo"),
    				   			"descripcion"=>$request->input("descripcion"),
    				   			"instructor"=>$request->input("instructor"),
    				   			"imagen"=>$request->input("imagen"),
    				   			"precio"=>$request->input("precio"));
    			if(!empty($datos)){
    				//Validar datos
			    	$validator = Validator::make($datos, [
			            'titulo' => 'required|string|max:255',
			            'descripcion' => 'required|string|max:255',
			            'instructor' => 'required|string|max:255',
			            'imagen' => 'required|string|max:255',
			            'precio' => 'required|numeric',
			        ]);
			        //Si falla la validación
			        if ($validator->fails()) {
			        	$errors = $validator->errors();
			        	$json = array(
			    		
			    			"status"=>404,
			    			"detalle"=>$errors
				    	
				    	);
				    	return json_encode($json, true);
			        }else{
			        	$traer_curso = Cursos::where("id", $id)->get(); //select * from cursos where id = $id
			        	if($value["id"] == $traer_curso[0]["id_creador"]){
			        		$datos = array("titulo"=>$datos["titulo"],
			        					   "descripcion"=>$datos["descripcion"],
			        					   "instructor"=>$datos["instructor"],
			        					   "imagen"=>$datos["imagen"],
	    				   				   "precio"=>$datos["precio"]);
			        		$cursos = Cursos::where("id", $id)->update($datos); //UPDATE cursos SET titulo = '', descripcion = '', instructor = ''... where id = $id
			        		$json = array(
					    		"status"=>200,
					    		"detalle"=>"Registro exitoso, su curso ha sido actualizado"
					    		
					    	);
					    	return json_encode($json, true);	
			        	}else{
			        		$json = array(
					    		"status"=>404,
					    		"detalle"=>"No está autorizado para modificar este curso"
					    	);
					    	return json_encode($json, true);
			        	}	        			       
			        }
    			}else{
    				$json = array(
			    		"status"=>404,
			    		"detalle"=>"Los registros no pueden estar vacíos"
			    	
			    	);
    			}
    		}
    	}
    	return json_encode($json, true);
    }

    public function destroy($id, Request $request){
    	$token = $request->header('Authorization');
    	$clientes = Clientes::all();
    	$json = array();
    	foreach ($clientes as $key => $value) {
    		
    		if("Basic ".base64_encode($value["id_cliente"].":".$value["llave_secreta"]) == $token){
    			$validar = Cursos::where("id", $id)->get(); //select * from cursos where id = $id
    			if(!empty($validar)){
    				if($value["id"] == $validar[0]["id_creador"]){
    					$curso = Cursos::where("id", $id)->delete(); //DELETE FROM cursos where id = $id
    					$json = array(
				    		"status"=>200,
				    		"detalle"=>"Se ha borrado su curso con éxito"
				    		
				    	);
				    	return json_encode($json, true);
    				}else{
    					$json = array(
					    		"status"=>404,
					    		"detalle"=>"No está autorizado para eliminar este curso"
					    	);
					    return json_encode($json, true);
    				}
    			}else{
    				$json = array(
			    		"status"=>404,
			    		"detalle"=>"El curso no existe"
			    	);
				    return json_encode($json, true);
    			}
    		}
    	}
    	return json_encode($json, true);
    	
    }
}
