import textwrap


def get_user(session, orm):
    if 'uid' in session:
        users = orm.user.select().where(orm.user.id == session['uid'])
        if len(orm.user.select().where(orm.user.id == session['uid'])) != 0:
            return users.get()
        else:
            return None
    return None


def session_login(username, session, orm):
    user = orm.user.select().where(orm.user.uname == username)
    session['uid'] = user[0].id


def G0(nums):
    final_str = ''
    for i in nums:
        final_str += bin(ord(i)).replace('0b', '').zfill(8)
    final_str = textwrap.wrap(final_str, 8)
    s1 = G1(final_str[0], final_str[1])
    s2 = G1(final_str[2], final_str[3])
    s3 = G1(s1, s2)
    return int(s3, 2)


def G1(part1, part2):
    mod = (int(part1, 2) + int(part2, 2)) % 256
    mod = list(bin(mod).replace('0b', '').zfill(8))
    for i in range(2):
        mod.append(mod[0])
        mod.pop(0)
    return ''.join(mod)


def check_parts(token):
    for i in token:
        if len(i) != 4:
            return None
    if token[0].isdigit() and token[1].isalpha():
        if G0(token[0]) == 24:
            return 1
        elif G0(token[0]) == 40:
            return 0
        else:
            return None


def validate_token(token):
    try:
        token = token.split('-')
    except:
        return None
    if len(token) != 4:
        return None
    else:
        return check_parts(token)
