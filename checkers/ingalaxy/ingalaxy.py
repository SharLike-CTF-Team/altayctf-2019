import random
import re
import sys
import time
import random
import rstr
import logging
import asyncio
import aiohttp
from lib.checkers import BaseServiceChecker


class Checker(BaseServiceChecker):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self.can_add_flags = False

    async def check(self):
        cookie_jar = aiohttp.CookieJar(unsafe=True)
        async with aiohttp.ClientSession(loop=self.event_loop, cookie_jar=cookie_jar, headers={'User-Agent': self.fake.user_agent()}) as client:
            try:
                async with client.request('GET', f'http://{self.target}/') as response:
                    if response.status != 200:
                        raise ValueError
                    self.status += 5
            except:
                self.messages.append('Main page unavailable')
                return

            if type(self.old_credentials) == dict:
                if 'user' in self.old_credentials.keys() and 'flag' in self.old_credentials.keys():
                    data = dict(
                        login=self.old_credentials['user']['login'],
                        password=self.old_credentials['user']['password']
                    )
                    try:
                        async with client.request('POST', f'http://{self.target}/', data=data, allow_redirects=False) as response:
                            try:
                                async with client.request('GET', f'http://{self.target}/messages') as response:
                                    t = await response.text()
                                    pattern = re.compile(r'/messages/([a-f0-9]{32})')
                                    m = re.search(pattern, t)
                                    url_part = m.group(1)
                                    self.status += 15
                                    async with client.request('GET', f'http://{self.target}/messages/{url_part}') as response:
                                        r = await response.text()
                                        if self.old_credentials['flag'] not in r:
                                            raise ValueError
                                        self.status += 25
                            except:
                                self.messages.append('Old flag in unavailable')
                    except:
                        self.messages.append('Cannot login in to old account')
                else:
                    self.messages.append('Old account data not exists')

            try:
                async with client.request('POST', f'http://{self.target}/logout', allow_redirects=False) as r:
                    assert r.status == 302
                    self.status += 5
                async with client.request('GET', f'http://{self.target}/register') as response:
                    if response.status != 200:
                        raise ValueError
            except:
                self.messages.append('Register page unavailable')
                return


            passwd = rstr.xeger(r'[a-zA-Z0-9]{13}')
            new_user = dict(
                login=rstr.xeger(r'[a-zA-Z0-9]{8}'),
                password=passwd,
                password_confirmation=passwd,
                name=rstr.xeger(r'[a-zA-Z0-9]{4}'),
                surname=rstr.xeger(r'[a-zA-Z0-9]{4}'),
                race='Unkn',
                gender='Multi',
                birthday='',
                homeplace='',
                selfdescription='Haa!'
            )

            data = aiohttp.FormData()
            data.add_field(
                'file',
                open('mono-empty.png', 'rb'),
                filename='logo.png',
                content_type='image/png')
            for a, b in new_user.items():
                data.add_field(a, str(b))

            try:
                async with client.request('POST', f'http://{self.target}/register', data=data, allow_redirects=False) as response:
                    assert response.status == 302
                    async with client.request('GET', f'http://{self.target}/news', allow_redirects=False) as response:
                        resp = await response.text()
                        creds = f'{new_user["name"]} {new_user["surname"]}'
                        if creds in resp:
                            pattern = re.compile(r'/profile/(\d+)\"')
                            m = re.search(pattern, resp)
                            user_id = int(m.group(1))
                            new_user['id'] = user_id
                            self.credentials['user'] = new_user
                            self.can_add_flags = True
                            self.status += 10
                        else:
                            raise ValueError
            except:
                self.messages.append('Cannot register new user')
            
            if self.can_add_flags:
                message_data = dict(
                    user_id=user_id
                )
                try:
                    async with client.request('POST', f'http://{self.target}/addMessage', data=message_data) as response:
                        assert response.history[0].status == 302
                        self.status += 10
                        resp = await response.text()
                        if 'Ваше сообщение' not in resp:
                            raise ValueError
                        flag = self.generate_flag()
                        flag_data = dict(
                            user_id=user_id,
                            text=flag
                        )

                        form_data = aiohttp.FormData()
                        form_data.add_field(
                            'file',
                            open('mono-empty.png', 'rb'),
                            filename='logo.png',
                            content_type='image/png'
                        )
                        for a, b in flag_data.items():
                            form_data.add_field(a, str(b))

                        try:
                            async with client.request('POST', f'http://{self.target}/addMessage', data=form_data) as response:
                                t = await response.text()
                                if flag not in t:
                                    raise ValueError
                                self.flag_retrieved = True
                                self.credentials['flag'] = flag
                                self.status += 30
                        except:
                            self.messages.append('Flag in message not found')
                except:
                    self.messages.append('Cannot add new message')
