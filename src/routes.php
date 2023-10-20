<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
// Enable CORS

header("Access-Control-Allow-Origin: http://localhost:5046");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


return function (App $app) {

    
    $container = $app->getContainer();  

    $app->get('/excel', function (Request $request, Response $response, array $args) use ($container) {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        $spreadsheet = $reader->load("uploads/RPS_SI2413_RekayasaPerangkatLunak.xlsx");

        $d = $spreadsheet->getSheet(1)->toArray();

        $str = $spreadsheet->getSheet(1)->getCell('B7')->getValue();

        echo $str;
        return count($str);
    });

    $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });

    $app->get('/about/', function (Request $request, Response $response, array $args) {
        // kirim pesan ke log
        $this->logger->info("ada orang yang mengakses '/about/'");

        // tampilkan pesan
        echo "ini adalah halaman about!";
    });

    $app->get("/buah/", function (Request $request, Response $response) {
        $sql = "SELECT * FROM products";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->get("/buah/{id}", function (Request $request, Response $response, $args) {
        $product_id = $args["id"];
        $sql = "SELECT * FROM products WHERE product_id=:product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":product_id" => $product_id]);
        $result = $stmt->fetch();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->get("/buah/search/", function (Request $request, Response $response, $args) {
        $keyword = $request->getQueryParam("keyword");
        $sql = "SELECT * FROM products WHERE name LIKE '%$keyword%'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->post("/api/fe/regis", function (Request $request, Response $response) {

        $new_regis = $request->getParsedBody();

        $sql = "INSERT INTO user_table (username, password_user, nama_user, alamat_user, peran_user, peran_user) VALUE (:username, :password_user, :nama_user, :alamat_user, :nomor_telepon_user, :peran_user)";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":username" => $new_regis["username"],
            ":password_user" => $new_regis["password_user"],
            ":nama_user" => $new_regis["nama_user"],
            ":alamat_user" => $new_regis["alamat_user"],
            ":nomor_telepon_user" => $new_regis["nomor_telepon_user"],
            ":peran_user" => $new_regis["peran_user"]
        ];

        if ($stmt->execute($data))
            return $response->withJson(["status" => "success", "data" => "1"], 200);

        return $response->withJson(["status" => "failed", "data" => "0"], 200);
    });

    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });
    
    $app->add(function ($req, $res, $next) {
        $response = $next($req, $res);
        return $response
                ->withHeader('Access-Control-Allow-Origin', 'http://localhost:5046')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    });
    
    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($req, $res) {
        $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
        return $handler($req, $res);
    });

    
};