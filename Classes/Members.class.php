<?php

class Members extends Account{

    public $yoe;
    public $diplomas;

    public function __construct($firstname,$lastname,$gender,$birthdate,$phoneNumber,$email){
        parent::__construct($firstname,$lastname,$gender,"member",$birthdate,$phoneNumber,$email);

    }

    public static function isAlreadyMember($email){
        $pdo = DataBase::getConnection();
        $statement = "SELECT * FROM `account` WHERE `email` = ?";
        $parameters = [$email];
        return $pdo->hasValidResults($statement,$parameters);
    }
    
    public function getType(){
        return "member";
    }

}