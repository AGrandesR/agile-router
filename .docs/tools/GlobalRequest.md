# GlobalRequest
This class is an interface for global use that contains the values ​​of the request. Really, there's nothing special about this class that you can't get from using vanilla PHP. In addition, that in some aspects it has its limitations. But we will improve it over time to make it as practical as possible.

###### _routes.json_ - Hypothetical class used by several different services in our code
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
###### _requestCall_ - Hypothetical server call
``` bash
curl localhost:9000/parser/mewtwo
```
###### _randomFile.php_ - Hypothetical function
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
