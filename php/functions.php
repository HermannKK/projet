<?php
    include_once 'config.php';
    session_start();

    function unquote(string $quoted){
        return stripslashes(substr($quoted, 1, -1));
    }


    function emailExist($unsafeEmail){
        global $db;
        $email=$db->quote($unsafeEmail);
        $sql = "SELECT id, login, mdp, nom, role FROM users WHERE login=$email";
        $query = $db->prepare($sql);
        $query->execute();
        $result = $query->fetch();
        if(!isset($result['login'])){
            return false;
        }
        else{
            return true;
        }
    }

    function login($unsafeEmail,$unsafePassword){
        global $db;
        $email=$db->quote($unsafeEmail);
        $password=$db->quote($unsafePassword);
        if(emailExist($unsafeEmail)){
            $sql = "SELECT id, login, mdp, nom, role FROM users WHERE login=$email";
            $query = $db->prepare($sql);
            $query->execute();
            $result = $query->fetch();
            if(password_verify($password,$result["mdp"])){
                $data = ['error' => ['code'=>NULL,'message'=>NULL], 'success' => true, 'role' =>$result["role"],'id' =>$result["id"]];
                $_SESSION["id"]=$result["id"];
                $_SESSION["login"]=$result["login"];
                $_SESSION["nom"]=$result["nom"];
                $_SESSION["role"]=$result["role"];
            }
            else{
                $data = ['error' => ['code'=>002,'message'=>'Mot de passe incorrect'], 'success' => false];
            }
        }
        else{
            $data = ['error' => ['code'=>001,'message'=>'Adresse email inconnue'], 'success' => false];
        }
        return json_encode( $data );
    }

    function addUser($unsafeEmail,$unsafePassword,$unsafeName,$unsafeRole){
        global $db;
        if(!emailExist($unsafeEmail) && $_SESSION['role']=='admin'){
            $email=$db->quote($unsafeEmail);
            $name=$db->quote($unsafeName);
            $unhasedPassword=$db->quote($unsafePassword);
            $hasedPassword=password_hash($unhasedPassword, PASSWORD_DEFAULT);
            $role=$db->quote($unsafeRole);
            $sql = "INSERT INTO users (login, mdp, nom ,role) VALUES (:email, :hasedPassword ,:name, :role)";
            $query = $db->prepare($sql);
            if($query->execute(['email'=>unquote($email),'hasedPassword'=>$hasedPassword ,"name"=>unquote($name),"role"=>unquote($role)])){
                $data = ['error' => ['code'=>NULL,'message'=>NULL], 'success' => true];
            }
            else{
                $data = ['error' => ['code'=>003,'message'=>'requete non executée'], 'success' => false];
            }
        }else{
            if(!isset($_SESSION['role'])){
                $data = ['error' => ['code'=>004,'message'=>'Autorisation insifisante'], 'success' => false];
            }
            else{
                $data = ['error' => ['code'=>004,'message'=>'Cette adresse email est deja enregistrée'], 'success' => false];
            }
            
        }
        return json_encode( $data );
    }


    function recuperation($unsafeEmail,$unsafePassword,$unsafeName,$unsafeRole){
        global $db;
        if(!emailExist($unsafeEmail) && $_SESSION['role']=='admin'){
            $email=$db->quote($unsafeEmail);
            $name=$db->quote($unsafeName);
            $unhasedPassword=$db->quote($unsafePassword);
            $hasedPassword=password_hash($unhasedPassword, PASSWORD_DEFAULT);
            $role=$db->quote($unsafeRole);
            $sql = "INSERT INTO users (login, mdp, nom ,role) VALUES (:email, :hasedPassword ,:name, :role)";
            $query = $db->prepare($sql);
            if($query->execute(['email'=>unquote($email),'hasedPassword'=>$hasedPassword ,"name"=>unquote($name),"role"=>unquote($role)])){
                $data = ['error' => ['code'=>NULL,'message'=>NULL], 'success' => true];
            }
            else{
                $data = ['error' => ['code'=>003,'message'=>'requete non executée'], 'success' => false];
            }
        }else{
            if(!isset($_SESSION['role'])){
                $data = ['error' => ['code'=>004,'message'=>'Autorisation insifisante'], 'success' => false];
            }
            else{
                $data = ['error' => ['code'=>004,'message'=>'Cette adresse email est deja enregistrée'], 'success' => false];
            }
            
        }
        return json_encode( $data );
    }


?>