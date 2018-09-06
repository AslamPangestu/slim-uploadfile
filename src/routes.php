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

//add new data
$app->post("/qrcode/", function (Request $request, Response $response){
    $new_data = $request->getParsedBody();
    //get image
    $uploadedFiles = $request->getUploadedFiles();
    $uploadedFile = $uploadedFiles['qrcode_path'];

    //cek upload success & will safe in tmp
    if($uploadedFile->getError() === UPLOAD_ERR_OK){
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

        // ubah nama file dengan id buku
        $filename = sprintf('%s.%0.8s', $args["id"], $extension);
        
        $directory = $this->get('settings')['upload_directory'];
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        $sql = "INSERT INTO tbl_generated_qrcode (content, qrcode_path) VALUE (:content, :qrcode_path)";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":content" => $new_data["content"],
            ":qrcode_path" => $filename
        ];

        if($stmt->execute($data)){
            // ambil base url dan gabungkan dengan file name untuk membentuk URL file
            $url = $request->getUri()->getBaseUrl()."/uploads/".$filename;
            return $response->withJson(["status" => "success", "data" => "1"], 200);
        }
        
        return $response->withJson(["status" => "failed", "data" => "0"], 200);
    }
});
