# Template for development PHP

Contains:
1. Docker
2. PHP
3. Nginx
4. Postgresql

## How to start

1. Create `.env`
    ```bash
    cp docker/.env.example docker/.env
    ```
1. Edit `docker/.env`  

   Change variable `COMPOSE_PROJECT_NAME=your_project_name`.  

1. Use Makefile.
