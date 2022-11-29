#### Clone the repository from github to your local machine:
```git clone https://github.com/danilo4web/php-challenge```

#### Open the project folder:
```cd php-challenge```

#### Create .env file configurations from the env.sample
```cp .env.sample .env```

#### Build the docker containers
```docker-compose up -d --build```

#### Run the composer update:
```docker exec -it php-challenge_php composer update```

#### Run the Queue consumer:
```docker exec -it php-challenge_php php cli/consumer.php```

#### You can check the queue messages by the Rabbitmq App on: 
```
http://localhost:15672/
user: guest
pass: guest
```

#### Run the integration tests:
```docker exec -it php-challenge_php composer test```

#### Check PSR-12:
```docker exec -it php-challenge_php composer check-psr12```


### Attention: `You should create a temp smtp account on: https://mailtrap.io/ and set/change the credentials in the .env file`
```
# SwiftMailer Settings
MAILER_HOST=smtp.mailtrap.io
MAILER_PORT=465
MAILER_USERNAME=test
MAILER_PASSWORD=test
```


## API Endpoints:
### Register New User:

##### `POST /users`
```json
{
    "name": "User Name",
    "email": "test@mail.com",
    "password": "secret123!"
}
```
Use this endpoint to register in the API.

### Login:
#### `POST /login`
```json
{
"email": "test@mail.com",
"password": "secret123!"
}
```
Use this endpoint to login in the api, as a response (if the credentials is valid) you'll get the token you should provide in the protected endpoints.

### Search Stock Market Quote
#### `GET /stock?q={symbol}`
Use this endpoint to check the current quote of the stock. Change the symbol {symbol} by other one you would like to see the current quote.


### History
#### `GET /history`
Use this endpoint to see the latest stock market quote search.

#### Postman collection with the endpoints:
**<a href="./postman_collection.json" target="_blank" rel="noopener">Postman Collection</a>**
