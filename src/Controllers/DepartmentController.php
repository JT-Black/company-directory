<?php

namespace App\Controllers;

use App\Exceptions\ValidationException;
use Exception;

class DepartmentController
{
    public function index() {
        $db = container("db");
        $query = 'SELECT * FROM department ORDER BY name';
        $stmt = $db->query($query);
        $departments = $stmt->fetchAll();
        return response()->json($departments);
    }

    public function create(){

        $validator = container("validator");
        $validation = $validator->validate($_POST + $_FILES, [
            'name' => 'required|max:50',
            'locationID' => 'required|integer',
        ]);

        if ($validation->fails()) {
            // handling errors
            $errors = $validation->errors();
            throw new ValidationException($errors->toArray());
        }

        $db = container("db");
        $values = input()->all(["name", "locationID"]);
        $stmt = $db->prepare('INSERT INTO department (name, locationID) VALUES (:name, :locationID)');
        $stmt->execute($values);
    }

    public function update($id){

        $validator = container("validator");
        $validation = $validator->validate($_POST + $_FILES, [
            'name' => 'required|max:50',
            'locationID' => 'required|integer',
        ]);

        if ($validation->fails()) {
            // handling errors
            $errors = $validation->errors();
            throw new ValidationException($errors->toArray());
        }
        
        $db = container("db");
        $values = input()->all(["name", "locationID"]);
        $stmt = $db->prepare('UPDATE department SET name = :name, locationID = :locationID WHERE id = :id');
        $stmt->execute($values + ["id" => $id]);
    }

    public function delete($id){
        $db = container("db");
        $query = 'SELECT * FROM personnel WHERE departmentID = :id';
        $stmt = $db->prepare($query);
        $stmt->execute(["id" => $id]);
        if (! empty($stmt->fetchAll())){

            throw new Exception("Department cannot be deleted!");
        }
        $stmt = $db->prepare('DELETE FROM department WHERE id = :id');
        $stmt->execute(["id" => $id]);
    }
}
