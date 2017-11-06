# seat-smf-bridge
This plugin is for Seat-2.x.  It's purpose is to provide a user/authentication bridge between SeAT and Simple Machines Forum (SMF).  The integration is done via cookies and direct database access as the SMF API uses globals everywhere, and .. just .. no.

If you have issues with this, you can contact me on Eve as **Denngarr B'tarn**, or on email as 'denngarr@cripplecreekcorp.com'


## Quick Installation:
(Under the assumption you have an instance of Simple Machine Forum already installed and working)

In your SeAT home directory, edit the .env file and add the following:

```
SMF_CONNECTION=mysql
SMF_HOST=127.0.0.1
SMF_PORT=3306
SMF_DATABASE=<name of the SMF database>
SMF_USERNAME=<username to the SMF database>
SMF_PASSWORD=<password to the SMF database>
SMF_PREFIX=smf_
SMF_SETTINGS_PATH=<The full directory path to SMF Forum installation>
```

Change the above settings to meet your environment.

Next, in your seat directory (By default:  /var/www/seat), type the following:

```
php artisan down
composer require denngarr/smf-bridge
```

After a successful installation, you can include the actual plugin by editing **config/app.php** and adding the following after:

```
        /*
         * Package Service Providers...
         */
```
add
```
        Denngarr\Seat\SmfBridge\SmfBridgeServiceProvider::class
```

and save the file.  Now you're ready to tell SeAT how to use the plugin:

```
php artisan vendor:publish --force
```

And now, when you log into 'Seat', you should see a 'Forum' link on the left.  Do not use this as the 'admin' user.  Use an actual Eve user via SSO is best.

Good luck, and Happy Hunting!!  o7


