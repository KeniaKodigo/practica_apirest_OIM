<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clientes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{
    public function index(){
        //all()
        $clientes = Clientes::all(); //select * from clientes

        if(!empty($clientes)){
            $json = array(
                "status" => 200,
                "total_clientes" => count($clientes),
                "detalle" => $clientes
            ); 
        }else{
            $json = array(
                "status" => 404,
                "total_clientes" => 0,
                "detalle" => "No hay clientes"
            ); 
        }

        //retornamos el arreglo en estilo json
        return json_encode($json, true);
    }

    //registrar un cliente
    public function store(Request $request){
        //Recoger Datos
        $datos = array(
            "nombre" => $request->input("nombre"),
            "apellido" => $request->input("apellido"),
            "email" => $request->input("email")
        );

        //validando si la persona ingreso los datos
        if(!empty($datos)){

            $validator = Validator::make($datos, [
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:clientes'
            ]);

            if($validator->fails()){
                $errors = $validator->errors();
                $json = array(
                    "status" => 400,
                    "detalle" => $errors
                );
                return json_encode($json, true);
            }else{
                //si la persona envio bien los campos registramos el cliente

                #creando los hash de id_cliente y llave_secreta
                $id_cliente = Hash::make($datos["nombre"].$datos["apellido"].$datos["email"]);
                //kenia paiz paizkenia5@gmail.com

                $llave_secreta = Hash::make($datos["email"].$datos["apellido"].$datos["nombre"], ['rounds' => 12]); //paizkenia5@gmail.com paiz kenia 

                //INSERT INTO table () VALUES ()
                $cliente = new Clientes();
                $cliente->nombre = $datos["nombre"];
                $cliente->apellido = $datos["apellido"];
                $cliente->email = $datos["email"];
                $cliente->id_cliente =  $id_cliente;
                $cliente->llave_secreta = $llave_secreta;
                $cliente->save();

                $json = array(
                    "status" => 200,
                    "detalle" => "Registro exitos, por favor guarda tus credenciales",
                    "credenciales" => array("id_cliente" => $id_cliente, "llave_secreta" => $llave_secreta)
                );

                return json_encode($json, true);
            }
        }else{
            $json = array(
                "status" => 404,
                "detalle" => "Error al guardar el cliente",
        
            );

            return json_encode($json, true);
        }
    }
}
