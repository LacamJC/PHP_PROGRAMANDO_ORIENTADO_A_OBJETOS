<?php


require_once 'classes/ar/Produto.php';
require_once 'classes/api/Connection.php';
require_once 'classes/api/Transaction.php';

try {
    Transaction::open('estoque');

    $p1 = new Produto;
    $p1->descricao     = 'Vinho brasileiro Tinto Merlot';
    $p1->estoque       = 10;
    $p1->preco_custo   = 12;
    $p1->preco_venda   = 18;
    $p1->codigo_barras = '1231243123';
    $p1->data_cadastro = date('Y-m-d');
    $p1->origem        = 'N';

    $p1->save();
    Transaction::close();
} catch (Exception $e) {
    Transaction::rollback();
    print $e->getMessage();
}
