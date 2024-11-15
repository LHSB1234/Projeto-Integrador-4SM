<?php
require_once '../config/database.php';

class PostController {
    public function getPosts() {
        global $conn;

        // Consulta para obter os posts
        $query = "SELECT * FROM posts";
        $result = $conn->query($query);

        // Array para armazenar os posts
        $posts = [];
        
        // Verifica se a consulta foi bem-sucedida
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $postId = $row['id'];
                $imageQuery = "SELECT image_url FROM post_images WHERE post_id = ?";
                
                // Prepara a consulta para evitar injeção de SQL
                $stmt = $conn->prepare($imageQuery);
                $stmt->bind_param("i", $postId);
                $stmt->execute();
                $imageResult = $stmt->get_result();
                
                // Array para armazenar as imagens
                $images = [];
                while ($imageRow = $imageResult->fetch_assoc()) {
                    $images[] = $imageRow['image_url'];
                }
                
                $row['images'] = $images; 
                $posts[] = $row;
            }
        } else {
            // Tratamento de erro se a consulta falhar
            http_response_code(500); // Código de erro do servidor
            echo json_encode(["error" => "Falha ao buscar posts: " . $conn->error]);
            return; // Retorna para evitar que o resto do código seja executado
        }

        // Retorna os posts como JSON
        header('Content-Type: application/json');
        echo json_encode($posts);
    }
}

// Execute o método diretamente
$controller = new PostController();
$controller->getPosts();
?>
