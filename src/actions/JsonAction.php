<?php
namespace Agrandesr\actions;

use Agrandesr\actions\ActionBuilder;
use Agrandesr\GlobalResponse;

class PhpAction extends ActionBuilder {
    protected array $rawData;

    public function execute() {
        GlobalResponse::addData($this->rawData);
    }
}