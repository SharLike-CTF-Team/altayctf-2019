from flask import (
    current_app,
    Blueprint,
    request,
    redirect,
    url_for,
    render_template,
    jsonify,
    session,
    json)

from .. import orm
from .. import helpers as hlp
from functools import wraps
from werkzeug.security import generate_password_hash, check_password_hash

import random

module = Blueprint('profile', __name__, url_prefix='/profile')
def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        usr = hlp.get_user(session, orm)
        if 'uid' not in session:
            return redirect(url_for('profile.login'))
        elif not usr:
            del session['uid']
            return redirect(url_for('profile.login'))
        return f(*args, **kwargs)
    return decorated_function

@module.route('/logout')
def logout():
    try:
        del session['uid']
    except:
        pass
    return redirect(url_for('profile.login'))


@module.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'GET':
        return render_template('profile/login.html')
    elif request.method == "POST":
        login = request.form['login']
        password = request.form['password']
        usr = orm.user.get_user_by_username(login)
        if usr and check_password_hash(usr.password, password):
            session['uid'] = usr.id
            return jsonify(dict(status=1, message="Вход выполнен"))
        else:
            return jsonify(dict(status=0, message="Неверные логин/пароль"))

@module.route('/registration', methods=['GET', 'POST'])
def registr():
    if request.method == 'GET':
        return render_template('profile/register.html')
    elif request.method == "POST":
        login = request.form['login']
        password = request.form['password']
        token = request.form['token']
        usr = orm.user.get_user_by_username(login)
        if not usr:
            if isinstance(hlp.validate_token(token), int):
                orm.user.add_user(login,
                                  generate_password_hash(password),
                                  hlp.validate_token(token))
                usr = orm.user.get_user_by_username(login)
                session['uid'] = usr.id
                return jsonify(dict(status=1))
            else:
                return jsonify(dict(status=0, message="Неверный токен"))
        else:
            return jsonify(dict(status=0, message="Данный логин уже занят"))
@module.route('/edit', methods=['GET', 'POST'])
@login_required
def edit():
    usr = hlp.get_user(session, orm)
    if request.method=='GET':
        return render_template('profile/edit.html',user=usr)
    else:
        new_login = request.form['login']
        new_password = request.form['password']
        inform = request.form['information']
        if new_password=='':
            if orm.user.update_user(usr.id,
                                    new_login,
                                    usr.password,
                                    usr.role,
                                    inform):
                return jsonify(dict(status=1, message="Данные изменены"))
        else:
            if orm.user.update_user(usr.id,
                                    new_login,
                                    generate_password_hash(new_password),
                                    usr.role,
                                    inform):
                return jsonify(dict(status=1, message="Данные изменены"))
        return jsonify(dict(status=0, message="Проблемки"))
