<?php

namespace Source\Models;
use CoffeeCode\DataLayer\DataLayer;

class User extends DataLayer
{

    public function __construct()
    {
          parent::__construct("clientes", ["nome", "sobrenome", "email"],"id" ,false);
    }
}