# Gym Assistant App
## Requirements
- Docker
- Make

## Deployment
1) `git clone https://github.com/lehenskiy/gym-assistant`
2) `cd gym-assistant`
3) `make init`
4) go to `ga.localhost`

- For local development run `composer install` since the host and container are not sharing `vendor` directory

## Running tests
1) `make migrate-test test`

- Run `make init` if not done yet
- If there are no new migrations, run `make test`
- Coverage report is stored in ./var/coverage/html
