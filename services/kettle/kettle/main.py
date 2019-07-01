import asyncio
from aiohttp import web
from routes import setup_routes
from settings import config, BASE_DIR
from libsimplecrypto import Cipher
from db import FSDB


def init_app():
    app = web.Application()
    c = Cipher(config['key'])
    db = FSDB(config['db']['fsdb_lifetime'], '/app/storage')
    app['db'] = db
    app['crypto'] = c
    app['config'] = config

    setup_routes(app)
    return app


app = init_app()