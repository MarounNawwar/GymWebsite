<?php

abstract class Account {

    protected $firstname;
    protected $lastname;
    protected $gender;
    protected $type;
    protected $birthdate;
    protected $phoneNumber;
    protected $email;

    //This is the abstract constructor that will be used to be supe
    public function __construct($firstname,$lastname,$gender,$type,$birthdate,$phoneNumber,$email){
        
        $this->firstname = $firstname;
        $this->lastname = $lastname; 
        $this->gender = $gender;
        $this->type = $type;
        $this->birthdate = $birthdate;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;

    }

    //Function used to create new Accounts 
    //It takes as parameters the token that will be used for the account activation process
    public function CreateNewAccount($token){

        $pdo = DataBase::getConnection();
    
        if($this->mailIsValid($this->email)){

            try{
            
                $statement = "INSERT INTO `user`(`First Name`, `Last Name`, `BirthDate`, `Gender`, `Mail`,`PhoneNumber`, `token`, `active`) VALUES (?,?,?,?,?,?,?,false)";
                $parameters = [$this->firstname,$this->lastname,$this->birthdate,$this->gender,$this->email,$this->phoneNumber,$token];
                $result = $pdo->query($statement,$parameters);

                return $result;

            }catch(Exception $e){

                //Code to show Error in Case registration failed
                $e->getMessage();

            }
    
        }else{

            //TODO: Code to show Error of unvalid Mail

        }

    }

    //Function that returns a boolean value concerning if the login authentication succeeded or not
    public static function loginAuthentication($email,$password){

        $pdo = DataBase::getConnection();

        $password = md5($password);

        try{

            $statement = "SELECT  * FROM `user` WHERE `Mail`= ? AND `Password` = ? ";
            $parameters = [$email,$password];

            return $pdo->hasValidResults($statement,$parameters);

        }catch(Exception $e){

            $e->getMessage();
        
        }

    }

    //Function used to update an already existing account
    public static function updateAccount($firstname,$lastname,$gender,$type,$birthdate,$phoneNumber,$email){

        $accMail = $_COOKIE['email'];
            
        $pdo = DataBase::getConnection();
 
        $statement = "UPDATE `user` SET `firstname`=? ,`lastname`=? ,`gender`=? ,`type`=? ,`birthdate`=? ,`phone`=? ,`Mail`=? WHERE `Mail` = ?";
        $parameters = [$firstname,$lastname,$gender,$type,$birthdate,$phoneNumber,$email,$accMail];

        $pdo->query($statement,$parameters);
        
    }

    //Function used to retrieve an account's information given it's Account ID
    public static function retrieveAccount($AccountID){

        $pdo = DataBase::getConnection();

        $statement = "SELECT * FROM `user` WHERE `ID` = ?";

        $results = $pdo->query($statement,[$AccountID]);

        $account = array();

        foreach($results as $result){
            
            $account['ID'] = $result['ID'];
            $account['firstname'] = $result['First Name'];
            $account['lastname'] = $result['Last Name'];
            $account['gender'] = $result['Gender'];
            $account['birthdate'] = $result['BirthDate'];
            $account['phoneNumber'] = $result['PhoneNumber'];
            $account['email'] = $result['Mail'];

            return $account;
        }

    }

    public static function retrieveAccountID($email){

        $pdo = DataBase::getConnection();

        $statement = "SELECT `ID` FROM `user` WHERE `Mail`= ?";
        $parameters = [$email];

        $results = $pdo->query($statement,$parameters);

        foreach($results as $result){

            return $result['ID'];

        }

    }

    //Function to retrieve the account type of a given user ID
    public static function retrieveAccountType($AccountID){

        $pdo = DataBase::getConnection();

        $statement = "SELECT * FROM `user` WHERE ID = ?";

        $results = $pdo->query($statement,[$AccountID]);

        foreach($results as $result){
            return $result['type'];
        }

    }

    //Check if a given mail is a valid mail for new registration or no
    public static function mailIsValid($email){

        $pdo = DataBase::getConnection();

        $statement = "SELECT * FROM user WHERE Mail = ? ";
        $parameters = [$email];
        return !($pdo->hasValidResults($statement,$parameters));
    }

    //Check if the user account is admin or not
    public static function isAdmin($AccountID){

        $pdo = DataBase::getConnection();
        $statement = "SELECT * FROM `administrator` WHERE `userID` = ? ";
        $params = [$AccountID];
        return $pdo->hasValidResults($statement,$params);

    }

    //Set the password and turn the account status to activated
    public static function setPassword($password,$mail){

        $pdo = DataBase::getConnection();

        //Hashing the password to put it in the 
        $password = md5($password);

        $statement = "UPDATE `user` SET `password` = ? WHERE `Mail` = ?";
        $parameters = [$password,$mail];   

        $result = $pdo->query($statement,$parameters);

        if($result){

            return true;
        
        }else{
        
            return false;
        
        }
    }

    //Set the account that has a given mail to active
    public static function setActive($mail){

        $pdo = DataBase::getConnection();

        $statement = "UPDATE `user` SET `active` = true WHERE `Mail` = ?";
        $parameters = [$mail];

        $result = $pdo->query($statement,$parameters);

        if($result){

            return true;
        
        }else{
        
            return false;
        
        }

    }

    //Getters and Setters

    public function getFirstname(){
        return $this->firstname;
    }

    public function getLastname(){
        return $this->lastname;
    }

    public function getGender(){
        return $this->gender;
    }

    public function getBirthdate(){
        return $this->birthdate;
    }

    public function getPhoneNumber(){
        return $this->phoneNumber;
    }

    public function getMail(){
        return $this->mail;
    }

    public function setFirstname($newFirstName){
        $this->firstname = $newFirstName;
    }

    public function setLastname($newLastName){
        $this->lastname = $newLastName;
    }

    public function setGender($newGender){
        $this->gender = $newGender;
    }

    public function setBirthdate($newBirthDate){
        $this->birthdate = $newBirthDate;
    }

    public function setPhoneNumber($newPhoneNumber){
        $this->phoneNumber = $newPhoneNumber;
    }

    public function setMail($newMail){
        $this->mail = $newMail;
    }

    public function getFullName(){
        return $this->firstname." ".$this->lastname;
    }

}