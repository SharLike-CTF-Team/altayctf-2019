from flask import (Flask)
from appcfg import DevelopmentConfig as DevCfg
from flask_session import Session
from app import errorhandlers as handlers
from app.orm import db_proxy as database

app = Flask(__name__)

@app.before_request
def _db_connect():
    database.connect()


@app.teardown_request
def _db_close(exc):
    if not database.is_closed():
        database.close()

def create_app():
    sess = Session()
    app.config.from_object(DevCfg)
    sess.init_app(app)
    database.init('telecom',
                  host='mysql',
                  user='telecom',
                  passwd='1q2w3e4r5t',
                  max_connections=50,
                  stale_timeout=3000)

    import app.clients.controllers as clients
    import app.profile.controllers as profile
    import app.contacts.controllers as contacts
    import app.index.controllers as index
    import app.admin.controllers as admin
    import app.appeal.controllers as appeal

    app.register_blueprint(clients.module)
    app.register_blueprint(profile.module)
    app.register_blueprint(contacts.module)
    app.register_blueprint(index.module)
    app.register_blueprint(admin.module)
    app.register_blueprint(appeal.module)

    app.register_error_handler(404, handlers.page_not_found)
    app.register_error_handler(405, handlers.forbidden)
    return app
