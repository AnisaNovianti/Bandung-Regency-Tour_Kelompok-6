<?php

require_once('Database.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: X-Requested-With');

class Users
{
    private string $table = 'users';
    private ?string $nama;
    private ?string $jenis_kelamin;
    private ?string $no_telepon;
    private ?string $alamat;
    private ?string $tanggal_keberangkatan;
    private ?string $jumlah_paket;
    public ?int $id;
    private ?object $statement;

    public function __construct()
    {
        $this->statement = new Database();
        $this->statement = $this->statement->connection;
        $this->nama = $_POST['nama'] ?? null;
        $this->jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
        $this->no_telepon = $_POST['no_telepon'] ?? null;
        $this->alamat = $_POST['alamat'] ?? null;
        $this->tanggal_keberangkatan = $_POST['tanggal_keberangkatan'] ?? null;
        $this->jumlah_paket = $_POST['jumlah_paket'] ?? null;
        $this->id = $_GET['id'] ?? null;
    }

    public function users(): void
    {
        if ($this->id) {
            $user = $this->getUser($this->id);
            $response = [
                'message' => 'Data User',
                'Data' => $user
            ];
            echo json_encode($response, JSON_PRETTY_PRINT);
        }else{
            $users = $this->getUsers();
            $total = count($users);
            $response = [
                'message' => 'Data User',
                'total' => $total,
                'Data' => $users
            ];
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }

    private function getUsers(): array
    {
        $query = "SELECT * FROM {$this->table}";
        $statement = $this->statement->prepare($query);
        $statement->execute();
        $users = $statement->fetchAll(PDO::FETCH_OBJ);
        return $users;
    }
        
    private function getUser(int $id): object
    {
        $query = "SELECT * FROM {$this->table} WHERE user_id = :user_id";
        $statement = $this->statement->prepare($query);
        $statement->bindParam(':user_id', $id);
        $statement->execute();
        if ($statement->rowCount() == 0){
            $response = [
                'message' => 'Data User Tidak Ditemukan',
            ];
            http_response_code(404);
            echo json_encode($response, JSON_PRETTY_PRINT);
            exit;
        }
        $user = $statement->fetch(PDO::FETCH_OBJ);
        return $user;
    }

    public function store(): void
    {
        $query = "SELECT * FROM {$this->table} WHERE no_telepon = :no_telepon";
        $statement = $this->statement->prepare($query);
        $statement->bindParam(':no_telepon', $this->no_telepon);
        $statement->execute();
        if ($statement->rowCount() > 0){
            $response = [
                'message' => 'Data User Sudah Ada',
            ];
            http_response_code(409);
            echo json_encode($response, JSON_PRETTY_PRINT);
    }else{
        $query = "INSERT INTO {$this->table} (nama, jenis_kelamin, no_telepon, alamat, tanggal_keberangkatan, jumlah_paket) VALUES (:nama, :jenis_kelamin, :no_telepon, :alamat, :tanggal_keberangkatan, :jumlah_paket)";
        $statement = $this->statement->prepare($query);
        $statement->bindParam(':nama', $this->nama);
        $statement->bindParam(':jenis_kelamin', $this->jenis_kelamin);
        $statement->bindParam(':no_telepon', $this->no_telepon);
        $statement->bindParam(':alamat', $this->alamat);
        $statement->bindParam(':tanggal_keberangkatan', $this->tanggal_keberangkatan);
        $statement->bindParam(':jumlah_paket', $this->jumlah_paket);
        $statement->execute();
        $getUsers = "SELECT * FROM users WHERE nama = :nama";
        $statement = $this->statement->prepare($getUsers);
        $statement->bindParam(':nama', $this->nama);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_OBJ);
        $response = [
            'message' => 'Data User Berhasil Disimpan',
            'user' => '$user'
        ];
        http_response_code(201);
        echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }
}

$users = new Users();

switch ($_SERVER['REQUEST_METHOD']){
    case 'GET':
        $users->users();
        break;
    case 'POST':
        $users->store();
        break;
    default:
        $response = [
            'messege' => 'Method Not Allowed',
        ];
        http_response_code(405);
        echo json_encode($response);
        break;
}