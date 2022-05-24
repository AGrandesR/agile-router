# GlobalResponse
Esta clase se trata de una interfaz de uso global para una clase de respuesta. En concreto, es una herramienta para estandarizar la respuesta a lo largo del código de una forma rápida y dinámica. Al mismo tiempo, está planteada para poder responder y añadir errores a la respuesta o devolver solo un conjunto de datos concretos si es necesario. A continuación tienes un ejemplo de uso.
###### _init.php_ - Hipotético fichero de arranque
``` php
use App\tool;
use App\send;
class init() {
    function run(){
        $tool=new tool();
        $tool->check(false);
        $tool=new send();
    }
}
```
###### _tool.php_ - Hipotética clase usada por varios servicios diferentes en nuestro código
``` php
use AgrandesR\GlobalResponse;
class tool() {
    function  check($bool){
        if($bool) GlobalResponse::addData('Data');
        else GlobalResponse::addError('CHECK FAIL!!!')
    }
}
```
###### _send.php_ - Hipotética clase que realizaría una acción de enviar el contenido al cliente
``` php
use AgrandesR\GlobalResponse;
class send() {
    function  result(){
        //Some code...
        GlobalResponse::addDataAndShowAndDie('All go ok','SendKey');
        //This code will never run
    }
}
```
El resultado del ciclo anterior sería:
``` json
{
    "develop":"execute"
}
```
Si moficamos el valor booleano del fichero _init.php_ de false a true.
``` php
use App\tool;
use App\send;
class init() {
    function run(){
        $tool=new tool();
        $tool->check(true);
        $tool=new send();
    }
}
```
Entonces daría:
``` json
{
    "develop":"execute"
}
```

===============
[Return to previous page](../../README.md)