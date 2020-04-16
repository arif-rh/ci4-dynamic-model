# DynaModel
Dynamic Model for CodeIgniter 4
===============================

## Feature
* Dynamically Creating the Model on-the-fly
* Support One-to-One / Many-to-One relationship
* Support One-to-Many relationship
* Can Filter data based on child/related criteria
* Can set the order of One-to-Many relationship result based on child criteria

## Installation

````composer require arif-rh/ci4-dynamic-model````

## Usage

### Simple
```` 
    // creating postModel on-the-fly, just pass a table name
    $postModel = \Arifrh\DynaModel\DB::table('posts');

    // then you can use it, to get all posts
    $postModel->findAll();
````

### Many-to-One Relationship
````
    // assume that posts always belongs to one author using author_id 
    $postModel->belongsTo('authors);

    // then you can grab author info along with posts
    $postModel->with('authors')->findAll();

    /**
    * by default, primary key of authors will be omit in the column result
    * because its value already exist in the foregin key of relationship
    * 
    * if authors has same column name with posts, then it will be aliased with prefix "author_"
    * for example, both posts and authors has "rating" column, then it will become author_rating
    */

    // you can call only spesific column if you need, pass it on the second parameters in array
    $postModel->with('authors', ['name', 'rating'])->findAll(); 
    // will display all posts column, plus author name and author rating

    // you can filter posts based on author criteria
    $postModel->with('authors')->whereRelation('authors', ['status' => 'active'])->findAll();
    // will display all posts only from active authors
````

### One-to-Many Relationship
````
    $postModel->hasMany('comments');

    // this will return posts with all related comments
    $postModel->with('comments')->findAll();

    // you can also filter posts based on comments criteria
    $postModel->with('comments')->whereRelation('comments', ['status' => 'approved'])->findAll();
    // will display all posts with approved comments only
````
