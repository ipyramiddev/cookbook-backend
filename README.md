# Cookbook Backend API (Laravel 9 Project)

This project is Backend API project for the Cookbook.
Cookbook is a web application that allows users to share, browse, and shop for different recipes. Cookbook automatically totals ingredients from different recipes that have been added to the cart, resulting in a grocery list you can use to simplify your grocery shopping experience.

## Prerequisites

- PHP >= 7.4
- Laravel Framework >= 8.0
- Composer - Dependency Manager for PHP
- MySQL >= 5.6

## Installation

1. Navigate to the project directory

```
cd project-directory
```

2. Install dependencies

```
composer install
```

3. Create a copy of the .env.example file and rename it to .env

```
cp .env.example .env
```

4. Generate an application key

```
php artisan key:generate
```

5. Generate an JWT secret key

```
php artisan jwt:secret 

```

6. Configure the database in the .env file

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run migrations to create the required database tables

```
php artisan migrate
```

8. (Optional) Seed the database with sample data

```
php artisan db:seed
```

## Running the Application

To start the Laravel development server, run the following command:

```
php artisan serve
```

You should see a message indicating that the server is running locally on `http://localhost:8000`.

Or
```
php artisan serve --host <my_ipaddress> --port <my_port>
```

Now you can login with one of following credientials.

    admin@cookbook.com / admin
    test1@cookbook.com / test1234
    test2@cookbook.com / test1234

## Testing

To run the tests for the project, use the following command:

```
php artisan test
```

