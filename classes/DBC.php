<?php
class DBC
{
    public $isConn;
    protected $datab;

    public function __construct($username = DBC_USER, $password = DBC_PASS, $host = DBC_HOST, $dbname = DBC_DB, $options = [])
    {
        $this->isConn = true;
        try {
            $this->datab = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options);
            $this->datab->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->datab->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function Disconnect()
    {
        $this->datab = null;
        $this->isConn = false;
    }

    public function get($query, $params = [])
    {
        try {
            $this->datab->quote($query);
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchALL();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function set($query, $params = [])
    {
        try {
            $this->datab->quote($query);
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function numRows($query, $params = [])
    {
        try {
            $this->datab->quote($query);
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function lastInsertId()
    {
        return $this->datab->lastInsertId();
    }
}

$dbc = new DBC();
?>