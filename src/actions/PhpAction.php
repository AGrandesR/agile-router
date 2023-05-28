<?php
namespace Agrandesr\actions;

use Agrandesr\actions\ActionBuilder;

class PhpAction extends ActionBuilder {
    protected string $path;

    public function execute() {
        $this->checkRequiredKeys(['path']);

        return require($this->path);
    }
}