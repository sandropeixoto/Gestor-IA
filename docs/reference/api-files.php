<?php
/**
 * API PluguePlus - Gerenciador de Arquivos (v2.0)
 * 
 * Funcionalidades: Upload, Download, Listar, Criar Pasta, Apagar Arquivo/Pasta.
 * Segurança: Autenticação via Token, Sanitização de Caminhos, Validação de Tipos.
 */

// --- 1. CONFIGURAÇÕES ---
define('API_KEY', 'EventosSefa2026');             
define('UPLOAD_DIR', __DIR__ . '/uploads/');     
define('BASE_URL', 'https://eventossefa.com.br/gestor-ia/uploads/'); 

// --- 2. SEGURANÇA ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Api-Key");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$headers = getallheaders();
$receivedKey = $headers['X-Api-Key'] ?? ($_REQUEST['key'] ?? '');

if ($receivedKey !== API_KEY) {
    http_response_code(403);
    echo json_encode(['erro' => 'Acesso negado. API Key inválida.']);
    exit;
}

// --- 3. HELPER SEGURO ---
function getSafePath($path) {
    if (strpos($path, '..') !== false) die(json_encode(['erro' => 'Path Traversal bloqueado.']));
    return UPLOAD_DIR . preg_replace('/[^a-zA-Z0-9_\-\.\/]/', '', $path);
}

function getWebUrl($relativePath) {
    return BASE_URL . ltrim($relativePath, '/');
}

// --- 4. ROTEAMENTO ---
$action = $_REQUEST['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Garante pasta uploads
if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);

switch ($action) {
    case 'upload':
        if ($method !== 'POST') die(json_encode(['erro' => 'Use POST.']));
        $file = $_FILES['file'] ?? ($_FILES['image'] ?? null);
        if (!$file) die(json_encode(['erro' => 'Nenhum arquivo.']));
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt'])) 
            die(json_encode(['erro' => 'Extensão proibida.']));

        $folder = $_POST['path'] ?? ''; 
        $targetDir = getSafePath($folder);
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        $fileName = uniqid() . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $targetDir . '/' . $fileName)) {
            $relativePath = ($folder ? $folder . '/' : '') . $fileName;
            echo json_encode([
                'sucesso' => true,
                'id' => $relativePath,
                'url' => getWebUrl($relativePath)
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['erro' => 'Falha na escrita.']);
        }
        break;

    case 'delete': 
    case 'delete_file':
        $path = $_REQUEST['path'] ?? ($_REQUEST['id'] ?? '');
        $file = getSafePath($path);
        if (is_file($file)) {
            unlink($file);
            echo json_encode(['sucesso' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['erro' => 'Arquivo não encontrado.']);
        }
        break;

    case 'create_folder':
        $path = $_POST['path'] ?? '';
        $dir = getSafePath($path);
        if (mkdir($dir, 0755, true)) echo json_encode(['sucesso' => true]);
        else echo json_encode(['erro' => 'Falha ao criar pasta.']);
        break;

    case 'delete_folder':
        $path = $_REQUEST['path'] ?? '';
        $dir = getSafePath($path);
        if (!is_dir($dir)) die(json_encode(['erro' => 'Pasta não encontrada.']));
        
        // Função Recursiva para limpar pasta antes de deletar
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()) rmdir($file->getRealPath());
            else unlink($file->getRealPath());
        }
        rmdir($dir);
        echo json_encode(['sucesso' => true]);
        break;

    case 'list':
        $path = $_GET['path'] ?? '';
        $dir = getSafePath($path);
        $out = [];
        if (is_dir($dir)) {
            foreach (scandir($dir) as $item) {
                if ($item == '.' || $item == '..') continue;
                $out[] = [
                    'name' => $item,
                    'type' => is_dir("$dir/$item") ? 'folder' : 'file',
                    'path' => ($path ? "$path/" : "") . $item
                ];
            }
        }
        echo json_encode(['records' => $out]);
        break;

    default:
        echo json_encode(['erro' => 'Ação desconhecida.']);
}
?>