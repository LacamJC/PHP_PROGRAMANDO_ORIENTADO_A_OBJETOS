<?php

class ProdutoGateway
{
    private static $conn;

    public static function setConnection(PDO $conn)
    {
        self::$conn = $conn;
        if (self::$conn) {
            print 'Objeto PDO conectado ao banco' . PHP_EOL;
        }
    }

    public function find($id, $class = 'stdClass')
    {
        $sql = "SELECT *  FROM produto WHERE id = {$id}";
        print "$sql" . PHP_EOL;
        $result = self::$conn->query($sql);
        return $result->fetchObject($class);
    }

    public function all($filter, $class = 'stdClass')
    {
        $sql = 'SELECT * FROM produto';
        if ($filter) {
            $sql .= " WHERE $filter";
        }

        print "$sql" . PHP_EOL;

        $result = self::$conn->query($sql);
        return $result->fetchAll(PDO::FETCH_CLASS, $class);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM produto WHERE id = {$id}";
        print $sql . PHP_EOL;
        return self::$conn->query($sql);
    }

    public function save($data)
    {
        if (empty($data->id)) {
            $id = $this->getLastId() + 1;
            $sql = "INSERT INTO produto (id, descricao, estoque, preco_custo, preco_venda, codigo_barras, data_cadastro, origem) VALUES('{$id}', " .
                "'{$data->descricao}'," .
                "'{$data->estoque}'," .
                "'{$data->preco_custo}'," .
                "'{$data->preco_venda}'," .
                "'{$data->codigo_barras}'," .
                "'{$data->data_cadastro}'," .
                "'{$data->origem}')";
        } else {
            $sql = "UPDATE produto SET 
                descricao = '{$data->descricao}', " .
                "estoque = '{$data->estoque}'," .
                "preco_custo = '{$data->preco_custo}'," .
                "preco_venda = '{$data->preco_venda}'," .
                "codigo_barras = '{$data->codigo_barras}'," .
                "data_cadastro = '{$data->data_cadastro}'," .
                "origem = '{$data->origem}' " .
                " WHERE id = '{$data->id}'";
        }

        print "$sql" . PHP_EOL;

        return self::$conn->exec($sql);
    }

    private function getLastId()
    {
        $sql = "SELECT max(id) as max FROM produto";
        $result = self::$conn->query($sql);
        $data = $result->fetch(PDO::FETCH_OBJ);
        return $data->max;
    }
}
