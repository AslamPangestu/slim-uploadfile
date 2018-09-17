<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

//get all qrcode list generated
$app->get("/qrcode/", function (Request $request, Response $response){
    $sql = "SELECT * FROM tbl_generated_qrcode";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});

//search data
$app->get("/qrcode/search/", function (Request $request, Response $response, $args){
    $keyword = $request->getQueryParam("result");
    $sql = "SELECT * FROM tbl_generated_qrcode WHERE result LIKE '%$keyword%'";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});


//add new data
$app->post("/qrcode/", function (Request $request, Response $response){
    $new_data = $request->getParsedBody();

    $sql = "INSERT INTO tbl_generated_qrcode (result) VALUE (:result)";
    $stmt = $this->db->prepare($sql);

    $data = [
        ":result" => $new_data["result"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});
