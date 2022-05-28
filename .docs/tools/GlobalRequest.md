# GlobalRequest
Esta clase se trata de una interfaz de uso global que contiene los valores de la request. Realmente, no hay nada especial en esta clase que no puedas obtener del uso de PHP vainilla. Además, de que en algunos aspectos tiene sus limitaciones. Pero la iremos mejorando con el tiempo para que sea lo más práctica posible.

###### _routes.json_ - Hipotética clase usada por varios servicios diferentes en nuestro código
``` json
"parser/{pokemon}/{trainner}": {
    "GET":{
        "render": {
            "type":"json",
            "content":{
                "data":[
                    "{pokemon}",
                    "{trainner}"
                ],
                "status":"ok"
            }
        }
    }
},
```
###### _requestCall_ - Hipotética llamada al servidor
``` bash
curl localhost:9000/parser/mewtwo
```
###### _randomFile.php_ - Hipotética función
``` php
use AgrandesR\GlobalRequest;
class randomFile() {
    function  func(){
        $slug=GlobalRequest::getSlug('pokemon'); //Slug=mewtwo

    }
}
```



---
[Return to previous page](../../README.md)

<!--
## BASIC INFO GETTER

<table class="tg">
<tbody>
  <tr>
    <td class="tg-c3ow" colspan="9">HYPERLINK</td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="7">LINK</td>
    <td class="tg-baqh" colspan="2">ZELDA</td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="5">SUBJECT</td>
    <td class="tg-baqh" colspan="4">PREDICATE</td>
  </tr>
  <tr>
    <td class="tg-c3ow" rowspan="4">PROTOCOL</td>
    <td class="tg-c3ow" colspan="6">ADDRESS</td>
    <td class="tg-baqh" rowspan="4">PARAMETERS<br></td>
    <td class="tg-baqh" rowspan="4">FRAGMENT<br></td>
  </tr>
  <tr>
    <td class="tg-baqh" colspan="3">HOST</td>
    <td class="tg-baqh" colspan="3">PATH</td>
  </tr>
  <tr>
    <td class="tg-baqh" colspan="3">DOMAIN</td>
    <td class="tg-baqh" rowspan="2">PORT</td>
    <td class="tg-baqh" rowspan="2">FILE SECTION</td>
    <td class="tg-baqh" rowspan="2">FILE SLUG</td>
  </tr>
  <tr>
    <td class="tg-0lax">SUBDOMAIN</td>
    <td class="tg-0lax">DOMAINNAME</td>
    <td class="tg-0lax">DOMAINEXTENSION</td>
  </tr>
</tbody>
</table>
-->
