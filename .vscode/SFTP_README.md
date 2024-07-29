## SFTP / SSH setup

### For Pantheon

- Generate a key in Codespaces, using `ssh-keygen` in the codespace terminal
- Retrieve the public Key:  `cat ~/.ssh/id_rsa.pub`
- Upload the Public key to the Pantheon User Settings in the Dashboard
- Retrieve the private Key:  `cat ~/.ssh/id_rsa`
- Add the Private Key to Github Codespace secrets for this repo, call it 'SSH_KEY'
- Copy the `/.vscode/sftp_pantheon.json` file to `/.vscode/sftp.json`
- Populate the config values for your server instance
- Rebuild the codespace
- Set your Remote dev workspace to 'sftp' mode
- Use the SFTP sidebar to connect to the remote server
- Saved files will be auto-uploaded to the remote server

### For Namecheap (cPanel)

- Enable SSH by searching for "manage SSH" in the Cpanel dashboard, then enable
- Generate a key in Codespaces, using `ssh-keygen`  in the codespace terminal
- Retrieve the public Key:  `cat ~/.ssh/id_rsa.pub`
- Upload the private and public keys to the Cpanel 'manage ssh' section
- Retrieve the private Key:  `cat ~/.ssh/id_rsa`
- Add the Private Key to Github Codespace secrets for this repo
- Copy the `/.vscode/sftp_namecheap.json` file to `/.vscode/sftp.json`
- Populate the config values for your server instance
- Rebuild the codespace

### For Lightsail

- Generate a key in Codespaces, using `ssh-keygen` in the codespace terminal
- Connect to ssh directly using the AWS Lightsail web SSH terminal
- Retrieve the public Key:  `cat ~/.ssh/id_rsa.pub`
- Add public key to the `~/.ssh/authorized_keys` file using `sudo nano ~/.ssh/authorized_keys`
- Retrieve the private Key:  `cat ~/.ssh/id_rsa`
- Add the Private Key to Github Codespace secrets for this repo
- Copy the `/.vscode/sftp_lightsail.json` file to `/.vscode/sftp.json`
- Populate the config values for your server instance
- Rebuild the codespace
