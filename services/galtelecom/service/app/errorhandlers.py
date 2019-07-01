from flask import (render_template,session)
from app.helpers import get_user
import app.orm as orm

def page_not_found(e):
    user = get_user(session=session,orm=orm)
    return render_template('404.html',user=user), 404


def forbidden(e):
    user = get_user(session=session, orm=orm)
    return render_template('405.html', user=user), 404