<?php

require_once('Database.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: X-Requested-With');

class Infohotels
{
    private string $table = 'infohotels';
    private ?string $photo;
    private ?string $hotel;
    private ?string $deskripsi;
    public ?int $id;
    private ?object $statement;

    public function __construct()
    {
        $this->statement = new Database();
        $this->statement = $this->statement->connection;
        $this->photo = $_POST['photo'] ?? null;
        $this->hotel = $_POST['hotel'] ?? null;
        $this->deskripsi = $_POST['deskripsi'] ?? null;
        $this->id = $_GET['id'] ?? null;
    }

    public function infohotels(): void
    {
        if ($this->id) {
            $infohotel= $this->getInfohotel($this->id);
            $response = [
                'message' => 'Data Info Hotel',
                'Data' => $infohotel
            ];
            echo json_encode($response, JSON_PRETTY_PRINT);
        }else{
            $infohotels = $this->getInfohotels();
            $total = count($infohotels);
            $response = [
                'message' => 'Data Info Hotel',
                'total' => $total,
                'Data' => $infohotels
            ];
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }

    private function getInfohotels(): array
    {
        $query = "SELECT * FROM {$this->table}";
        $statement = $this->statement->prepare($query);
        $statement->execute();
        $infohotels = $statement->fetchAll(PDO::FETCH_OBJ);
        return $infohotels;
    }

    private function getInfohotel(int $id): object
    {
        $query = "SELECT * FROM {$this->table} WHERE hotel_id = :hotel_id";
        $statement = $this->statement->prepare($query);
        $statement->bindParam(':hotel_id', $id);
        $statement->execute();
        if ($statement->rowCount() == 0){
            $response = [
                'message' => 'Data Info Hotel Tidak Ditemukan',
            ];
            http_response_code(404);
            echo json_encode($response, JSON_PRETTY_PRINT);
            exit;
        }
        $infohotel = $statement->fetch(PDO::FETCH_OBJ);
        return $infohotel;
    }
}


$infohotels = new Infohotels();

switch ($_SERVER['REQUEST_METHOD']){
    case 'GET':
        $infohotels->infohotels();
        break;
    default:
        $response = [
            'messege' => 'Method Not Allowed',
        ];
        http_response_code(405);
        echo json_encode($response);
        break;
}