from peewee import *
from playhouse.pool import PooledMySQLDatabase
import datetime
import random
db_proxy = PooledMySQLDatabase(None)


class M(Model):
    class Meta:
        database = db_proxy


class user(M):
    uname = TextField()
    password = TextField()
    role = IntegerField()
    information = TextField(default='')

    @staticmethod
    def get_info_about_user(id):
        return user.select(user.information).where(user.id==id)[0].information

    @staticmethod
    def update_user(user_id, uname, password, role, information):
        try:
            user.update(uname=uname,
                        password=password,
                        role=role,
                        information=information).where(user.id==user_id).execute()
            return True
        except:
            return False

    @staticmethod
    def add_information(id,inform):
        user.update(information=inform).where(user.id==id).execute()

    @staticmethod
    def add_user(uname, password, role):
        user.create(uname=uname, password=password, role=role)
        return True

    @staticmethod
    def get_user_by_username(username):
        try:
            return user.get(user.uname == username)
        except:
            return False

    @staticmethod
    def get_user_by_id(usrid):
        try:
            User = user.get(user.id == usrid)
            return User
        except:
            return False


class streets(M):
    name = TextField()

    @staticmethod
    def add_street(street_name):
        streets.create(name=street_name)
        return True

    @staticmethod
    def get_streets():
        return [a for a in streets.select()]

class clients(M):
    first_name = TextField()
    second_name = TextField()
    sur_name = TextField()
    birthday = DateField(default=datetime.date(random.randint(1900,1995),random.randint(1,12), random.randint(1,30)))
    street = ForeignKeyField(streets)
    house_number = IntegerField(default=random.randint(1,1000))
    block = TextField(default='')
    flat = IntegerField(default=random.randint(1,10))
    phone = TextField()

    @staticmethod
    def add_client(first_name, second_name, sur_name, street, phone):
        clients.create(first_name=first_name,
                       second_name=second_name,
                       sur_name=sur_name,
                       street=street,
                       phone=phone)
        return True

    @staticmethod
    def get_clients_by_params(phone='_'):
        res_clients = clients.select().where(clients.phone.contains(phone))
        for i in res_clients:
            i.active = 0
            for ap in i.appeal_set:
                if ap.active==1:
                    i.active = 1
                    break
        return res_clients


class appeal(M):
    client = ForeignKeyField(clients)
    start_time = DateTimeField(datetime.datetime.now())
    end_time = DateTimeField(null=True, default=None)
    creator = ForeignKeyField(user)
    comment = TextField()
    active = IntegerField()

    @staticmethod
    def have_active_appeals(client_id):
        if len(appeal.select().where(appeal.client==client_id,appeal.active==1)) == 0:
            return False
        else:
            return True

    @staticmethod
    def add_appeal(client_id,creator,comment):
        appeal.create(client=client_id,
                      start_time = datetime.datetime.now(),
                      creator=creator,
                      comment=comment,
                      active=1)


    @staticmethod
    def get_all_active_appeals():
        return appeal.select().where(appeal.end_time==None)

    @staticmethod
    def get_appeal_by_params(phone):
        return appeal.select(
            clients.id,
            clients.first_name,
            clients.second_name,
            clients.sur_name,
            clients.phone,
            appeal.id,
            appeal.start_time,
            appeal.comment
        ).join(clients, on=(appeal.client==clients.id)).where(clients.phone.contains(phone), appeal.end_time == None)

    @staticmethod
    def pause_appeal(appeal_id):
        return appeal.update(end_time = datetime.datetime.now(),active = 0).where(appeal.id == appeal_id).execute()


if __name__ == '__main__':
    pass
