# Container Templates

## Directory structure

    - .docker/
        # Contains all Dockerfiles and configuration files for the containers
        # like php, mariaDB, httpd
    - app/
        # contains all the application files
        # like composer.json, vendor/ config etc.
        public/
            # Document root
    - bin/
        # contains binaries like php-shell
        # console or wp 
    .env
        # Contains environment variables for 
        # build and runtime of the containers

## PHP and file permissions

In order to prevent file permission issues between the host user and the php container, the Dockerfile for the php container assigns the UID/GID of the host user to the user www-data of the container _at build time_. To do this, you have to add your UID/GID (typically they are `1000`) to the .env file

Another way would be to use the `.docker/php/entrypoint.sh` to do this at runtime but I haven't tested it yet.
