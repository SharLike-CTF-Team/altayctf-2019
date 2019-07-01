#!/usr/bin/env python
import os
from flask_script import Manager, Command, Server
from app import create_app

app = create_app()
manager = Manager(app)

manager.add_command("run", Server(port=5000, host='0.0.0.0', threaded=True))

if __name__ == '__main__':
    manager.run()
