# Agile router

Agile router is a composer library designed to create APIs in PHP in a fast and efficient way. It is an agile and great router. The main ideas is to make the creation of apis:

1. Agile: You can create first apis concept very quickly and improve with time.
2. Easy: The idea is that the router make all the boring work of check parameters and the developer could start to work to fun parts of the project XD

# How to start 🚀

Follow the next steps to start to working with this php router. If you found anything wrong or bad explained don't doubt to contact with us (correctly _me_... but _us_, sounds better 😅).

## Prerequisites 📋

You will need to have installed php and composer in your computer.

## Installation 🔧

You need to require the package to your project.

```bash
composer require agrandesr/agile-router
```

Next, you can use in your code. We encourage to use in the root file _index.php_. It is important to write under the autoload require.

```php
<?php

require './vendor/autoload.php';

use Agrandesr\Router;

$router = new Router();

$router->run();

```

Finally, you need to create a _routes.json_ file to start to create your API.

There is a easy example to test!

```json
{
    "hi": {
        "GET": {
            "execute":[
                {
                    "type":"json",
                    "content":{
                        "showAll":false,
                        "body":{
                            "Obi-wan":"Hello there!",
                            "Grievous": "General Kenobi"
                        }
                    }
                }
            ]
        }
    }
}
```

With this you will have a operative endpoint "hi" with the content:

```json
{
    "Obi-wan":"Hello there!",
    "Grievous": "General Kenobi"
}
```

You only need to up the server with php. For example:
``` bash
php -S localhost:8000
```

Now you can start to use the router json file following the documentation. We recommend to read quickly all the documentation at least one time before start. Yes, yes, it is boring, but trust me it will be very usefull.


# Documentation

The main elements of the library are used in the Router file. But they can be use in different ways.

*Future I will try to make an easy video tutorial to make easy the first steps.*

## Routes file options

The most important part of the framework are the routes files. The file, named routes.json have to be in the root of the project with index.php (you can change that with an advanced configuration, but first easy mode). In this file you can create and run a first operative API in a few steps.

The structure of the file is first, the paths; second, the methods; third, the framework properties for this endpoint.

```json
{
    "path":{
        "METHOD1":{
            "FrameworkProperty1":{},
            "FrameworkProperty2":{},
            "FrameworkProperty3":{}
        },
        "METHOD2":{},
        "METHOD3":{}
    }
}
```

The paths are defined by users and separated by the character "/". To indicate a dynamic route, that is to say that a slug of the route is variable, you should only enclose it in brackets ({}). For example:

```bash
section/{slug}/section/{slug}
```

The methods are the normal request methods:

- GET
- POST
- UPDATE
- PUT
- DELETE
- ...

### Checkers

The framework consists of some properties to validate the most common data of a request, such as the parameters in the request or the Body.

Below is an example of a request using the available checkers.

```json
{
    "params":{
        "POST":{
            "req_headers":["x-header"],
            "req_parameters":["param"],
            "req_body":["id","data.name","data.surname"],
            "render":{
                "type":"json",
                "content":{
                    "header":"^x-header^",
                    "param":"?param?",
                    "showAll":false,
                    "body":{
                        "id":"$id$",
                        "data": {
                            "name":"$data.name$",
                            "surname":"$data.surname$"
                        }
                    }
                }
            }
        }
    }
}
```

How you can see you can check the body, header and request parameters in a easy way.

At the same time, you can use these values for the response indicated in content for renders of type JSON (it can also be used with renders of type SQL).


For specific checker documentation:

- [headers checker](.docs/checkers/headerChecker.md)
- [params checker](.docs/checkers/paramChecker.md)
- [body checker](.docs/checkers/bodyChecker.md)
- [token checker](.docs/checkers/tokenChecker.md)

### Render method

The render method or property within the JSON is in charge of performing the final action and whose objective is to print the final result of the API.
The idea is that the JSON method can be used mainly to prototype your API and that the frontend can start working with the API already deployed in production. Afterwards, you can calmly work replacing the JSON-type renderer with a CLASS-type renderer to make the code you need.
Also, from JSON and CLASS you have SQL type. This is intended for rapidly prototyping APIs that interface to a database.
You can find more information on each method below.

- [JSON render](.docs/render/jsonRoutes.md) - Show a basic JSON default response.
- [CLASS render](.docs/render/ClassRoutes.md) - Run the class that you decide and inside the class you can do what you want like vainilla php.
- [SQL render](.docs/render/sqlRoutes.md) - Return the response of a query to a SQL database.

## PHP Framework tools

Hi! The next tools are optionals. There were created to develop the framework and they are already downloaded. For this reason, could be a good idea to make a look. If the classes are good for you, cool! If you don't like... You can only ignore them or rewrite the code to delete... 😭

- [GlobalRequest](.docs/tools/GlobalRequest.md)
- [GlobalResponse](.docs/tools/GlobalResponse.md)

## Some relevant information

### Routes priority

It is very important to know that the JSON path is from top to bottom. So if you write two possible routes, the first one will always be executed. It is very important to keep that in mind. For example, for the call:

```bash
localhost/test/slug/test
```

With the Routes file (simplified):

```json
{
    "test/{id}/test":{"...":"..."},
    "test/slug/test":{"...":"..."},
    "{id}/{value}/test":{"...":"..."}
}
```

Always will be enter to the configuration of the first route in your json file.

<br><br><br>

---

---

<br><br><br>

# Aditional information

<!--

Contributing 🖇️ 
Please read [CONTRIBUTING.md]() for details of our code of conduct, and the process for sending us pull requests. 

-->

## Versioning: 📌

For all available versions, see the [tags in this repository](https://github.com/AGrandesR/AGR/tags).

## Authors ✒️

* **A.Grandes.R** - *Main worker* - [AGrandesR](https://github.com/AGrandesR)

You can also look at the list of all [contributors] (https://github.com/your/project/contributors) who have participated in this project.

## License 📄

This project is under the License MIT - read the file [LICENSE.md](LICENSE.md) for more details.

## Thanks to: 🎁

* [Villanuevand](https://github.com/Villanuevand) for his incredible [template](https://gist.github.com/Villanuevand/6386899f70346d4580c723232524d35a) for documentation 😊
* [Alberto Ramirez](https://github.com/albertorc87) for his inspiration in the magic world of PHP.
