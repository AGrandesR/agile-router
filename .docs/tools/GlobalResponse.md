# GlobalResponse
This class is a global usage interface for a response class. Specifically, it is a tool to standardize the response throughout the code in a fast and dynamic way. At the same time, it is designed to be able to respond and add errors to the response or return only a specific set of data if necessary. Below is an example of use.
###### _init.php_ - Hypothetical startup file
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
###### _tool.php_ - Hypothetical class used by several different services in our code
``` php
use AgrandesR\GlobalResponse;
class tool() {
    function  check($bool){
        if($bool) GlobalResponse::addData('Data');
        else GlobalResponse::addError('CHECK FAIL!!!')
    }
}
```
###### _send.php_ - Hypothetical class that would perform an action of sending the content to the client
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
The result of the previous cycle would be:
``` json
{
    "develop":"execute"
}
```
If we change the boolean value of the _init.php_ file from false to true.
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
So it would give:
``` json
{
    "develop":"execute"
}
```

---
[Return to previous page](../../README.md)