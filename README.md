#User Management Plugin for Cakephp 2.x

UserMgmt is a User Management Plugin for cakephp 2.x
Plugin version 2.x (Stable) Last updated on 24-Feb-2015

Hey wanna Demo? [http://usermgmt.ektasoftwares.com]
[http://usermgmt.ektasoftwares.com]:http://usermgmt.ektasoftwares.com

Wanna more featues? http://umpremium.ektasoftwares.com
[http://umpremium.ektasoftwares.com]:http://umpremium.ektasoftwares.com

It's based on jedt/SparkPlug plugin and forked from [junifar/User-Management-Plugin-for-Cakephp-2.x]
[junifar/User-Management-Plugin-for-Cakephp-2.x]:junifar/User-Management-Plugin-for-Cakephp-2.x

##Main Features

1. Clean code with formatting
2. Login
3. Registration
4. Cookie login/ Remember me functionality
5. Add/Edit/Delete User By Admin
6. Add/Edit/Delete Group By Admin
7. Change Password
8. Forgot Password
9. Change User Password by Admin
10. List of all Users
11. List of all Groups
12. Manage site Permissions using Ajax updation, Permission caching functionality for fast checking
13. User's Email Verification
14. User Profile View
15. User activation by Admin
16. Routing long urls to small urls

##INSTALLATION

1. Download the latest version or use git to keep the plugin up to date

	* [https://github.com/kongkannika/User-Management-Plugin-for-Cakephp-2.x]
	* Go to `yourapp/app/Plugin`
	* Extract here
	* Name it `Usermgmt`
	
[https://github.com/kongkannika/User-Management-Plugin-for-Cakephp-2.x]:https://github.com/kongkannika/User-Management-Plugin-for-Cakephp-2.x

2. Schema import (use your favorite sql tool to import the schema)

	`yourapp/app/Plugin/Usermgmt/Config/Schema/usermgmt-2.x.sql`

3. Configure your AppController class

	Your `yourapp/app/Controller/AppController.php` should look like this:

	```php
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

	```php
	// load Usermgmt plugin and apply plugin routes. Keep all the other plugins you are using here
	CakePlugin::loadAll(array(
	    'Usermgmt' => array('routes' => true, 'bootstrap' => true),
	));
	```

5. Add plugin css in your layout file

	For example `yourapp/app/View/Layouts/default.ctp`
	`echo $this->Html->css('/usermgmt/css/umstyle');`

6. Adjust plugin configuration

	Change `/app/Plugin/Usermgmt/Config/bootstrap.php` (parameters are explained there) to suit your needs.
	Recaptcha support added you should get recatcha keys from google and enter them in `/app/Plugin/Usermgmt/Config/bootstrap.php` file.
	Please follow the article steps 1 to 7 for recaptcha keys [http://www.chetanvarshney.com/programming/php/recaptcha-keys-for-user-management-plugin]
[http://www.chetanvarshney.com/programming/php/recaptcha-keys-for-user-management-plugin]:http://www.chetanvarshney.com/programming/php/recaptcha-keys-for-user-management-plugin

7. Default user name password
 
	username: admin
	password: test123

ALL DONE !

