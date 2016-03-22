<?php

include 'classePessoa.php';

$pessoa = new Pessoa( $_POST );

var_dump( $pessoa->listar( array( "*" ) ) );
