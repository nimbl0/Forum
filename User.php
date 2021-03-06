<?php

include_once("Database.php");
include_once("SignIn.php");

class User {

    public $id;
    public $name;
    public $email;
    public $password;
    public $registrationDate;
    public $postCount;
    public $avatar;

    public static $dbName = "forum.db";

    public function __construct() {

    }

    public function __toString() {
        return $this->name . ";" . $this->email . ";" . $this->registrationDate . ";" . $this->postCount;
    }

    public static function createUserTable() {
        $db = new Database(User::$dbName);
        $db->connect();

        $sql = "create table if not exists user(
            id integer primary key autoincrement, 
            name text not null,
            password text not null,
            email text not null,
            registrationDate text not null,
            postCount numeric not null,
            avatar text)";
        $stmt = $db->pdo->prepare($sql);
        $stmt->execute();
    }

    public static function create($name, $password, $email, $avatar){
        User::createUserTable();

        $currentDate = new DateTime();

        $db = new Database(User::$dbName);
        $db->connect();

        $sql = "insert into user(name, password, email, registrationDate, postCount) 
                values (:name, :passwd, :email, :registrationDate, :postCount)";

        $stmt = $db->pdo->prepare($sql);

        $stmt->bindValue(":name", $name);
        $stmt->bindValue(":passwd", password_hash($password, PASSWORD_DEFAULT));
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":registrationDate", $currentDate->format("d.m.Y-H:i:s"));
        $stmt->bindValue(":postCount", 0);
        $stmt->bindValue(":avatar", $avatar);
        $stmt->execute();

    }

    public function update($name, $password, $email) {
        User::createUserTable();
        $db = new Database(User::$dbName);
        $db->connect();

        $sql = "update :dbName set name = :name, password = :password, email = :email where id = :id";

        $stmt = $db->pdo->prepare($sql);

        $stmt->bindValue(":dbName", User::$dbName);
        $stmt->bindValue(":name", $name);
        $stmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT));
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":id", $this->id);

        $stmt->execute();

    }

    public static function loadDataById($id): User {
        User::createUserTable();
        $db = new Database(User::$dbName);
        $db->connect();

        $sql = "select * from user where id = $id";
        $stmt = $db->pdo->query($sql);

        $user = new User();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user->id = $row["id"];
            $user->name = $row["name"];
            $user->password = $row["password"];
            $user->email = $row["email"];
            $user->registrationDate = $row["registrationDate"];
            $user->postCount = $row["postCount"];
            $user->avatar = $row["avatar"];
        }

        return $user;
    }


    public static function loadDataByName($name): User {
        User::createUserTable();
        $db = new Database(User::$dbName);
        $db->connect();

        $sql = "select * from user where name = '$name'";
        $stmt = $db->pdo->query($sql);

        $user = new User();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user->id = $row["id"];
            $user->name = $row["name"];
            $user->password = $row["password"];
            $user->email = $row["email"];
            $user->registrationDate = $row["registrationDate"];
            $user->postCount = $row["postCount"];
            $user->avatar = $row["avatar"];
        }

        if($user->id == null) {
            throw new Exception("Username doesn't exist.");
        }

        return $user;
    }

    public static function getAllUsers(): array {
        $users = [];
        User::createUserTable();

        $db = new Database(User::$dbName);
        $db->connect();

        $sql = "select * from user";
        $stmt = $db->pdo->query($sql);

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User();
            $user->id = $row["id"];
            $user->name = $row["name"];
            $user->password = $row["password"];
            $user->email = $row["email"];
            $user->registrationDate = $row["registrationDate"];
            $user->postCount = $row["postCount"];
            $user->avatar = $row["avatar"];

            $users[] = $user;
        }

        return $users;
    }

    public static function deleteUser() {
        $user = User::getUserBySessionId();

        logout();

        $db = new Database(User::$dbName);
        $db->connect();

        $delete = "delete from user where id = '$user->id'";
        $stmt = $db->pdo->prepare($delete);
        $stmt->execute();

        $delete = "delete from posts where authorId = '$user->id'";
        $stmt = $db->pdo->prepare($delete);
        $stmt->execute();

        $delete = "delete from comments where authorId = '$user->id'";
        $stmt = $db->pdo->prepare($delete);
        $stmt->execute();

        $db->close();
    }

    public static function getUserBySessionId(){
        session_start();
        $userId = getUserIdBySessionID(session_id());
        $user = User::loadDataById($userId);

        if($user === null || $userId === "") {
            throw new Exception("");
        }

        return $user;
    }

    public static function getPostCount() {
        $db = new Database(User::$dbName);
        $db->connect();

        $userId = self::getUserBySessionId()->id;
        $select = "select * from posts where authorId = '$userId'";
        $stmt = $db->pdo->query($select);

        $postCount = 0;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $postCount++;
        }

        $db->close();

        return $postCount;
    }

    public static function updatePostCount() {
        $db = new Database(User::$dbName);
        $db->connect();

        $userId = User::getUserBySessionId()->id;
        $postCount = User::getPostCount();

        $update = "update user set postCount = $postCount where id = $userId";
        $stmt = $db->pdo->prepare($update);
        $stmt->execute();

        $db->close();
    }

    public static function updatePostCountById($userId) {
        $db = new Database(User::$dbName);
        $db->connect();

        $postCount = User::getPostCount();

        $update = "update user set postCount = $postCount where id = $userId";
        $stmt = $db->pdo->prepare($update);
        $stmt->execute();

        $db->close();
    }

}