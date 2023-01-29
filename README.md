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
                        "response":false,
                        "data":{
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