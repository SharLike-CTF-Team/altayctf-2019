import pathlib
import yaml
import datetime
from secrets import token_hex
from math import sqrt


class FSDB:
    def __init__(self, lifetime, root):
        self.posts_lifetime = lifetime
        self.root_dir = pathlib.Path(root)
        self.users_dir = self.root_dir / 'users'
        self.posts_dir = self.root_dir / 'posts'
        for path in [self.root_dir, self.users_dir, self.posts_dir]:
            if not path.exists():
                path.mkdir()
    
    def get_all_users(self):
        return (userdata.stem for userdata in self.users_dir.glob('**/*.yaml'))

    def get_user_meta(self, username):
        if username not in self.get_all_users():
            return None
        else:
            userfile = self.users_dir / f"{username}.yaml"
            return yaml.load(open(str(userfile), 'r').read())

    def create_user(self, user_meta):
        if user_meta['username'] not in self.get_all_users():
            try:
                new_user = self.users_dir / f"{user_meta['username']}.yaml"
                new_user = new_user.resolve()
                with open(str(new_user), 'w') as f:
                    f.write(yaml.dump(user_meta))
                return dict(status=1, message='User successfully registered')
            except:
                return dict(status=1, message='Service error')
        else:
            return dict(status=0, message='User already registered')
    
    def create_post(self, username, post_meta):
        if username in self.get_all_users():
            try:
                post_ts = post_meta['location']['t']
                year = datetime.datetime.fromtimestamp(post_ts).year
                post_dir = self.posts_dir / str(year)
                if not post_dir.exists():
                    post_dir.mkdir()
                message_id = token_hex(nbytes=10)
                post_file = post_dir / f"{username}.{message_id}.message"
                with open(str(post_file), 'w') as f:
                    f.write(yaml.dump(post_meta))
                return dict(status=1, message='Message published!')
            except:
                return dict(status=1, message='Service error')
        else:
            return dict(status=0, message='User not found in database')
    
    def get_last_posts(self, username):
        user_info = self.get_user_meta(username)
        if not user_info:
            return dict(status=0, message='Invalid user')
        posts = []
        for post in self.posts_dir.glob('**/*.message'):
            creation_ts = post.stat().st_mtime
            creation_time = datetime.datetime.fromtimestamp(creation_ts)

            if datetime.datetime.now() - datetime.timedelta(minutes=self.posts_lifetime) > creation_time:
                post.unlink()
                continue

            post_meta = yaml.load(open(str(post), 'r').read())

            if abs(user_info['location']['t'] - post_meta['location']['t']) > 60*60*24*365*15:
                posts.append(dict(
                    post_info='Unknown',
                    more='Post was published not in your time, access denied'
                ))
                continue
            x1, y1, z1 = post_meta['location']['x'], post_meta['location']['y'], post_meta['location']['z']
            x2, y2, z2 = user_info['location']['x'], user_info['location']['y'], user_info['location']['z']
            distance_to_post = sqrt((x2-x1)**2 + (y2-y1)**2 + (z2-z1)**2)
            if distance_to_post > 10:
                posts.append(dict(
                    title=post_meta['text'],
                    more='You are too far from this post, some fields were hidden',
                    distance=distance_to_post
                ))
                continue
            posts.append(dict(
                filename=str(post),
                text=post_meta['text'],
                tags=post_meta['tags'],
                nsfw=post_meta['nsfw']
            ))
        return posts
