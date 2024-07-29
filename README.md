## WP / codespace classicpress starter

Starter for codespace dev for WP and/or ClassicPress Projects, with Basic Extensions, SFTP and PHPCS setup

- Fork this repo to get started with a new WP or ClassicPress project in your own codespace
- Save the repo as a template, and use for each new project
- Modify the `.devcontainer/` and `.vscode/` files to customize your dev environment as needed

## Codespace Development Environment Setup

### 1. Using the dev environment in Codespace container only

- Create a new Repo in Github using this repo as a template
- Create a codespace from the new repo
- Allow the codespace to build - review the output, watch for errors
- Allow port permissions when prompted
- Open a browser to http://127.0.0.1:8080/wp-login.php - admin|admin
- PHPMyadmin at http://127.0.0.1:8081 - wordpress|wordpress
- For ClassicPress, just go to the plugins section and click 'switch'


## SFTP Development Remote (pantheon or namecheap)

### 1. setup the dev environment

- Create a new Repo in Github using this repo as a template
- Create a codespace from the new repo
- Allow the codespace to build
- Change the contents of .devcontainer/devcontainer.json to those located in .devcontainer/devcontainer-base.json
- Rebuild the container (full rebuild)
- Follow SFTP setup instructions in `.vscode/SFTP_README.md`

### 2. setup Terminus for Pantheon

- Create a new token in your Pantheon User settings
- Add the token to Github Codespace secrets for this repo - call it 'PANTHEON_TOKEN'
- Add your site_id from your Dashboard URL to Github Codespace secrets for this repo - call it PANTHEON_SITEID
- Rebuild codespace

## SFTP Development Remote (lightsail only)

### 1. setup the dev environment

- Create a new Repo in Github using this repo as a template
- Create a codespace from the new repo
- Allow the codespace to build
- Change the contents of .devcontainer/devcontainer.json to those located in .devcontainer/devcontainer-base.json
- Rebuild the container (full rebuild)
- Follow SFTP setup instructions in `.vscode/SFTP_README.md`

### 2. setup a new WP instance in Lightsail

### 3 - assign static ip and setup sftp

update the `/.vscode/sftp.json` file's IP address to match a static ip assigned to the new lightsail instance

### 4 - add a private key for SFTP / SSH access:

- follow instructions in `/vscode/SFTP_README.md`

### 5 - ssl certificate generation

https://docs.aws.amazon.com/lightsail/latest/userguide/amazon-lightsail-enabling-https-on-wordpress.html

If you run into any issues, revoke and reinstall:  
https://docs.bitnami.com/aws/how-to/understand-bncert/

Make sure all propagation is complete before continuing

Command to use:
`sudo /opt/bitnami/bncert-tool`

### 6 - search replace DB for old IP address

replace in the DB the old IP address `1##.##.##.##.nip.io` with the new domain

### 7a - Connect using Phpmyadmin

add the local id_rsa.pub to the authorized_keys on the remote server, then create a tunnel: `ssh -N -L 8888:127.0.0.1:80 -i ~/.ssh/id_rsa bitnami@###.##.##.###` and access at:
`http://localhost:8888/phpmyadmin`

### 7b - Connect to remote DB using tablePlus

- Add your local id_rsa :  `nano ~/.ssh/authorized_users`
- Create a secure tunnel:  `ssh -N -L 3306:127.0.0.1:3306 -i ~/.ssh/id_rsa bitnami@##.###.###.###`
- Connect using TablePlus at 127.0.0.1:3306 using the bitnami cred's in the wp-config.php file

### 8 - login to WP admin

to get the wordpress password for `user`, log into via ssh and use:

`cat ~/bitnami_application_password`

### 9 - troubleshoot permissions

- if you get weird problems with permissions, use these command to reset them:

```
sudo chown -R bitnami:daemon /bitnami/wordpress
sudo find /bitnami/wordpress -type d -exec chmod 755 {} \;
sudo find /bitnami/wordpress -type f -exec chmod 664 {} \;
sudo chmod 640 /bitnami/wordpress/wp-config.php
sudo chown -R root:daemon /opt/bitnami/php/tmp
sudo chmod -R 775 /opt/bitnami/wordpress/wp-content/cache
```

### 10 - Rsync Local files to remote lightsail instance

```
rsync -rlpz --progress --ignore-existing --times --temp-dir=~/tmp --delay-updates path/to/local_files/* bitnami@##.###.###.###:/bitnami/wordpress/wp-content/uploads
```

