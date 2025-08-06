<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Training{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    public function all(){
        $stmt = $this->pdo->query("SELECT * FROM workouts ORDER BY created_at DESC");
        $workouts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($workouts as &$workout){
            $stmt = $this->pdo->prepare("SELECT name, series, weight FROM exercises WHERE workout_id = ?");
            $stmt->execute([$workout["id"]]);
            $workout["execises"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $workouts;
    }

    public function create($data){
        
        
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("INSERT INTO workouts (name) VALUES (?)");
            $stmt->execute([$data["name"]]);
            $workoutId = $this->pdo->lastInsertId();

            foreach($data["exercises"] as &$exercise){
                $stmt = $this->pdo->prepare("INSERT INTO exercises (workout_id, name, series, weight) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $workoutId,
                    $exercise["name"],
                    $exercise["series"],
                    $exercise["weight"]
                ]);

            }
            $this->pdo->commit();
            return true;
           
        }  catch (\PDOException $e) {
            if($this->pdo->inTransaction()){

                $this->pdo->rollBack();
            }
           
            return false;
        }
    }

    public function update($id, $data){
        try {
            $this->pdo->beginTransaction();
            
            //Atualiza o nome do treino
            $stmt = $this->pdo->prepare("UPDATE workouts SET name = ? WHERE id = ?");
            $stmt->execute([$data["name"], $id]);

            //Deleta os exercícios antigos
            $stmt = $this->pdo->prepare("DELETE FROM exercises WHERE workout_id = ?");
            $stmt->execute([$id]);

            //insere os novos exercícios
            foreach($data["exercises"] as $exercise){
                $stmt = $this->pdo->prepare("INSERT INTO exercises (workout_id, name, series, weight) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $id,
                    $exercise["name"],
                    $exercise["series"],
                    $exercise["weight"]
                ]);
            }

            $this->pdo->commit();
            return true;

        } catch (\PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return false;
        }
    } 

    public function delete($id){
        try {
            $stmt = $this->pdo->prepare("DELETE FROM workouts WHERE id = ?");
            $stmt->execute([$id]);

            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    
    }

}
