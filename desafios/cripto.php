<?php

define('PLAINTEXT_FILE', 'arquivo_plano.txt');
define('CIPHERTEXT_FILE', 'arquivo_cifrado.txt');
define('DECRYPTED_FILE', 'arquivo_descriptografado.txt');
define('ENCRYPTION_KEY', "uma_chave_de_criptografia_mais_segura");
define('IV', "1234567890123456");

// Criptografa o arquivo
function encryptFile($plaintext_file, $ciphertext_file, $encryption_key, $iv) {
    $plaintext = file_get_contents($plaintext_file);
    if ($plaintext === false) {
        throw new Exception("Não foi possível ler o arquivo de texto plano");
    }
    $cipher = openssl_encrypt($plaintext, "aes-256-cbc", $encryption_key, OPENSSL_RAW_DATA, $iv);
    if ($cipher === false) {
        throw new Exception("Erro ao criptografar o arquivo");
    }
    if (file_put_contents($ciphertext_file, $cipher) === false) {
        throw new Exception("Erro ao gravar o arquivo criptografado");
    }
}

// Descriptografa o arquivo
function decryptFile($ciphertext_file, $decrypted_file, $encryption_key, $iv) {
    $ciphertext = file_get_contents($ciphertext_file);
    if ($ciphertext === false) {
        throw new Exception("Não foi possível ler o arquivo criptografado");
    }
    $plain = openssl_decrypt($ciphertext, "aes-256-cbc", $encryption_key, OPENSSL_RAW_DATA, $iv);
    if ($plain === false) {
        throw new Exception("Erro ao descriptografar o arquivo");
    }
    if (file_put_contents($decrypted_file, $plain) === false) {
        throw new Exception("Erro ao gravar o arquivo descriptografado");
    }
}

// Executa a criptografia e descriptografia
try {
    encryptFile(PLAINTEXT_FILE, CIPHERTEXT_FILE, ENCRYPTION_KEY, IV);
    decryptFile(CIPHERTEXT_FILE, DECRYPTED_FILE, ENCRYPTION_KEY, IV);
    echo "Arquivo criptografado e descriptografado com sucesso!";
} catch (Exception $e) {
    echo "Ocorreu um erro: " . $e->getMessage();
}

?>

