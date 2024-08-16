# Symfony Application with Docker

## Prerequisites

Before you start, ensure you have the following installed on your system:

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Make](https://www.gnu.org/software/make/)

## Getting Started

Follow these steps to set up and start the Symfony application using Docker.

### 1. Clone the Repository

Clone the repository containing your Symfony application:

```bash
git clone https://your-repository-url.git
cd your-repository-directory
```

### 2. Configuration
Ensure that you have a .env file in the root directory of your project. 
This file should contain necessary environment variables for Symfony and Docker, such as database connection details.
Create this .env file from the .env.dist file in the root folder of project 

### 3. Docker Setup
   The project uses Docker and Docker Compose to manage the environment. A Makefile is provided to simplify Docker Compose commands.

### * Build image and start the docker container
Run the following command to build and start the Docker containers:

````bash
    make up
````

This command will:

Build the Docker images if needed
Start the containers defined in docker-compose.yml

### * Stop the docker container

````bash
    make down
````

### * Check the status of your docker containers

````bash
    make ps
````

### * Access to the console of your php application

````bash
    make bash
````

### * Run composer inside your php container

````bash
    composer install
````

### * Run the migrations to the database inside your php container

````bash
    sf doctrine:migrations:migrate
````

4. Access the Application
   Once the containers are up and running, you can access your Symfony application in your web browser or using Postman application at:

````bash
    http://localhost:<port>
````

Where ```<port>``` is defined in the `.env` file

5. Test the application

### API Endpoints:

* Auth endpoints

* * `POST /api/register` Register user
* * `POST /api/login` Authenticate user response with a JWT token

* User endpoints

* * `GET /api/user` Get data of user authenticated
* * `PUT /api/user` Update profile of user authenticated

* Content endpoints

* * `POST /api/content` Create content
* * `POST /api/content` Fetch all contents with options to filter by name nor description
* * `GET /api/content/{id}` Fetch detail of content
* * `PUT /api/content/{id}` Update existent content
* * `DELETE /api/content/{id}` Remove existent content

* Marketplace endpoints

* * `POST /api/content/{id}/rate` Add rate to a content
* * `POST /api/content/{id}/favorite` Mark as favorite a content
* * `POST /api/content/favorites` Fetch favorites contents of user authenticated

### TO-DO

- API Documentation
- Fixtures over entities to load data in database
- Functional tests (using PHPUnit)
- Unit Tests (Using PHPUnit)
- Configuration to go to production environment
  - Dockerfile
  - docker-compose (prod mode)
  - Bash script to run all configuration of deployment

