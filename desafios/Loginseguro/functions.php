    <?php

    // Funções para proteção contra ataques de injeção de SQL
    function connect() {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($mysqli->connect_error) {
            die('Erro ao conectar ao banco de dados: ' . $mysqli->connect_error);
        }
        return $mysqli;
    }

    function escape($value) {
        $mysqli = connect();
        $value = $mysqli->real_escape_string($value);
        $mysqli->close();
        return $value;
    }

    // Funções para geração de código QR
    function generateQrCode($email, $secret) {
        require_once 'phpqrcode/qrlib.php';

        // Define o nome do arquivo
        $filename = 'qr_codes/' . md5($email . $secret) . '.png';

        // Gera o código QR
        QRcode::png($secret, $filename, 'L', 6, 2);

        // Retorna o caminho para o arquivo
        return $filename;
    }

    // Funções de autenticação
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    function login($email, $password, $twoFactorCode = null) {
        global $mysqli;

        // Escapa os caracteres especiais
        $email = escape($email);

        // Verifica se o usuário já tentou fazer login muitas vezes
        if (checkRateLimit($email)) {
            return false;
        }

        // Busca o usuário no banco de dados
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = $mysqli->query($query);

        if (!$result || $result->num_rows !== 1) {
            registerLoginAttempt($email, false);
            return false;
        }

        // Verifica se a senha está correta
        $user = $result->fetch_assoc();
        if (!password_verify($password, $user['password'])) {
            registerLoginAttempt($email, false);
            return false;
        }

        // Verifica se a autenticação em dois fatores está habilitada
        if ($user['secret'] && $twoFactorCode) {
            require_once 'GoogleAuthenticator.php';
            $ga = new PHPGangsta_GoogleAuthenticator();
            $result = $ga->verifyCode($user['secret'], $twoFactorCode, 2);

            if (!$result) {
                registerLoginAttempt($email, false);
                return false;
            }
        }

        // Registra a tentativa de login bem-sucedida
        registerLoginAttempt($email, true);

        // Define as variáveis de sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];

        return true;
    }

    function checkRateLimit($email) {
        global $mysqli;

        // Verifica se o usuário já tentou fazer login muitas vezes
        $query = "SELECT COUNT(*) as attempts FROM login_attempts WHERE email = '$email' AND time > (NOW() - INTERVAL 1 HOUR)";
        $result = $mysqli->query($query);

        if (!$result || $result->num_rows !== 1) {
            return false;
        }

        $row = $result->fetch_assoc();
        return $row['attempts'] >= 5;
    }

    function registerLoginAttempt($email, $success) {
        global $mysqli;
        $row = $result->fetch_assoc();
        return $row['attempts'] >= 5;
    }

    function registerLoginAttempt($email, $success) {
        global $mysqli;

        $ip = $_SERVER['REMOTE_ADDR'];
        $time = date('Y-m-d H:i:s');
        $success = $success ? '1' : '0';
        $email = escape($email);

        $query = "INSERT INTO login_attempts (email, ip, time, success) VALUES ('$email', '$ip', '$time', '$success')";
        $mysqli->query($query);
    }

    // Função de logout
    function logout() {
        session_destroy();
    }

    ?>


   
