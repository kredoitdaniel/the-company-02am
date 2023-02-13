<?php

require_once "Database.php";

class User extends Database
{
    // store
    public function store($request)
    {
        // $request will catch the values from $_POST
        $first_name = $request['first_name'];
        $last_name  = $request['last_name'];
        $username   = $request['username'];
        $password   = $request['password'];

        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (first_name, last_name, username, password) VALUES ('$first_name', '$last_name', '$username', '$password')";

        if($this->conn->query($sql)){
            header('location: ../views');
            exit;
        }
        else {
            die('Error creating new user: ' . $this->conn->error);
        }
    }
    // end store


    // login
    public function login($request)
    {
        $username = $request['username'];
        $password = $request['password'];

        $sql = "SELECT * FROM users WHERE username = '$username'";

        $result = $this->conn->query($sql);

        # Check the username
        if($result->num_rows == 1){
            # check if the password is correct
            $user = $result->fetch_assoc();
            // $user = ['id' => 1, 'first_name' => 'james', 'last_name' => 'bond', 'username' => 'james', 'password' => '$%7sh&6*hd#$'];

            if(password_verify($password, $user['password'])){
                # Create session variables for future use.
                session_start();
                $_SESSION['id']         = $user['id'];
                $_SESSION['username']   = $user['username'];
                $_SESSION['full_name']  = $user['first_name']. " " .$user['last_name'];

                header('location: ../views/dashboard.php');
                exit;
            } else {
                die('Password is incorrect.');
            }
        } else {
            die('Username not found.');
        }
    }
    // end login


    // logout
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        header('location: ../views');
        exit;
    }
    // end logout


    // getALlUsers
    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";

        if($result = $this->conn->query($sql)){
            return $result;
        } else {
            die('Error retrieving all users: ' . $this->conn->error);
        }
    }
    // end getALlUsers


    // getUser
    public function getUser()
    {
        $id = $_SESSION['id'];

        $sql = "SELECT first_name, last_name, username, photo FROM users WHERE id = $id";

        if($result = $this->conn->query($sql)){
            return $result->fetch_assoc();
        }
    }
    // end getUser

    // UPDATE USER
    public function update($request, $files){
        session_start();

        $id         = $_SESSION['id'];
        $first_name = $request['first_name'];
        $last_name  = $request['last_name'];
        $username   = $request['username'];
        $photo      = $files['photo']['name'];
        $tmp_photo  = $files['photo']['tmp_name'];

        $sql = "UPDATE users
                SET first_name = '$first_name',
                    last_name = '$last_name',
                    username = '$username'
                WHERE id = $id
                ";

        if($this->conn->query($sql)){
            $_SESSION['username'] = $username;
            $_SESSION['full_name'] = "$first_name $last_name";

            # IF there is an uploaded photo, save the photo name into the db and save the file to the images folder
            if($photo){
                $sql = "UPDATE users SET photo = '$photo' WHERE id = $id";
                $destination = "../assets/images/$photo";

                // SAVE the image name to the DB
                if($this->conn->query($sql)){
                    // SAVE the image file to the images folder
                    if(move_uploaded_file($tmp_photo, $destination)){
                        header('location: ../views/dashboard.php');
                        exit;
                    } else {
                        die("Error in moving the photo.");
                    }
                } else {
                    die("Error in uploading the photo: " . $this->conn->error);
                }
            }

            header('location: ../views/dashboard.php');
            exit;
        } else {
            die("Error in updating the user details: " . $this->conn->error);
        }
    }
    // END OF UPDATE USER


    // DELETE USER
    public function delete()
    {
        session_start();
        $id = $_SESSION['id'];

        $sql = "DELETE FROM users WHERE id = $id";

        if ($this->conn->query($sql)){
            $this->logout();
        }
        else {
            die('Error deleting your account: ' . $this->conn->error);
        }
    }
    // END OF DELETE USER
}