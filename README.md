# API for NilaiKu

Sebuah Project Laravel 12 untuk backend API dari NilaiKu.


## Pustaka

 - [Laravel 12](https://laravel.com)
 - [Filament](#)
 - [Swagger UI](#)
 - [Sanctum](#)


## API Features

- post - login
- post - register
- post - predict (data prediksi) 
- get - all subject
- post - logout
- get - student by id/guest token session 
- put - update student by id (not mvp)
- post - student (data diri) 

## API Reference

#### Get all items

```http
  GET /api/v1/predict
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `api_key` | `string` | **Required**. Your API key |

#### Get item

```http
  GET /api/items/${id}
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `id`      | `string` | **Required**. Id of item to fetch |

#### add(num1, num2)

Takes two numbers and returns the sum.


## Installation

Install my-project with npm

```bash
  git clone https://github.com/GisatAZK2/Nilaiku-Api
  cd my-project
```
Install composer

```bash
  composer install

  cp .env.example .env
```

```bash
  php artisan key:generate
```
Buat dan sesuaikan database di file .env

Migrate database

```bash
  php artisan:migrate
```

Run Database Seeder

```bash
  php artisan db:seed --class=DatabaseSeeder
```

Add swagger

API docs, to access http://127.0.0.1:8000/api/documentation

```bash
  php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
  php artisan l5-swagger:generate
```
## Environment Variables

To run this project, you will need to add the following environment variables to your .env file for dev

`DB_DATABASE=<sesuaikan nama db dengan yang anda buat>`

`L5_SWAGGER_GENERATE_ALWAYS=true`

`L5_SWAGGER_CONST_HOST=http://127.0.0.1:8000`

`ML_API_URL=http://127.0.0.1:5001`

`ML_API_KEY=c43649ac42bc8e0259106ffd7cb9571cda6a03a1010d2c2c6415bab08dbf98e3`


## Running 

Mulai laravel

```bash
  php artisan serve
```

Storage Link

```bash
  php artisan storge:link
```

Buka http://127.0.0.1:8000

Buka http://127.0.0.1:8000/api/documentation untuk melihat dokumentasi penggunaan api

