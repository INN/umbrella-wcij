import os

from tools.fablib import *

from fabric.api import task

"""
Base configuration
"""
env.project_name = 'wcij'
env.file_path = '.'

env.sftp_deploy = True

try:
    env.domain
except AttributeError:
    env.domain = 'wcij.dev'

try:
    env.hipchat_token = os.environ['HIPCHAT_DEPLOYMENT_NOTIFICATION_TOKEN']
    env.hipchat_room_id = os.environ['HIPCHAT_DEPLOYMENT_NOTIFICATION_ROOM_ID']
except KeyError:
    pass

# Environments
@task
def production():
    """
    Work on production environment
    """
    env.settings = 'production'
    env.hosts = [os.environ['WCIJ_PRODUCTION_SFTP_HOST'], ]
    env.path = os.environ['WCIJ_PRODUCTION_SFTP_PATH']
    env.user = os.environ['FLYWHEEL_SFTP_USER']
    env.password = os.environ['FLYWHEEL_SFTP_PASS']
    env.domain = 'www.wisconsinwatch.org'
    env.port = 22

@task
def staging():
    """
    Work on staging environment
    """
    env.settings = 'staging'
    env.hosts = [os.environ['WCIJ_STAGING_SFTP_HOST'], ]
    env.path = os.environ['WCIJ_STAGING_SFTP_PATH']
    env.user = os.environ['FLYWHEEL_SFTP_USER']
    env.password = os.environ['FLYWHEEL_SFTP_PASS']
    env.domain = 'staging.wisconsinwatch.flywheelsites.com'
    env.port = 22

try:
    from local_fabfile import *
except ImportError:
    pass
