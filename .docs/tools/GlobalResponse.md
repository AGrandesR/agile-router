# GlobalResponse
This class is a global usage interface for a response class. Specifically, it is a tool to standardize the response throughout the code in a fast and dynamic way. At the same time, it is designed to be able to respond and add errors to the response or return only a specific set of data if necessary. Below is an example of use.

## Basic example of use
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

## Functions
There are different functions to use static in GlobalResponse.

### getType()
This return the type that have now GlobalResponse to return the data. By default, JSON. The return type is always in uppercase and have to be in the list of allowed types. Actual availables: _JSON, TXT_.

**Parameters:**

- $type['string|required']: This is the name of the type that we can use in GlobalResponse. Actual availables: _JSON, TXT_.


**Return:**

- $ret['bool']:
### setType()
This can change the type of response, that is by default in JSON.

**Parameters:**

- $type['string|required']: This is the name of the type that we can use in GlobalResponse. Actual availables: _JSON, TXT_.


**Return:**

- $ret['bool']:

**Warnings:**

- Add warning if type doesn't is in the list of actual availables. (msg: Try to set a $type response, but this response type is not allowed)
    
### setRenderType()
- _Alias:_ **[setType](#setType())**

---
[Return to previous page](../../README.md)