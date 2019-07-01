import re
import time
import random
import aiohttp
import asyncio
import textwrap
import string
import rstr
import json
from random import randint, choice
from lib.checkers import BaseServiceChecker


def rnd_4let_and_num():
    return ''.join(choice(string.ascii_uppercase + string.ascii_lowercase + string.digits) for _ in range(4))


def rnd_4let():
    return ''.join(choice(string.ascii_uppercase + string.ascii_lowercase) for _ in range(4))


def generate_operator_token():
    fp = ['0001','0010','0100','1000']
    token = '{0}-{1}-{2}-{3}'.format(choice(fp), rnd_4let(), rnd_4let_and_num(), rnd_4let_and_num())
    return token


class Checker(BaseServiceChecker):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)

    async def check(self):
        cookie_jar = aiohttp.CookieJar(unsafe=True)
        async with aiohttp.ClientSession(loop=self.event_loop, cookie_jar=cookie_jar, headers={'User-Agent': self.fake.user_agent()}) as client:
            try:
                async with client.request('GET', f'http://{self.target}/') as response:
                    if response.status != 200:
                        raise ValueError
            except:
                self.messages.append('Main page unavailable')
                return

            if 'user_data' in self.old_credentials.keys() and 'flag' in self.old_credentials.keys():
                old_data = dict(
                    login=self.old_credentials['user_data']['login'],
                    password=self.old_credentials['user_data']['password']
                )
                try:
                    async with client.request('POST', f'http://{self.target}/profile/login', data=old_data) as response:
                        t = await response.text()
                        res = json.loads(t)
                        assert res['status'] == 1
                        self.status += 20
                        try:
                            async with client.request('GET', f'http://{self.target}/profile/edit') as response:
                                t = await response.text()
                                if self.old_credentials['flag'] not in t:
                                    raise ValueError
                                self.status += 30
                        except:
                            self.messages.append('Old flag unavailable')
                            return
                except:
                    self.messages.append('Cannot log in as old user')
                    return
            else:
                self.messages.append('Old credentials not provided, skipping checks')

        async with aiohttp.ClientSession(loop=self.event_loop, cookie_jar=cookie_jar, headers={'User-Agent': self.fake.user_agent()}) as client:
            # GET http://192.168.1.8:5000/profile/registration
            # POST http://192.168.1.8:5000/profile/registration 
            try:
                async with client.request('GET', f'http://{self.target}/profile/registration') as response:
                    if response.status != 200:
                        raise ValueError
                    self.status += 5
            except:
                self.messages.append('Register page unavailable')
                return
        
            passwd = rstr.xeger(r'[a-zA-Z0-9]{13}')
            user_data = dict(
                login=self.fake.profile(fields=['username'])['username'],
                password=passwd,
                repassword=passwd,
                email=self.fake.profile(fields=['mail'])['mail'],
                token=generate_operator_token()
            )

            self.credentials['user_data'] = user_data
            try:
                async with client.request('POST', f'http://{self.target}/profile/registration', data=user_data) as response:
                    t = await response.text()
                    result = json.loads(t)
                    if response.status != 200 or result['status'] != 1:
                        raise ValueError
                    self.status += 5
            except:
                self.messages.append('Cannot register new user')

            # GET http://192.168.1.8:5000/profile/edit
            # POST http://192.168.1.8:5000/profile/edit?userid=3
            try:
                async with client.request('GET', f'http://{self.target}/profile/edit') as response:
                    t = await response.text()
                    pattern = re.compile(r'/profile/edit\?userid\=(\d+)')
                    m = re.search(pattern, t)
                    user_id = m.group(1)
                    self.status += 5
            except:
                self.messages.append('Edit profile page unavailable')

            new_user_data = dict(
                login=user_data['login'],
                password='',
                information=self.generate_flag()
            )
            self.credentials['flag'] = new_user_data['information']
            try:
                async with client.request('POST', f'http://{self.target}/profile/edit?userid={user_id}', data=new_user_data) as response:
                    t = await response.text()
                    result = json.loads(t)
                    if response.status != 200 or result['status'] != 1:
                        raise ValueError
                    async with client.request('GET', f'http://{self.target}/profile/edit') as response:
                        t = await response.text()
                        if new_user_data['information'] not in t:
                            raise ValueError
                        else:
                            self.flag_retrieved = True
                            self.status += 5
            except:
                self.messages.append('Cannot add flag')


            # GET http://192.168.1.8:5000/clients/add
            # POST http://192.168.1.8:5000/clients/add
            try:
                async with client.request('GET', f'http://{self.target}/clients/add') as response:
                    if response.status != 200:
                        raise ValueError
                    self.status += 5
            except:
                self.messages.append('Client add page unavailable')
            
            
            client_data = dict(
                surName=self.fake.suffix_male(),
                firstName=self.fake.first_name(),
                secondName=self.fake.last_name(),
                street=random.randint(1, 5),
                house=self.fake.building_number(),
                block=self.fake.building_number(),
                phone=re.sub(r'\D', '', self.fake.phone_number()),
                birthday=''
            )

            try:
                async with client.request('POST', f'http://{self.target}/clients/add', data=client_data) as response:
                    t = await response.text()
                    r = json.loads(t)
                    if r['status'] != 1:
                        raise ValueError
                    self.status += 5
            except:
                self.messages.append('Cannot add client')

            # GET http://192.168.1.8:5000/clients/individual
            # POST http://192.168.1.8:5000/clients/individual

            try:
                async with client.request('GET', f'http://{self.target}/clients/individual') as response:
                    if response.status != 200:
                        raise ValueError
                    self.status += 5
            except:
                self.messages.append('Client search page unavailable')
            
            search_data = dict(
                phone=client_data['phone'][1:7]
            )
            try:
                async with client.request('POST', f'http://{self.target}/clients/individual', data=search_data) as response:
                    t = await response.text()
                    pattern = re.compile(r'/appeal/create/(\d+)')
                    m = re.search(pattern, t)
                    appeal_id = m.group(1)
                    if client_data['phone'] not in t:
                        raise ValueError
                    self.status += 5
                    # GET http://192.168.1.8:5000/appeal/create/3
                    # POST http://192.168.1.8:5000/appeal/create/3 302
                    appeal_data = dict(
                        comment=self.fake.sentence(nb_words=15)
                    )
                    try:
                        async with client.request('GET', f'http://{self.target}/appeal/create/{appeal_id}') as response:
                            if response.status != 200:
                                raise ValueError
                            try:
                                async with client.request('POST', f'http://{self.target}/appeal/create/{appeal_id}', data=appeal_data) as response:
                                    t = await response.text()
                                    pattern = re.compile(r'/appeal/pause/(\d+)')
                                    m = re.search(pattern, t)
                                    real_id = m.group(1)
                                    self.status += 5
                                    try:
                                        async with client.request('GET', f'http://{self.target}/appeal/pause/{real_id}', allow_redirects=False) as response:
                                            if response.status != 302:
                                                raise ValueError
                                            self.status += 5
                                    except:
                                        self.messages.append('Cannot close appeal')
                            except:
                                self.messages.append('Cannot create new appeal')
                    except:
                        self.messages.append('Appeal page unavailable')
            except:
                self.messages.append('Client search not working')
