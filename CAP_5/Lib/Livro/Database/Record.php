<?php

namespace Livro\Database;

use Exception;

abstract class Record
{
    protected $data;

    public function __construct($id = NULL)
    {
        if ($id) {
            $object = $this->load($id);

            if ($object) {
                $this->fromArray($object->toArray());
            }
        }
    }

    public function __clone()
    {
        unset($this->data['id']);
    }

    public function __set($prop, $value)
    {
        if (method_exists($this, 'set_' . $prop)) {
            call_user_func(array($this, 'set_' . $prop), $value);
        } else {
            if ($value === NULL) {
                unset($this->data[$prop]);
            } else {
                $this->data[$prop] = $value;
            }
        }
    }

    public function __get($prop)
    {
        if (method_exists($this, 'get_' . $prop)) {
            return call_user_func(array($this, 'get_' . $prop));
        } else {
            if (isset($this->data[$prop])) {
                return $this->data[$prop];
            }
        }
    }

    public function __isset($prop)
    {
        return isset($this->data[$prop]);
    }

    private function getEntity()
    {
        $class = get_class($this); // Obtém o nome da classe
        return constant("{$class}::TABLENAME"); // Retorna a constante de classe TABLENAME
    }

    public function fromArray($data)
    {
        $this->data = $data;
    }

    public function toArray()
    {
        return $this->data;
    }

    public function store()
    {
        $prepared = $this->prepare($this->data);

        // Verifica se tem ID ou se existe na base de dados
        if (empty($this->data['id'])) { // or (!$this->load($this->id))
            // Incrementa o ID 
            print "POAKDOKASPDOK" . PHP_EOL;
            if (empty($this->data['id'])) {
                $this->id = $this->getLast() + 1;
                $prepared['id'] = $this->id;
            }

            // Cria uma instrução sql de insert
            $sql = "INSERT INTO {$this->getEntity()} " .
                '(' . implode(', ', array_keys($prepared)) . ' )' .
                ' VALUES ' .
                '(' . implode(', ', array_values($prepared)) . ' )';
        } else {
            //monta um update

            $sql = "UPDATE {$this->getEntity()}";
            if ($prepared) {
                foreach ($prepared as $column => $value) {
                    if ($column !== 'id') {
                        $set[] = "{$column} = {$value}";
                    }
                }
            }

            $sql .= ' SET ' . implode(', ', $set);
            $sql .= ' WHERE id=' . (int)$this->data['id'];
        }

        if ($conn = Transaction::get()) {
            Transaction::log($sql);
            $result = $conn->exec($sql);
            echo PHP_EOL . $sql;
            return $result;
        } else {
            throw new Exception('Não há transação ativa');
        }
    }

    public function load($id)
    {
        $sql = "SELECT * FROM {$this->getEntity()}";
        $sql .= " WHERE id=" . (int)$id;

        if ($conn = Transaction::get()) {
            Transaction::log($sql);
            $result = $conn->query($sql);

            if ($result) {
                $object = $result->fetchObject(get_class($this));
            }

            return $object;
        } else {
            throw new Exception('Não há transação ativa');
        }
    }

    public function delete($id = NULL)
    {
        $id = $id ? $id : $this->id;

        $sql = "DELETE FROM {$this->getEntity()}";
        $sql .= " WHERE id=" . (int) $this->data['id'];

        if ($conn = Transaction::get()) {
            Transaction::log($sql);
            $result = $conn->exec($sql);
            return $result;
        } else {
            throw new Exception('Não há transição ativa');
        }
    }

    public static function find($id)
    {
        $classname = get_called_class();
        $ar = new $classname;
        return $ar->load($id);
    }

    private function getLast()
    {
        if ($conn = Transaction::get()) {
            $sql = "SELECT max(id) as max FROM {$this->getEntity()}";

            Transaction::log($sql);

            $result = $conn->query($sql);

            $row = $result->fetch();

            return $row[0];
        } else {
            throw new Exception('Não há transação ativa');
        }
    }

    public function prepare($data)
    {
        $prepared = array();

        foreach ($data as $key => $value) {
            $prepared[$key] = $this->escape($value);
        }

        return $prepared;
    }

    public function escape($value)
    {
        if (is_string($value) and (!empty($value))) {
            $value = addslashes($value);
            return "'$value'";
        } else if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        } else if ($value !== '') {
            return $value;
        } else {
            return "NULL";
        }
    }
}
