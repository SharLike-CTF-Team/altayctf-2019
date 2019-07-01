## AltayCtf'19
### Services:
- GalTelecom, python+flask+mysql
- InGalaxy, php+laravel+mysql
- Kettle, python+aioHTTP
- Patent, go+sphinx+postgres

Deployment:
`docker-compose build && docker-compose up -d`

To enable patent search in patent service, run script.sh every N seconds (N usually equals to round time). This can be done with `screen`:
```bash
screen -dmS searchd watch --interval N /home/patent/script.sh
```
### Checkers
Written in python/aioHTTP client. `self.fake` is [faker](https://github.com/joke2k/faker) instance. Python `__debug__` constant should be `False` for results correctness.
