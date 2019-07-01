from flask import (
    Blueprint,
    render_template,
    session,
    make_response,
    request,
    redirect,
    url_for,
    jsonify)
from .. import orm
from .. import helpers
from functools import wraps
import re

module = Blueprint('clients', __name__, url_prefix='/clients')

def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        usr = helpers.get_user(session, orm)
        if 'uid' not in session:
            return redirect(url_for('profile.login'))
        elif not usr:
            del session['uid']
            return redirect(url_for('profile.login'))
        return f(*args, **kwargs)
    return decorated_function

@module.route('/add', methods=['GET', 'POST'])
@login_required
def add():
    usr = helpers.get_user(session, orm)
    streets = orm.streets.get_streets()
    if request.method == 'GET':
        return render_template('clients/add_client.html',user = usr, streets=streets)
    else:
        street_id=request.form['street']
        orm.clients.add_client(request.form['firstName'],
                               request.form['secondName'],
                               request.form['surName'],
                               street_id,
                               request.form['phone']
                               )
        return jsonify(dict(status=1, message="Client created"))

@module.route('/individual',methods=['GET', 'POST'])
@login_required
def individual():
    usr = helpers.get_user(session, orm)
    streets = orm.streets.get_streets()
    if request.method == 'GET':
        return render_template('clients/clients.html',user = usr,streets=streets)
    else:
        phone = ''.join(re.findall(r'\d',request.form['phone']))

        clients = orm.clients.get_clients_by_params(phone=phone)
        return render_template('clients/clients.html', user=usr, streets=streets, clients=clients)

