#  **SQL response**
This is a simple request to do MySQL actions (in future any SQL action).

``` json
"render":{
    "req_parameters":["id"],
    "type":"sql",
    "content":{
        "sql":"SELECT * FROM user WHERE id=?id?",
        "limit":100,
        "page":0
    }
}
```
### Configuration
You need to configure the env file with the connection to data:
- DB_TYPE:
- DB_HOST:
- DB_USER:
- DB_PASS:
- DB_DTBS:
- DB_PORT:

If you are going to have more than one connection you can add a flag after DB like:

- DB_RANDOM_TYPE
- DB_RANDOM_HOST
- DB_RANDOM_USER
- DB_RANDOM_PASS
- DB_RANDOM_DTBS
- DB_RANDOM_PORT

### Content values
The values of the object (content) are:
* **sql**: The namespace of the class that you want to call.
* **flag**: The name of the prefix in the database env keys.
<!--
* **limit**: The class name.
* **page**: The function that you want to call.
-->

### Example:
#### _render object:_
``` json
"render":{
    "req_parameters":["id"],
    "type":"sql",
    "content":{
        "sql":"SELECT * FROM user WHERE id=?id?",
        "limit":100,
        "page":0,
        "flag":"FLAGNAME"
    }
}
```
#### _env_files_
``` env
DB_FLAGNAME_HOST=localhost
DB_FLAGNAME_TYPE=mysql
DB_FLAGNAME_USER=root
DB_FLAGNAME_PASS=12345678
DB_FLAGNAME_DTBS=tests
DB_FLAGNAME_PORT=3306
```
#### _database user_
| id | name            | data            |
|----|-----------------|-----------------|
| 1  | Steve Rogers    | Captain America |
| 2  | Tony Stark      | Iron Man        |
| 3  | Natasha Romanov | Black Widow     |
| 4  | Thor            | The thunder god |
| 5  | Loki            | The blue boy    |

#### _response_
``` json
{
    "success": true,
    "code": 200,
    "data": [
        {
            "id": 1,
            "name": "Steve Rogers",
            "data": "Captain America"
        },
        {
            "id": 2,
            "name": "Tony Stark",
            "data": "Iron Man"
        },
        {
            "id": 3,
            "name": "Natasha Romanov",
            "data": "Black Widow"
        },
        {
            "id": 4,
            "name": "Thor",
            "data": "The thunder god"
        },
        {
            "id": 5,
            "name": "Loki",
            "data": "The blue boy"
        }
    ]
}
```


