from flask import (
    Blueprint,
    render_template,
    session,
    make_response,
    jsonify,
    request,
    redirect,
    url_for)
from .. import orm
from .. import helpers
from functools import wraps
from werkzeug.security import generate_password_hash
import re

module = Blueprint('appeal', __name__, url_prefix='/appeal')


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


@module.route('/list', methods=['GET', 'POST'])
@login_required
def appeal():
    user = helpers.get_user(session, orm)
    if request.method == 'GET':
        ans = orm.appeal.get_all_active_appeals()
        return render_template('appeal/appeals.html', user=user, ans=ans)
    else:
        phone = re.sub(r"\D", "", request.form['phone'])
        user = helpers.get_user(session, orm)
        ans = orm.appeal.get_appeal_by_params(phone)
        return render_template('appeal/appeals.html', user=user, ans=ans)


@module.route('/pause/<appealid>', methods=['GET'])
@login_required
def pause(appealid):
    if request.method == 'GET':
        orm.appeal.pause_appeal(appealid)
        return redirect(url_for('appeal.appeal'))

@module.route('/create/<clientid>', methods=['GET','POST'])
@login_required
def create(clientid):
    user = helpers.get_user(session, orm)
    if request.method == 'GET':
        if orm.appeal.have_active_appeals(clientid):
            return redirect(url_for('appeal.appeal'))
        return render_template('appeal/create_appeal.html',user = user,clientid=clientid)
    else:
        comment = request.form['comment']
        orm.appeal.add_appeal(clientid,user.id,comment)
        return redirect(url_for('appeal.appeal'))
