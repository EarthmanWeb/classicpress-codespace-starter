## SFTP / SSH setup

### For Namecheap (cPanel)

- Enable SSH by searching for "manage SSH" in the Cpanel dashboard, then enable
- Generate a key in Codespaces, using `ssh-keygen`
- Upload the private and public keys to the Cpanel manage ssh section
- Add the Private Key to Github Codespace secrets for this repo
- Copy the `/.vscode/sftp_namecheap.json` file to `/.vscode/sftp.json`
- Populate the config values for your server instance
- Rebuild the codespace

### For Lightsail

- note - the below is already handled in the devcontainer startup script, but if you need to do it manually:

* Generate a key in Codespaces, using `ssh-keygen`
* Connect to ssh directly using the AWS Lightsail web SSH terminal
* Add public key to the `~/.ssh/authorized_keys` file using `sudo nano ~/.ssh/authorized_keys`
* Add the Private Key to Github Codespace secrets for this repo
* Copy the `/.vscode/sftp_lightsail.json` file to `/.vscode/sftp.json`
* Populate the config values for your server instance
* Rebuild the codespace
