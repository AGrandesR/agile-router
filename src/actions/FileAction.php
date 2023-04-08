<?php

namespace Agrandesr\actions;

use Agrandesr\actions\ActionBuilder;
use Agrandesr\tool\Utils;

class FileAction extends ActionBuilder {
    protected string $path;

    public function execute() {

        $extension = pathinfo($this->path, PATHINFO_EXTENSION);

        $mimeType=Utils::getMimeTypeFromExtension($extension);
        header("Content-Type: $mimeType");
        ob_end_flush();
        readfile($this->path);
    }
}