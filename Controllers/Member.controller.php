<?php

class Member extends Controller
{

    public static function registrationParametersAreSet(){

        if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['gender'])
            && isset($_POST['birthdate']) && isset($_POST['phone']) && isset($_POST['email']))
            return true;
        else
            return false;
    }
    
    public static function RegisterMember(){

        $verificationToken = Controller::generateToken();
        
        $account = new Members($_POST['firstname'], $_POST['lastname'], $_POST['gender'], $_POST['birthdate'], $_POST['phone'], $_POST['email']);

        $newAccountId = $account->CreateNewAccount($verificationToken);

        if ($newAccountId != null) {

            //Send Confirmation mail
            Mail::sendRegistrationMail($_POST['email'],$newAccountId,$verificationToken);

            //TODO:: Open the registration succeed alert and open the membership page
            

        } else {
            
        }
    }

    public static function confirmActivation(){

        $mail = MailServer::getInstance();

        $result = $mail->verifyActivationCode($_GET['token'],$_GET['email']);

        if($result){

            Controller::redirect('RegistrationNewPassword',['mail' => $_GET['email']]);

        }else{
            
            Controller::redirect('ErrorPage');
        
        }

    }

    //This function intends on setting a new password for the users
    public static function setPassword(){

        $accMail = $_POST['mail'];
        $password = $_POST['password'];
        $confPassword = $_POST['passwordConfirmation'];

        //Check if the password and the confirmation password are the same
        if($password === $confPassword){

            if(Account::setPassword($password,$accMail)){
                
                $result = Account::setActive($accMail);

                setcookie('login',true,time() + (86400 * 30 * 3),"/");
                setcookie('email',$_POST['mail'],time() + (86400 * 30 * 3),"/");

                echo $result;

            }else{
            
                die("Failed setting up the password");
            
            }

        }else{
            
            die("Wrong password");
            
        }

    }

}

//Catch the request for new Member registration
if (isset($_POST['registerNewMember'])) {

    if (Account::isAdmin(1) && Member::registrationParametersAreSet()) {

        Member::RegisterMember();
        
    }else{//Case where the user's access is not prohibited

        Controller::redirect('javascript://history.go(-1)');

    }
}

//Catch the request for mail registration confirmation
if(isset($_GET['token'])&&!empty($_GET['token'])
   &&isset($_GET['email'])&&!empty($_GET['email'])){

    Member::confirmActivation();

}

//Catch the request for sending the password
if(isset($_POST['password'])&&isset($_POST['passwordConfirmation'])
    &&!empty($_POST['password'])&&!empty($_POST['passwordConfirmation'])
    &&isset($_POST['mail'])&&!empty($_POST['mail'])){

    Member::setPassword();

}