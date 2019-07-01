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

module = Blueprint('admin', __name__, url_prefix='/admin')

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

@module.route('/create', methods=['GET', 'POST'])
@login_required
def create():
    usr = helpers.get_user(session, orm)
    if request.method == 'GET':
        return render_template('admin/create.html',user = usr)
    elif request.method == 'POST':
        login = request.form['login']
        password = request.form['password']
        role = request.form['role']
        usr = orm.user.get_user_by_username(login)
        if not usr:
            orm.user.add_user(login,
                              generate_password_hash(password),
                              role)
            return jsonify(dict(status=1, message="Пользователь успешно создан"))
        else:
            return jsonify(dict(status=0, message="Данный логин уже используется"))
@module.route('/list')
@login_required
def list():
    usr = helpers.get_user(session, orm)
    users=orm.user.select()
    if request.method == 'GET':
        return render_template('admin/list.html',user=usr,users=users)

@module.route('/view/<userid>')
@login_required
def view_user(userid):
    usr = helpers.get_user(session, orm)
    user = orm.user.get_user_by_id(userid)
    return render_template('admin/info.html',user = usr, user_info = user)

@module.route('/edit/<userid>',methods=['GET','POST'])
@login_required
def edit_user(userid):
    user = helpers.get_user(session, orm)
    usr = orm.user.get_user_by_id(userid)
    if request.method == 'GET':
        return render_template('admin/edit.html',user=user,usr=usr)
    if request.method == 'POST':
        new_login = request.form['login']
        new_password = request.form['password']
        role = int(request.form['role'])
        inform = request.form['information']
        if new_password=='':
            if orm.user.update_user(userid,
                                    new_login,
                                    usr.password,
                                    role,
                                    inform):
                return jsonify(dict(status=1, message="Данные изменены"))
        else:
            if orm.user.update_user(userid,
                                    new_login,
                                    generate_password_hash(new_password),
                                    role,
                                    inform):
                return jsonify(dict(status=1, message="Данные изменены"))
        return jsonify(dict(status=0, message="Проблемки"))

