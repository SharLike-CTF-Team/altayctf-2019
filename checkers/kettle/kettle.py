import time
import random
import asyncio
import aiohttp
import logging
import json
import datetime
import rstr
import yaml
import base64
from secrets import token_hex
from random import randint
from lib.checkers import BaseServiceChecker
from services.kettle.libsimplecrypto import Cipher, SimpleRSA, arc4_encrypt, arc4_decrypt

class Checker(BaseServiceChecker):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self.word_list = [
            'future',
            'nsfw',
            'metaphysics',
            'god',
            'mind',
            'classic',
            'timebox',
            'precede',
            'post-apocalypse',
            'cyberpunk',
            'afterlife',
            'divination',
            'reality'
        ]

    async def check(self):
        cj = aiohttp.CookieJar(unsafe=True)
        async with aiohttp.ClientSession(loop=self.loop, cookie_jar=cj, headers={'User-Agent': self.fake.user_agent()}) as client:
            try:
                async with client.request('GET', f'http://{self.target}/') as response:
                    if response.status != 418:
                        raise ValueError
                    self.status += 2
            except:
                self.messages.append('Main page unavailable (oops, how you did it?)')
                return
            
            try:
                async with client.request('GET', f'http://{self.target}/pub') as response:
                    text = await response.text()
                    keyparts = json.loads(text)
                    server_pubkey = (keyparts['N'], keyparts['e'])
                    self.status += 3
            except:
                self.messages.append('Cannore retrieve server pubkey')
                return
                
            my_key = SimpleRSA()

            userdata = dict(
                location=dict(
                    x=randint(10000, 20000),
                    y=randint(10000, 20000),
                    z=randint(10000, 20000),
                    t=datetime.datetime(
                        year=randint(2050, 2060),
                        month=randint(3, 10),
                        day=randint(2, 26),
                        hour=randint(1, 23)).timestamp()
                ),
                username=rstr.xeger(r'[A-Z]{1}[a-z]{4}\#\d{3}'),
                user_key=dict(
                    N=my_key.get_pub()[0],
                    e=my_key.get_pub()[1]
                )
            )
            self.credentials['userdata'] = userdata
            self.credentials['keys'] = my_key.dump()

            data = yaml.dump(userdata)
            try:
                async with client.request('POST', f'http://{self.target}/brew', data=dict(user_meta=data)) as response:
                    text = await response.text()
                    if "User successfully registered" not in text:
                        raise ValueError
                    self.status += 10
            except:
                self.messages.append('Cannot register new user')
                return
            
            post_data = dict(
                nsfw=randint(0, 2),
                location=userdata['location'],
                text=self.fake.sentence(nb_words=15),
                tags=[random.choice(self.word_list) for _ in range(randint(1, 3))]
            )
            self.credentials['flag'] = self.generate_flag()
            post_data['tags'].append(self.credentials['flag'])

            data = json.dumps(post_data)
            tmpkey = token_hex(nbytes=10)
            other_key = server_pubkey
            enc_key = my_key.encrypt(tmpkey, other_key)
            cipher = arc4_encrypt(tmpkey, data)

            encrypted_data = dict(
                username=userdata['username'],
                key=enc_key,
                data=str(base64.b64encode(cipher))[2:-1]
            )

            try:
                async with client.request('POST', f'http://{self.target}/api/create_post', data=encrypted_data) as response:
                    text = await response.text()
                    data = json.loads(text)
                    data = data['result']
                    tmpkey = my_key.decrypt(int(data['key']))
                    message = arc4_decrypt(tmpkey, base64.b64decode(data['data']))
                    res = json.loads(message.decode())
                    assert res['status'] == 1
                    self.status += 15
            except:
                self.messages.append('Cannot create new post')


            request = dict(req=0)
            data = json.dumps(request)
            tmpkey = token_hex(nbytes=10)
            other_key = server_pubkey
            enc_key = my_key.encrypt(tmpkey, other_key)
            cipher = arc4_encrypt(tmpkey, data)

            encrypted_data = dict(
                username=userdata['username'],
                key=enc_key,
                data=str(base64.b64encode(cipher))[2:-1]
            )

            try:
                async with client.request('POST', f'http://{self.target}/api/get_last_posts', data=encrypted_data) as response:
                    text = await response.text()
                    data = json.loads(text)
                    data = data['result']
                    tmpkey = my_key.decrypt(int(data['key']))
                    message = arc4_decrypt(tmpkey, base64.b64decode(data['data']))
                    if self.credentials['flag'] not in message.decode():
                        self.messages.append('New flag not found')
                    self.flag_retrieved = True
                    self.status += 20
            except:
                self.messages.append('Cannot get last posts')
            
            if 'userdata' in self.old_credentials.keys() and 'keys' in self.old_credentials.keys():
                request = dict(empty=0)
                data = json.dumps(request)
                tmpkey = token_hex(nbytes=10)
                other_key = server_pubkey
                my_key = SimpleRSA()
                my_key.load(*self.old_credentials['keys'])
                enc_key = my_key.encrypt(tmpkey, other_key)
                cipher = arc4_encrypt(tmpkey, data)

                encrypted_data = dict(
                    username=self.old_credentials['userdata']['username'],
                    key=enc_key,
                    data=str(base64.b64encode(cipher))[2:-1]
                )

                try:
                    async with client.request('POST', f'http://{self.target}/api/get_last_posts', data=encrypted_data) as response:
                        text = await response.text()
                        data = json.loads(text)
                        data = data['result']
                        tmpkey = my_key.decrypt(int(data['key']))
                        message = arc4_decrypt(tmpkey, base64.b64decode(data['data']))
                        if self.old_credentials['flag'] not in message.decode():
                            self.messages.append('Old flag not found')
                        else:
                            self.status += 50
                except:
                    self.messages.append('Cannot get last posts from old account')
            else:
                self.messages.append('Old credentials not provided, skipping some related checks')
