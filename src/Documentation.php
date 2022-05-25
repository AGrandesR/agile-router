<?php
namespace AgrandesR;

class Documentation {
    private string $htmlBody="";

    function __construct(array $routerData) {
        //region 1. HEADER of DOCUMENTATION! : )
        if(isset($routerData['info'])){
            $this->createHeader(isset($routerData['info']['title']) && $routerData['info']['title']!="" ? $routerData['info']['title'] : 'API documentation', 1);
            if(isset($routerData['info']['description'])) $this->createParagraph($routerData['info']['description']);

        }
        //endregion

        //region 2. Add endpoints
        foreach($routerData['routes'] as $pathName=>$pathObject) {
            $this->htmlBody.="<div class='routeBlock'>";
            $pathName= '/'.$pathName;
            $this->createHeader($pathName,2);
            foreach($pathObject as $method=>$methodObject) {
                // print_r($method);die;
                $this->htmlBody.="<div class='path'>";
                $this->htmlBody.="<div class='endpoint'><span class='endpoint_method color_".strtolower($method)."'>$method</span><span class='endpoint_path'>$pathName</span></div>";
                if(isset($methodObject['description'])) $this->createParagraph($methodObject['description'],'path_description');
                $this->htmlBody.="</div>";
            }
            $this->htmlBody.="</div>";
        }
        //endregion
    }

    public function render() {
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><meta http-equiv='X-UA-Compatible' content='IE=edge'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Document</title></head>";
        echo "<style>.routeBlock{padding:10px;} .routeBlock > h2 { padding:2px 20px;}</style>";
        echo "<style>.path{background-color: beige; padding:10px; border:1px solid grey; margin:4px 8px;}.endpoint{height:25px; display:flex;flex-direction:row;justify-content:stretch; align-items:center;align-content:stretch; background-color:#d4f7cd;border: 1px solid #a7e1a1;}.color_get,.color_post{background:#10a54a;}.endpoint_method{padding:5px 15px; width:30px}.endpoint_path{padding:0px 10px;}</style>";
        echo "<body style='background-color: whitesmoke;'>";
        echo $this->htmlBody;
        echo "</body></html>";
    }

    private function createHeader(string $title, int $level) {
        $this->htmlBody .= "<h$level>$title</h$level>";
    }
    private function createParagraph(string $content, string $classes='') {
        $this->htmlBody .= "<p class='$classes'>$content</p>";
    }
}