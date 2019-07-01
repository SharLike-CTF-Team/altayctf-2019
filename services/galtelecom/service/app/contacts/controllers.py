from flask import (
    Blueprint,
    render_template,
    session,
    make_response,
    request,
    redirect,
    url_for)
from .. import orm
from .. import helpers
from functools import wraps

module = Blueprint('contacts', __name__, url_prefix='/contacts')


@module.route('/')
def contacts():
    usr = helpers.get_user(session, orm)
    if request.method == 'GET':
        return render_template('contacts.html', user=usr)
