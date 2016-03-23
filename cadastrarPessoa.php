<?php
include 'classePessoa.php';
$pessoa = new Pessoa( $_POST );
$linhas = $pessoa->listar( array( "*" ) );
?>

<!DOCTYPE html>
<html>

    <head>
        <title>Lista de Cadastros</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/lista.css">
    </head>

    <body>
        <h1>Lista de Cadastros</h1>

        <table class="responstable">

            <tr>
                <th>Id</th>
                <th>CPF</th>
                <th>Nome</th>
                <th>Email</th>
            </tr>

            <?php if ( $linhas ): ?>
                <?php foreach ( $linhas as $l ): ?>
                    <tr>
                        <td><?php echo $l->id; ?></td>
                        <td><?php echo $l->cpf; ?></td>
                        <td><?php echo $l->nome; ?></td>
                        <td><?php echo $l->email; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

        </table>

    </body>
</html>