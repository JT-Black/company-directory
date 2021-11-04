<?php

namespace App\Controllers;

use Exception;
use App\Exceptions\ValidationException;

class LocationController
{
    public function index() {
        $db = container("db");
        $locations = [];
        $query = 'SELECT * from location ORDER BY name';
        $stmt = $db->query($query);
        while ($row = $stmt->fetch())
        {
            $locations[] = $row;
        }
        return response()->json($locations);
    }

    public function view($id){
        $db = container("db");
        $stmt = $db->prepare('SELECT * FROM location WHERE id = :id');
        $stmt->execute(["id" => $id]);
        $location = $stmt->fetch();
        return response()->json($location);
    }

    public function create(){
        $validator = container("validator");
        $validation = $validator->validate($_POST + $_FILES, [
            'name' => 'required|max:50',
        ]);

        if ($validation->fails()) {
            // handling errors
            $errors = $validation->errors();
            throw new ValidationException($errors->toArray());
        }

        $db = container("db");
        $values = input()->all(["name"]);
        $stmt = $db->prepare('INSERT INTO location (name) VALUES (:name)');
        $stmt->execute($values);

    }

    public function update($id){
        $validator = container("validator");
        $validation = $validator->validate($_POST + $_FILES, [
            'name' => 'required|max:50',
        ]);

        if ($validation->fails()) {
            // handling errors
            $errors = $validation->errors();
            throw new ValidationException($errors->toArray());
        }

        $db = container("db");
        $values = input()->all(["name"]);
        $stmt = $db->prepare('UPDATE location SET name = :name WHERE id = :id');
        $stmt->execute($values + ["id" => $id]);
    }

    public function delete($id){
        $db = container("db");
        $query = 'SELECT count(id) as count FROM department WHERE locationID = :id';

        $stmt = $db->prepare($query);
        $stmt->execute(["id" => $id]);
        $result = $stmt->fetch();

        if ((int) $result["count"] !== 0){

            throw new Exception("Location cannot be deleted!");
        }
        $stmt = $db->prepare('DELETE FROM location WHERE id = :id');
        $stmt->execute(["id" => $id]);

    }
}
