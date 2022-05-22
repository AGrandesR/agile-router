#  **JSON response**

In the first case, when you need to create a simple json response it is very easy. You need to indicate the **type** as **json** and insert the json in the content.

You can insert the json in content like a string:
``` json
{
"render":{
    "type":"json",
    "content":"{\"test\":\"test with string\"}"
}

```
Or you can insert the json in content like a object:
``` json
{
"render":{
    "type":"json",
    "content":{
        "test":"test with object"
    }
}
```

Return to [home](../../README.md#render-method)