# seat-smf-bridge
This will be an user/auth bridge with SeAT being the system of record,  syncing users to Simple Machines Forum via API calls.

Quick Installation:
(Under the assumption you have an instance of Simple Machine Forum already installed and working)

In your SeAT home directory, edit the .env file and add the following:

SMF_CONNECTION=mysql
SMF_HOST=127.0.0.1
SMF_PORT=3306
SMF_DATABASE=<name of the SMF database>
SMF_USERNAME=<username to the SMF database>
SMF_PASSWORD=<password to the SMF database>
SMF_PREFIX=smf_
SMF_SETTINGS_PATH=<The full directory path to SMF Forum installation>

Change the above settings to meet your environment.

