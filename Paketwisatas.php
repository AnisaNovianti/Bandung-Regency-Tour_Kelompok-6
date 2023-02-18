<?php

require_once('Database.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: X-Requested-With');

class Paketwisatas
{
    private string $table = 'paketwisatas';
    private ?string $photo;
    private ?string $nama_paket;
    private ?string $harga;
    private ?string $paket_wisata;
    private ?string $akomodasi;
    public ?int $id;
    private ?object $statement;

    public function __construct()
    {
        $this->statement = new Database();
        $this->statement = $this->statement->connection;
        $this->photo = $_POST['photo'] ?? null;
        $this->nama_paket = $_POST['nama_paket'] ?? null;
        $this->harga = $_POST['harga'] ?? null;
        $this->paket_wisata = $_POST['paket_wisata'] ?? null;
        $this->akomodasi = $_POST['akomodasi'] ?? null;
        $this->id = $_GET['id'] ?? null;
    }

    public function paketwisatas(): void
    {
        if ($this->id) {
            $paketwisata = $this->getPaketwisata($this->id);
            $response = [
                'message' => 'Data Paket Wisata',
                'Data' => $paketwisata
            ];
            echo json_encode($response, JSON_PRETTY_PRINT);
        }else{
            $paketwisatas = $this->getPaketwisatas();
            $total = count($paketwisatas);
            $response = [
                'message' => 'Data Paket Wisata',
                'total' => $total,
                'Data' => $paketwisatas
            ];
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }

    private function getPaketwisatas(): array
    {
        $query = "SELECT * FROM {$this->table}";
        $statement = $this->statement->prepare($query);
        $statement->execute();
        $paketwisatas = $statement->fetchAll(PDO::FETCH_OBJ);
        return $paketwisatas;
    }
        
    private function getPaketwisata(int $id): object
    {
        $query = "SELECT * FROM {$this->table} WHERE paket_id = :paket_id";
        $statement = $this->statement->prepare($query);
        $statement->bindParam(':paket_id', $id);
        $statement->execute();
        if ($statement->rowCount() == 0){
            $response = [
                'message' => 'Data Paket Wisata Tidak Ditemukan',
            ];
            http_response_code(404);
            echo json_encode($response, JSON_PRETTY_PRINT);
            exit;
        }
        $paketwisata = $statement->fetch(PDO::FETCH_OBJ);
        return $paketwisata;
    }
}


$paketwisatas = new Paketwisatas();

switch ($_SERVER['REQUEST_METHOD']){
    case 'GET':
        $paketwisatas->paketwisatas();
        break;
    default:
        $response = [
            'messege' => 'Method Not Allowed',
        ];
        http_response_code(405);
        echo json_encode($response);
        break;
}