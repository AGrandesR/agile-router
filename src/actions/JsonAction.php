<?php
namespace Agrandesr\actions;

use Agrandesr\actions\ActionBuilder;
use Agrandesr\extra\StringRouter;
use Agrandesr\GlobalResponse;

class JsonAction extends ActionBuilder {
    protected array $rawData;

    protected mixed $body;
    protected bool $parseValues=true;
    protected bool $showAll=true;

    public function execute() {
        $data = $this->body;
        if ($this->parseValues) $data = StringRouter::dataParseValues($data);
        GlobalResponse::setData($data);

        if($this->showAll) GlobalResponse::showAll();
        else GlobalResponse::showData();

        GlobalResponse::render();
    }
}