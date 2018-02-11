# GeoIP lookup

This API allows IP lookups for geolocation data.

Note that this is intended as a programming test, and was not ever used in production - use at your own risk.

Credits go to [DB-IP](https://db-ip.com/) for the database

## Installation

Note that the commands bellow have been tested only using Docker for Mac 17.12

If you're not using this system, you'll need to replace the "xdebug.remote_host"
to your own host IP address (check ifconfig). Alternatively, just remove xdebug
from the Dockerfile, it is not required.

Be patient on the import-db command, it can take hours (13M records)

```
make install
make start
make import-db
```

Then head to [http://localhost:8081]()

To stop the containers:
```
make stop
```

## Tests

To run tests:

```
make install-tests
make start
make run-tests
```

## API Docs

Sorry, no OpenAPI doc yet, this will have to do ;)

IPs can be IP V4 or V6.

```
GET http://localhost:8081/lookup?ip=<IP>
200:
{
  "city": string,
  "region": string,
  "ip": string,
  "rangeStart": string,
  "rangeEnd": string
}
400:
GET http://localhost:8081/lookup?ip=<invalid IP>
404:
GET http://localhost:8081/lookup?ip=<unknownIP>
500:
GET http://localhost:8081/lookup?ip=<IP>
```

## TODOs

* There's no validation yet in the data we import
* No production environment prepared yet
* Prod env would need a proper load test, after a few tests querying takes 1s...
* No tests on import command with HTTP enabled
* Find a way to install & start the containers in one command (making the container silent maybe?)
* Swagger doc
* $body->rewind() in the tests shouldn't be necessary
* Ensure we always have json responses even on errors
