<?php

class Database
{
    private PDO $conn;

    public function __construct(private string $host,
                                private string $name,
                                private string $user,
                                private string $password)
    {
        try{
            $this->conn= $this->getConnection();
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
      }
    }

    public function __destruct()
    {
    }
        
    public function getConnection(): PDO
    {
        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";
        
        return new PDO($dsn, $this->user, $this->password, [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);
    }

    public function insertUser(string $Email, string $UserPassword ): void
    {
        $sql = "INSERT INTO teilnehemer (email, passwort)
        VALUES (:email, :passwort)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":email", $Email, PDO::PARAM_STR);
        $stmt->bindValue(":passwort", $UserPassword, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function getUser(string $email): array | false
    {
        $sql = "SELECT *
                FROM teilnehemer
                WHERE email = :email";
                
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":email",  $email, PDO::PARAM_STR);

        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data !== false) {
        }
        
        return $data;
    }
    
    public function ResetPasswort($email, string $newPasswort): void
    {
        $sql = "UPDATE teilnehemer
                SET passwort = :passwort
                WHERE email = :email";
                
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":passwort", $newPasswort, PDO::PARAM_STR);
        
        $stmt->execute();
    }
    
    public function delete($email): void
    {
        $sql = "DELETE FROM teilnehemer
                WHERE email = :email";
                
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        
        $stmt->execute();
    }

}
?>