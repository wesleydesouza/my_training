<?php 
namespace App\Controllers;

use App\Models\Training;

class TrainingController{
    private $model;

    public function __construct()
    {
        $this->model = new Training();
    }

    public function index(){
        echo json_encode($this->model->all());
    } 

    public function store(){
        $data = json_decode(file_get_contents("php://input"), true);

        if(!$data || !isset($data["name"]) || !isset($data["exercises"])){
            http_response_code(400);
            echo json_encode(["error" => "Dados inválidos"]);
            return;
        }

        if(!$this->model->create($data)){
            http_response_code(500);
            echo json_encode(["erro" => "Erro ao criar treino"]);
            return;
        }

        http_response_code(201);
        echo json_encode(["message" => "Treino criado com sucesso"]);
    }

    public function update($id){
        $data = json_decode(file_get_contents("php://input"), true);

        if($this->model->update($id, $data)){
            echo json_encode(["message" => "Treino atualizado com sucesso"]);
            return;
        }

        http_response_code(500);
        echo json_encode(["error" => "Erro ao atualizar o treino"]);
    }

    public function destroy($id){
        if($this->model->delete($id)){
            echo json_encode(["message" => "Treino deletado com sucesso"]);
            return;
        }

        http_response_code(500);
        echo json_encode(["error" => "Erro ao deletar o treino"]);
    }
}