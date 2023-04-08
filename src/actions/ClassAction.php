<?php

class ClassAction extends ActionBuilder {
    protected string $path;
    protected string $name;
    protected string $function;
    protected array $parameters;

    public function execute() {
        $this->checkRequiredKeys(['path','name','function']);

        $path = $this->path . '\\' .$this->name;
        $func = $this->function;
            $class= new $path();
        if(isset($this->parameters))
        $booleanValue = $class->$func(...);
    }
}