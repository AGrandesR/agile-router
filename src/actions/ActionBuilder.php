<?php
namespace Agrandesr\actions;
use Agrandesr\extra\StringRouter;
use Exception;

class ActionBuilder {
    private string $type;
    protected array $rawContent;

    function __construct(string $type, array $contentOfTheAction=[], $parseStringValues=true) {
        $this->type=$type;
        foreach ($contentOfTheAction as $key => $value) {
            $this->$key = StringRouter::dataParseValues($value);
        }
        $this->rawContent=$contentOfTheAction;
    }

    protected function checkRequiredKeys(array $keys) {
        foreach ($keys as $key) {
            if(!isset($this->$key)) throw new Exception("The $key is required in the " . $this->type . " action type.");
        }
    }
}