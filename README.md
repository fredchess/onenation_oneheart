# Requirements

- Git
- PHP v8+
- Composer v2
- Mysql

# How to install and run this project

- Clone the project locally
- create a new branch from main
- Copy the content of .env.example to `.env` and change values in `.env` (database connexion ad others)
- run ` composer update` to install dependencies (Fix all dependencies errors if needed)
- Create a new mysql database (same name as in `.env`)
- Run the migrations: `php artisan migrate`
- Run the server : `php artisan serve`


# How to contribute

Just create a Pull Request from your branch to the main branch.

