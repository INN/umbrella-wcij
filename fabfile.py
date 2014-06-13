from tools.fablib import *

"""
Base configuration
"""
env.project_name = 'wcij'
env.file_path = '.'

# Environments
def production():
    """
    Work on production environment
    """
    env.settings = 'production'
    env.hosts = [os.environ['WCIJ_PRODUCTION_SFTP_HOST'], ]
    env.user = os.environ['WCIJ_PRODUCTION_SFTP_USER']
    env.password = os.environ['WCIJ_PRODUCTION_SFTP_PASSWORD']

def staging():
    """
    Work on staging environment
    """
    env.settings = 'staging'
    env.hosts = [os.environ['WCIJ_STAGING_SFTP_HOST'], ]
    env.user = os.environ['WCIJ_STAGING_SFTP_USER']
    env.password = os.environ['WCIJ_STAGING_SFTP_PASSWORD']

try:
    from local_fabfile import  *
except ImportError:
    pass
