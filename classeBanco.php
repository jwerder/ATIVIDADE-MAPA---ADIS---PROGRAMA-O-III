<?php

abstract class classeBanco
{

    /**
     * dados para conexão com o banco
     * @access private
     */
    private $servidor = 'localhost';
    private $usuario = 'root';
    private $senha = 'root';
    private $esquema = '';

    /**
     * dados da conexão com o banco
     * @access private
     */
    private $conexao;
    private $resultado;
    private $erroMensagem;
    private $tabela;

    /**
     * Criar nova conexão
     * @var String host
     * @var String user
     * @var String password
     * @var String nome do schema
     * @var String porta, padrão é 3306
     * @return mixed link da conexão se sucesso ou false quando falha
     */
    function __construct( $esquema )
    {
        $this->setEsquema( $esquema );
        $this->conexao = new mysqli( $this->getServidor(), $this->getUsuario(), $this->getSenha(), $this->getEsquema() );

        if ( mysqli_connect_errno() ) {
            trigger_error( "Falha ao conectar no banco. " . $this->conexao->error, PHP_EOL );
        }

        $this->tabela = strtolower( $this->getClasseFilha() );
    }

    public function setServidor( $servidor )
    {
        $this->servidor = $servidor;
    }

    public function getServidor()
    {
        return $this->servidor;
    }

    public function setUsuario( $usuario )
    {
        $this->usuario = $usuario;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function setSenha( $senha )
    {
        $this->senha = $senha;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setEsquema( $esquema )
    {
        $this->esquema = $esquema;
    }

    public function getEsquema()
    {
        return $this->esquema;
    }

    /**
     * Fechar conexão
     * @return void
     */
    private function fecharConexao()
    {
        $this->conexao->close;
    }

    private function getClasseFilha()
    {
        $backtrace = debug_backtrace();
        return get_class( $backtrace[0]['object'] );
    }

    /**
     * inserir na tabela
     * @param array dados para inserção
     * @return mixed id da tabela se insert com sucesso, e false quando falha
     */
    public function salvar( $col )
    {
        $campo = "";
        $valor = "";
        foreach ( $col as $f => $v ) {
            $campo .= "$f,";
            $valor .= "'$v',";
        }
        $campoTratado = substr( $campo, 0, -1 );
        $valorTratado = substr( $valor, 0, -1 );
        $sql = "INSERT INTO `{$this->tabela}` ({$campoTratado}) VALUES ({$valorTratado});";
        if ( $result = $this->executaQuery( $sql ) ) {
            return $this->conexao->insert_id;
        }
        return false;
    }

    /**
     * Atualiar registro na tabela
     * @param Array dados para atualização
     * @param String condição
     * @example update("table",array("field1"=>"val1","field2"=>"val2"),"field1=key1");
     * @return boolean true se atualização com sucesso, e false se falha
     */
    public function atualizar( $dado, $condicao )
    {
        $sql = "UPDATE `{$this->tabela}` SET ";
        foreach ( $dado as $f => $v ) {
            $valores = (is_numeric( $v ) && (intval( $v ) == $v)) ? $v . "," : "'$v',";
            $sql .= "`" . $f . "`=" . $valores . "";
        }
        $sql = substr( $sql, 0, -1 );
        if ( $condicao != "" ) {
            $sql .= " WHERE " . $condicao;
        }
        return $this->executaQuery( $sql );
    }

    /**
     * Deleta um registro da tabela
     * @param String condição para deleção
     * @return boolean true se deleçao com sucesso, e false se falha
     */
    public function deletar( $condicao )
    {
        $sql = "DELETE FROM `{$this->tabela}` WHERE $condicao";
        return $this->executaQuery( $sql );
    }

    /**
     * Selecionar dados da tabela
     * @param Array campos da tabela
     * @param String condição do statement
     * @return true se query executada com sucesso, e false se falha
     */
    public function listar( $col, $condicao = "" )
    {
        $fld = "";
        $sql = "SELECT ";
        foreach ( $col as $c ) {
            if ( $c != "*" ) {
                $fld .= "`" . $c . "`,";
            } else {
                $fld .= $c . ",";
            }
        }
        $fld = substr( $fld, 0, -1 );
        $tbl = "`{$this->tabela}`";
        $sql .= $fld . " FROM " . $tbl;
        if ( $condicao != "" ) {
            $sql .= " WHERE " . $condicao;
        }
        if ( $this->executaQuery( $sql ) ) {
            $retorno = array();
            while ( $obj = $this->resultado->fetch_object() ) {
                $retorno[] = $obj;
            }
            $this->resultado->close();
            return $retorno;
        } else {
            $this->erroMensagem = "";
            $this->erroMensagem = $this->conexao->error;
            return false;
        }
    }

    /**
     * Executa query
     * @param String sql
     * @return true se sucesso, e false se falha
     */
    private function executaQuery( $sql )
    {
        $result = $this->conexao->query( $sql );
        if ( $result ) {
            $this->resultado = $result;
            return true;
        } else {
            $this->erroMensagem = "";
            $this->erroMensagem = $this->conexao->error;
            return false;
        }
    }

    /**
     * Obter mensagem de erro
     * @return String mensagem de erro
     */
    public function getErroMensagem()
    {
        return $this->erroMensagem;
    }

    /**
     * Obter linhas
     * @return array dados das linhas
     */
    public function getLinhas()
    {
        return $this->resultado->fetch_array( MYSQL_ASSOC );
    }

    /**
     * Obter número de linhas
     * @return número de linhas
     */
    public function getNumLinhas()
    {
        return $this->resultado->num_rows;
    }

    public function __destruct()
    {
        $this->fecharConexao();
    }

}
?>