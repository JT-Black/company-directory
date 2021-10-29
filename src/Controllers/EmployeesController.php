<?php
namespace App\Controllers;

use App\Exceptions\ValidationException;

class EmployeesController
{
    public function index() {
        $db = container("db");
        $query = 'SELECT
                personnel.*,
                department.name AS department,
                location.name AS location
            FROM
                personnel
                LEFT JOIN department ON personnel.departmentID = department.id
                LEFT JOIN location ON department.locationID = location.id';

        if(input("search") !== null) {
            $query .= ' WHERE
                firstName LIKE :search
                OR lastName LIKE :search
                OR email LIKE :search
                OR department.name LIKE :search
                OR location.name LIKE :search';
        }

        $query .= ' ORDER BY
            personnel.lastName,
            personnel.firstName,
            department.name,
            location.name';

        $stmt = $db->prepare($query);

        if(input("search") !== null){
            $stmt->execute(["search" => "%" . input("search") . "%"]);
        } else {
            $stmt->execute();
        }

        $employees = $stmt->fetchAll();
        return response()->json($employees);
    }

    public function create(){

        $validator = container("validator");
        $validation = $validator->validate($_POST + $_FILES, [
            'email' => 'required|email',
            'departmentID' => 'required|integer',
            'firstName' => 'required|max:50',
            'lastName' => 'required|max:50',
            'jobTitle' => 'nullable',
        ]);

        if ($validation->fails()) {
            // handling errors

            $errors = $validation->errors();
            throw new ValidationException($errors->toArray());
        }

        $db = container("db");
        $values = input()->all(["firstName", "lastName", "email", "departmentID", "jobTitle"]);
        $stmt = $db->prepare('INSERT INTO personnel (firstName, lastName, email, departmentID, jobTitle) VALUES (:firstName, :lastName, :email, :departmentID, :jobTitle)');
        $stmt->execute($values);
    }

    public function update($id){

        $validator = container("validator");
        $validation = $validator->validate($_POST + $_FILES, [
            'email' => 'required|email',
            'departmentID' => 'required|integer',
            'firstName' => 'required|max:50',
            'lastName' => 'required|max:50',
            'jobTitle' => 'nullable',
        ]);

        if ($validation->fails()) {
            // handling errors

            $errors = $validation->errors();
            throw new ValidationException($errors->toArray());
        }


        $db = container("db");
        $values = input()->all(["firstName", "lastName", "email", "departmentID", "jobTitle"]);
        $stmt = $db->prepare('UPDATE personnel SET firstName = :firstName, lastName = :lastName, email = :email, departmentID = :departmentID, jobTitle = :jobTitle WHERE id = :id');
        $stmt->execute($values + ["id" => $id]);
    }

    public function delete($id){
        $db = container("db");
        $stmt = $db->prepare('DELETE FROM personnel WHERE id = :id');
        $stmt->execute(["id" => $id]);
    }
}

