from views import WebHandler, APIHandler
from aiohttp import web


def setup_routes(app):
    app.add_routes([
        web.get('/', WebHandler.index),
        web.get('/pub', WebHandler.pubkey),
        web.post('/brew', WebHandler.brew),
        web.post('/api/create_post', APIHandler.create_post),
        web.post('/api/get_last_posts', APIHandler.get_last_posts)
    ])
