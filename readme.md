# Laravel Scraper

Esta aplicación tiene como  funcionalidad principal ser una API que ejecute un scraper hacia algunas tiendas de e-commerce (Liverpool, Linio, etc.) y obtenga información básica de los productos (nombre, descripción, precio), almacenandolos en una base de datos para su posterior consulta.

## Getting Started


### Prerequisitos

 - PHP >= 7.0.0
 - Laravel 5.5
 - MySQL 5.6


### Instalación


Clonar el repositorio de github.
```s 
$ git clone git@github.com:reynaldoeg/scraper.git
```

Entrar a la raiz del directorio
```s 
$ cd scraper
```

Instalar dependencias con Composer
```s 
$ composer install
```

### Configuración

Copiar archivo .env.example y cambiarle el nombre por .env y configurar opciones locales
```s 
$ cp .env.example .env
```

Configurar opciones locales y acceso a base de datos:
```s 
APP_NAME=Scraper
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_LOG_LEVEL=debug
APP_URL=http://127.0.0.1

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Generar "application key"
```s 
$ php artisan key:generate
```

### Migraciones
Esta aplicación incluye migraciones para crear las tablas que se utilizan y llenarlas con la información básica para su funcionamiento.

Para crear el repositorio de migraciones:
```s 
$ php artisan migrate:install
```

Para crear las tablas:
```s 
$ php artisan migrate
```

Para crear usuario del sistema:
```s 
$ php artisan db:seed
```

### Servidor local

Correr servidor local
```s 
$ php artisan serve
```

<p>Laravel se podrá ejecutar en la siguiente dirección <a href="http://127.0.0.1:8000" target="_blank">http://127.0.0.1:8000</a></p>

Para ingresar al sistema, se pueden usar las siguientes credenciales:
- Usuario: admin@mail.com
- Password: 123456

Aquí se podrá visualizar los productos cargados a la base de datos.

## Autenticación

El siguiente comando creará las claves de cifrado necesarias para generar tokens de acceso seguro:
```s 
$ php artisan passport:client
```

Generar claves de usuario:
```s 
$ php artisan passport:client
```
A la pregunta "Which user ID should the client be assigned to?" escribir por ejemplo:
- 0

A la pregunta "What should we name the client?" escribir por ejemplo:
- client

A la pregunta "Where should we redirect the request after authorization?" dejar la opción predeterminada.

Con esto se generará el Client ID y el Client Secret necesarios para obtener el access token.

### Hacer peticiones con PostMan
Mediante el programa de [postman](https://www.getpostman.com/) o algún otro similar se pueden hacer las peticiones a la API.
Para obtener el Acess Tiken se hace una petición al siguiente endpoint:
```s 
POST  http://127.0.0.1:8000/api/oauth/token  
```
y se envían los datos obtenidos de Client Id y Client Secret:
- grant_type: client_credentials
- client_id: 3
- client_secret: xxxxxxxxxxxxxx

Con lo que se obtendrá el access-token para poder hacer las peticiones

```s 
{
    "token_type": "Bearer",
    "expires_in": 604798,
    "access_token": "eyJ0eXAiOi...VcJ4EY"
} 
```

Para obtener los productos de un ecommerce por ejemplo linio, se hace la siguiente petición:
```s 
GET  http://127.0.0.1:8000/api/sources/linio/3  
```
Enviando el Header de Authorization con el access_token obtenido
- Authorization: Bearer eyJ0eXAiOi...VcJ4EY


## Métodos

### Obtener productos de tiendas

```s 
GET  http://127.0.0.1:8000/api/sources/linio/3  
```
Tiendas hasta ahora disponibles:
- linio
- liverpool

El último parámetro de la url indica el número de productos a descargar.
Si el producto ya existe en la base de datos, ya no lo descarga.
**Respuesta:**
Si la petición es correcta, se regresa un json con los atributos:
- new: con los nuevos productos que se guardan en la base
-  existing: con los productos que ya existen y no se duplican en la base

```s 
{
    "new": [
        {
            "name": "Monitor",
            "desc": "Monitor HP 32 de 31.5’’",
            "price": 8999
        },
        {
            "name": "IPhone",
            "desc": "IPhone XS MAX 64GB - Gold",
            "price": 29999
        }
    ],
    "existing": [
        {
            "name": "Monitor",
            "desc": "Monitor HP Curvo 27b de 27''",
            "price": 6149
        },
        {
            "name": "Membresía",
            "desc": "Membresía Linio Plus 1 año",
            "price": 250
        },
        {
            "name": "Memoria",
            "desc": "Memoria Micro Sd Hc I 32gb Kingston Clase 10",
            "price": 109
        }
    ]
}  
```
### Obtener productos almacenados en la base de datos

```s 
GET  http://127.0.0.1:8000/api/products  
```

**Respuesta:**
Si la petición es correcta, se regresa un json con todos los productos:

```s 
[
    {
        "id": 1,
        "store": "Linio",
        "name": "Monitor",
        "description": "Monitor HP Curvo 27b de 27''",
        "price": "6149",
        "created_at": "2018-10-11 23:20:46",
        "updated_at": "2018-10-11 23:20:46"
    },
    {
        "id": 2,
        "store": "Linio",
        "name": "Membresía",
        "description": "Membresía Linio Plus 1 año",
        "price": "250",
        "created_at": "2018-10-11 23:20:46",
        "updated_at": "2018-10-11 23:20:46"
    },
    {
        "id": 3,
        "store": "Linio",
        "name": "Memoria",
        "description": "Memoria Micro Sd Hc I 32gb Kingston Clase 1",
        "price": "109",
        "created_at": "2018-10-11 23:20:46",
        "updated_at": "2018-10-11 23:20:46"
    }
]
```
Para limitar la petición a productos de una tienda se puede incluir un parámetro en la url con el nombre de la misma:

```s 
GET  http://127.0.0.1:8000/api/products/linio  
```

```s 
GET  http://127.0.0.1:8000/api/products/liverpool  
```
Tiendas hasta ahora disponibles:
- linio
- liverpool

## Authors

* **Reynaldo Esparza**  - [Github](https://github.com/reynaldoeg)


## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

