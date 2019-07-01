from aiohttp import web
import json
import functools
import yaml
import base64
import logging

def secure_communication(func):
    @functools.wraps(func)
    async def wrapper(*a, **kw):
        request = a[0]
        data = await request.post()
        for k in ['username', 'key', 'data']:
            if k not in data.keys():
                return web.json_response(dict(status=0, message='Invalid request params'))
        user_meta = request.app['db'].get_user_meta(data['username'])
        if not user_meta:
            return web.json_response(dict(status=0, message='User not found'))
        try:
            request_body = request.app['crypto'].decrypt_request(int(data['key']), base64.b64decode(data['data']))
            request_body = json.loads(request_body.decode())
            result = await func(user_meta, request_body, a[0])
            return web.json_response(result)
        except Exception as e:
            logging.error(e, exc_info=True)
            return web.json_response(dict(status=0, message='Coffee magic not working'))
        return await func(*a, **kw)
    return wrapper

class WebHandler:
    def __init__(self):
        pass

    @staticmethod
    async def index(request):
        return web.Response(text="I'm a teapot", status=418)

    @staticmethod
    async def pubkey(request):
        N, e = request.app['config']['key']['N'], request.app['config']['key']['e']
        return web.json_response(dict(N=N, e=e))
    
    @staticmethod
    async def brew(request):
        try:
            data = await request.post()
            user_meta = yaml.load(data['user_meta'])
            result = request.app['db'].create_user(user_meta)
            return web.json_response(result)
        except:
            return web.json_response(dict(result='Cannot register new user'))


class APIHandler:
    def __init__(self):
        pass

    @staticmethod
    @secure_communication
    async def create_post(user_meta, data, request):
        try:
            results = request.app['db'].create_post(user_meta['username'], data)
            results = json.dumps(results)
            return dict(result=request.app['crypto'].encrypt_response(user_meta, results))
        except:
            return dict(status=0, message='Coffee magic not working')

    @staticmethod
    @secure_communication
    async def get_last_posts(user_meta, data, request):
        try:
            posts = request.app['db'].get_last_posts(user_meta['username'])
            results = json.dumps(posts)
            return dict(result=request.app['crypto'].encrypt_response(user_meta, results))
        except Exception as e:
            logging.error(e, exc_info=True)
            return dict(status=0, message='Coffee magic not working')