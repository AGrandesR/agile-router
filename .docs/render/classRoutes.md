###  **Class response**
This is probably the really important render that you want. It is easy. You need to indicate the **type** as **class** and add the next object to value **content**.
``` json
{
    "path":"App\\internal",
    "name":"Test",
    "function":"json"
}
```
The values of the object are:
* **path**: The namespace of the class that you want to call
* **name**: The class name.
* **function**: The function that you want to call.

#### Example:
_render object:_
``` json
{
"render":{
    "type":"class",
    "content":{
        "path":"App\\internal",
        "name":"Test",
        "function":"json"
    }
}
```
_php class (in App\internal namespace)_
``` php
<?php
namespace App\internal;

class Test {
    public function json() {
        header('Content-Type: application/json');
        echo json_encode(["test"=>"This is only a test"]);
    }
}
```