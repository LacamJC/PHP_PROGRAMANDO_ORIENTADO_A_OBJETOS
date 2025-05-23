<?php

class CSVParser
{
    private $filename, $data, $header, $counter, $separator;

    public function __construct($filename, $separator = ',')
    {
        $this->filename = $filename;
        $this->separator = $separator;
        $this->counter = 1;
    }

    public function parse()
    {
        if(!file_exists($this->filename)){
            throw new FileNotFoundException("Arquivo {$this->filename} não encontrado");
        }
        if(!is_readable($this->filename)){
            throw new FilePermissionException("Sem permissões para usar o arquivo {$this->filename}");
        }
        $this->data = file($this->filename);
        $this->header = str_getcsv($this->data[0], $this->separator);
    }

    public function fetch()
    {
        if (isset($this->data[$this->counter])) {
            $row = str_getcsv($this->data[$this->counter ++], $this->separator);
            foreach ($row as $key => $value) {
                $row[$this->header[$key]] = $value;
            }
            return $row;
        }
    }
}
