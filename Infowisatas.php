<?php

require_once('Database.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: X-Requested-With');

class Infowisatas
{
    private string $table = 'infowisatas';
    private ?string $photo;
    private ?string $wisata;
    private ?string $deskripsi;
    public ?int $id;
    private ?object $statement;

    public function __construct()
    {
        $this->statement = new Database();
        $this->statement = $this->statement->connection;
        $this->photo = $_POST['photo'] ?? null;
        $this->wisata = $_POST['wisata'] ?? null;
        $this->deskripsi = $_POST['deskripsi'] ?? null;
        $this->id = $_GET['id'] ?? null;
    }

    public function infowisatas(): void
    {
        if ($this->id) {
            $infowisata = $this->getInfowisata($this->id);
            $response = [
                'message' => 'Data Info Wisata',
                'Data' => $infowisata
            ];
            echo json_encode($response, JSON_PRETTY_PRINT);
        }else{
            $infowisatas = $this->getInfowisatas();
            $total = count($infowisatas);
            $response = [
                'message' => 'Data Info Wisata',
                'total' => $total,
                'Data' => $infowisatas
            ];
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }

    private function getInfowisatas(): array
    {
        $query = "SELECT * FROM {$this->table}";
        $statement = $this->statement->prepare($query);
        $statement->execute();
        $infowisatas = $statement->fetchAll(PDO::FETCH_OBJ);
        return $infowisatas;
    }
        
    private function getInfowisata(int $id): object
    {
        $query = "SELECT * FROM {$this->table} WHERE wisata_id = :wisata_id";
        $statement = $this->statement->prepare($query);
        $statement->bindParam(':wisata_id', $id);
        $statement->execute();
        if ($statement->rowCount() == 0){
            $response = [
                'message' => 'Data Info Wisata Tidak Ditemukan',
            ];
            http_response_code(404);
            echo json_encode($response, JSON_PRETTY_PRINT);
            exit;
        }
        $infowisata = $statement->fetch(PDO::FETCH_OBJ);
        return $infowisata;
    }
}


$infowisatas = new Infowisatas();

switch ($_SERVER['REQUEST_METHOD']){
    case 'GET':
        $infowisatas->infowisatas();
        break;
    default:
        $response = [
            'messege' => 'Method Not Allowed',
        ];
        http_response_code(405);
        echo json_encode($response);
        break;
}