<?php
namespace Source\Controllers;


use Source\Models\User;
use Source\Models\Validations;
require "vendor/autoload.php";
require "source/Config.php";
/* require "../../vendor/autoload.php";
require "../Config.php"; */ 

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        $data = json_decode(file_get_contents("php://input"), false);

        if (!$data) {
            header("HTTP/1.1 400 NÃO DEU!");
            echo json_encode(array("response" => "SEU TESTE FUNCIONOU!"));
            exit;
        }
        $errors = array();
        //validações
        if (!Validations::validarString($data->nome)) {
            array_push($errors, "nome inválido!");
        }
        if (!Validations::validarString($data->sobrenome)) {
            array_push($errors, "sobrenome inválido!");
        }
        if (!Validations::validarEmail($data->email)) {
            array_push($errors, "nome inválido!");
        }
        if (count($errors) > 0) {
            header("HTTP/1.1 400 NÃO DEU!");
            echo json_encode(array("response" => "HÁ CAMPOS INVALIDOS NO FORM", "campos" => $errors));
            exit;
        }

        $user = new User();
        $user->nome = $data->nome;
        $user->sobrenome = $data->sobrenome;
        $user->email = $data->email;
        $user->save();

        if ($user->fail()) {
            header("HTTP/1.1 500 internal server error");
            echo json_encode(array("response" => "Metodo não previsto na API"));
            exit;
        }

        header("HTTP/1.1 201 sucesso!");
        echo json_encode(array("response" => "Usuário criado com sucesso!"));


        break;

    case "GET":
        header("HTML/1.1 200 ok");
        $user = new User();
        if ($user->find()->count() > 0) {
            $return = array();
            foreach ($user->find()->fetch(true) as $user) {
                array_push($return, $user->data());
            }
            echo json_encode(array("response" => $return));
        } else {
            echo json_encode(array("response" => "nenhum usuario localizado"));
        }
        break;

    case "PUT":
        $userID = filter_input(INPUT_GET, "id");
        if (!$userID) {
            header("HTTP:// 400 BAD REQUEST");
            echo json_encode(array("resposta" => "id não informado."));
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), false);
        if (!$data) {
            header("HTTP/1.1 400 NÃO DEU!");
            echo json_encode(array("response" => "SEU TESTE FUNCIONOU!"));
            exit;
        }
        $errors = array();
        if(!Validations::validarInteiro($userID)){
            header("HTTP/1.1 500 erro interno. NÃO DEU!");
            echo json_encode(array("response" => "id invalido. precisa ser numerico."));
            exit;
        }
        if (!Validations::validarString($data->first_name)) {
            array_push($errors, "nome inválido");
        }
        if (!Validations::validarString($data->last_name)) {
            array_push($errors, "sobrenome inválido");
        }
        if (!Validations::validarEmail($data->email)) {
            array_push($errors, "email inválido");
        }
        if (count($errors) > 0) {
            header("HTTP/1.1 400 NÃO DEU!");
            echo json_encode(array("response" => "HÁ CAMPOS INVALIDOS NO FORM", "campos" => $errors));
            exit;
        }

        $user = (new User())->findById($userID);
        if (!$user) {
            header("HTTP/1.1 500 erro interno. NÃO DEU!");
            echo json_encode(array("response" => "nenhum usuário localizado"));
            exit;
        }
        $user->nome = $data->first_name;
        $user->sobrenome = $data->last_name;
        $user->email = $data->email;
        $user->save();

        if ($user->fail()) {
            header("HTTP/1.1 500 erro interno. NÃO DEU!");
            echo json_encode(array("response" => "HÁ CAMPOS INVALIDOS NO FORM", "campos" => $errors));
            exit;
        }
        header("HTTP/1.1 201 Rolou!");
        echo json_encode(array("response" => "cadastro atualizado com sucesso!"));



        break;

    case "DELETE":
        $userID = filter_input(INPUT_GET, "id");
        if (!$userID) {
            header("HTTP:// 400 BAD REQUEST");
            echo json_encode(array("resposta" => "id não informado."));
            exit;
        }
        if(!Validations::validarInteiro($userID)){
            header("HTTP/1.1 500 erro interno. NÃO DEU!");
            echo json_encode(array("response" => "id invalido. precisa ser numerico."));
            exit;
        }

        $user = (new User())->findById($userID);
        if (!$user) {
            header("HTTP/1.1 500 erro interno. NÃO DEU!");
            echo json_encode(array("response" => "nenhum usuário localizado"));
            exit;
        }
        $verify = $user->destroy();

        if ($user->fail()) {
            header("HTTP/1.1 500 erro interno. NÃO DEU!");
            echo json_encode(array("response" => $user->fail()->getMessage()));
            exit;
        }
        if ($verify) {
            header("HTTP/1.1 201 Rolou!");
            echo json_encode(array("response" => "cadastro excluido com sucesso!"));
        } else {
            header("HTTP/1.1 201 Não rolou!");
            echo json_encode(array("response" => "cadastro não pode ser excluido!"));

            exit;
        }

        break;

    default:
        header("HTTP/1.1 401 NÃO AUTORIZADO");
        echo "Metodo não previsto pela API!";
        break;
}
