<?php

include 'classeBanco.php';

class Pessoa extends classeBanco
{

    private $id = null;
    public $cpf = '';
    public $nome = '';
    public $email = '';

    public function __construct( $post )
    {
        parent::__construct( 'unicesumar_mapa' );
        $id = $this->salvar( $post );
        $this->setId( $id );
    }

    public function setId( $id )
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

}