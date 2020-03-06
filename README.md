# Container Templates

This is yet another docker compose template for a typical LAMP stack for PHP web development. I created it with a few ideas in mind:

* Figure out how these docker and docker-compose things work
* Having a not to complex but flexible point to start from
* Having a dev enviroment at the end that works on (hopefully) all operating systems
* Keeping things simple and secure (for example I don't like the idea to give containers or even scripts access to my hosts file)

Provided services:

* httpd
* php-fpm 7.4 with composer, phive and SSH access to github and bitbucket
* mariadb 10.4
* memcached
* Elasticsearch
* Kibana

## Directory structure

    - .docker/
        # Contains all Dockerfiles and configuration files for the containers
        # like php, mariaDB, httpd
    - .logs/
        # error logs from php and apache
    - app/
        # contains all the application files
        # like composer.json, vendor/ config etc.
        # the content of this directory is actually
        # the stuff that gets deployed
        public/
            # Document root
    - bin/
        # binaries for the environment
    .env
        # Contains environment variables for 
        # build and runtime of the containers


## Bootstrapping

1. Clone this directory and remove `.git` and edit `.gitignore` (removing `app/` from it) as you usually want to start a new project with its own version history.
2. Copy `.env.example` to `.env` and fill in all variables.
3. Run `docker-compose up -d` and you should be able to browse `http://172.16.0.2` and see a welcome page. If you want to run several of these environments parallel, you have to adapt the subnet mask and IP addresses of the containers (see below). Don't worry if you see warnings during the build of the php container. They come from the memcached pecl extensions and can be ignored.

Now you have the following services:

 * **httpd (Apache2)**: with address `172.16.0.2:80`
 * **mariadb**: with Host: `db:3306` User: `db`, Password: `db`, Database: `db`
 * **memcached**: with Host: `memcached:11211`
 * **elasticsearch**: with Host: `elasticsearch:9200`
 * **kibana**: with Address: `172.16.0.7:5601`

## VHost config and document root
The document root is set to `app/public` but can be changed in `.docker/httpd/container-vhost.conf`. By default Apache's error logs are not mapped to the host. If you need them, uncomment the volume section in docker-compose.yml:

    services:
      httpd:
        build:
          context: .
          dockerfile: .docker/httpd/Dockerfile
        volumes:
          - ./app/public:/var/www/public:rw
          #  Enable if debugging of webserver is required:
          - ./.logs/httpd:/var/log/apache2:rw

## PHP configuration
The php service is configured with a default development configuration. If you want to change some configuration values you can do so by editing `.docker/php/php.ini`

### Database and Memcached connection

The database host is available with the following credentials:

    Host: db
    Port: 3306
    User: db
    Password: db
    Database name: db

wheras Memcached is available via

    Host: memcached
    Port: 11211

## PHP and file permissions

In order to prevent file permission issues between the host user and the php container, the Dockerfile for the php container assigns the UID/GID of the host user to the user www-data of the container _at build time_. To do this, you have to add your UID/GID (typically they are `1000`) to the .env file

Another way would be to use either the `ENTRYPOINT` file or the `command` in docker-compose.yml to do this at runtime but I haven't tested it yet.

## Elasticsearch

Elasticsearch will store its data to `.docker/elasticsearch/data`. It is important that this directory exists before you start the container, otherwise it will come to issues according to file permissions (on linux hosts only probably). However, the official ES image presumes that the UID and GID of your host user is `1000`. 

## Kibana

You can browse the Kibana frontend via http://172.16.0.7:5601. Note that the Kibana container needs a while to boot even docker-compose shows you its ready.

If you want to map port 5601 to localhost just uncommend the section in docker-compose.yml

      kibana:
        image: kibana:7.6.0
        ports:
          - 5601:5601

## Domain names
The httpd service by default accepts all `localhost` and `*.localhost` domain names. So all you need to do is adding a domain name to your hosts file like this:

    172.16.0.2  acme.localhost

## Networking and ports
If you want to use your actual localhost (127.0.0.1), just enable the `ports` section for the httpd service in docker-compose.yml:

    services:
      httpd:
        ports:
          - 80:80

This will only work if there is no other service or container listening to port 80 on your host.

In case you want to have dedicated IP addresses for your development environment you'll have to change the subnet and all IP addresses in docker-compose.yml. Remember to change the remote_host for the XDebug config of the php service as well:

    services:
      httpd:
        networks:
          default:
            ipv4_address: 172.16.0.2
      php:
        environment:
          XDEBUG_CONFIG: "remote_enable=1 remote_host=172.16.0.1 idekey=PHPSTORM profiler_enable=0"
        networks:
          default:
            ipv4_address: 172.16.0.2
    
    networks:
      default:
        driver: bridge
        ipam:
          config:
            - subnet: 172.16.0.0/24
