# Spray - Laravel Code Generation Tool

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

Spray is a powerful tool for Laravel developers that focuses on code generation. It allows developers to concentrate on the business logic of their projects without worrying about routine API and code tasks.

## Prerequisites

Before using Spray, make sure you have the following:

1. A ready MySQL database for your project.
2. An empty Laravel project with an initialized database connection in the `.env` file.
3. The following path exists in your environment variables:                                                                            
`C:\Users\{UserName}\AppData\Roaming\Composer\vendor\bin`

## Getting Started

To use Spray, follow these steps:

1. Open terminal inside your project's root directory and Run the command `s-spray`.
2. You can also run the command `s-spray` outside any project and select the project you want or add a new project.
3. Follow the instructions.

Spray will check if the directory is a Laravel directory. Afterward, it will prompt you to enter your X-API-KEY for your Postman account and the collection ID for your project. You can skip this step and add these fields later. This step is necessary to create and keep your APIs up-to-date in your Postman collection.

You can also customize the body type of the Store and Bulk Store APIs that are generated in the Postman collection. By default, the body type is set to RAW JSON. However, you have the option to change it to the following options:
- Urlencoded
- Formdata
- Raw JSON

This allows you to choose the body type that best suits your API requirements and preferences.

## Code Generation

Spray will copy important files to your project the first time you use it. These files are crucial for getting your project ready to use and adding new APIs.
 The files include:

- Trait files for resource, response, and file management (such as file uploads).
- Language files in Arabic and English.
- Important middlewares like CORS, Authentication, and SetLocale. Please make sure to register these middlewares in your `App\Http\Kernel.php` file if you want to use them. After registering the middlewares, you can then use them in your `App\Providers\RouteServiceProvider.php` or with any specific route as per your requirements.
- A generic exception handler.
- Generic Controller, Service, Model, FormRequest, and Resource classes. These classes form the main structure of our projects, following a Service-Oriented Architecture (SOA), and handle all routine APIs, including Get All, Find By Id, Store, Bulk Store, Update, Delete, and Bulk Delete.


**Note: You can find these files inside the `resources` directory in Spray project.**

Spray will also model the database structure and save it for **monitoring** purposes. It will display a list of all existing tables and allow you to select any table or select all tables with options to generate APIs for them.

The code generation process includes:

- Generating a Controller that extends the generic Controller.
- Generating a Service that extends the generic Service.
- Generating a Model that extends the generic Model, including the fillable array, timestamps properties, and any hasMany or belongsTo relations.
- Generating a Request that extends the generic Request with validators for your selected APIs, filled with rules based on the table structure.
- Generating a Resource that extends the generic Resource with a filled array based on the table columns.
- Generating a Factory filled with faker for each column, using the column name for built-in functions or the column type for custom faking.
- Generating routes based on your selected APIs and appending them to the `routes/api.php` file.
- Generating the APIs in your Postman collection, allowing you to conveniently test and interact with them.
- Filling the language files with validation attributes based on your unique database table columns, translated into English, and leaving them empty in the Arabic file.

After the code generation process is complete, all the generated APIs for your selected tables are ready to use without any further editing. Simply run `php artisan optimize` to finalize the generation process.

## Monitoring and Updating

Spray continuously monitors any changes in your database, such as adding, updating, or deleting columns or adding new tables. It will highlight the updated tables in the console to alert you to the changes.

Spray can also update all previously generated code, including form requests, resources, model fillable and timestamps properties, factories, Postman collection and language files. This feature saves you time by automatically updating the generated code to reflect any changes in your database.

## Requirements

- PHP version 7.4 or higher.

## Installation

You can install Spray using Composer:

```bash
composer global require sarkis-sh/laravel-spray
```

Alternatively, you can clone the repository and run the file inside the `bin` folder.

## Usage

Spray is a console-based tool. Simply follow the instructions provided by Spray in the terminal.

## Example

### Users table
| Name        | Type              | Null |
|-------------|-------------------|------|
| id          | unsigned bigint   | No   |
| name        | varchar(100)      | No   |
| age         | unsigned int      | Yes  |
| email       | varchar(51)       | No   |
| password    | varchar(255)      | No   |
| created_at  | timestamp         | Yes  |
| updated_at  | timestamp         | Yes  |

### Generated Classes

#### UserController
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Models\User;

class UserController extends GenericController
{
    public function __construct(private UserService $userService)
    {
        parent::__construct(new UserRequest(), new UserResource([]), new UserService(new User()));
    }
}
```

#### UserService
```php
<?php

namespace App\Services;

class UserService extends GenericService
{
}
```

#### User Model
```php
<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends GenericModel
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'age',
        'email',
        'password',
    ];

    /**
     * Get all posts for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }
}

```
#### UserRequest
```php

<?php

namespace App\Http\Requests;

class UserRequest extends GenericRequest
{
    /**
     * Dynamically Get the the validation rules based on the request's action method.
     *
     * @return array
     */
    public function rules()
    {
        $method = request()->route()->getActionMethod();
        return $this->{$method . 'Validator'}();
    }

    /**
     * Validation rules for the 'store' action.
     *
     * @return array
     */
    private function storeValidator()
    {
        return [
            'name'      =>  'required|string|max:100',
            'age'       =>  'nullable|integer',
            'email'     =>  'required|string|max:51|unique:users,email',
            'password'  =>  'required|string|max:255',
        ];
    }

    /**
     * Validation rules for the 'bulkStore' action.
     *
     * @return array
     */
    private function bulkStoreValidator()
    {
        return [
            'list'             =>  'required|array',
            'list.*.name'      =>  'required|string|max:100',
            'list.*.age'       =>  'nullable|integer',
            'list.*.email'     =>  'required|string|max:51|unique:users,email',
            'list.*.password'  =>  'required|string|max:255',
        ];
    }

    /**
     * Validation rules for the 'update' action.
     *
     * @return array
     */
    private function updateValidator()
    {
        return [
            'name'      =>  'required|string|max:100',
            'age'       =>  'nullable|integer',
            'email'     =>  'required|string|max:51|unique:users,email,' . $this->id . ',id',
            'password'  =>  'required|string|max:255',
        ];
    }

    /**
     * Validation rules for the 'bulkDelete' action.
     *
     * @return array
     */
    private function bulkDeleteValidator()
    {
        return [
            'ids'    =>  'required|array',
            'ids.*'  =>  'required|integer|exists:users,id',
        ];
    }
}

```
#### UserResource
```php
<?php

namespace App\Http\Resources;

class UserResource extends GenericResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'          =>  $this->id,
            'name'        =>  $this->name,
            'age'         =>  $this->age,
            'email'       =>  $this->email,
            'password'    =>  $this->password,
            'created_at'  =>  $this->created_at,
        ];
    }
}

```
#### UserFactory
```php

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'   =>  $this->faker->name(),
            'age'    =>  $this->faker->numberBetween(0, 2147483647),
            'email'  =>  $this->faker->unique()->safeEmail(),
        ];  
    }
}
```
#### Routes
```php
Route::group([
    'prefix' => '/users',
    'controller' => UserController::class,
    // 'middleware' => ''
], function () {
    Route::delete('/', 'bulkDelete');
    Route::delete('/{id}', 'delete');
    Route::put('/{id}', 'update');
    Route::post('/bulk', 'bulkStore');
    Route::post('/', 'store');
    Route::get('/{id}', 'findById');
    Route::get('/', 'getAll');
});

```

## License

Spray is released under the MIT License.


## Future Plans

Enhance Spray to support multiple SQL databases, including but not limited to MySQL, SQL Server, SQLite, PostgreSQL, etc.