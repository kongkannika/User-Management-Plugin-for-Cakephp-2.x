UserMgmt is a User Management Plugin for cakephp 2.x
Plugin version 2.x (Stable) Last updated on 24-Feb-2015

Hey wanna Demo? `http://usermgmt.ektasoftwares.com`

Wanna more featues? `http://umpremium.ektasoftwares.com`


It's based on jedt/SparkPlug plugin

INSTALLATION
------------

1. Download the latest version or use git to keep the plugin up to date
	https://github.com/kongkannika/User-Management-Plugin-for-Cakephp-2.x
	go to yourapp/app/Plugin
	extract here
	name it Usermgmt

2. Schema import (use your favorite sql tool to import the schema)

	`yourapp/app/Plugin/Usermgmt/Config/Schema/usermgmt-2.x.sql`

3. Configure your AppController class

Your `yourapp/app/Controller/AppController.php` should look like this:

```
<?php
	class AppController extends Controller {
		var $helpers = array('Form', 'Html', 'Session', 'Js', 'Usermgmt.UserAuth');
		public $components = array('Session', 'RequestHandler', 'Usermgmt.UserAuth');
		function beforeFilter(){
			$this->userAuth();
		}
		private function userAuth(){
			$this->UserAuth->beforeFilter($this);
		}
	}
?>
```

4. Enable Plugin in your bootstrap.php

        `yourapp/app/Config/bootstrap.php` should include this line

        // load Usermgmt plugin and apply plugin routes. Keep all the other plugins you are using here
```
        CakePlugin::loadAll(array(
            'Usermgmt' => array('routes' => true, 'bootstrap' => true),
        ));
```

5. Add plugin css in your layout file
    for example `yourapp/app/View/Layouts/default.ctp`
    `echo $this->Html->css('/usermgmt/css/umstyle');`

6. Adjust plugin configuration

    Change `/app/Plugin/Usermgmt/Config/bootstrap.php` (parameters are explained there) to suit your needs.
    Recaptcha support added you should get recatcha keys from google and enter them in /app/Plugin/Usermgmt/Config/bootstrap.php file.
    Please follow the article steps 1 to 7 for recaptcha keys `http://www.chetanvarshney.com/programming/php/recaptcha-keys-for-user-management-plugin`

7. Default user name password 
username- admin
password- test123

ALL DONE !
