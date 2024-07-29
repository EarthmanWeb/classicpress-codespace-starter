#!/usr/bin/env bash
# A script for initializing codespace dev env.
# Note the script is intended to be run at root of project.
# It needs to be adjusted for projects using a non-web docroot.
# set -x
# set -eu -o pipefail

# Change dir to Codespace home
cd /workspaces/$(echo $RepositoryName)

# Setup bash aliases
echo "alias repair-codespace=\"/workspaces/$(echo $RepositoryName)/.devcontainer/src/codespaces-environment/init-codespace.sh\"" >> ~/.bashrc
echo "alias uli=\"drush uli --uri=https://$CODESPACE_NAME-8080.githubpreview.dev\"" >> ~/.bashrc
echo "export CODESPACE_URL=\"$CODESPACE_NAME-8080.githubpreview.dev\"" >> ~/.bashrc

# Setup Xdebug
# PHP_INI_PATH=$(php --ini | grep "Loaded Configuration File" | cut -d ":" -f 2 | tr -d "[:space:]")
# echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" >> $PHP_INI_PATH
# echo "xdebug.mode=debug" >> $PHP_INI_PATH
# echo "xdebug.start_with_request=yes" >> $PHP_INI_PATH
# echo "xdebug.client_host=127.0.0.1" >> $PHP_INI_PATH
# echo "xdebug.client_port=9003" >> $PHP_INI_PATH
# echo "xdebug.idekey=VSCODE" >> $PHP_INI_PATH

# setup wpcs for php formatting
cd /workspaces/$(echo $RepositoryName)/.wpcs
composer update
composer install
composer config allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
/workspaces/$(echo $RepositoryName)/.wpcs/vendor/bin/phpcs --config-set installed_paths /workspaces/$(echo $RepositoryName)/.wpcs/vendor/wp-coding-standards/wpcs,/workspaces/$(echo $RepositoryName)/.wpcs/vendor/phpcsstandards/phpcsutils,/workspaces/$(echo $RepositoryName),/workspaces/$(echo $RepositoryName)/.wpcs/vendor/squizlabs/php_codesniffer,/workspaces/$(echo $RepositoryName)/.wpcs/vendor/phpcsstandards,/workspaces/$(echo $RepositoryName)/.wpcs/vendor/phpcompatibility/php-compatibility
git config pull.rebase false 
set +x


# Add ssh key for usage.
# if the .ssh folder does not exist
if [ ! -d /home/vscode/.ssh ]; then
mkdir /home/vscode/.ssh
sudo chmod 700 /home/vscode/.ssh
ssh-keygen -t rsa -b 4096 -f /home/vscode/.ssh/id_rsa -N ""
set +x
echo -e
echo -e
# color teh output green
echo -e "\e[32m"
echo "Use command: cat /home/vscode/.ssh/id_rsa and then Copy the private key above into your github secrets as SSH_KEY, then fully rebuild the container"
echo -e "\e[0m"
# set -x
fi


touch "/workspaces/classicpress_starter_01/wordpress/wp-content/.bash_history"
SNIPPET="export PROMPT_COMMAND='history -a' && export HISTFILE=/workspaces/classicpress_starter_01/wordpress/wp-content/.bash_history"
echo "$SNIPPET" >> "/home/vscode/.bashrc"

# persist the bash history beyond rebuilds on a volume
echo "export HISTFILE=/workspaces/classicpress_starter_01/wordpress/wp-content/.bash_history" >> ~/.bashrc
echo "export HISTSIZE=100000" >> ~/.bashrc
echo "export HISTFILESIZE=100000" >> ~/.bashrc
echo "shopt -s histappend" >> ~/.bashrc
echo "PROMPT_COMMAND='history -a'" >> ~/.bashrc



echo -e
echo -e

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
ssh-keygen -f /home/vscode/.ssh/id_rsa  | tee /home/vscode/.ssh/id_rsa.pub >/dev/null
sudo chmod 644 /home/vscode/.ssh/id_rsa.pub
touch /home/vscode/.ssh/known_hosts
sudo chmod 644 /home/vscode/.ssh/known_hosts
echo -e
echo -e
cat /home/vscode/.ssh/id_rsa.pub
# color teh output green
echo -e "\e[32m"
echo "Add the public key above into your Lightsail instance's authorized_keys file, or add it to your Cpanel Dashboard in Namecheap, then configure SFTP IP address and reload the codespace"
echo -e "\e[0m"
else
sudo chmod 700 /home/vscode/.ssh
ssh-keygen -t rsa -b 4096 -f /home/vscode/.ssh/id_rsa -N ""
echo -e
echo -e
# # color teh output green
echo -e "\e[32m"
echo "Use command: cat /home/vscode/.ssh/id_rsa.pub to get the public key to add into your Lightsail instance's authorized_keys file, or add it to your Cpanel Dashboard in Namecheap, then configure SFTP IP address and reload the codespace"
echo -e
echo -e
echo "Use command: cat /home/vscode/.ssh/id_rsa and then Copy the private key above into your github secrets as SSH_KEY to persist this key"
echo -e "\e[0m"
fi
