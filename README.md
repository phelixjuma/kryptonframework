KRYPTON PHP FRAMEWORK
=========================

A flexible PHP framework for API-based applications


Requirements
============

* PHP >= 7.1
* ext-json
* ext-mbstring
* ext-openssl
* ext-iconv
    

Installation
============

    git clone https://github.com/kuza-lab/kryptonframework.git


Set-up
======

    1. Clone the project into your project directory
    2. Run composer install
    3. Copy .env.example to .env and set the values
    4. Configure server to point to the root directory (where we have the index.php in the same directory as composer.json)
    5. Set up URL re-writing as described below


URL Re-writing: Apache
======================
Create .htaccess file in the document root and paste the following code:

```
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.php [L,QSA]

#RewriteCond %{HTTPS} !on
#RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI}

# Handle Authorization Header
RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
```  

URL Re-writing: Nginx
=====================

Write the following code in your nginx config file

```
location / {
                try_files $uri $uri/ /index.php;
        }
        
location ~ \.php$ {
            try_files      $uri = 404;
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include        fastcgi_params;
    }
```

Usage
=====

1. The models are written in the Models directory
2. The Controllers are written in the Controllers directory
3. Add private public key pair for JWT in the Keys directory

Sample Model: Users.php
=========================

```php

<?php 

    namespace Kuza\Krypton\Framework\Models;

    use Kuza\Krypton\Database\Model;

    Class Users extends Model {
        
        /**
         * Constructor 
         */
        public function __constructor() {
            $table_name = "users";
            parent::__construct($table_name);
        }
        
         /**
          * Add a new user to the database
          * @param $data 
          * @return int|bool 
          */
        public function createUser($data) {
            return parent::insert($data);
        }
        
        public function getUsers() {
            $usersList = parent::select();
            
            return $usersList;
        }
        
        public function getOne($id) {
            $user = parent::selectOne($id);
            
            return $user;
        }
        
        public function updateUser($id, $data) {
            $criteria = [
                "id"    => $id
            ];
            return parent::update($data, $criteria);
        }
        
        public function deleteUser($id) {
            return parent::deleteOne($id);
        }
        
    }

```

Sample Controller: users.php
============================
```php
<?php

namespace Kuza\Krypton\Framework\Controllers;

use Kuza\Krypton\Framework\Models\Users;

/**
 * @var \Kuza\Krypton\Classes\Requests $requests
 */
$requests = $this->requests;

/**
 * @var \Kuza\Krypton\Framework\Models\Users $userModel 
 */
$userModel = $this->DIContainer->get("\Kuza\Krypton\Framework\Models\Users");

$this->router

->get("users", function () use($requests, $userModel) {

    $usersList = $userModel->getUsers();

    if ($usersList) {
        
        $requests->apiData = [
                "success"   => true,
                "message"   => "Users successfully found",
                "data"      => $usersList 
            ];
    } else {
        $requests->apiData = [
                "success"   => false,
                "message"   => "No user found"
            ];
    }

})

->get("users/{id}", function ($id) use($requests, $userModel) {
    
})

->post("users", function() use($requests, $userModel) {
    
})

->patch("users", function() use($requests, $userModel) {
    
})

->delete("users/{id}", function($id) use($requests, $userModel) {
    
});
```


Credits
=======

* Phelix Juma from Kuza Lab Ltd (jumaphelix@kuzalab.com)
* Allan Otieno from Kuza Lab Ltd (allan@kuzalab.com)
