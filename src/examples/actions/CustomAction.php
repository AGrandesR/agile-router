<?php
namespace Agrandesr\examples\actions;
use Agrandesr\actions\ActionBuilder;

class CustomAction extends ActionBuilder {
    public function execute() {
        echo "HEHEHEHE";die;
    }
}