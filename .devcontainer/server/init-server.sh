#! /bin/bash

set -x
set -euo pipefail

REPO_FOLDER="/workspaces/$RepositoryName"
LOGS_FOLDER="/home/vscode/logs"

sudo service apache2 stop

sudo usermod -a -G www-data vscode
sudo usermod -a -G vscode www-data
sudo sed -i 's/www-data/vscode/g' /etc/apache2/envvars
sudo sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

sudo chmod -R 777 $REPO_FOLDER

# make a new folder if it doesn't exist
sudo mkdir -p $LOGS_FOLDER
sudo chown -R vscode:vscode $LOGS_FOLDER
sudo chmod 777 $LOGS_FOLDER
sudo touch $LOGS_FOLDER/error.log
sudo touch $LOGS_FOLDER/access.log
sudo chmod -R 777 $LOGS_FOLDER/logs

# create symlink to logs from workspace folder
sudo mkdir -p $REPO_FOLDER/logs
sudo mkdir -p $REPO_FOLDER/wordpress-db
sudo chmod 777 $REPO_FOLDER/logs
# only remove if it isn't there but it's a symlink
[ -L $REPO_FOLDER/logs/error.log ] && sudo rm $REPO_FOLDER/logs/error.log
[ -L $REPO_FOLDER/logs/access.log ] && sudo rm $REPO_FOLDER/logs/access.log
ln -s $LOGS_FOLDER/error.log $REPO_FOLDER/logs/error.log
ln -s $LOGS_FOLDER/access.log $REPO_FOLDER/logs/access.log

# Enable mod_rewrite
sudo a2enmod rewrite
sudo a2enmod headers
# Imagick
# sudo apt-get install --assume-yes --no-install-recommends --quiet build-essential libmagickwand-dev
# apt-get clean all
# pecl install imagick

# Set DocumentRoot to your WordPress directory and enable .htaccess overrides
echo "Configuring Apache Virtual Host..."
sudo bash -c "cat > /etc/apache2/sites-available/wordpress.conf << EOF
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot $REPO_FOLDER/wordpress
    <Directory $REPO_FOLDER/wordpress>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog $LOGS_FOLDER/error.log
    CustomLog $LOGS_FOLDER/access.log combined
</VirtualHost>
EOF"

# Apache
sudo chmod 777 /etc/apache2/sites-available/wordpress.conf

# Enable the WordPress site configuration
sudo a2ensite wordpress.conf

# disable the default site: 000-default.conf
sudo a2dissite 000-default.conf

# only if it exists: /usr/local/etc/php/conf.d/xdebug.ini
[ -f /usr/local/etc/php/conf.d/xdebug.ini ] && sudo mv /usr/local/etc/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini.disabled

# Reload Apache to apply changes
sudo service apache2 stop
sudo service apache2 start
sudo service apache2 reload

LOCALE="en_US"

#Xdebug
# echo xdebug.log_level=0 | sudo tee -a /usr/local/etc/php/conf.d/xdebug.ini

# install dependencies
cd $REPO_FOLDER
npm install 
composer install

# Setup local plugin
# cd $REPO_FOLDER/wordpress/wp-content/plugins/wp-codespace && npm install && npx playwright install && npm run compile:css
# code -r wp-codespace.php

# Setup bash
echo "export PATH=\"\$PATH:$REPO_FOLDER/vendor/bin:$REPO_FOLDER/node_modules/.bin/\"" >> ~/.bashrc
echo "cd $REPO_FOLDER/wordpress" >> ~/.bashrc
source ~/.bashrc

# A script for initializing codespace dev env.
# Note the script is intended to be run at root of project.
# It needs to be adjusted for projects using a non-web docroot.
cd $REPO_FOLDER

# Setup bash aliases
echo "alias repair-codespace=\"$REPO_FOLDER/.devcontainer/src/codespaces-environment/init-codespace.sh\"" >> ~/.bashrc
echo "alias uli=\"drush uli --uri=https://$CODESPACE_NAME-80.githubpreview.dev\"" >> ~/.bashrc
echo "export CODESPACE_URL=\"$CODESPACE_NAME-80.githubpreview.dev\"" >> ~/.bashrc

# Setup Xdebug
# PHP_INI_PATH=$(php --ini | grep "Loaded Configuration File" | cut -d ":" -f 2 | tr -d "[:space:]")
# echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" >> $PHP_INI_PATH
# echo "xdebug.mode=debug" >> $PHP_INI_PATH
# echo "xdebug.start_with_request=yes" >> $PHP_INI_PATH
# echo "xdebug.client_host=localhost" >> $PHP_INI_PATH
# echo "xdebug.client_port=9003" >> $PHP_INI_PATH
# echo "xdebug.idekey=VSCODE" >> $PHP_INI_PATH

# setup wpcs for php formatting
cd $REPO_FOLDER/.wpcs
composer update
composer install
composer config allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
$REPO_FOLDER/.wpcs/vendor/bin/phpcs --config-set installed_paths $REPO_FOLDER/.wpcs/vendor/wp-coding-standards/wpcs,$REPO_FOLDER/.wpcs/vendor/phpcsstandards/phpcsutils,$REPO_FOLDER,$REPO_FOLDER/.wpcs/vendor/squizlabs/php_codesniffer,$REPO_FOLDER/.wpcs/vendor/phpcsstandards,$REPO_FOLDER/.wpcs/vendor/phpcompatibility/php-compatibility
git config pull.rebase false 

if [ ! -d /home/vscode/.ssh ]; then
    mkdir /home/vscode/.ssh
fi
# Add ssh key from secrets or generate.
if [ -n "${SSH_KEY-}" ]; then
    touch /home/vscode/.ssh/id_rsa
    sudo echo "$SSH_KEY" | tee /home/vscode/.ssh/id_rsa >/dev/null
    sudo chown vscode:vscode /home/vscode/.ssh/id_rsa
    sudo chmod 600 /home/vscode/.ssh/id_rsa
    # only remove ~/.ssh/id_rsa.pub if exists
    [ -f ~/.ssh/id_rsa.pub ] && sudo rm ~/.ssh/id_rsa.pub
    ssh-keygen -f /home/vscode/.ssh/id_rsa -y | tee /home/vscode/.ssh/id_rsa.pub >/dev/null
    sudo chmod 644 /home/vscode/.ssh/id_rsa.pub
    touch /home/vscode/.ssh/known_hosts
    sudo chmod 644 /home/vscode/.ssh/known_hosts
    echo -e "\e[32m"
    echo "Add the public key above into your Lightsail instance's authorized_keys file, or add it to your Cpanel Dashboard in Namecheap, then configure SFTP IP address and reload the codespace"
    echo -e "\e[0m"
else
    sudo chmod 700 /home/vscode/.ssh
    ssh-keygen -t rsa -b 4096 -f /home/vscode/.ssh/id_rsa -N ""
    echo -e "\e[32m"
    echo "Use command: cat /home/vscode/.ssh/id_rsa.pub to get the public key to add into your Lightsail instance's authorized_keys file, or add it to your Cpanel Dashboard in Namecheap, then configure SFTP IP address and reload the codespace"
    echo -e "\e[0m"
fi

# only do this if the wordpress folder doesn't exist
if [ ! -d "$REPO_FOLDER/wordpress" ]; then
    # WordPress Core install
    cd $REPO_FOLDER
    wp core download --locale=$LOCALE --path=wordpress
    cd $REPO_FOLDER/wordpress
    wp config create --dbname=wordpress --dbuser=wordpress --dbpass=wordpress --dbhost=db --force

    # wp core install --path="$REPO_FOLDER/wordpress" --url="http://127.0.0.1" --title="WordPress Demo" --admin_user="adm1n_user" --admin_password="p4ssw0rd!!**2024" --admin_email=demo@earthman.ca
    wp config set DEBUG true --raw

    # Multisite install
    wp core multisite-install --path="$REPO_FOLDER/wordpress" --url="http://127.0.0.1" --title="WordPress Multisite Demo" --admin_user="adm1n_user" --admin_password="p4ssw0rd!!**2024" --admin_email=demo@earthman.ca
    wp config set WP_ALLOW_MULTISITE true --raw
    wp config set MULTISITE true --raw
    wp config set SUBDOMAIN_INSTALL false --raw  # set to 'true' for subdomains
    wp config set DOMAIN_CURRENT_SITE '127.0.0.1'  # adjust as necessary
    wp config set PATH_CURRENT_SITE '/'  # adjust if WP is not at the root
    wp config set SITE_ID_CURRENT_SITE 1
    wp config set BLOG_ID_CURRENT_SITE 1
    # END Multisite

    # Find the line number to insert the WP config addendum
    LINE_NUMBER=$(grep -n 'stop editing!' wp-config.php | cut -d ':' -f 1)
    sed -i "${LINE_NUMBER}r ../.devcontainer/server/local-wpconfig.txt" wp-config.php
    sed -i "s/CODESPACE_NAME/$CODESPACE_NAME/g" wp-config.php

    # if .htaccess doesn't exist, create it
    if [ ! -f ".htaccess" ]; then
        touch .htaccess
        # insert the contents into htaccess
        # cat ../.devcontainer/server/local-htaccess-wp.txt > .htaccess

        # Multisite install
        cat ../.devcontainer/server/local-htaccess-wpms.txt > .htaccess
        # END Multisite
   fi

    # set .htaccess permissions to 644
    chmod 644 .htaccess
    # Add PHP values
    echo "php_value post_max_size 1024M" >> .htaccess
    echo "php_value upload_max_filesize 1024M" >> .htaccess
    echo "php_value memory_limit 1024M" >> .htaccess
    echo "php_value max_execution_time 300" >> .htaccess
    echo "php_value max_input_time 300" >> .htaccess
    echo "php_value max_input_vars 1000" >> .htaccess
    echo "php_value display_errors 1" >> .htaccess
fi

sudo wp cli update --yes --stable
# RUN wp package install aaemnnosttv/wp-cli-dotenv-command:@stable
# RUN wp package install wp-cli/server-command:@stable
# RUN wp package install wp-cli/restful:@stable
# RUN wp package install wp-cli/rewrite-command:@stable
# RUN wp package install wp-cli/scaffold-command:@stable
# RUN wp package install wp-cli/search-replace-command:@stable
# RUN wp package install wp-cli/widget-command:@stable
# RUN wp package install wp-cli/wp-config-transformer:@stable
# RUN wp package install wp-cli/wp-cli:@stable
wp rewrite structure '/%postname%/' --hard

# remove wp-content folder and replace with a symlink to the workspace folder / wp-content
cd $REPO_FOLDER
sudo rm -rf $REPO_FOLDER/wordpress/wp-content
sudo ln -s $REPO_FOLDER/wp-content $REPO_FOLDER/wordpress/wp-content

# Demo content for WordPress
# wp plugin install wordpress-importer --activate
# curl https://raw.githubusercontent.com/WPTT/theme-unit-test/master/themeunittestdata.wordpress.xml > demo-content.xml
# wp import demo-content.xml --authors=create
# rm demo-content.xml

cd $REPO_FOLDER/wordpress
# install base theme and plugins
# wp theme activate responsive
# wp plugin activate --all

# persist the bash history beyond rebuilds on a volume
echo "export HISTFILE=$REPO_FOLDER/wordpress/wp-content/.bash_history" >> ~/.bashrc
echo "export HISTSIZE=100000" >> ~/.bashrc
echo "export HISTFILESIZE=100000" >> ~/.bashrc
echo "shopt -s histappend" >> ~/.bashrc
echo "PROMPT_COMMAND='history -a'" >> ~/.bashrc
