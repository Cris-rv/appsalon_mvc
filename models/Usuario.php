<?php 

namespace Model;

use Model\ActiveRecord;

class Usuario extends ActiveRecord {
    //Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = []) // Colocamos $args=[] porque los datos que recibimos de $_POST estan en forma de arreglo y para almacenarlos los mapeamos mediante el name del input y los agregamos en un arreglo asociativo
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? 0;
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';
    }

    // Mensajes de validacion para la creacion de una cuenta
    public function validarNuevaCuenta() {

        if(!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es obligatorio';
        }

        if(!$this->apellido) {
            self::$alertas['error'][] = 'El Apellido es obligatorio';
        }

        if(!$this->email) {
            self::$alertas['error'][] = 'El E-mail es obligatorio';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'El Password es obligatorio';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }

        if(!$this->telefono) {
            self::$alertas['error'][] = 'El Telefono es obligatorio';
        }

        return self::$alertas;
    }

    public function validarLogin() {

        if(!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        return self::$alertas;
    }

    public function validarEmail() {

        if(!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        return self::$alertas;
    }

    public function validarPassword() {

        if(!$this->password) {
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe ser de al menos 6 caracteres';
        }

        return self::$alertas;
    }

    // Revisa si el usuario ya existe
    public function existeUsuario() {
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1 ";

        $resultado = self::$db->query($query);

        if($resultado->num_rows) {
            self::$alertas['error'][] = "El usuario ya existe";
        }

        return $resultado;
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken() {
        $this->token = uniqid();
    }

    public function comprobarPasswordAndVerificado($password) {
        $resultado = password_verify($password, $this->password);

        // Si no hay resultado o confirmado esta como null entonces retorna un mensaje de error
        if(!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = "Password incorrecto o usuario no confirmado";
        } else {
            return true;
        };
    }

}