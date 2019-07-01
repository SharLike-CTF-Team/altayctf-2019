import os


class Config(object):
    SECRET_KEY = "SJDGjhaSHKJDSdacxGDbzeddasdasfczxhkVNB2346ugCJHS"
    HOST = "0.0.0.0"
    PORT = 5000
    DEBUG = True
    THREADED = True
    SESSION_TYPE = 'filesystem'
    TRAP_BAD_REQUEST_ERRORS = True
    DATABASE = {
        'name': 'knowledge_base',
        'engine': 'peewee.MySQLDatabase',
        'host': 'mysql',
        'user': 'admin_of_knowledge_base',
        'passwd': 'qsdrtg1qaz23pl'
    }


class ProductionConfig(Config):
    DEBUG = True
    RECAPTCHA_ENABLED = False


class DevelopmentConfig(Config):
    DEVELOPMENT = True
    DEBUG = True
    RECAPTCHA_ENABLED = False
