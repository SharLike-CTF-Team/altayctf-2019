import re
import time
import random
import asyncio
import aiohttp
import rstr
from lib.checkers import BaseServiceChecker


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
                    self.status += 5
            except:
                self.messages.append('Main page unavailable')
                return

            signup_data = dict(
                username=self.fake.profile(fields=['username'])['username'],
                password=rstr.xeger(r'[a-zA-Z0-9!@#$%^&*]{13}'),
                secretkey=rstr.xeger(r'[a-zA-Z0-9]{6}')
            )

            self.credentials['account_data'] = signup_data
            try:
                async with client.request('POST', f'http://{self.target}/signup/', data=signup_data) as response:
                    if response.status != 200 or str(response.url) != f'http://{self.target}/search/':
                        raise ValueError
                    self.status += 10
            except:
                self.messages.append('Cannot register new user')

            patent_data = dict(
                ObjectName=self.fake.sentence(nb_words=2),
                ObjectDescription=self.fake.sentence(nb_words=7),
                Owner=self.generate_flag(),
                CVV=self.fake.credit_card_security_code(card_type=None),
                CardNumber=self.fake.credit_card_number(card_type=None),
                Mon=random.randint(1, 13),
                Year=random.randint(42, 89)
            )
            self.credentials['patent_data'] = patent_data
            try:
                async with client.request('POST', f'http://{self.target}/addpatent/', data=patent_data) as response:
                    pattern = re.compile(r'Сard added with id\: (\d+)')
                    r = await response.text()
                    m = re.search(pattern, r)
                    patent_id = int(m.group(1))
                    self.credentials['patent_id'] = patent_id
                    self.status += 15
            except:
                self.messages.append('Cannot add patent')


            card_data = dict(
                CardId=patent_id,
                SecretKey=signup_data['secretkey']
            )
            try:
                async with client.request('POST', f'http://{self.target}/cardinfo/', data=card_data, params=dict(CardId=card_data['CardId'])) as response:
                    r = await response.text()
                    pattern = re.compile(r'placeholder="Owner" value="([^"]+)"')
                    m = re.search(pattern, r)
                    flag = m.group(1)
                    assert flag == patent_data['Owner']
                    self.status += 15
                    self.flag_retrieved = True
            except Exception as e:
                self.messages.append('Cannot retrieve new flag', e)

            try:
                async with client.request('GET', f'http://{self.target}/bye', allow_redirects=False) as response:
                    if response.status != 302:
                        raise ValueError
                    self.status += 5
            except Exception as e:
                self.messages.append('Cannot log out', e)

            if type(self.old_credentials) == dict:
                if 'account_data' not in self.old_credentials.keys():
                    self.messages.append('Old checker credentials not provided, skipping checks')
                    return
            else:
                self.messages.append('Old checker credentials not provided, skipping checks')
                return

            old_login_data = dict(
                username=self.old_credentials['account_data']['username'],
                password=self.old_credentials['account_data']['password']
            )
            try:
                async with client.request('POST', f'http://{self.target}/signin/', data=old_login_data, allow_redirects=False) as response:
                    if response.status != 302:
                        raise ValueError
                    self.status += 10
            except Exception as e:
                self.messages.append(f'Cannot log in as {old_login_data["username"]}')
                return

            if 'patent_data' in self.old_credentials.keys() and 'patent_id' in self.old_credentials.keys():
                search_params = dict(
                    query=self.old_credentials['account_data']['username'],
                    filterPage=1,
                    perPage=100
                )
                try:
                    async with client.request('GET', f'http://{self.target}/search/', params=search_params) as response:
                        resp = await response.text()
                        pattern = re.compile(r'Found records\: (\d+)')
                        m = re.search(pattern, resp)
                        records_number = m.group(1)
                        if int(records_number) > 1:
                            pattern = re.compile(r'href\=\"\/cardinfo\?CardId\=({})'.format(self.old_credentials['patent_id']))
                            m = re.search(pattern, resp)
                            patent_id = int(m.group(1))
                            self.status += 30
                        else:
                            self.messages.append('Patent search not working')
                except Exception as e:
                    self.messages.append('Patent search not working')
                
                old_card_data = dict(
                    CardId=self.old_credentials['patent_id'],
                    SecretKey=self.old_credentials['account_data']['secretkey']
                )
                try:
                    async with client.request('POST', f'http://{self.target}/cardinfo/', data=old_card_data, params=dict(CardId=old_card_data['CardId'])) as response:
                        r = await response.text()
                        pattern = re.compile(r'placeholder="Owner" value="([^"]+)"')
                        m = re.search(pattern, r)
                        flag = m.group(1)
                        assert flag == self.old_credentials['patent_data']['Owner']
                        self.status += 10
                except Exception as e:
                    self.messages.append(f'Cannot get last flag')

                try:
                    async with client.request('GET', f'http://{self.target}/bye', allow_redirects=False) as response:
                        if response.status != 302:
                            raise ValueError
                except Exception as e:
                    self.messages.append('Cannot log out')
