from flask import (
    Blueprint,
    render_template,
    session,
    make_response,
    request,
    redirect,
    url_for,
)
from .. import orm

module = Blueprint('index', __name__, url_prefix='/')


@module.route('/', methods=['GET'])
def index():
    if 'uid' not in session:
        return redirect(url_for('profile.login'))
    else:
        user = orm.user.get_user_by_id(session['uid'])
        return render_template('index.html', user=user)
