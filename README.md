KRYPTON PHP FRAMEWORK
=========================

A flexible minimalistic PHP framework for API-based applications


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
2. The repositories are written in the repository directory
3. The Controllers are written in the Controllers directory
4. The tests are in the tests directory
5. Automated asynchronous tasks in the tasks directory (if any)
6. routes.php defines the routes
7. Route middlewares are in the middlreware directory
8. Page views (html) are put in the views directory
9. Page layouts are defined in the layouts directory
10. Static files like js/css/images/fonts are all in the static directory

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

Sample API Controller
============================
```php
<?php

namespace Kuza\Krypton\Framework\Controllers;

use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Framework\Controller;
use Kuza\Krypton\Framework\Models\UserModel;
use Kuza\Krypton\Framework\Repository\UserRepository;

class UsersApi extends Controller {

    protected $userRepository;

    /**
     * UsersApi constructor.
     * @param UserModel $user
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     */
    public function __construct(UserRepository $userRepository) {
        parent::__construct();

        $this->userRepository = $userRepository;
    }

    /**
     * Get all users
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     * @throws \Kuza\Krypton\Exceptions\CustomException
     */
    public function allUsers() {

        $usersList = $this->userRepository->getUsers();
        $count = $this->userRepository->countUsers();

        $this->apiResponse(Requests::RESPONSE_OK, true, "", $usersList,[], $count);
    }

    /**
     * @param $userId
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     * @throws \Kuza\Krypton\Exceptions\CustomException
     */
    public function oneUser($userId) {

        $this->userRepository->setUserById($userId);

        $this->apiResponse(Requests::RESPONSE_OK, true, "", $this->userRepository->getUserDetails());
    }

    /**
     * Handle creation of a user
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     * @throws \Kuza\Krypton\Exceptions\CustomException
     */
    public function userRoles($userId) {

        $this->userRepository->setUserById($userId);

        $this->apiResponse(Requests::RESPONSE_OK, true, "", $this->userRepository->getUserDetails());
    }
}
```

Sample View Controller
============================
```php
<?php

namespace Kuza\Krypton\Framework\Controllers;

use Kuza\Krypton\Framework\Controller;
use Kuza\Krypton\Framework\Repository\UserRepository;


class UsersView extends Controller {

    protected $userRepository;

    /**
     * UsersView constructor.
     * @param UserRepository $userRepository
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     */
    public function __construct(UserRepository $userRepository) {
        parent::__construct();

        $this->userRepository = $userRepository;
    }

    /**
     * View the list of users.
     *
     * @Route("/admin/users")
     *
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     * @throws \Kuza\Krypton\Exceptions\CustomException
     */
    public function getUsers() {

        $usersList = $this->userRepository->getUsers();
        $count = $this->userRepository->countUsers();

        $this->view("users", ["usersList" => $usersList, "count" => $count]);
    }
}
```


Credits
=======

* Phelix Juma from Kuza Lab Ltd (jumaphelix@kuzalab.com)
* Allan Otieno from Kuza Lab Ltd (allan@kuzalab.com)
